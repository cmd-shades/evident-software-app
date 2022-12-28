<?php
$_version = "3.9.0.0";
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
if (!class_exists("KoolPivotTable", false)) {
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /* =========================================================================== */
  function _ireplace($key, $rep, $str) {
    return str_ireplace($key, $rep, $str);
  }
  function _preg_replace($key, $rep, $str) {
    return preg_replace($key, $rep, $str);
  }
  require_once(dirname(__FILE__) . '/PivotIValueMap.php');
  function Groups_Compare_desc($_group1, $_group2) {
    if ($_group1->_SortValue == $_group2->_SortValue)
      return 0;
    if ($_group1->_SortValue < $_group2->_SortValue)
      return 1;
    if ($_group1->_SortValue > $_group2->_SortValue)
      return -1;
  }
  function Groups_Compare_asc($_group1, $_group2) {
    if ($_group1->_SortValue == $_group2->_SortValue)
      return 0;
    if ($_group1->_SortValue < $_group2->_SortValue)
      return -1;
    if ($_group1->_SortValue > $_group2->_SortValue)
      return 1;
  }
  function _seach($_needle, $_haystack) {
    foreach ($_haystack as $_key => $_value) {
      $_current_key = $_key;
      if ($_needle === $_value)
        return $_current_key;
    }
    return FALSE;
  }
  function _dumpStr() {
    $_numargs = func_num_args();
    $_arg_list = func_get_args();
    for ($i = 1; $i < $_numargs; $i++) {
      $_name = _seach($_arg_list[$i], $_arg_list[0]);
      echo "$" . $_name . " = " . $_arg_list[$i] . "<br />\n";
    }
  }
  function _dumpArr() {
    $_start = '<<';
    $_end = '>>';
    $_numargs = func_num_args();
    $_arg_list = func_get_args();
    for ($i = 1; $i < $_numargs; $i++) {
      $_name = _seach($_arg_list[$i], $_arg_list[0]);
      echo $_start . " $" . $_name . " = ";
      print_r($_arg_list[$i]);
      echo $_end . "<br>";
    }
  }
  function _dumpArrAtr() {
    $_start = '<<';
    $_end = '>>';
    $_numargs = func_num_args();
    $_arg_list = func_get_args();
    for ($i = 2; $i < $_numargs; $i++) {
      $_name = _seach($_arg_list[$i], $_arg_list[0]);
      echo $_start . " $" . $_name . "->" . $_arg_list[1] . " = ";
      print_r($_arg_list[$i]->$_arg_list[1]);
      echo $_end . "<br>";
    }
  }
  class _Key {
    public static function _Inverse($_direction) {
      if ($_direction == _Key::_ASC)
        return _Key::_DESC;
      else if ($_direction == _Key::_DESC)
        return _Key::_ASC;
      else
        return null;
    }
    const _grand = "grand";
    const _grandName = "|->grand<-|";
    const _grandConstant = "'grand'";
    const _DefaultLastField = "|->Default Last Field<-|";
    const _name = "name";
    const _data = "data";
    const _filter = "filter";
    const _column = "column";
    const _row = "row";
    const _ASC = "asc";
    const _DESC = "desc";
    const _field = "field";
    const _pivotField = "pivotField";
    const _value = "value";
    const _comparisonOperator = "comparison operator";
    const _alias = "alias";
    const _Direction = "Direction";
    const _SortValue = "SortValue";
    const _select = "select";
    const _condition = "condition";
    const _group = "group";
    const _GroupValue = "GroupValue";
    const _FieldName = "FieldName";
    const _FieldId = "FieldId";
    const _tabledata = "tabledata";
    const _Command = "Command";
    const _Expand = "Expand";
    const _Collapse = "Collapse";
    const _SortGroup = "SortGroup";
    const _Refresh = "Refresh";
    const _MoveField = "MoveField";
    const _cacheValues = "cache values";
    const _cacheFilters = "cache filters";
    const _cacheFilterConditions = "cache filter conditions";
    const _cacheHeaders = "cache headers";
    const _Level = "Level";
    const _Depth = "Depth";
    const _Length = "Length";
    const _Sort = "Sort";
    const _SortStatus = "SortStatus";
    const _ExceptionList = "ExceptionList";
    const _IncludeAll = "IncludeAll";
    const _AllowReorder = "AllowReorder";
    const _FilterPanelOpen = "FilterPanelOpen";
    const _field_type = "_field_type";
    const _PanelWidth = "PanelWidth";
    const _PanelHeight = "PanelHeight";
    const _equal_to = "equal_to";
    const _not_equal_to = "not_equal_to";
    const _less_than = "less_than";
    const _greater_than = "greater_than";
    const _less_than_or_equal_to = "less_than_or_equal_to";
    const _greater_than_or_equal_to = "greater_than_or_equal_to";
    const _between = "between";
    const _not_between = "not_between";
    const _contain = "contain";
    const _start_with = "start_with";
    const _end_with = "end_with";
    const _Expandable = "Expandable";
    const _ValueChain = "ValueChain";
    const _SQLCondition = "SQLCondition";
    const _nestedSQLCondition = "nestedSQLCondition";
    const _sqlValues = "sqlValues";
    const _allConditions = "allConditions";
    const _allNestedConditions = "allNestedConditions";
    const _UniqueID = "UniqueID";
    const _SubGroupIds = "SubGroupIds";
    const _Key = "Key";
    const _none = "none";
    const _PageIndex = "PageIndex";
    const _PageSize = "PageSize";
    const _TotalRows = "TotalRows";
    const _TotalPages = "TotalPages";
    const _GoPage = "GoPage";
    const _Args = "Args";
    const _ChangePageSize = "ChangePageSize";
    const _PVField_Ids = "PVField_Ids";
    const _CacheID = "CacheID";
    const _HorizontalScrolling = "HorizontalScrolling";
    const _VerticalScrolling = "VerticalScrolling";
    const _ScrollTop = "ScrollTop";
    const _ScrollLeft = "ScrollLeft";
    const _ClientEvents = "ClientEvents";
    const _GroupsToSort = "GroupsToSort";
    const _DefaultSort = "KPTSort";
    const _From = "From";
    const _To = "To";
    const _FromPosition = "FromPosition";
    const _ToPosition = "ToPosition";
    const __viewstate = "_viewstate";
    const _Go = "Go";
    const _Next = "Next";
    const _Prev = "Prev";
    const _Last = "Last";
    const _First = "First";
    const _OkUpper = "Ok";
    const _okLower = "ok";
    const _Cancel = "Cancel";
    const _Includes = "Includes";
    const _Excludes = "Excludes";
    const _Select_All = "Select_All";
    const _Grand_Total = "Grand_Total";
    const _Category_Total = "Category_Total";
    const _Category_Sum = "Category_Sum";
    const _Category_Count = "Category_Count";
    const _Category_Min = "Category_Min";
    const _Category_Max = "Category_Max";
    const _Category_Average = "Category_Average";
    const _Category_PercentageSum = "Category_PercentageSum";
    const _Category_PercentageCount = "Category_PercentageCount";
    const _PageInfoTemplate = "PageInfoTemplate";
    const _ManualPagerTemplate = "ManualPagerTemplate";
    const _PageSizeText = "PageSizeText";
    const _NextPageToolTip = "NextPageToolTip";
    const _PrevPageToolTip = "PrevPageToolTip";
    const _FirstPageToolTip = "FirstPageToolTip";
    const _LastPageToolTip = "LastPageToolTip";
    const _SortHeaderToolTip = "SortHeaderToolTip";
    const _SortAscToolTip = "SortAscToolTip";
    const _SortDescToolTip = "SortDescToolTip";
    const _SortNoneToolTip = "SortNoneToolTip";
    const _ColumnZoneEmptyMessage = "ColumnZoneEmptyMessage";
    const _RowZoneEmptyMessage = "RowZoneEmptyMessage";
    const _FilterZoneEmptyMessage = "FilterZoneEmptyMessage";
    const _DataZoneEmptyMessage = "DataZoneEmptyMessage";
    const _Drag_To_Reorder = "Drag_To_Reorder";
    const _Done = "Done";
    const _Loading = "Loading";
    const _and = "and";
    const _Sorted_Asc = "Sorted_Asc";
    const _Sorted_Desc = "Sorted_Desc";
    const _Filtering = "Filtering";
    const _DataFieldToSort = "DataFieldToSort";
    const _ChangeSortData = "ChangeSortData";
    const _Check = "Check";
    const _checked = "checked";
    const _unchecked = "unchecked";
    const _Width = "Width";
    const _Height = "Height";
    const _OpenFilterPanel = "OpenFilterPanel";
    const _CloseFilterPanel = "CloseFilterPanel";
    const _FilterBy = "FilterBy";
    const _Expression = "Expression";
    const _aggregateFunction = "aggregateFunction";
    const _nestedField = "nestedField";
    const _value1 = "value1";
    const _value2 = "value2";
    const _Options = "Options";
    const _Values = "Values";
    const _r = "r";
    const _c = "c";
    const _sortState = "sortState";
    const _initSort = "initSort";
    const _groupSort = "groupSort";
    const _fieldSort = "fieldSort";
    const _top_N = "top_N";
    const _bottom_N = "bottom_N";
    const _top_percent = "top_percent";
    const _bottom_percent = "bottom_percent";
  }
  class _PV {
    const _Column = 0;
    const _Row = 1;
    const _Filter = 2;
    const _Data = 3;
    public static $_index = array(_Key::_column => _PV::_Column, _Key::_row => _PV::_Row, _Key::_filter => _PV::_Filter, _Key::_data => _PV::_Data);
    public static $_reverse_index = array(_PV::_Column => _Key::_column, _PV::_Row => _Key::_row, _PV::_Filter => _Key::_filter, _PV::_Data => _Key::_data);
    public static $_mapClass = array(
      "table" => "kptTableEx",
      "cell" => "kptCellEx",
      "expandedCell" => "kptExpandedCellEx",
      "dataCell" => "kptDataCellEx",
      "emptyDataCell" => "kptEmptyDataCellEx",
      "columnHeader" => "kptColumnHeaderEx",
      "rowHeader" => "kptRowHeaderEx",
      "totalColumn" => "kptColumnTotalCellEx",
      "totalRow" => "kptRowTotalCellEx",
      "columnHeaderTotal" => "kptColumnHeaderTotalEx",
      "rowHeaderTotal" => "kptRowHeaderTotalEx",
      "dataDesc" => "kptDataDescCellEx",
      "filterZone" => "kptFilterZoneEx",
      "dataZone" => "kptDataZoneEx",
      "columnZone" => "kptColumnZoneEx",
      "rowZone" => "kptRowZoneEx",
      "horizontalScroll" => "kptHorizontalScrollingEx",
      "verticalScroll" => "kptVerticalScrollingEx",
      "fieldItem" => "kptFieldItemEx",
    );
    public static function _mapString($_str) {
      $_classStr = "";
      if (is_string($_str)) {
        $_classes = explode(" ", trim($_str));
        foreach ($_classes as $_class)
          if (!empty($_class)) {
            if (isset(_PV::$_mapClass[$_class]))
              $_classStr .= _PV::$_mapClass[$_class] . " ";
            else
              $_classStr .= $_class . " ";
          }
      }
      return $_classStr;
    }
    public static function _mapArrayKeys($_var) {
      $_arr = array();
      if (is_array($_var))
        foreach ($_var as $_k => $_v)
          $_arr[_PV::$_mapClass[$_k]] = $_v;
      return $_arr;
    }
    public static function _Inverse($_PV_index) {
      if ($_PV_index == _PV::_Row)
        return _PV::_Column;
      else if ($_PV_index == _PV::_Column)
        return _PV::_Row;
      else
        return null;
    }
  }
  class _SQL {
    const _AND = " AND ";
    const _OR = " OR ";
    const _XOR = " XOR ";
    const _AVG = "AVG";
    const _SUM = "SUM";
    const _COUNT = "COUNT";
    const _MIN = "MIN";
    const _MAX = "MAX";
    const _Equal = "=";
    const _Unequal = "!=";
    const _LessThan = "<";
    const _GreaterThan = ">";
    const _LessThanOrEqualTo = "<=";
    const _GreaterThanOrEqualTo = ">=";
    const _Like = " LIKE ";
    const _WildCard = "%";
    const _HaveParentheses = TRUE;
    const _NoParentheses = FALSE;
  }
  interface _ISimpleBuffer {
    function _put($_var);
    function _get();
    function _clear();
    function _empty();
  }
  class _SimpleStack implements _ISimpleBuffer {
    private $_stack = array();
    function _put($_var) {
      array_push($this->_stack, $_var);
    }
    function _get() {
      $_var = array_pop($this->_stack);
      return $_var;
    }
    function _clear() {
      $this->_stack = array();
    }
    function _empty() {
      return empty($this->_stack);
    }
    function _print() {
    }
  }
  class _SimpleQueue implements _ISimpleBuffer {
    private $_queue = array();
    function _put($_var) {
      array_push($this->_queue, $_var);
    }
    function _get() {
      $_var = array_splice($this->_queue, 0, 1);
      if (!empty($_var))
        return $_var[0];
      else
        return NULL;
    }
    function _clear() {
      $this->_queue = array();
    }
    function _empty() {
      return empty($this->_queue);
    }
    function _print() {
    }
  }
  class _OrderedBuffer implements _ISimpleBuffer {
    #code
    private $_buff = array();
    function _put($_var) {
      foreach ($this->_buff as $_k => $_v) {
        if ($_var < $_v) {
          array_splice($this->_buff, $_k, 0, $_var);
          return $this;
        } else if ($_var == $_v)
          return $this;
      }
      array_push($this->_buff, $_var);
      return $this;
    }
    function _get() {
      return array_pop($this->_buff);
    }
    function _clear() {
      $this->_buff = array();
    }
    function _empty() {
      return empty($this->_buff);
    }
  }
  class _SimpleTree {
    var $Expand = FALSE;
    var $_Parent;
    var $_SubGroups;
    var $Value;
    protected $_Level = 1;
    protected $_Depth = 0;
    protected $_Width = 1;
    protected $_Length = 0;
    protected $_Order;
    protected $_visible = TRUE;
    function _AddChild($_group) {
      $_group->_Parent = $this;
      $this->_SubGroups[strtolower($_group->Value)] = $_group;
      if ($this->Expand) {
        $_added_width = $_group->_Width;
        $_added_length = $_group->_Length;
        $_child = $_group;
        $_parent = $this;
        while ($_parent != NULL) {
          $_added_depth = $_child->_Depth - $_parent->_Depth + 1;
          if ($_added_depth > 0)
            $_parent->_Depth += $_added_depth;
          $_parent->_Width += $_added_width;
          $_parent->_Length += $_added_length;
          $_child = $_parent;
          $_parent = $_parent->_Parent;
        }
      }
      $this->_IncreaseLevel($_group, $this->_Level);
    }
    function _hasChild($_var = "") {
      if (!empty($_var)) {
        if (is_string($_var))
          return isset($this->_SubGroups[strtolower($_var)]);
        else if (is_int($_var)) {
          $_arr = array_values($this->_SubGroups);
          return isset($_arr[$_var]) && $_arr[$_var]->_getVisible();
        } else
          return FALSE;
      }
      else {
        return (!empty($this->_SubGroups));
      }
    }
    function _hasExpandedChild($_var = "") {
      if ($this->Expand) {
        if (!empty($_var)) {
          if (is_string($_var))
            return isset($this->_SubGroups[strtolower($_var)]);
          else if (is_int($_var)) {
            $_arr = array_values($this->_SubGroups);
            return isset($_arr[$_var]) && $_arr[$_var]->_getVisible();
          } else
            return FALSE;
        }
        else {
          return (!empty($this->_SubGroups));
        }
      } else
        return FALSE;
    }
    function _getChild($_var = 0) {
      if ($this->_hasChild($_var)) {
        if (is_string($_var))
          return $this->_SubGroups[strtolower($_var)];
        else if (is_int($_var)) {
          $_arr = array_values($this->_SubGroups);
          return $_arr[$_var];
        } else
          return NULL;
      } else
        return NULL;
    }
    function _getChilds() {
      $_arr = array();
      foreach ($this->_SubGroups as $_subGroup)
        if ($_subGroup->_getVisible())
          array_push($_arr, $_subGroup);
      return $_arr;
    }
    function _getParent() {
      return $this->_Parent;
    }
    function _IncreaseLevel($_group, $_added_level) {
      $_buff = new _SimpleStack();
      $_buff->_put($_group);
      while (!$_buff->_empty()) {
        $_e = $_buff->_get();
        $_e->_Level += $_added_level;
        foreach ($_e->_SubGroups as $_sub_group)
          $_buff->_put($_sub_group);
      }
    }
    function _setLevel($_value) {
      $this->_Level = $_value;
      return $this;
    }
    function _GetLevel() {
      return $this->_Level - 3;
    }
    function _GetLevel2() {
      return $this->_Level;
    }
    function _setDepth($_value) {
      $this->_Depth = $_value;
      return $this;
    }
    function _GetDepth() {
      return $this->_Depth;
    }
    function _setWidth($_value) {
      $this->_Width = $_value;
      return $this;
    }
    function _increaseWidth($_value) {
      $this->_Width += $_value;
      return $this;
    }
    function _GetWidth() {
      return $this->_Width;
    }
    function _GetLength() {
      return $this->_Length;
    }
    function _GetOrder() {
      return $this->_Order;
    }
    function _SetOrder($_var) {
      $this->_Order = $_var;
      return $this;
    }
    function _setVisible($_value = TRUE) {
      $this->_visible = $_value;
      return $this;
    }
    function _getVisible() {
      return $this->_visible;
    }
    function _GetProperties(&$_l, &$_d, &$_w, &$_len, &$_o) {
      $_l = $this->_GetLevel();
      $_d = $this->_GetDepth();
      $_w = $this->_GetWidth();
      $_len = $this->_GetLength();
      $_o = $this->_GetOrder();
    }
  }
  class _HtmlIStyle {
    private $_selectors = array();
    private function __construct() {
    }
    public static function _new() {
      $_style = new _HtmlIStyle();
      return $_style;
    }
    function _style($_var) {
      if (is_array($_var)) {
        foreach ($_var as $_selector => $_properties)
          if (!empty($_properties)) {
            if (!isset($this->_selectors[$_selector]))
              $this->_selectors[$_selector] = "";
            if (!strpos($this->_selectors[$_selector], $_properties))
              $this->_selectors[$_selector] .= $_properties . ";";
          }
      }
      return $this;
    }
    function _getStyleScript() {
      $_s = "";
      foreach ($this->_selectors as $_selector => $_properties) {
        $_s .= "." . $_selector . "\n { " . $_properties . " } \n";
      }
      return '<style type="text/css">' . $_s . "</style>";
    }
  }
#Immutable class
  class _HtmlProperty {
    #code
    private $_n;
    private $_v;
    private function __construct() {
    }
    public static function _new($_name = NULL, $_value = NULL) {
      $_prt = new _HtmlProperty();
      if (isset($_name))
        $_prt->_n = $_name;
      if (isset($_value))
        $_prt->_v = $_value;
      return $_prt;
    }
    function _GetExpression($_quote = '"') {
      if (isset($this->_n) && isset($this->_v))
        return $this->_n . '=' . $_quote . $this->_v . $_quote;
      else
        return "";
    }
    function _GetName() {
      if (isset($this->_n))
        return $this->_n;
      else
        return "";
    }
    function _GetValue() {
      if (isset($this->_v))
        return $this->_v;
      else
        return "";
    }
  }
  class _HtmlElement {
    #code
    protected $_template = "<{tag} {properties}>{content}</{tag}>";
    protected $_con;
    protected $_iStyle;
    protected $_Properties = array();
    protected $_ChildGroups = array();
    protected $_tag;
    function __construct() {
      $this->_iStyle = _HtmlIStyle::_new();
    }
    public static function _new($_var) {
      $_e = new _HtmlElement();
      $_e->_tag($_var);
      return $_e;
    }
    function _setIStyle($_var) {
      if (is_array($_var))
        $this->_iStyle->_style($_var);
      return $this;
    }
    function _getIStyle() {
      return $this->_iStyle->_getStyleScript();
    }
    function _tag($_var) {
      $this->_template = _replace("{tag}", $_var, $this->_template);
      $this->_tag = $_var;
      return $this;
    }
    function _getTag() {
      return $this->_tag;
    }
    function _Colspan($_var) {
      $_prt = _HtmlProperty::_new("colspan", $_var);
      $this->_Properties["cs"] = $_prt;
      return $this;
    }
    function _GetColspan() {
      if (isset($this->_Properties["cs"]))
        return $this->_Properties["cs"]->_GetValue();
      else
        return 1;
    }
    function _Rowspan($_var) {
      $_prt = _HtmlProperty::_new("rowspan", $_var);
      $this->_Properties["rs"] = $_prt;
      return $this;
    }
    function _GetRowspan() {
      if (isset($this->_Properties["rs"]))
        return $this->_Properties["rs"]->_GetValue();
      else
        return 1;
    }
    function _Align($_var) {
      $_prt = _HtmlProperty::_new("align", $_var);
      $this->_Properties["al"] = $_prt;
      return $this;
    }
    function _Width($_var) {
      $_prt = _HtmlProperty::_new("width", $_var);
      $this->_Properties["w"] = $_prt;
      return $this;
    }
    function _Style($_var) {
      $_prt = _HtmlProperty::_new("style", $_var);
      $this->_Properties["st"] = $_prt;
      return $this;
    }
    function _class() {
      $_class = "";
      $_numargs = func_num_args();
      $_arg_list = func_get_args();
      for ($i = 0; $i < $_numargs; $i++)
        $_class .= $_arg_list[$i] . " ";
      $_prt = _HtmlProperty::_new("class", $_class);
      $this->_Properties["cl"] = $_prt;
      return $this;
    }
    function _id($_var) {
      $_prt = _HtmlProperty::_new("id", $_var);
      $this->_Properties["id"] = $_prt;
      return $this;
    }
    function _Content($_var) {
      $this->_con = $_var;
      return $this;
    }
    function _property($_classStr, $_prt) {
      $_classes = explode(" ", trim($_classStr));
      foreach ($_classes as $_class) {
        if (isset($_prt[$_class])) {
          foreach ($_prt[$_class] as $_n => $_v) {
            $_htmlPrt = _HtmlProperty::_new($_n, $_v);
            array_push($this->_Properties, $_htmlPrt);
          }
        }
      }
      return $this;
    }
    function _AddProperties($_var) {
      if ($_var instanceof _HtmlProperty)
        array_push($this->_Properties, $_var);
      else if (is_array($_var)) {
        foreach ($_var as $_n => $_v) {
          $_prt = _HtmlProperty::_new($_n, $_v);
          array_push($this->_Properties, $_prt);
        }
      }
      return $this;
    }
    function _setProperties($_var) {
      $this->_Properties = $_var;
      return $this;
    }
    function _getProperties() {
      return $this->_Properties;
    }
    function _GetPropertiesExpression($_quote = '"') {
      $_s = "";
      foreach ($this->_Properties as $_prt)
        if (isset($_prt))
          $_s .= $_prt->_GetExpression($_quote) . " ";
      return $_s;
    }
    function _AddChild($_var, $_position = -1) {
      return $this->_AddChildGroup(array($_var), $_position);
    }
    function _AddChildGroup($_var, $_position = -1) {
      $_len = count($this->_ChildGroups);
      if (($_position < 0) || ($_position > $_len))
        $_position = $_len;
      array_splice($this->_ChildGroups, $_position, 0, array($_var));
      return $this;
    }
    function _SetChild($_var, $_position) {
      return $this->_SetChildGroup(array($_var), $_position);
    }
    function _SetChildGroup($_var, $_position) {
      if ($_position > -1)
        $this->_ChildGroups[$_position] = $_var;
      return $this;
    }
    function _SortChildGroupAsc() {
      ksort($this->_ChildGroups);
    }
    function _GetColArray() {
      $_arr = array();
      foreach ($this->_ChildGroups as $_childs) {
        foreach ($_childs as $_child) {
          if (isset($_child) && $_child->_GetContent() != "") 
            array_push($_arr, $_child);
        }
      }
      return $_arr;
    }
    function _GetRowArray() {
      $_arr = array();
      foreach ($this->_ChildGroups as $_childs) {
        foreach ($_childs as $_child) {
          if ($_child->_getTag() === 'tr') {
            if (isset($_child) && $_child->_GetContent() != "") 
              array_push($_arr, $_child);
          }
          else {
            $_rows = $_child->_GetRowArray();
            foreach ($_rows as $_row)
              if (isset($_row) && $_row->_GetContent() != "") 
                array_push($_arr, $_row);
          }
        }
      }
      return $_arr;
    }
    function _GetContent() {
      $_s = $this->_con;
      foreach ($this->_ChildGroups as $_childs)
        foreach ($_childs as $_child)
          if (isset($_child))
            $_s .= $_child->_GetHtml();
      return $_s;
    }
    function _GetHtml() {
      $_content = $this->_GetContent();
      if (isset($_content) && $_content !== '') {
        $_s = $this->_template;
        $_s = _replace("{properties}", $this->_GetPropertiesExpression(), $_s);
        $_s = _replace("{content}", $_content, $_s);
        return $_s;
      } else
        return "";
    }
    function _GetNonTagContent($_changes = array(), $_caseSensitive = TRUE) {
      $_s = $this->_replaceAll($_changes, $this->_con, $_caseSensitive);
      foreach ($this->_ChildGroups as $_childs)
        foreach ($_childs as $_child)
          if (isset($_child))
            $_s .= $_child->_GetNonTagContent($_changes, $_caseSensitive);
      return $_s;
    }
    function _GetChangedContent($_changes, $_caseSensitive = TRUE) {
      $_s = $this->_replaceAll($_changes, $this->_con, $_caseSensitive);
      foreach ($this->_ChildGroups as $_childs)
        foreach ($_childs as $_child)
          if (isset($_child))
            $_s .= $_child->_GetChangedHtml($_changes, $_caseSensitive);
      return $_s;
    }
    function _getChangedHtml($_changes, $_caseSensitive = TRUE, $_quote = '"') {
      $_content = $this->_GetChangedContent($_changes, $_caseSensitive);
      if (isset($_content) && $_content !== '') {
        $_s = $this->_template;
        $_s = _replace("{properties}", $this->_GetPropertiesExpression($_quote), $_s);
        $_s = _replace("{content}", $_content, $_s);
        return $_s;
      } else
        return "";
    }
    function _replaceAll($_changes, $_str, $_caseSensitive = TRUE) {
      foreach ($_changes as $_key => $_rep) {
        if ($_caseSensitive)
          $_str = _replace($_key, $_rep, $_str);
        else
          $_str = _ireplace($_key, $_rep, $_str);
      }
      return $_str;
    }
  }
  class _HtmlCol extends _HtmlElement {
  }
  class _HtmlRow extends _HtmlElement {
  }
  class _HtmlTable extends _HtmlElement {
    #code
    function _GetArrayWithBlank() {
      $_bl_col = _HtmlProvider::_newCol()->_Content("blank");
      $_rows = $this->_GetRowArray();
      $_arr = array();
      $_max_row_len = 0;
      #Expanded colspan of each row left to right.
      foreach ($_rows as $_r => $_row) {
        $_cols = $_row->_GetColArray();
        $_arr[$_r] = $_cols;
        $_offset = 1;
        foreach ($_cols as $_c => $_col) {
          $_cs = $_col->_GetColspan();
          for ($_j = 1; $_j < $_cs; $_j++)
            array_splice($_arr[$_r], $_c + $_offset, 0, array($_bl_col));
          $_offset += $_cs - 1;
        }
        $_row_len = count($_arr[$_r]);
        if ($_row_len > $_max_row_len)
          $_max_row_len = $_row_len;
      }
      $_total_row = count($_arr);
      #Expanded rowspan by columns left to right, NOT by rows top to bottom.
      $_position = 0;
      while ($_position < $_total_row * $_max_row_len) {
        $_r = $_position % $_total_row;
        $_c = (integer) $_position / $_total_row;
        if (isset($_arr[$_r][$_c])) {
          $_col = $_arr[$_r][$_c];
          $_cs = $_col->_GetColspan();
          $_rs = $_col->_GetRowspan();
          for ($_i = 1; $_i < $_rs; $_i++) {
            for ($_j = 0; $_j < $_cs; $_j++)
              array_splice($_arr[$_r + $_i], $_c, 0, array($_bl_col));
            $_row_len = count($_arr[$_r + $_i]);
            if ($_row_len > $_max_row_len)
              $_max_row_len = $_row_len;
          }
        }
        $_position++;
      }
      return $_arr;
    }
  }
  class _HtmlProvider {
    #code
    public static function _newCol() {
      $_e = new _HtmlCol();
      return $_e->_tag("td");
    }
    public static function _newRow() {
      $_e = new _HtmlRow();
      return $_e->_tag("tr");
    }
    public static function _newTable() {
      $_e = new _HtmlTable();
      return $_e->_tag("table");
    }
    public static function _newDiv() {
      $_e = _HtmlElement::_new("div");
      return $_e;
    }
    public static function _newSpan() {
      $_e = _HtmlElement::_new("span");
      return $_e;
    }
    public static function _newElement($tag) {
      $_e = _HtmlElement::_new($tag);
      return $_e;
    }
  }
  class _SQLCondition {
    protected $_SQLExpression;
    private function __construct() {
      $this->_SQLExpression = "";
    }
    public static function _newCondition($_sql_condition = "") {
      $_condition = new _SQLCondition();
      if ($_sql_condition instanceof _SQLCondition)
        $_condition->_SQLExpression = $_sql_condition->_ToSQLExpression();
      else if (is_string($_sql_condition))
        $_condition->_SQLExpression = $_sql_condition;
      return $_condition;
    }
    function _expression($_expression) {
      if (is_string($_expression))
        $this->_SQLExpression = $_expression;
      return $this;
    }
    function _addParentheses() {
      $this->_SQLExpression = "(" . $this->_SQLExpression . ")";
      return $this;
    }
    function _AddCondition($_sql_condition, $_operator = _SQL::_AND, $_conditionGroup = _SQL::_HaveParentheses) {
      $_expression = $this->_SQLExpression;
      if ($_sql_condition instanceof _SQLCondition)
        $s = $_sql_condition->_ToSQLExpression();
      else if (is_string($_sql_condition))
        $s = $_sql_condition;
      else
        return NULL;
      if ($s != "") {
        if ($this->_SQLExpression != "") {
          if ($_conditionGroup == _SQL::_HaveParentheses)
            $_expression = $_expression . $_operator . "(" . $s . ")";
          else if ($_conditionGroup == _SQL::_NoParentheses)
            $_expression = $_expression . $_operator . $s;
        }
        else {
          if ($_conditionGroup == _SQL::_HaveParentheses)
            $_expression = "(" . $s . ")";
          else if ($_conditionGroup == _SQL::_NoParentheses)
            $_expression = $s;
        }
      }
      $this->_SQLExpression = $_expression;
      return $this;
    }
    function _ToSQLExpression() {
      return $this->_SQLExpression;
    }
  }
  class _PivotDataSource {
    public $CharSet;
    public $Link;
    protected $_select;
    protected $_distinct;
    protected $_from;
    protected $_where;
    protected $_groupby;
    protected $_selectFields;
    protected $_numberOfFields;
    protected $_fromView;
    protected $_querySize = 0;
    function __construct($_link) {
      $this->Link = $_link;
      $this->_numberOfFields = 0;
      $this->_distinct = FALSE;
      $this->_querySize = 0;
    }
    function setQuerySize($_var) {
      if (is_int($_var))
        $this->_querySize = $_var;
      return $this;
    }
    function getQuerySize() {
      return $this->_querySize;
    }
    function select($_select) {
      if (is_string($_select)) {
        $this->_select = $_select;
        $_fields = explode(",", $_select);
        if (is_array($_fields))
          foreach ($_fields as $_field)
            if (!empty($_field)) {
              $_i = strripos($_field, ' as ');
              if ($_i > 0) {
                $this->_selectFields[$this->_numberOfFields]["expression"] = trim(substr($_field, 0, $_i));
                $this->_selectFields[$this->_numberOfFields]["alias"] = trim(substr($_field, $_i + 4));
              } else {
                $this->_selectFields[$this->_numberOfFields]["expression"] = trim($_field);
                $this->_selectFields[$this->_numberOfFields]["alias"] = trim($_field);
              }
              $this->_numberOfFields++;
            }
      } else if (is_array($_select)) {
        foreach ($_select as $_field)
          if (!empty($_field)) {
            $_i = strripos($_field, ' as ');
            if ($_i > 0) {
              $this->_selectFields[$this->_numberOfFields]["expression"] = trim(substr($_field, 0, $_i));
              $this->_selectFields[$this->_numberOfFields]["alias"] = trim(substr($_field, $_i + 4));
            } else {
              $this->_selectFields[$this->_numberOfFields]["expression"] = trim($_field);
              $this->_selectFields[$this->_numberOfFields]["alias"] = trim($_field);
            }
            $this->_numberOfFields++;
          }
      }
      return $this;
    }
    function _getSelectFields() {
      return $this->_selectFields;
    }
    function _distinct($_var) {
      $this->_distinct = $_var;
      return $this;
    }
    function selectCommand($_str) {
      $this->_fromView = $_str;
      return $this;
    }
    function from($_str) {
      $this->_from = $_str;
      return $this;
    }
    function join($_table) {
      $this->_from = " (" . $this->_from . " JOIN " . $_table;
      return $this;
    }
    function fullJoin($_table) {
      $this->_from = " (" . $this->_from . " FULL JOIN " . $_table;
      return $this;
    }
    function leftJoin($_table) {
      $this->_from = " (" . $this->_from . " LEFT JOIN " . $_table;
      return $this;
    }
    function rightJoin($_table) {
      $this->_from = " (" . $this->_from . " RIGHT JOIN " . $_table;
      return $this;
    }
    function innerJoin($_table) {
      $this->_from = " (" . $this->_from . " INNER JOIN " . $_table;
      return $this;
    }
    function on($_condition) {
      $this->_from .= " ON " . $_condition . ") ";
      return $this;
    }
    function where($_str) {
      $this->_where = $_str;
      return $this;
    }
    function groupby($_groupby) {
      $this->_groupby = $_groupby;
      return $this;
    }
    function _buildSelectQuery($_fields, $_conditions, $_group_fields) {
      $_fKey = _Key::_field;
      $_cKey = _Key::_allConditions;
      if (!empty($this->_fromView)) {
        $_fKey = _Key::_nestedField;
        $_cKey = _Key::_allNestedConditions;
      }
      $_select = "";
      if (!empty($_fields)) {
        foreach ($_fields as $_field) {
          $_s = trim($_field[$_fKey]);
          if (isset($_field[_Key::_aggregateFunction]))
            $_s = $_field[_Key::_aggregateFunction] . "(" . $_s . ")";
          $_select .= $_s . " AS " . $this->_StrToSQLColumn($_field[_Key::_alias]) . ", ";
        }
        $_select = trim($_select, ", ");
      }
      $_distinct = ($this->_distinct) ? "DISTINCT " : "";
      if ($_select != "")
        $_select = "SELECT " . $_distinct . $_select;
      $_where = "";
      if (isset($_conditions))
        $_where = trim($_conditions[$_cKey]->_ToSQLExpression());
      if (empty($this->_fromView))
        if (!empty($this->_where)) {
          if ($_where != "")
            $_where .= " AND " . $this->_where;
          else
            $_where = $this->_where;
        }
      if ($_where != "")
        $_where = " WHERE " . $_where;
      $_group = "";
      if (!empty($_group_fields)) {
        foreach ($_group_fields as $_group_field) {
          $_group .= $_group_field[$_fKey] . ", ";
        }
        $_group = trim($_group, ", ");
      }
      if (empty($this->_fromView))
        if (!empty($this->_groupby)) {
          if ($_group != "")
            $_group .= " , " . $this->_groupby;
          else
            $_group = $this->_groupby;
        }
      if ($_group != "")
        $_group = " GROUP BY " . $_group;
      $_from = " FROM ";
      if (empty($this->_fromView))
        $_from .= $this->_from;
      else
        $_from .= "(" . $this->_fromView . ") tmp ";
      $_query = $_select . $_from . $_where . $_group;
      return $_query;
    }
    function _EscapeString($_str) {
      return _replace("'", "''", $_str);
    }
    function _HtmlToSqlStr($_str) {
      $_sql = $this->_EscapeString($_str);
      $_sql = _replace(htmlentities("&"), "&", $_sql);
      return $_sql;
    }
    function _StrToSQLStrConstant($_str) {
      return "'" . $_str . "'";
    }
    function _StrToSQLColumn($_str) {
      return "\"" . $_str . "\"";
    }
    function _toSQLDate($_str) {
      switch (strtolower($_str)) {
        case "year": return "year";
        case "quarter": return "quarter";
        case "month": return "month";
        case "day": return "";
        default: return "error";
      }
    }
  }
  class PdoPivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_statement = $this->Link->prepare($_query);
      $_statement->execute();
      while ($_row = $_statement->fetch(PDO::FETCH_ASSOC)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
    function _queryLimit($_query, $_offset, $_querySize) {
      $_query .= " LIMIT " . $_offset . "," . $_querySize;
      $_arr = array();
      $_statement = $this->Link->prepare($_query);
      $_statement->execute();
      while ($_row = $_statement->fetch(PDO::FETCH_ASSOC)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
  }
  class PostgreSQLPivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_result = pg_query($this->Link, $_query);
      while ($_row = pg_fetch_assoc($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
    function _queryLimit($_query, $_offset, $_querySize) {
      $_query .= " LIMIT $_querySize OFFSET $_offset";
      $_arr = array();
      $_result = pg_query($this->Link, $_query);
      while ($_row = pg_fetch_assoc($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
  }
  class MySQLiPivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_result = mysqli_query($this->Link, $_query);
      $_arr = mysqli_fetch_all($_result, MYSQLI_ASSOC);
      return $_arr;
    }
    function _queryLimit($_query, $_offset, $_querySize) {
      $_query .= " LIMIT " . $_offset . "," . $_querySize;
      $_arr = array();
      $_result = mysqli_query($this->Link, $_query);
      $_arr = mysqli_fetch_all($_result, MYSQLI_ASSOC);
      return $_arr;
    }
  }
  class MySQLPivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_result = mysql_query($_query, $this->Link);
      while ($_row = mysql_fetch_assoc($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
    function _queryLimit($_query, $_offset, $_querySize) {
      $_query .= " LIMIT " . $_offset . "," . $_querySize;
      $_arr = array();
      $_result = mysql_query($_query, $this->Link);
      while ($_row = mysql_fetch_assoc($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
  }
  class FireBirdPivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_result = ibase_query($this->Link, $_query);
      while ($_row = ibase_fetch_assoc($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
    function _queryLimit($_query, $_offset, $_querySize) {
      $_query .= " ROWS " . ($_offset + 1) . " TO " . ($_offset + 1 + $_querySize);
      $_arr = array();
      $_result = ibase_query($_query, $this->Link);
      while ($_row = ibase_fetch_assoc($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
  }
  class ODBCPivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_result = odbc_exec($this->Link, $_query);
      while ($_row = odbc_fetch_array($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
  }
  class MSSQLPivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_result = mssql_query($_query, $this->Link);
      while ($_row = mssql_fetch_array($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
    function _queryLimit($_query, $_offset, $_querySize) {
      $_selectPos = strpos($_query, "SELECT ");
      $_asPos = strpos($_query, " AS ");
      $_field = substr($_query, $_selectPos + 7, $_asPos - $_selectPos - 7);
      $_query = substr_replace($_query, "ROW_NUMBER() OVER (ORDER BY $_field) AS RowNumber, ", $_selectPos + 7, 0);
      $_query = "SELECT * FROM ($_query) tmp2 WHERE RowNumber BETWEEN $_offset AND $_querySize ";
      $_arr = array();
      $_result = mssql_query($_query, $this->Link);
      while ($_row = mssql_fetch_assoc($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
  }
  class SQLSRVPivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_result = sqlsrv_query($this->Link, $_query);
      while ($_row = sqlsrv_fetch_array($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
    function _queryLimit($_query, $_offset, $_querySize) {
      $_selectPos = strpos($_query, "SELECT ");
      $_asPos = strpos($_query, " AS ");
      $_field = substr($_query, $_selectPos + 7, $_asPos - $_selectPos - 7);
      $_query = substr_replace($_query, "ROW_NUMBER() OVER (ORDER BY $_field) AS RowNumber, ", $_selectPos + 7, 0);
      $_query = "SELECT * FROM ($_query) tmp2 WHERE RowNumber BETWEEN $_offset AND $_querySize ";
      $_arr = array();
      $_result = sqlsrv_query($this->Link, $_query);
      while ($_row = sqlsrv_fetch_array($_result)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
  }
  class OraclePivotDataSource extends _PivotDataSource {
    function _queryAll($_query) {
      $_arr = array();
      $_statement = oci_parse($this->Link, $_query);
      oci_execute($_statement);
      while ($_row = oci_fetch_array($_statement)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
    function _queryLimit($_query, $_offset, $_querySize) {
      $_selectPos = strpos($_query, "SELECT ");
      $_asPos = strpos($_query, " AS ");
      $_field = substr($_query, $_selectPos + 7, $_asPos - $_selectPos - 7);
      $_query = substr_replace($_query, "ROW_NUMBER() OVER (ORDER BY $_field) AS RowNumber, ", $_selectPos + 7, 0);
      $_query = "SELECT * FROM ($_query) tmp2 WHERE RowNumber BETWEEN $_offset AND " . ($_offset + $_querySize);
      $_arr = array();
      $_statement = oci_parse($this->Link, $_query);
      oci_execute($_statement);
      while ($_row = oci_fetch_array($_statement)) {
        array_push($_arr, $_row);
      }
      return $_arr;
    }
  }
  /*   * ***************************************************************************************** */
  class _PivotViewState {
    public $_PivotTable;
    public $_Data;
    public $_Encrypt = FALSE;
    public $_SaveToSession = FALSE;
    public $LoadFilters = true;
    function _Init($_pivottable) {
      $this->_PivotTable = $_pivottable;
      $this->_SaveToSession = $_pivottable->KeepViewStateInSession;
      $_string = (isset($_POST[$this->_PivotTable->_UniqueID . _Key::__viewstate])) ? $_POST[$this->_PivotTable->_UniqueID . _Key::__viewstate] : "";
      if ($this->_SaveToSession && $_string == "") {
        $_string = (isset($_SESSION[$this->_PivotTable->_UniqueID . _Key::__viewstate])) ? $_SESSION[$this->_PivotTable->_UniqueID . _Key::__viewstate] : "";
      }
      $_args = array(
        'pivotId' => $_pivottable->id,
        'viewstate' => & $_string
      );
      $_pivottable->EventHandler->OnReadingViewstate($this, $_args);
      if ($_string != "" && $this->_Encrypt) {
        $_string = base64_decode($_string);
      }
      $_string = _replace("\\", "", $_string);
      $this->_Data = json_decode($_string, TRUE);
    }
    function _Clear() {
      $this->_Data = array();
    }
    function _Render() {
      $_pivottable = $this->_PivotTable;
      $_statevalue = json_encode($this->_Data);
      if ($this->_Encrypt) {
        $_statevalue = base64_encode($_statevalue);
      }
      if ($this->_SaveToSession) {
        $_SESSION[$this->_PivotTable->_UniqueID . _Key::__viewstate] = $_statevalue;
      }
      $_tpl_viewstate = "<input id='{id}' name='{id}' type='hidden' value='{value}' autocomplete='off' />";
      $_viewstate = _replace("{id}", $this->_PivotTable->_UniqueID . _Key::__viewstate, $_tpl_viewstate);
      $_viewstate = _replace("{value}", $_statevalue, $_viewstate);
      return $_viewstate;
    }
  }
  /* =========================================================================== */
  class _PivotLocalization {
    var $_Commands;
    var $_Messages;
    function __construct() {
      $this->_Commands = array(
        _Key::_Go => "Go",
        _Key::_Next => "Next",
        _Key::_Prev => "Next",
        _Key::_Last => "Last",
        _Key::_First => "First",
        _Key::_none => "[No Filter]",
        _Key::_equal_to => " is equal to",
        _Key::_not_equal_to => " is NOT equal to",
        _Key::_less_than => "is less than",
        _Key::_greater_than => " is greater than",
        _Key::_less_than_or_equal_to => " is less than or equal to",
        _Key::_greater_than_or_equal_to => " is greater than or equal to",
        _Key::_between => " is between",
        _Key::_not_between => " is NOT between",
        _Key::_contain => " contains",
        _Key::_start_with => " starts with",
        _Key::_end_with => " ends with",
        _Key::_top_N => " top N",
        _Key::_bottom_N => " bottom N",
        _Key::_top_percent => " top percent",
        _Key::_bottom_percent => " bottom percent",
        _Key::_OkUpper => "Ok",
        _Key::_Cancel => "Cancel",
        _Key::_Includes => "Includes",
        _Key::_Excludes => "Excludes",
        _Key::_Select_All => "(Select All)",
        _Key::_Grand_Total => "Grand Total",
        _Key::_Category_Total => "{category} Total",
        _Key::_Category_Sum => "Sum of {category}",
        _Key::_Category_Count => "Count of {category}",
        _Key::_Category_Min => "{category} Min",
        _Key::_Category_Max => "{category} Max",
        _Key::_Category_Average => "Average of {category}",
        _Key::_Category_PercentageSum => "Percentage of sum of {category}",
        _Key::_Category_PercentageCount => "Percentage of count of {category}",
      );
      $this->_Messages = array(
        _Key::_PageInfoTemplate => "Page <strong>{PageIndex}</strong> in <strong>{TotalPages}</strong>, items <strong>{FirstIndexInPage}</strong> to <strong>{LastIndexInPage}</strong> of <strong>{TotalRows}</strong>.",
        _Key::_ManualPagerTemplate => "Change page: {TextBox} (of {TotalPage} pages) {GoPageButton}",
        _Key::_PageSizeText => "Page Size:",
        _Key::_NextPageToolTip => "Next Page",
        _Key::_PrevPageToolTip => "Previous Page",
        _Key::_FirstPageToolTip => "First Page",
        _Key::_LastPageToolTip => "Last Page",
        _Key::_SortHeaderToolTip => "Click here to sort",
        _Key::_SortAscToolTip => "Sort Asc",
        _Key::_SortDescToolTip => "Sort Desc",
        _Key::_SortNoneToolTip => "No sort",
        _Key::_ColumnZoneEmptyMessage => "[Column Fields]",
        _Key::_RowZoneEmptyMessage => "[Row Fields]",
        _Key::_FilterZoneEmptyMessage => "Drag the filter field here.",
        _Key::_DataZoneEmptyMessage => "[Data Fields]",
        _Key::_Drag_To_Reorder => "Drag to order",
        _Key::_Done => _Key::_Done,
        _Key::_Loading => "Loading..",
        _Key::_and => _Key::_and,
        _Key::_Sorted_Asc => "Sorted asc",
        _Key::_Sorted_Desc => "Sorted desc",
        _Key::_Filtering => "Fitlering",
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
  class _PivotCache {
    var $_CacheFolder;
    var $_CacheTime;
    var $_UniqueID;
    function __construct($_cachefolder, $_cachetime) {
      $this->_CacheFolder = ($_cachefolder != null) ? $_cachefolder : sys_get_temp_dir();
      $this->_CacheTime = ($_cachetime != null) ? $_cachetime : 5 * 60; //5min
    }
    function _Save($_key, $_array) {
      $_string = json_encode($_array);
      file_put_contents($this->_CacheFolder . "/" . $this->_UniqueID . $_key . ".kpt", $_string);
      return TRUE;
    }
    function _Load($_key) {
      $_filename = $this->_CacheFolder . "/" . $this->_UniqueID . $_key . ".kpt";
      if (is_file($_filename) && (time() - filemtime($_filename) < $this->_CacheTime)) {
        $_string = file_get_contents($this->_CacheFolder . "/" . $this->_UniqueID . $_key . ".kpt");
        return json_decode($_string, TRUE);
      }
      return null;
    }
  }
  class _PivotField {
    var $_SortStatus = FALSE;
    var $_PivotTable;
    var $_ViewState;
    var $_UinqueID;
    var $_ExpandedParentGroups = array();
    var $_Items;
    var $_Exception;
    var $_UniqueID;
    var $_FilterPanelOpen = FALSE;
    var $_PanelWidth = 0;
    var $_PanelHeight = 0;
    var $SqlOperator = _SQL::_SUM;
    var $_sqlExpression;
    public $_sqlAlias;
    public $_Type;
    public $_key;
    public $_select;
    private $_valueMap;
    var $FieldName;
    var $Text;
    var $Sort; //"desc"|"asc"|"custom"
    var $Expand;
    var $Filters;
    var $IncludeAll = TRUE;
    var $ExceptionList;
    var $AllowReorder;
    var $AllowSorting;
    var $AllowFiltering;
    var $Tooltip;
    var $HeaderTextWrap = TRUE;
    var $RelevantField;
    var $DependantFields = array();
    function __construct($_fieldname) {
      $this->FieldName = $_fieldname;
      $this->_setSqlExpression($_fieldname);
      $this->ExceptionList = array();
      if ($this->_select === NULL)
        $this->_select = array();
      $this->Filters = array();
    }
    public static function _new($_FN, $_type) {
      switch ($_type) {
        case "sum":
          return new PivotSumField($_FN);
        case "average":
          return new PivotAverageField($_FN);
        case "percentage sum":
          return new PivotPercentageSumField($_FN);
        case "percentage count":
          return new PivotPercentageCountField($_FN);
        case "min":
          return new PivotMinField($_FN);
        case "max":
          return new PivotMaxField($_FN);
        case "count":
          return new PivotCountField($_FN);
        case "pivot":
        default:
          return new PivotField($_FN);
      }
    }
    function _setSqlExpression($_str) {
      $this->_sqlExpression = $_str;
      return $this;
    }
    function setValueMap($_valueMap) {
      $this->_valueMap = $_valueMap;
      return $this;
    }
    function getValueMap() {
      return $this->_valueMap;
    }
    function _getMappedValue($_value) {
      if (isset($this->_valueMap))
        $_value = $this->_valueMap->map($_value);
      if (is_array($_value))
        $_value = $_value[$this->FieldName];
      return $_value;
    }
    function _getProperties(&$_FN, &$_FId, &$_FExp, &$_FA, &$_FOp) {
      $_FN = $this->FieldName;
      $_FId = $this->_UniqueID;
      $_FExp = $this->_sqlExpression;
      $_FA = $this->_sqlAlias;
      $_FOp = $this->SqlOperator;
    }
    function _Init($_pivottable, $_index) {
      $this->_PivotTable = $_pivottable;
      $this->_ViewState = $_pivottable->_ViewState;
      $this->_UniqueID = $this->_PivotTable->_UniqueID . "_" . md5("$_index");
      $this->_sqlAlias = 'f' . $_index;
      if ($this->Text === null)
        $this->Text = $this->FieldName;
      $this->_Items = array();
      if ($this->Expand === null)
        $this->Expand = FALSE;
      if ($this->_ExpandedParentGroups === null)
        $this->_ExpandedParentGroups = array();
      if ($this->AllowReorder === null)
        $this->AllowReorder = $this->_PivotTable->AllowReorder;
      if ($this->AllowSorting === null)
        $this->AllowSorting = $this->_PivotTable->AllowSorting;
      if ($this->AllowFiltering === null)
        $this->AllowFiltering = $this->_PivotTable->AllowFiltering;
      if ($this->Sort === null && $this->AllowSorting)
        $this->Sort = _Key::_ASC;
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->Sort = $_state[_Key::_Sort];
        $_filters = $_state[_Key::_filter];
        $this->IncludeAll = $_state[_Key::_IncludeAll];
        $_exception_list = $_state[_Key::_ExceptionList];
        $this->_FilterPanelOpen = $_state[_Key::_FilterPanelOpen];
        $this->_Type = $_state[_Key::_field_type];
        for ($i = 0; $i < count($_filters); $i++) {
          $_filters[$i][1] = urldecode($_filters[$i][1]);
          if (isset($_filters[$i][2]))
            $_filters[$i][2] = urldecode($_filters[$i][2]);
        }
        if ($this->_ViewState->LoadFilters)
          $this->Filters = $_filters;
        for ($i = 0; $i < count($_exception_list); $i++)
          $_exception_list[$i] = (urldecode($_exception_list[$i]));
        $this->ExceptionList = $_exception_list;
        if ($this->_FilterPanelOpen) {
          $this->_PanelWidth = $_state[_Key::_PanelWidth];
          $this->_PanelHeight = $_state[_Key::_PanelHeight];
        }
      }
    }
    function _Process($_command) {
      $_pv = $this->_PivotTable;
      $_ds = $_pv->DataSource;
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        $_com = $_c[_Key::_Command];
        $_arg = $_c[_Key::_Args];
        switch ($_com) {
          case _Key::_Sort:
            if ($_pv->EventHandler->OnBeforeFieldSort($this, array()) == TRUE) {
              $this->Sort = $_arg[_Key::_Sort];
              $_pv->EventHandler->OnFieldSort($this, array());
              $_FT = $this->_Type;
              $_pv->_GroupsToSort[_PV::_Inverse($_FT)] = null;
              $_pv->_ResetFieldType = $_FT;
              $_pv->SetSortState(_Key::_fieldSort);
            }
            break;
          case _Key::_OpenFilterPanel:
            if ($_pv->EventHandler->OnBeforeFilterPanelOpen($this, array()) == TRUE) {
              $this->_FilterPanelOpen = TRUE;
              $this->_PanelWidth = $_arg[_Key::_Width];
              $this->_PanelHeight = $_arg[_Key::_Height];
                $this->_loadItems();
              $_pv->EventHandler->OnFilterPanelOpen($this, array());
            }
            break;
          case _Key::_CloseFilterPanel:
            $this->_FilterPanelOpen = FALSE;
            switch ($_arg[_Key::_Command]) {
              case _Key::_okLower:
                if ($_pv->EventHandler->OnBeforeFieldFilter($this, array()) == TRUE) {
                  if ($_arg[_Key::_FilterBy] == _Key::_Values) {
                    $this->Filters = array();
                    $this->IncludeAll = TRUE;
                    $this->ExceptionList = array();
                    $this->AddFilter(array(
                      $_arg[_Key::_Expression],
                      $_ds->_HtmlToSqlStr(urldecode($_arg[_Key::_value1])),
                      $_ds->_HtmlToSqlStr(urldecode($_arg[_Key::_value2]))
                    ));
                  } else if ($_arg[_Key::_FilterBy] == _Key::_Options) {
                    $this->Filters = array();
                    $this->IncludeAll = $_arg[_Key::_IncludeAll];
                    $this->ExceptionList = ($_arg[_Key::_ExceptionList] != null) ? $_arg[_Key::_ExceptionList] : array();
                    for ($i = 0; $i < count($this->ExceptionList); $i++)
                      $this->ExceptionList[$i] = $_ds->_HtmlToSqlStr(urldecode($this->ExceptionList[$i]));
                  }
                  $_pv->_RecalFilter = TRUE;
                  $_pv->EventHandler->OnFieldFilter($this, array());
                }
                break;
              case _Key::_Cancel:
                break;
            }
            break;
          case _Key::_Collapse:
            if ($_pv->EventHandler->OnBeforeFieldCollapse($this, array()) == TRUE) {
              $this->Expand = FALSE;
              $_pv->EventHandler->OnFieldCollapse($this, array());
            }
            break;
          case _Key::_Expand:
            if ($_pv->EventHandler->OnBeforeFieldExpand($this, array()) == TRUE) {
              $this->Expand = TRUE;
              $_pv->EventHandler->OnFieldExpand($this, array());
            }
            break;
        }
      }
      foreach ($this->ExceptionList as $_exception_value)
        $this->_Exception[$_exception_value] = 1;
      if ($this->AllowFiltering && $this->_FilterPanelOpen)
        $_pv->_FilterPanelItem = $this;
    }
    function _SaveViewState() {
      $_exception_list = $this->ExceptionList;
      for ($i = 0; $i < count($_exception_list); $i++)
        $_exception_list[$i] = urlencode($_exception_list[$i]);
      $_filters = $this->Filters;
      for ($i = 0; $i < count($_filters); $i++) {
        $_filters[$i][1] = urlencode($_filters[$i][1]);
        if (isset($_filters[$i][2]))
          $_filters[$i][2] = urlencode($_filters[$i][2]);
      }
      $this->_ViewState->_Data[$this->_UniqueID] = array(
        _Key::_FieldName => urlencode($this->FieldName),
        _Key::_Sort => $this->Sort,
        _Key::_ExceptionList => $_exception_list,
        _Key::_IncludeAll => $this->IncludeAll,
        _Key::_filter => $_filters,
        _Key::_AllowReorder => $this->AllowReorder,
        _Key::_FilterPanelOpen => $this->_FilterPanelOpen,
        _Key::_field_type => $this->_Type,
      );
      if ($this->_FilterPanelOpen) {
        $this->_ViewState->_Data[$this->_UniqueID] = array_merge($this->_ViewState->_Data[$this->_UniqueID], array(
          _Key::_PanelWidth => $this->_PanelWidth,
          _Key::_PanelHeight => $this->_PanelHeight,
        ));
      }
    }
    function AddFilter($_filter) {
      array_push($this->Filters, $_filter);
    }
    function AddException($_value) {
      array_push($this->ExceptionList, $_value);
    }
    function AddDependantField($_field) {
      array_push($this->DependantFields, $_field);
    }
    function _hasFilter() {
      $_hasFilter = (!empty($this->Filters) || !empty($this->ExceptionList));
      if (empty($this->ExceptionList) && $this->IncludeAll == FALSE)
        $_hasFilter = TRUE;
      return $_hasFilter;
    }
    function _RecordingItem($_value) {
      $this->_Items[$_value] = 1;
    }
    function _loadItems() {
      $_pv = $this->_PivotTable;
      $_ds = $_pv->DataSource;
      $this->_getProperties($_FN, $_FId, $_FExp, $_FA, $_FOp);
      $fields = array(array(_Key::_field => $_FExp, _Key::_nestedField => $_FN, _Key::_alias => $_FA));
      if (isset($this->DependantFields))
        foreach ($this->DependantFields as $_field) {
          $_field->_getProperties($_dFN, $_dFId, $_dFExp, $_dFA, $_dFOp);
          array_push($fields, array(_Key::_field => $_dFExp, _Key::_nestedField => $_dFN, _Key::_alias => $_dFA));
      }
      $_query = $_ds->_distinct(TRUE)->_buildSelectQuery($fields, NULl, NULL);
      $_rows = $_ds->_queryAll($_query);
      foreach ($_rows as $_row) {
        $_filter = true;
        if (isset($this->DependantFields)) {
          foreach ($this->DependantFields as $_field) {
            $_field->_getProperties($_dFN, $_dFId, $_dFExp, $_dFA, $_dFOp);
            $_dvalue = $_field->_getMappedValue($_row[$_dFA]);
            $_filter = $_filter && $_field->_DoFiltering($_dvalue);
          }
        }
        if ($_filter) {
          $_value = $this->_getMappedValue($_row[$_FA]);
          $this->_RecordingItem($_value);
        }
      }
      ksort($this->_Items);
    }
    function _setItems($_items) {
      foreach ($_items as $_item)
        $this->_RecordingItem($_item);
      return $this;
    }
    function _getItems() {
      $_items = array();
      foreach (array_keys($this->_Items) as $_item)
        array_push($_items, $_item);
      return $_items;
    }
    function _doConditionalFiltering($_value) {
      foreach ($this->Filters as $_filter) {
        switch ($_filter[0]) {
          case _Key::_equal_to:
            if (!($_value == $_filter[1]))
              return FALSE;
            break;
          case _Key::_not_equal_to:
            if (!($_value != $_filter[1]))
              return FALSE;
            break;
          case _Key::_less_than:
            if (!($_value < $_filter[1]))
              return FALSE;
            break;
          case _Key::_greater_than:
            if (!($_value > $_filter[1]))
              return FALSE;
            break;
          case _Key::_less_than_or_equal_to:
            if (!($_value <= $_filter[1]))
              return FALSE;
            break;
          case _Key::_greater_than_or_equal_to:
            if (!($_value >= $_filter[1]))
              return FALSE;
            break;
          case _Key::_between:
            if (!(($_value > $_filter[1]) && ($_value < $_filter[2])))
              return FALSE;
            break;
          case _Key::_not_between:
            if (!(($_value < $_filter[1]) || ($_value > $_filter[2])))
              return FALSE;
            break;
          case _Key::_contain:
            if (strpos(strtolower($_value), strtolower($_filter[1])) === FALSE)
              return FALSE;
            break;
          case _Key::_start_with:
            if (strpos(strtolower($_value), strtolower($_filter[1])) !== 0)
              return FALSE;
            break;
          case _Key::_end_with:
            if (strpos(strrev(strtolower($_value)), strrev(strtolower($_filter[1]))) !== 0)
              return FALSE;
            break;
        }
      }
      return TRUE;
    }
    function _DoFiltering($_value) {
      if ($this->IncludeAll && in_array(($_value), $this->ExceptionList))
        return FALSE;
      if ($this->IncludeAll == FALSE && !in_array(($_value), $this->ExceptionList))
        return FALSE;
      $_filter = $this->_doConditionalFiltering($_value);
      return $_filter;
    }
    function _filterData() {
      foreach ($this->_ExpandedParentGroups as $_EPGroup) {
        foreach ($this->Filters as $_filter) {
          $_EPGroup->_setVisibleTopBottom($_filter[0], $_filter[1]);
        }
      }
      return TRUE;
    }
    function _RenderFilterPanel() {
      $_cssClasses = $this->_PivotTable->CssClasses;
      $_tpl_main = "<div id='{id}' class='kptFilterPanel {css}' style='width:{width}px;height:{height}px;'>{function_panel}<div class='kptScrollPanel' style='height:200px;overflow-y:scroll;overflow-x:auto;'>{valuefilter}<div></div><div  id='{id}_filterwithoptions' class='kptFilterWithOptions'>{include_exclude}{list}</div></div>{hidden}</div>";
      $_tpl_main = _replace('{css}', isset($_cssClasses['filter panel']) ? $_cssClasses['filter panel'] : '', $_tpl_main);
      $_tpl_function_panel = "<div class='kptFunctionPanel'>{ok}{cancel}</div>";
      $_tpl_button = "<input id='{id}' type='button' value='{text}' class='kpt{type}Button' />";
      $_tpl_valuefilter = "<div id='{id}_filterwithvalues' class='kptFilterWithValues'>{field}<select id='{id}_select' name='{id}_select'>{options}</select><input id='{id}_value1' name='{id}_value1' value='{value1}' style='display:none' /><span style='display:none'> {and} <input id='{id}_value2' name='{id}_value2' value='{value2}' /></span></div>";
      $_tpl_option = "<option value='{value}' {selected}>{text}</option>";
      $_tpl_include_exclude = "<div class='kptIncludeExclude'>{include}{exclude}</div>";
      $_tpl_radio = "<span class='kptInExOption'><input id='{id}' class='kptRadio' type='radio' name='{name}' {checked} value='{value}'/><label class='kptLabel' for='{id}'>{text}</label></span>";
      $_tpl_list = "<div class='kptList'>{items}</div>";
      $_tpl_item = "<div class='kptListOption'><input id='{id}' class='kptCheck' type='checkbox' {checked} /><label class='kptLabel' for='{id}'>{text}</label></div>";
      $_tpl_hidden_field = "<input type='hidden' id='{id}_hidden' name='{id}_hidden' value='{value}' />";
      $_firstload = FALSE;
      if (!isset($_POST[$this->_UniqueID . "_hidden"])) {
        $_POST[$this->_UniqueID . "_include_exclude"] = _Key::_Includes;
        if (count($this->Filters) > 0) {
          $_POST[$this->_UniqueID . "_select"] = $this->Filters[0][0];
          $_POST[$this->_UniqueID . "_value1"] = $this->Filters[0][1];
          if (isset($this->Filters[0][2]))
            $_POST[$this->_UniqueID . "_value2"] = $this->Filters[0][2];
          $_POST[$this->_UniqueID . "_hidden"] = "vl";
        }
        else {
          $_POST[$this->_UniqueID . "_hidden"] = "ie";
        }
        $_firstload = TRUE;
      }
      $_cancel = _replace("{id}", $this->_UniqueID . "_cancel", $_tpl_button);
      $_cancel = _replace("{text}", $this->_PivotTable->Localization->_Commands[_Key::_Cancel], $_cancel);
      $_cancel = _replace("{type}", _Key::_Cancel, $_cancel);
      $_ok = _replace("{id}", $this->_UniqueID . "_ok", $_tpl_button);
      $_ok = _replace("{text}", $this->_PivotTable->Localization->_Commands[_Key::_OkUpper], $_ok);
      $_ok = _replace("{type}", _Key::_OkUpper, $_ok);
      $_function_panel = _replace("{ok}", $_ok, $_tpl_function_panel);
      $_function_panel = _replace("{cancel}", $_cancel, $_function_panel);
      $_valuefilter = _replace("{id}", $this->_UniqueID, $_tpl_valuefilter);
      $_valuefilter = _replace("{value1}", isset($_POST[$this->_UniqueID . "_value1"]) ? $_POST[$this->_UniqueID . "_value1"] : "", $_valuefilter);
      $_valuefilter = _replace("{value2}", isset($_POST[$this->_UniqueID . "_value2"]) ? $_POST[$this->_UniqueID . "_value2"] : "", $_valuefilter);
      $_values = array(_Key::_none,
        _Key::_equal_to,
        _Key::_not_equal_to,
        _Key::_less_than,
        _Key::_greater_than,
        _Key::_less_than_or_equal_to,
        _Key::_greater_than_or_equal_to,
        _Key::_between,
        _Key::_not_between,
        _Key::_contain,
        _Key::_start_with,
        _Key::_end_with,
      );
      $_valuesFilterData = array(
        _Key::_top_N,
        _Key::_bottom_N,
        _Key::_top_percent,
        _Key::_bottom_percent
      );
      $_options = "";
      foreach ($_values as $_value) {
        $_option = _replace("{value}", $_value, $_tpl_option);
        $_option = _replace("{text}", $this->_PivotTable->Localization->_Commands[$_value], $_option);
        $_option = _replace("{selected}", (isset($_POST[$this->_UniqueID . "_select"]) && $_POST[$this->_UniqueID . "_select"] == $_value) ? "selected='selected'" : "", $_option);
        $_options.=$_option;
      }
      if (count($this->_ExpandedParentGroups) > 0)
        foreach ($_valuesFilterData as $_value) {
          $_option = _replace("{value}", $_value, $_tpl_option);
          $_option = _replace("{text}", $this->_PivotTable->Localization->_Commands[$_value], $_option);
          $_option = _replace("{selected}", (isset($_POST[$this->_UniqueID . "_select"]) && $_POST[$this->_UniqueID . "_select"] == $_value) ? "selected='selected'" : "", $_option);
          $_options.=$_option;
        }
      $_valuefilter = _replace("{options}", $_options, $_valuefilter);
      $_valuefilter = _replace("{field}", $this->Text, $_valuefilter);
      $_valuefilter = _replace("{and}", $this->_PivotTable->Localization->_Messages[_Key::_and], $_valuefilter);
      $_include = _replace("{id}", $this->_UniqueID . "_include", $_tpl_radio);
      $_include = _replace("{name}", $this->_UniqueID . "_include_exclude", $_include);
      $_include = _replace("{text}", $this->_PivotTable->Localization->_Commands[_Key::_Includes], $_include);
      $_include = _replace("{value}", _Key::_Includes, $_include);
      $_include = _replace("{checked}", (isset($_POST[$this->_UniqueID . "_include_exclude"]) && $_POST[$this->_UniqueID . "_include_exclude"] == _Key::_Includes) ? "checked='checked'" : "", $_include);
      $_exclude = _replace("{id}", $this->_UniqueID . "_exclude", $_tpl_radio);
      $_exclude = _replace("{name}", $this->_UniqueID . "_include_exclude", $_exclude);
      $_exclude = _replace("{text}", $this->_PivotTable->Localization->_Commands[_Key::_Excludes], $_exclude);
      $_exclude = _replace("{value}", _Key::_Excludes, $_exclude);
      $_exclude = _replace("{checked}", (isset($_POST[$this->_UniqueID . "_include_exclude"]) && $_POST[$this->_UniqueID . "_include_exclude"] == _Key::_Excludes) ? "checked='checked'" : "", $_exclude);
      $_include_exclude = _replace("{include}", $_include, $_tpl_include_exclude);
      $_include_exclude = _replace("{exclude}", $_exclude, $_include_exclude);
      $_items = "";
      $_selectall_item = _replace("{id}", $this->_UniqueID . "_selectall", $_tpl_item);
      $_selectall_item = _replace("{text}", $this->_PivotTable->Localization->_Commands[_Key::_Select_All], $_selectall_item);
      $_selectall_item = _replace("{checked}", "", $_selectall_item);
      $_items.=$_selectall_item;
      $i = 0;
      foreach ($this->_Items as $_k => $_v) {
        {
          $_item = _replace("{id}", $this->_UniqueID . "_" . $i, $_tpl_item);
          $_item = _replace("{text}", $_k, $_item);
          if (!$_firstload) {
            $_item = _replace("{checked}", isset($_POST[$this->_UniqueID . "_" . $i]) ? "checked='checked'" : "", $_item);
          } else {
            $_item = _replace("{checked}", $this->_DoFiltering($_k) ? "checked='checked'" : "", $_item);
          }
          $i++;
          $_items.=$_item;
        }
      }
      $_list = _replace("{items}", $_items, $_tpl_list);
      $_hidden = _replace("{id}", $this->_UniqueID, $_tpl_hidden_field);
      $_hidden = _replace("{value}", $_POST[$this->_UniqueID . "_hidden"], $_hidden);
      $_main = _replace("{id}", $this->_UniqueID, $_tpl_main);
      $_main = _replace("{width}", $this->_PanelWidth, $_main);
      $_main = _replace("{height}", $this->_PanelHeight, $_main);
      $_main = _replace("{function_panel}", $_function_panel, $_main);
      $_main = _replace("{include_exclude}", $_include_exclude, $_main);
      $_main = _replace("{valuefilter}", $_valuefilter, $_main);
      $_main = _replace("{list}", $_list, $_main);
      $_main = _replace("{hidden}", $_hidden, $_main);
      return $_main;
    }
    function _RenderField($_field = null) {
      $_tpl_item = "<span id='{id}' class='kptFieldItem{dragable} {css}' title='{tooltip}'>{text}{sort}{filter}</span>";
      $_tpl_desc = "<span class='kptDesc'>{text}</span>";
      $_tpl_filter = "<span class='kptFilterButton' title='{tooltip}'></span>";
      $_tpl_sort = "<span class='kptSortButton kptSort{direction}{status}' title='{tooltip}' onclick='pivot_sort_toggle(this)'></span>";
      $_item = _replace("{id}", $this->_UniqueID, $_tpl_item);
      $_item = _replace("{dragable}", $this->AllowReorder ? " kptDragable" : "", $_item);
      $_cssClasses = $this->_PivotTable->CssClasses;
      $_item = _replace("{css}", isset($_cssClasses['field']) ? $_cssClasses['field'] : "", $_item);
      $_item = _replace("{tooltip}", ($this->Tooltip != null) ? $this->Tooltip : (($this->AllowReorder) ? $this->_PivotTable->Localization->_Messages[_Key::_Drag_To_Reorder] : ""), $_item);
      $_item = _replace("{text}", $this->Text, $_item);
      if ($_field === _PV::_Data) {
        $_item = _replace("{sort}", "", $_item);
      }
      {
        switch (strtolower($this->Sort)) {
          case _Key::_ASC:
            $_sort = _replace("{direction}", "Asc", $_tpl_sort);
            $_sort = _replace("{tooltip}", $this->_PivotTable->Localization->_Messages[_Key::_Sorted_Asc], $_sort);
            break;
          case _Key::_DESC:
            $_sort = _replace("{direction}", "Desc", $_tpl_sort);
            $_sort = _replace("{tooltip}", $this->_PivotTable->Localization->_Messages[_Key::_Sorted_Desc], $_sort);
            break;
          case _Key::_none:
          default:
            $_sort = "";
            break;
        }
        $_status = ($this->_SortStatus) ? "On" : "Off";
        $_sort = _replace("{status}", $_status, $_sort);
        $_item = _replace("{sort}", $this->AllowSorting ? $_sort : "", $_item);
        $_filter = _replace("{tooltip}", $this->_PivotTable->Localization->_Messages[_Key::_Filtering], $_tpl_filter);
        $_item = _replace("{filter}", $this->AllowFiltering ? $_filter : "", $_item);
      }
      return $_item;
    }
    function RenderHeader($_value) {
      return $_value;
    }
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      return _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_Total]);
    }
    function RenderHeaderTotal2($_value, $_s) {
      return _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_Total]) . $_s;
    }
    function DataProcess($_value) {
      return $_value;
    }
    function _PostDataProcess($_values) {
      return $_values;
    }
    function DataAggregate($_value, $_aggregated_value) {
      return (($_aggregated_value === null) ? 0 : $_aggregated_value) + $_value;
    }
    function DisplayFormat($_value) {
      return $_value;
    }
  }
  class PivotField extends _PivotField {
    var $_Ranges;
    var $_Matches;
    var $NoMatchValue;
    var $ConvertToPercent = FALSE;
    function __construct($_fieldname) {
      parent::__construct($_fieldname);
      $this->_Ranges = array();
      $this->_Matches = array();
    }
    function _PostDataProcess($_values) {
      if ($this->ConvertToPercent) {
        $_c_grand = $this->_PivotTable->_headGroups[_PV::_Column]->_getSubGroup(0);
        $_r_grand = $this->_PivotTable->_headGroups[_PV::_Row]->_getSubGroup(0);
        $this->_PivotTable->_GetKeys($_c_grand, $_r_grand, $_fields_key, $_group_key, $_key);
        $_data_alias = $this->_sqlAlias;
        $_grand_total = $_values[$_fields_key][$_group_key][$_key][$_data_alias];
        foreach ($_values as $_f => $_values2)
          foreach ($_values2 as $_g => $_values3)
            foreach ($_values3 as $_k => $_values4) {
              $_values[$_f][$_g][$_k][$_data_alias] = $_grand_total != 0 ? $_values[$_f][$_g][$_k][$_data_alias] * 100 / $_grand_total : 0;
            }
      }
      return $_values;
    }
  }
  class PivotDateField extends PivotField {
    private static $_dateType = array("year", "quarter", "month", "day");
    private $_display = array("year" => FALSE, "quarter" => FALSE, "month" => FALSE, "day" => TRUE);
    public function setDateFields($_var) {
      if (is_array($_var)) {
        foreach ($_var as $_k => $_v)
          if (in_array(strtolower($_k), self::$_dateType))
            $this->_display[strtolower($_k)] = $_v;
      }
      return $this;
    }
    public function getDateFields() {
      return $this->_display;
    }
  }
  class PivotStringField extends PivotField {
    var $SqlOperator = "";
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      return $this->Text;
    }
  }
  class PivotSumField extends PivotField {
    var $ValueForNull = 0;
    var $DecimalNumber = 0;
    var $DecimalPoint = ".";
    var $ThousandSeperate = ",";
    var $FormatString = "{n}";    
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      $_s = _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_Sum]);
      return $_s;
    }
    function DataProcess($_value) {
      $_value = parent::DataProcess($_value);
      if ($_value == null)
        return $this->ValueForNull;
      else
        return $_value;
    }
    function DisplayFormat($_value) {
      $_number = (float) $_value;
      return _replace("{n}", number_format($_number, $this->DecimalNumber, $this->DecimalPoint, $this->ThousandSeperate), $this->FormatString);
    }
  }
  class PivotAverageField extends PivotSumField {
    var $DecimalNumber = 2;
    var $DecimalPoint = ".";
    var $ThousandSeperate = ",";
    var $SqlOperator = _SQL::_AVG;
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      return _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_Average]);
    }
  }
  class PivotPercentageSumField extends PivotSumField {
    var $DecimalNumber = 2;
    var $FormatString = "{n}%";
    var $ConvertToPercent = TRUE;
    var $SqlOperator = _SQL::_SUM;
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      return _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_PercentageSum]);
    }
  }
  class PivotPercentageCountField extends PivotSumField {
    var $DecimalNumber = 2;
    var $FormatString = "{n}%";
    var $ConvertToPercent = TRUE;
    var $SqlOperator = _SQL::_COUNT;
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      return _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_PercentageCount]);
    }
  }
  class PivotMinField extends PivotSumField {
    var $SqlOperator = _SQL::_MIN;
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      return _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_Min]);
    }
  }
  class PivotMaxField extends PivotSumField {
    var $SqlOperator = _SQL::_MAX;
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      return _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_Max]);
    }
  }
  class PivotCountField extends PivotSumField {
    var $SqlOperator = _SQL::_COUNT;
    function RenderHeaderTotal($_value, $_pivot_group = null) {
      if (isset($_pivot_group) && $_pivot_group->GetField()->FieldName == "'grand'")
        return $this->Text;
      return _replace("{category}", $_value, $this->_PivotTable->Localization->_Commands[_Key::_Category_Count]);
    }
  }
  class _GrandRCField extends PivotField {
    var $FieldName;
    var $Expand;
    var $_Type;
    public static function _newGrand() {
      $_field = new _PivotField(_Key::_grandConstant);
      $_field->Expand = TRUE;
      $_field->_setSqlExpression(_Key::_grandConstant);
      return $_field;
    }
  }
  class _PivotGroup extends _SimpleTree {
    var $_Field;
    var $_Expandable = FALSE;
    var $_Key;
    var $_ValueChain = "";
    public $_SQLCondition;
    public $_nestedSQLCondition;
    public $_sqlValues = array();
    var $_PivotTable;
    var $_ViewState;
    var $_UniqueID;
    var $_Sort;
    var $_AllowSorting;
    var $_SortValue;
    function __construct($_value, $_field) {
      $this->Value = $_value;
      $this->_Length = strlen($this->Value);
      $this->_SubGroups = array();
      if (isset($_field)) {
        $this->_Field = $_field;
        $this->Expand = $_field->Expand;
        $this->_PivotTable = $_field->_PivotTable;
      }
    }
    public static function _NewGroup($_value, $_field) {
      $_group = new _PivotGroup($_value, $_field);
      return $_group;
    }
    public static function _newGrandGroup($_value, $_field) {
      $_group = new _GrandHeaderGroup($_value, $_field);
      return $_group;
    }
    function _hasSubGroup($_var = "") {
      return parent::_hasChild($_var);
    }
    function _hasExpandedSubGroup($_var = "") {
      return parent::_hasExpandedChild($_var);
    }
    function _getSubGroup($_var = 0) {
      return parent::_getChild($_var);
    }
    function _getSubGroups() {
      return parent::_getChilds();
    }
    function _SetGroupIdentities($_parent) {
      $this->_ValueChain = ($_parent != NULL) ? $_parent->_ValueChain . "_" . $this->Value : $this->Value;
      $this->_Key = md5($this->_ValueChain);
      $this->_UniqueID = $this->_PivotTable->_UniqueID . "_" . $this->_Key;
    }
    function _Init($_pivottable) {
      $this->_PivotTable = $_pivottable;
      $this->_ViewState = $_pivottable->_ViewState;
      $this->_SetGroupIdentities($this->_Parent);
      if ($this->_AllowSorting === null)
        $this->_AllowSorting = $this->_PivotTable->AllowSortingData;
      if ($this->_Sort === null)
        $this->_Sort = _Key::_ASC;
      if ($this->_SQLCondition === null)
        $this->_SQLCondition = "";
      if ($this->_nestedSQLCondition === null)
        $this->_nestedSQLCondition = "";
    }
    function _SaveViewState() {
      if ($this->Expand != $this->_Field->Expand || $this->_Sort == _Key::_DESC) {
        $this->_ViewState->_Data[$this->_UniqueID] = array(
          _Key::_Expand => $this->Expand,
          _Key::_Sort => $this->_Sort,
        );
      }
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->Expand = $_state[_Key::_Expand];
        $this->_Sort = $_state[_Key::_Sort];
      }
    }
    function _setVisibleTopBottom($_filter, $_value) {
      $_count = count($this->_SubGroups);
      $_i = 0;
      switch ($_filter) {
        case _Key::_top_N:
          foreach ($this->_SubGroups as $_subGroup) {
            if ($_i < $_value)
              $_subGroup->_setVisible(TRUE);
            else
              $_subGroup->_setVisible(FALSE);
            $_i++;
          }
          break;
        case _Key::_bottom_N:
          foreach ($this->_SubGroups as $_subGroup) {
            if ($_count - $_i - 1 < $_value)
              $_subGroup->_setVisible(TRUE);
            else
              $_subGroup->_setVisible(FALSE);
            $_i++;
          }
          break;
        case _Key::_top_percent:
          foreach ($this->_SubGroups as $_subGroup) {
            if (100 * $_i / $_count < $_value)
              $_subGroup->_setVisible(TRUE);
            else
              $_subGroup->_setVisible(FALSE);
            $_i++;
          }
          break;
        case _Key::_bottom_percent:
          foreach ($this->_SubGroups as $_subGroup) {
            if (100 * ($_count - $_i - 1) / $_count < $_value)
              $_subGroup->_setVisible(TRUE);
            else
              $_subGroup->_setVisible(FALSE);
            $_i++;
          }
          break;
      }
    }
    function _SetSort($_value) {
      $this->_Sort = $_value;
      return $this;
    }
    function _SetExpand($_value) {
      $this->Expand = $_value;
      return $this;
    }
    function _SetSortValue($_value) {
      $this->_SortValue = $_value;
      return $this;
    }
    function GetSortValue() {
      return $this->_SortValue;
    }
    function GetField() {
      return $this->_Field;
    }
    function _Process($_command) {
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        $_pv = $this->_PivotTable;
        switch ($_c[_Key::_Command]) {
          case _Key::_Expand:
            if ($_pv->EventHandler->OnBeforeGroupExpand($this, array()) == TRUE) {
              $this->_SetExpand(TRUE);
              $_pv->EventHandler->OnGroupExpand($this, array());
            }
            break;
          case _Key::_Collapse:
            if ($_pv->EventHandler->OnBeforeGroupCollapse($this, array()) == TRUE) {
              $this->_SetExpand(FALSE);
              $_pv->EventHandler->OnGroupCollapse($this, array());
            }
            break;
          case _Key::_SortGroup:
            if ($_pv->EventHandler->OnBeforeGroupSort($this, array()) == TRUE) {
              $this->_SetSort($_c[_Key::_Args][_Key::_Sort])
                  ->_SetPivotSort();
              $_pv->SetSortState(_Key::_groupSort);
              $_pv->EventHandler->OnGroupSort($this, array());
            }
            break;
        }
      }
    }
    function _SetPivotSort() {
      $_group_to_sort = array(
        _Key::_UniqueID => $this->_UniqueID,
        _Key::_Direction => $this->_Sort);
      $this->_PivotTable->_GroupsToSort[$this->_Field->_Type] = $_group_to_sort;
      return $this;
    }
    function _ExportProperties() {
      $_subgroup_ids = array();
      foreach ($this->_SubGroups as $_subgroup)
        array_push($_subgroup_ids, $_subgroup->_UniqueID);
      return array(
        _Key::_Expand => $this->Expand,
        _Key::_value => urlencode($this->Value),
        _Key::_Expandable => $this->_Expandable,
        _Key::_ValueChain => $this->_ValueChain,
        _Key::_SQLCondition => $this->_SQLCondition,
        _Key::_nestedSQLCondition => $this->_nestedSQLCondition,
        _Key::_UniqueID => $this->_UniqueID,
        _Key::_SubGroupIds => $_subgroup_ids,
        _Key::_Key => $this->_Key,
        _Key::_Sort => $this->_Sort,
        _Key::_SortValue => $this->_SortValue,
        _Key::_sqlValues => $this->_sqlValues,
      );
    }
    function _DoSortingGroups($_direction) {
      if (count($this->_SubGroups) > 0) {
        switch ($_direction) {
          case _Key::_ASC:
            uasort($this->_SubGroups, 'Groups_Compare_asc');
            break;
          case _Key::_DESC:
            uasort($this->_SubGroups, 'Groups_Compare_desc');
            break;
        }
        foreach ($this->_SubGroups as $_subgroup)
          $_subgroup->_DoSortingGroups($_direction);
      }
    }
    function _DoSortingFields() {
      if (!empty($this->_SubGroups)) {
        foreach ($this->_SubGroups as $_sub_group) {
          $_field = $_sub_group->_Field;
          $_direction = ($_field->Sort != NULL) ? $_field->Sort : $this->_PivotTable->_DefaultSort[$_field->_Type];
          break;
        }
        switch ($_direction) {
          case _Key::_ASC:
            uasort($this->_SubGroups, 'Groups_Compare_asc');
            break;
          case _Key::_DESC:
            uasort($this->_SubGroups, 'Groups_Compare_desc');
            break;
          case "custom":
            if (function_exists("Groups_Compare_custom"))
              uasort($this->_SubGroups, 'Groups_Compare_custom');
            break;
        }
        foreach ($this->_SubGroups as $_subgroup)
          $_subgroup->_DoSortingFields();
      }
    }
    function _GetGroupInSequence() {
      $_order = 0;
      $_level = 2;
      $_arr = array();
      if ($this->Expand) {
        $_buff = new _SimpleStack();
        $this->_setLevel($_level);
        $this->_setWidth(1);
        $this->_setDepth(0);
        $_buff->_put($this);
        $_buff2 = new _SimpleStack();
        while (!$_buff->_empty()) {
          $_group = $_buff->_get();
          $_group->_SetOrder($_order++);
          $_buff2->_put($_group);
          if ($_group->Expand) {
            foreach ($_group->_SubGroups as $_sub_group)
              if ($_sub_group->_getVisible()) {
                $_sub_group->_setLevel($_group->_GetLevel2() + 1);
                $_sub_group->_setWidth(1);
                $_sub_group->_setDepth(0);
                $_buff->_put($_sub_group);
              }
          }
        }
        while (!$_buff2->_empty()) {
          $_group = $_buff2->_get();
          $_parent = $_group->_getParent();
          if ($_group->_GetDepth() >= $_parent->_GetDepth())
            $_parent->_setDepth($_group->_GetDepth() + 1);
          $_parent->_increaseWidth($_group->_GetWidth());
          array_push($_arr, $_group);
        }
      }
      return $_arr;
    }
    function _RenderHeader() {
      $_tpl_main = "{sign}{text}";
      $_tpl_main = "{sign}{text}{sort}";
      $_tpl_sign = "<span class='{status}' onclick='pivot_group_toggle(this)'></span>";
      $_tpl_desc = "<span class='kptDesc'>{text}</span>";
      $_tpl_sort = "<span class='kptSortButton kptSort{direction}{status}' title='{tooltip}' onclick='pivot_group_sort_toggle(this)'></span>";
      if ($this->_PivotTable->AllowSortingData) {
        switch (strtolower($this->_Sort)) {
          case _Key::_ASC:
            $_sort = _replace("{direction}", "Asc", $_tpl_sort);
            $_sort = _replace("{tooltip}", $this->_PivotTable->Localization->_Messages[_Key::_Sorted_Asc], $_sort);
            break;
          case _Key::_DESC:
            $_sort = _replace("{direction}", "Desc", $_tpl_sort);
            $_sort = _replace("{tooltip}", $this->_PivotTable->Localization->_Messages[_Key::_Sorted_Desc], $_sort);
            break;
          case _Key::_none:
          default:
            $_sort = "";
            break;
        }
        $_status = "Off";
        if (!empty($this->_PivotTable->_GroupsToSort))
          foreach ($this->_PivotTable->_GroupsToSort as $_PV_index => $_group_to_sort)
            if (!empty($_group_to_sort) && $_group_to_sort[_Key::_UniqueID] == $this->_UniqueID)
              $_status = "On";
        $_sort = _replace("{status}", $_status, $_sort);
      } else
        $_sort = "";
      $_tpl_main = _replace("{sort}", $this->_PivotTable->AllowSortingData ? $_sort : "", $_tpl_main);
      $_sign = "";
      if ($this->_Expandable)
        $_sign = _replace("{status}", $this->Expand ? "kptExpand" : "kptCollapse", $_tpl_sign);
      #Show group tree node properties
      $_s = "";
      $_main = _replace("{text}", $this->_Field->RenderHeader($this->Value) . $_s, $_tpl_main);
      $_main = _replace("{sign}", $_sign, $_main);
      return $_main;
    }
    function _RenderHeaderTotal() {
      return $this->_Field->RenderHeaderTotal($this->Value);
    }
    function _Render() {
      return "";
    }
  }
  class _ColumnHeaderGroup extends _PivotGroup {
    var $Expand = TRUE;
  }
  class _RowHeaderGroup extends _PivotGroup {
    var $Expand = TRUE;
  }
  class _GrandHeaderGroup extends _PivotGroup {
    var $Expand = TRUE;
    function _RenderHeader() {
      return "<b>" . $this->_PivotTable->Localization->_Commands[_Key::_Grand_Total] . "</b>";
    }
  }
  class _GrandColumnHeaderGroup extends _GrandHeaderGroup {
  }
  class _GrandRowHeaderGroup extends _GrandHeaderGroup {
  }
  class _PivotCommand {
    var $_UniqueID;
    var $_PivotTable;
    var $_Commands;
    function _Init($_pivottable) {
      $this->_PivotTable = $_pivottable;
      $this->_UniqueID = $_pivottable->_UniqueID . "_cmd";
      $this->_LoadCommands();
    }
    function _LoadCommands() {
      if (isset($_POST[$this->_UniqueID])) {
        $_string = $_POST[$this->_UniqueID];
        $_string = _replace("\\", "", $_string);
        $this->_Commands = json_decode($_string, TRUE);
      }
    }
    function _Render() {
      $_tpl_command = "<input id='{id}' name='{id}' type='hidden' value='' />";
      $_command = _replace("{id}", $this->_UniqueID, $_tpl_command);
      return $_command;
    }
  }
  class _StatusBar {
    var $LoadingText;
    var $DoneText;
    function _Init($_pivottable) {
      if ($this->LoadingText === null)
        $this->LoadingText = $_pivottable->Localization->_Messages[_Key::_Loading];
      if ($this->DoneText === null)
        $this->DoneText = $_pivottable->Localization->_Messages[_Key::_Done];
    }
    function _Render() {
      $_tpl_status = "<div class='kptStatus'><span class='kptDoneText'>{donetext}</span><span class='kptLoadingText'>{loadingtext}</span></div>";
      $_status = _replace("{donetext}", $this->DoneText, $_tpl_status);
      $_status = _replace("{loadingtext}", $this->LoadingText, $_status);
      return $_status;
    }
  }
  class PivotPager {
    var $PageSize = 10;
    var $PageIndex = 0;
    var $ShowPageSize = FALSE;
    var $PageSizeText;
    var $PageSizeOptions = "5,10,20,40";
    var $ShowPageInfo = TRUE;
    var $PageInfoTemplate;
    var $_TotalRows;
    var $_TotalPages;
    var $_UniqueID;
    var $_PivotTable;
    var $_ViewState;
    function _Init($_pivottable) {
      $this->_PivotTable = $_pivottable;
      $this->_ViewState = $_pivottable->_ViewState;
      $this->_UniqueID = $_pivottable->_UniqueID . "_pg";
      if ($this->PageInfoTemplate === null)
        $this->PageInfoTemplate = $_pivottable->Localization->_Messages[_Key::_PageInfoTemplate];
      if ($this->PageSizeText === null)
        $this->PageSizeText = $_pivottable->Localization->_Messages[_Key::_PageSizeText];
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->PageIndex = $_state[_Key::_PageIndex];
        $this->PageSize = $_state[_Key::_PageSize];
        $this->_TotalRows = $_state[_Key::_TotalRows];
        $this->_TotalPages = $_state[_Key::_TotalPages];
      }
    }
    function _Process($_command) {
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        $_com = $_c[_Key::_Command];
        $_arg = $_c[_Key::_Args];
        switch ($_com) {
          case _Key::_GoPage:
            if ($this->_PivotTable->EventHandler->OnBeforePageChange($this, array(_Key::_PageIndex => $_arg[_Key::_PageIndex])) == TRUE) {
              $this->PageIndex = $_arg[_Key::_PageIndex];
              $this->_PivotTable->EventHandler->OnPageChange($this, array(_Key::_PageIndex => $_arg[_Key::_PageIndex]));
            }
            break;
          case _Key::_ChangePageSize:
            if ($this->_PivotTable->EventHandler->OnBeforePageSizeChange($this, array(_Key::_PageSize => $_arg[_Key::_PageSize])) == TRUE) {
              $this->PageSize = $_arg[_Key::_PageSize];
              $this->_PivotTable->EventHandler->OnPageSizeChange($this, array(_Key::_PageSize => $_arg[_Key::_PageSize]));
            }
            break;
        }
      }
      $this->_TotalPages = ceil($this->_TotalRows / $this->PageSize);
      if ($this->PageIndex >= $this->_TotalPages)
        $this->PageIndex = $this->_TotalPages - 1;
      if ($this->PageIndex < 0)
        $this->PageIndex = 0;
    }
    function _SaveViewState() {
      $this->_ViewState->_Data[$this->_UniqueID] = array(
        _Key::_PageIndex => $this->PageIndex,
        _Key::_PageSize => $this->PageSize,
        _Key::_TotalRows => $this->_TotalRows,
        _Key::_TotalPages => $this->_TotalPages
      );
    }
    function _RenderPageInfo() {
      $_tpl_info = "<div class='kptInfo'>{text}</div>";
      $_text = _replace("{PageIndex}", ($this->_TotalPages > 0) ? ($this->PageIndex + 1) : 0, $this->PageInfoTemplate);
      $_text = _replace("{TotalPages}", $this->_TotalPages, $_text);
      $_firstindex = ($this->_TotalPages > 0) ? ($this->PageIndex * $this->PageSize + 1) : 0;
      $_lastindex = ($this->PageIndex + 1) * $this->PageSize;
      if ($_lastindex > $this->_TotalRows)
        $_lastindex = $this->_TotalRows;
      $_text = _replace("{FirstIndexInPage}", $_firstindex, $_text);
      $_text = _replace("{LastIndexInPage}", $_lastindex, $_text);
      $_text = _replace("{TotalRows}", $this->_TotalRows, $_text);
      $_info = _replace("{text}", $_text, $_tpl_info);
      return $_info;
    }
    function _RenderPageSize() {
      $_tpl_pagesize = "<div class='kptPageSize'>{text}{select}</div>";
      $_tpl_select = "<select onchange='pivot_pagesize_select_onchange(this)'>{options}</select>";
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
    function Render() {
      return "[pager zone]";
    }
  }
  class PivotPrevNextAndNumericPager extends PivotPager {
    var $Range = 10;
    var $FirstPageText;
    var $FirstPageToolTip;
    var $PrevPageText;
    var $PrevPageToolTip;
    var $NextPageText;
    var $NextPageToolTip;
    var $LastPageText;
    var $LastPageToolTip;
    function _Init($_pivottable) {
      parent::_Init($_pivottable);
      $_c = $_pivottable->Localization->_Commands;
      $_m = $_pivottable->Localization->_Messages;
      if ($this->FirstPageText === null)
        $this->FirstPageText = $_c[_Key::_First];
      if ($this->FirstPageToolTip === null)
        $this->FirstPageToolTip = $_m[_Key::_FirstPageToolTip];
      if ($this->PrevPageText === null)
        $this->PrevPageText = $_c[_Key::_Prev];
      if ($this->PrevPageToolTip === null)
        $this->PrevPageToolTip = $_m[_Key::_PrevPageToolTip];
      if ($this->NextPageText === null)
        $this->NextPageText = $_c[_Key::_Next];
      if ($this->NextPageToolTip === null)
        $this->NextPageToolTip = $_m[_Key::_NextPageToolTip];
      if ($this->LastPageText === null)
        $this->LastPageText = $_c[_Key::_Last];
      if ($this->LastPageToolTip === null)
        $this->LastPageToolTip = $_m[_Key::_LastPageToolTip];
    }
    function Render() {
      $_tpl_pager = "<div class='kptPager kptNextPrevAndNumericPager'>{nav}{pagesize}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kptNav'>{first} {prev} {numbers} {next} {last}</div>";
      $_tpl_number = "<a class='kptNum {selected}' {href} {onclick}><span>{number}</span></a> ";
      $_tpl_button = "<input type='button' onclick='{onclick}' title='{title}' class='nodecor'/>";
      $_tpl_a = "<a href='javascript:void 0' onclick='{onclick}' title='{title}'>{text}</a>";
      $_tpl_bound = "<span class= '{class}'>{button}</span>";
      $_start_num = floor($this->PageIndex / $this->Range) * $this->Range;
      $_numbers = "";
      if ($_start_num > 0) {
        $_number = _replace("{href}", "href='javascript:void 0'", $_tpl_number);
        $_number = _replace("{onclick}", "onclick='pivot_gopage(this," . ($_start_num - 1) . ")'", $_number);
        $_number = _replace("{number}", "...", $_number);
        $_numbers .= $_number;
      }
      for ($i = $_start_num; $i < $_start_num + $this->Range && $i < $this->_TotalPages; $i++) {
        $_number = _replace("{number}", ($i + 1), $_tpl_number);
        if ($i == $this->PageIndex) {
          $_number = _replace("{selected}", "kptNumSelected", $_number);
          $_number = _replace("{href}", "", $_number);
          $_number = _replace("{onclick}", "", $_number);
        } else {
          $_number = _replace("{selected}", "", $_number);
          $_number = _replace("{href}", "href='javascript:void 0'", $_number);
          $_number = _replace("{onclick}", "onclick='pivot_gopage(this," . $i . ")'", $_number);
        }
        $_numbers .= $_number;
      }
      if ($_start_num + $this->Range < $this->_TotalPages) {
        $_number = _replace("{href}", "href='javascript:void 0'", $_tpl_number);
        $_number = _replace("{onclick}", "onclick='pivot_gopage(this," . ($_start_num + $this->Range) . ")'", $_number);
        $_number = _replace("{number}", "...", $_number);
        $_number = _replace("{selected}", "", $_number);
        $_numbers .= $_number;
      }
      $_first_button = _replace("{onclick}", ($this->PageIndex > 0) ? "pivot_gopage(this,0)" : "", $_tpl_button);
      $_first_button = _replace("{title}", $this->FirstPageToolTip, $_first_button);
      $_first_a = _replace("{onclick}", ($this->PageIndex > 0 && $this->FirstPageText !== null) ? "pivot_gopage(this,0)" : "", $_tpl_a);
      $_first_a = _replace("{text}", $this->FirstPageText, $_first_a);
      $_first_a = _replace("{title}", $this->FirstPageToolTip, $_first_a);
      $_first = _replace("{button}", $_first_button . $_first_a, $_tpl_bound);
      $_first = _replace("{class}", "kptFirst", $_first);
      $_prev_button = _replace("{onclick}", ($this->PageIndex > 0) ? "pivot_gopage(this," . ($this->PageIndex - 1) . ")" : "", $_tpl_button);
      $_prev_button = _replace("{title}", $this->PrevPageToolTip, $_prev_button);
      $_prev_a = _replace("{onclick}", ($this->PageIndex > 0 && $this->PrevPageText !== null) ? "pivot_gopage(this," . ($this->PageIndex - 1) . ")" : "", $_tpl_a);
      $_prev_a = _replace("{text}", $this->PrevPageText, $_prev_a);
      $_prev_a = _replace("{title}", $this->PrevPageToolTip, $_prev_a);
      $_prev = _replace("{button}", $_prev_button . $_prev_a, $_tpl_bound);
      $_prev = _replace("{class}", "kptPrev", $_prev);
      $_next_button = _replace("{onclick}", ($this->PageIndex < $this->_TotalPages - 1) ? "pivot_gopage(this," . ($this->PageIndex + 1) . ")" : "", $_tpl_button);
      $_next_button = _replace("{title}", $this->NextPageToolTip, $_next_button);
      $_next_a = _replace("{onclick}", (($this->PageIndex < $this->_TotalPages - 1) && $this->NextPageText !== null) ? "pivot_gopage(this," . ($this->PageIndex + 1) . ")" : "", $_tpl_a);
      $_next_a = _replace("{text}", $this->NextPageText, $_next_a);
      $_next_a = _replace("{title}", $this->NextPageToolTip, $_next_a);
      $_next = _replace("{button}", $_next_a . $_next_button, $_tpl_bound);
      $_next = _replace("{class}", "kptNext", $_next);
      $_last_button = _replace("{onclick}", ($this->PageIndex < $this->_TotalPages - 1) ? "pivot_gopage(this," . ($this->_TotalPages - 1) . ")" : "", $_tpl_button);
      $_last_button = _replace("{title}", $this->LastPageToolTip, $_last_button);
      $_last_a = _replace("{onclick}", (($this->PageIndex < $this->_TotalPages - 1) && $this->LastPageText !== null) ? "pivot_gopage(this," . ($this->_TotalPages - 1) . ")" : "", $_tpl_a);
      $_last_a = _replace("{text}", $this->LastPageText, $_last_a);
      $_last_a = _replace("{title}", $this->LastPageToolTip, $_last_a);
      $_last = _replace("{button}", $_last_a . $_last_button, $_tpl_bound);
      $_last = _replace("{class}", "kptLast", $_last);
      $_nav = _replace("{numbers}", $_numbers, $_tpl_nav);
      $_nav = _replace("{prev}", $_prev, $_nav);
      $_nav = _replace("{next}", $_next, $_nav);
      $_nav = _replace("{first}", $_first, $_nav);
      $_nav = _replace("{last}", $_last, $_nav);
      $_pagesize = ($this->ShowPageSize) ? $this->_RenderPageSize() : "";
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{nav}", $_nav, $_tpl_pager);
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      return $_pager;
    }
  }
  class PivotPrevNextPager extends PivotPager {
    var $FirstPageText;
    var $FirstPageToolTip;
    var $PrevPageText;
    var $PrevPageToolTip;
    var $NextPageText;
    var $NextPageToolTip;
    var $LastPageText;
    var $LastPageToolTip;
    function _Init($_tableview) {
      parent::_Init($_pivottable);
      $_c = $_pivottable->Localization->_Commands;
      $_m = $_pivottable->Localization->_Messages;
      if ($this->FirstPageText === null)
        $this->FirstPageText = $_c[_Key::_First];
      if ($this->FirstPageToolTip === null)
        $this->FirstPageToolTip = $_m[_Key::_FirstPageToolTip];
      if ($this->PrevPageText === null)
        $this->PrevPageText = $_c[_Key::_Prev];
      if ($this->PrevPageToolTip === null)
        $this->PrevPageToolTip = $_m[_Key::_PrevPageToolTip];
      if ($this->NextPageText === null)
        $this->NextPageText = $_c[_Key::_Next];
      if ($this->NextPageToolTip === null)
        $this->NextPageToolTip = $_m[_Key::_NextPageToolTip];
      if ($this->LastPageText === null)
        $this->LastPageText = $_c[_Key::_Last];
      if ($this->LastPageToolTip === null)
        $this->LastPageToolTip = $_m[_Key::_LastPageToolTip];
    }
    function Render() {
      $_tpl_pager = "<div class='kptPager kptNextPrevNextPager'>{pagesize}{nav}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kptNav'>{first} {prev} {next} {last}</div>";
      $_tpl_button = "<input type='button' onclick='{onclick}' title='{title}' class='nodecor'/>";
      $_tpl_a = "<a href='javascript:void 0' onclick='{onclick}' title='{title}'>{text}</a>";
      $_tpl_bound = "<span class= '{class}'>{button}</span>";
      $_first_button = _replace("{onclick}", ($this->PageIndex > 0) ? "pivot_gopage(this,0)" : "", $_tpl_button);
      $_first_button = _replace("{title}", $this->FirstPageToolTip, $_first_button);
      $_first_a = _replace("{onclick}", ($this->PageIndex > 0 && $this->FirstPageText !== null) ? "pivot_gopage(this,0)" : "", $_tpl_a);
      $_first_a = _replace("{text}", $this->FirstPageText, $_first_a);
      $_first_a = _replace("{title}", $this->FirstPageToolTip, $_first_a);
      $_first = _replace("{button}", $_first_button . $_first_a, $_tpl_bound);
      $_first = _replace("{class}", "kptFirst", $_first);
      $_prev_button = _replace("{onclick}", ($this->PageIndex > 0) ? "pivot_gopage(this," . ($this->PageIndex - 1) . ")" : "", $_tpl_button);
      $_prev_button = _replace("{title}", $this->PrevPageToolTip, $_prev_button);
      $_prev_a = _replace("{onclick}", ($this->PageIndex > 0 && $this->PrevPageText !== null) ? "pivot_gopage(this," . ($this->PageIndex - 1) . ")" : "", $_tpl_a);
      $_prev_a = _replace("{text}", $this->PrevPageText, $_prev_a);
      $_prev_a = _replace("{title}", $this->PrevPageToolTip, $_prev_a);
      $_prev = _replace("{button}", $_prev_button . $_prev_a, $_tpl_bound);
      $_prev = _replace("{class}", "kptPrev", $_prev);
      $_next_button = _replace("{onclick}", ($this->PageIndex < $this->_TotalPages - 1) ? "pivot_gopage(this," . ($this->PageIndex + 1) . ")" : "", $_tpl_button);
      $_next_button = _replace("{title}", $this->NextPageToolTip, $_next_button);
      $_next_a = _replace("{onclick}", (($this->PageIndex < $this->_TotalPages - 1) && $this->NextPageText !== null) ? "pivot_gopage(this," . ($this->PageIndex + 1) . ")" : "", $_tpl_a);
      $_next_a = _replace("{text}", $this->NextPageText, $_next_a);
      $_next_a = _replace("{title}", $this->NextPageToolTip, $_next_a);
      $_next = _replace("{button}", $_next_a . $_next_button, $_tpl_bound);
      $_next = _replace("{class}", "kptNext", $_next);
      $_last_button = _replace("{onclick}", ($this->PageIndex > 0) ? "pivot_gopage(this," . ($this->_TotalPages - 1) . ")" : "", $_tpl_button);
      $_last_button = _replace("{title}", $this->LastPageToolTip, $_last_button);
      $_last_a = _replace("{onclick}", (($this->PageIndex < $this->_TotalPages - 1) && $this->LastPageText !== null) ? "pivot_gopage(this," . ($this->_TotalPages - 1) . ")" : "", $_tpl_a);
      $_last_a = _replace("{text}", $this->LastPageText, $_last_a);
      $_last_a = _replace("{title}", $this->LastPageToolTip, $_last_a);
      $_last = _replace("{button}", $_last_a . $_last_button, $_tpl_bound);
      $_last = _replace("{class}", "kptLast", $_last);
      $_nav = _replace("{prev}", $_prev, $_tpl_nav);
      $_nav = _replace("{next}", $_next, $_nav);
      $_nav = _replace("{first}", $_first, $_nav);
      $_nav = _replace("{last}", $_last, $_nav);
      $_pagesize = ($this->ShowPageSize) ? $this->_RenderPageSize() : "";
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{nav}", $_nav, $_tpl_pager);
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      return $_pager;
    }
  }
  class PivotNumericPager extends PivotPager {
    var $Range = 10;
    function Render() {
      $_tpl_pager = "<div class='kptPager kptNumericPager'>{pagesize}{nav}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kptNav'>{numbers}</div>";
      $_tpl_number = "<a class='kptNum {selected}' {href} {onclick}><span>{number}</span></a> ";
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
          $_number = _replace("{selected}", "kptNumSelected", $_number);
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
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{nav}", $_nav, $_tpl_pager);
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      return $_pager;
    }
  }
  class PivotManualPager extends PivotPager {
    var $ManualPagerTemplate;
    var $ButtonType = "Button"; //"Button"|"Link"|"Image"
    var $GoPageButtonText;
    var $TextBoxWidth = "25px";
    function _Init($_pivottable) {
      parent::_Init($_pivottable);
      if ($this->ManualPagerTemplate === null)
        $this->ManualPagerTemplate = $_pivottable->Localization->_Messages[_Key::_ManualPagerTemplate];
      if ($this->GoPageButtonText === null)
        $this->GoPageButtonText = $_pivottable->Localization->_Commands[_Key::_Go];
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
      }
    }
    function Render() {
      $_tpl_pager = "<div class='kptPager kptManualPager'>{pagesize}{nav}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kptNav'>{main}</div>";
      $_tpl_textbox = "<input id='{id}' name='{id}' type='textbox' style='width:{width};' value='{text}'/>";
      $_tpl_main = $this->ManualPagerTemplate;
      $_tpl_gopage = "";
      switch (strtolower($this->ButtonType)) {
        case "link":
          $_tpl_gopage = "<a class='kptGoButton' href='javascript:void 0' onclick='grid_gopage(this,0)'>{text}</a>";
          break;
        case "image":
          $_tpl_gopage = "<input class='kptGoButton kptGoImage' type='button' onclick='grid_gopage(this,0)' />";
          break;
        case "button":
        default:
          $_tpl_gopage = "<input class='kptGoButton' type='button' onclick='grid_gopage(this,0)' value='{text}' />";
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
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{nav}", $_nav, $_tpl_pager);
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      return $_pager;
    }
  }
  class PivotEventHandler {
    function OnBeforeFieldMove($_sender, $_args) {
      return TRUE;
    }
    function OnFieldMove($_sender, $_args) {
    }
    function OnBeforeFieldSort($_sender, $_args) {
      return TRUE;
    }
    function OnFieldSort($_sender, $_args) {
    }
    function OnBeforeGroupSort($_sender, $_args) {
      return TRUE;
    }
    function OnGroupSort($_sender, $_args) {
    }
    function OnBeforeFieldFilter($_sender, $_args) {
      return TRUE;
    }
    function OnFieldFilter($_sender, $_args) {
    }
    function OnBeforeFieldCollapse($_sender, $_args) {
      return TRUE;
    }
    function OnFieldCollapse($_sender, $_args) {
    }
    function OnBeforeFieldExpand($_sender, $_args) {
      return TRUE;
    }
    function OnFieldExpand($_sender, $_args) {
    }
    function OnBeforeGroupExpand($_sender, $_args) {
      return TRUE;
    }
    function OnGroupExpand($_sender, $_args) {
    }
    function OnBeforeGroupCollapse($_sender, $_args) {
      return TRUE;
    }
    function OnGroupCollapse($_sender, $_args) {
    }
    function OnBeforePageChange($_sender, $_args) {
      return TRUE;
    }
    function OnPageChange($_sender, $_args) {
    }
    function OnBeforePageSizeChange($_sender, $_args) {
      return TRUE;
    }
    function OnPageSizeChange($_sender, $_args) {
    }
    function OnBeforeFilterPanelOpen($_sender, $_args) {
      return TRUE;
    }
    function OnFilterPanelOpen($_sender, $_args) {
    }
    function OnRefresh($_sender, $_args) {
    }
    function OnBeforeCellRender($_sender, $_args) {
    }
    function OnBeforeChangeSortData($_sender, $_args) {
      return TRUE;
    }
    function OnChangeSortData($_sender, $_args) {
    }
    function OnReadingViewstate($_sender, & $_args) {
      return true;
    }
    function OnSavingViewstate($_sender, $_args) {
      return true;
    }
  }
  class _Appearance {
    var $RowHeaderMinWidth;
  }
  class _PivotTableExportSettings {
    public $IgnorePaging = FALSE;
    protected $_config = array();
    protected $_changes = array();
    protected $_htmlStyles = array();
    protected $_properties = array();
    protected $_default = array();
    function __construct() {
      $this->_default["config"]["pdf"] = array(
        "pageOrientation" => "L",
        "pageDimension" => array(600, 400),
        "font" => array("family" => 'FreeSans', "style" => "", "size" => 10),
      );
      $this->_default["properties"]["table"] = array(
        "border" => "1",
        "cellspacing" => "0",
      );
      $this->config(array(
        "fileName" => "KoolPivotTableExport",
        "template" => "{KoolPivotTable}",
        "showFilterZone" => TRUE,
        "showDataZone" => TRUE,
        "caseSensitive" => TRUE,
        "pdf" => $this->_default["config"]["pdf"],
      ));
      $this->htmlStyle(array(
        "table" => "border:1px solid grey;"
        . "border-collapse:collapse;color:black;",
        "totalRow" => "background-color:lightblue; font-weight:bold;",
        "totalColumn" => "background-color:lightblue; font-weight:bold;",
        "dataCell" => "text-align:right;",
        "emptyDataCell" => "text-align:center;",
        "expandedCell" => "vertical-align:top;",
        "cell" => "padding:5px; border:1px solid grey;",
      ));
      $this->htmlProperty(array(
        "table" => $this->_default["properties"]["table"],
      ));
    }
    function config($_var) {
      if (is_array($_var))
        $this->_config = array_merge($this->_config, $_var);
      return $this;
    }
    function _getConfig() {
      if (isset($this->_config["pdf"]["font"]) && is_array($this->_config["pdf"]["font"]))
        $this->_config["pdf"]["font"] = array_merge($this->_default["config"]["pdf"]["font"], $this->_config["pdf"]["font"]);
      if (isset($this->_config["pdf"]) && is_array($this->_config["pdf"]))
        $this->_config["pdf"] = array_merge($this->_default["config"]["pdf"], $this->_config["pdf"]);
      return $this->_config;
    }
    function htmlStyle($_var) {
      if (is_array($_var))
        $this->_htmlStyles = array_merge($this->_htmlStyles, $_var);
      return $this;
    }
    function _getHtmlStyles() {
      return $this->_htmlStyles;
    }
    function htmlProperty($_var) {
      if (is_array($_var))
        $this->_properties = array_merge($this->_properties, $_var);
      return $this;
    }
    function _getProperties() {
      if (isset($this->_properties["table"]) && is_array($this->_properties["table"]))
        $this->_properties["table"] = array_merge($this->_default["properties"]["table"], $this->_properties["table"]);
      return $this->_properties;
    }
    function changeText($_var) {
      if (is_array($_var))
        $this->_changes = array_merge($this->_changes, $_var);
      return $this;
    }
    function _getChanges() {
      return $this->_changes;
    }
  }
  class _SimpleList {
    private $_list;
    public static function _new() {
      $_simList = new _SimpleList();
      $_simList->_list = array();
      return $_simList;
    }
    public function _push($_e) {
      if (isset($_e)) {
        if (!is_array($_e))
          $_arrE = array($_e);
        else
          $_arrE = $_e;
        foreach ($_arrE as $_e)
          array_push($this->_list, $_e);
      }
    }
    public function _pop() {
      $_e = array_pop($this->_list);
      return $_e;
    }
    public function _put($_e, $_i = -1) {
      if (isset($_e)) {
        if (!is_array($_e))
          $_arrE = array($_e);
        else
          $_arrE = $_e;
        if ($_i < 0 || $_i > count($this->_list))
          $_i = count($this->_list);
        array_splice($this->_list, $_i, 0, $_arrE);
      }
    }
    public function _get($_i = -1) {
      if ($_i < 0 || $_i > count($this->_list) - 1)
        $_i = count($this->_list) - 1;
      $_arrE = array_slice($this->_list, $_i, 1);
      if (isset($_arrE[0]))
        return $_arrE[0];
      else
        return NULL;
    }
    public function _remove($_i = -1) {
      if ($_i < 0)
        $_i = 0;
      else if ($_i > count($this->_list) - 1)
        $_i = count($this->_list) - 1;
      $_arrE = array_splice($this->_list, $_i, 1);
      if (isset($_arrE[0]))
        return $_arrE[0];
      else
        return NULL;
    }
    function _toArray() {
      return $this->_list;
    }
    function _empty() {
      return empty($this->_list);
    }
    function _length() {
      return count($this->_list);
    }
  }
  class _FieldList {
    private $_fields;
    private function __construct() {
    }
    public static function _new() {
      $_fieldList = new _FieldList();
      $_fieldList->_fields = _SimpleList::_new();
      return $_fieldList;
    }
    function _addField($_f, $_i = -1) {
      $this->_fields->_put($_f, $_i);
      return $this;
    }
    function _getField($_var) {
      if (is_string($_var)) {
        $_fields = $this->_fieldArray();
        foreach ($_fields as $_field)
          if ($_field->FieldName == $_var)
            return $_field;
      }
      return NULL;
    }
    function _removeField($_i = -1) {
      $_f = $this->_fields->_remove($_i);
      return $_f;
    }
    function _field($_i = -1) {
      $_f = $this->_fields->_get($_i);
      return $_f;
    }
    function _fieldArray($_i = 0) {
      return array_slice($this->_fields->_toArray(), $_i);
    }
    function _fieldString($_i = 0) {
      $_s = "";
      foreach ($this->_fieldArray($_i) as $_field)
        $_s .= $_field->Text . ">>>";
      return rtrim($_s, ">");
    }
    function _length() {
      return $this->_fields->_length();
    }
    function _buildFieldKey() {
      $_fk = "";
      foreach ($this->_fields->_toArray() as $_field) {
        $_fk .= "_" . $_field->_UniqueID;
        $_field->_key = $_fk;
      }
      return $this;
    }
    function _buildSelect($_i = 0) {
      $_fields = $this->_fieldArray($_i);
      foreach ($_fields as $_field) {
        $_field->_select = array(
          _Key::_field => $_field->_sqlExpression,
          _Key::_nestedField => $_field->FieldName,
          _Key::_alias => $_field->_sqlAlias,
          _Key::_pivotField => $_field,
        );
        if ($_field->_Type == _PV::_Data) {
          $_field->_select[_Key::_aggregateFunction] = $_field->SqlOperator;
        }
      }
      return $this;
    }
  }
  class KoolPivotTable {
    var $_version = "3.9.0.0";
    var $_UniqueID;
    var $_style;
    var $_Values;
    var $_RawValues;
    var $_headGroups;
    var $_arrGroups;
    var $_PVFields;
    var $_ScrollTop = 0;
    var $_ScrollLeft = 0;
    var $_ViewState;
    var $_Command;
    var $_Rebind = FALSE;
    var $_RecalFilter = FALSE;
    var $_RecalFilterCondition = FALSE;
    var $_RecalHeader = FALSE;
    var $_CacheID;
    var $_FilterPanelItem;
    var $_GroupsToSort = array();
    var $_DataFieldToSort;
    var $_DefaultSort = array(_PV::_Row => _Key::_ASC, _PV::_Column => _Key::_ASC);
    var $_ResetFieldType = null;
    var $_idToGroup;
    private $_initSortedGroup = array(_Key::_name => "", _Key::_Direction => _Key::_DESC);
    private $_sortState = _Key::_initSort;
    var $id;
    var $scriptFolder;
    var $styleFolder;
    var $HorizontalScrolling = FALSE;
    var $VerticalScrolling = FALSE;
    var $Pager;
    var $Width;
    var $Height;
    var $AjaxEnabled = FALSE;
    var $AjaxLoadingImage = null;
    var $AjaxHandlePage;
    var $ShowColumnZone = TRUE;
    var $ShowRowZone = TRUE;
    var $ShowDataZone = TRUE;
    var $ShowFilterZone = TRUE;
    var $SeparateDataZone = false;
    var $SeparateRowZone = false;
    var $EmptyValue = "-";
    var $ErrorValue = "-";
    var $ShowStatus = TRUE;
    var $KeepViewStateInSession = FALSE;
    var $Localization;
    var $AllowCaching = FALSE;
    var $CacheFolder; //default will be the temp folder
    var $CacheTime; //How many second;
    var $AllowSorting = FALSE;
    var $AllowSortingData = FALSE;
    var $AllowReorder = FALSE;
    var $AllowFiltering = FALSE;
    var $Status;
    var $ClientEvents;
    var $EventHandler;
    var $Appearance;
    var $DataSource;
    var $ExportSettings;
    var $CharSet = "UTF-8";
    var $ShowGrandColumn = TRUE;
    var $ShowGrandRow = TRUE;
    var $CssClasses = array();
    var $RowZoneWidth = 'auto';
    var $RowZoneMinWidth = 'auto';
    function __construct($_id) {
      $this->id = $_id;
      $this->_UniqueID = $_id;
      foreach (_PV::$_index as $_PV_index)
        $this->_PVFields[$_PV_index] = _FieldList::_new();
      $this->_Values = array();
      foreach (array(_PV::_Column => _Key::_c, _PV::_Row => _Key::_r) as $_PV_index => $_value) {
        $_group = _pivotGroup::_newGrandGroup($_value, null);
        $_group->_Init($this);
        $this->_headGroups[$_PV_index] = $_group;
        $_grand_field = _GrandRCField::_newGrand();
        $this->_PVFields[$_PV_index]->_addField($_grand_field);
      }
      $this->_ViewState = new _PivotViewState($this);
      $this->Localization = new _PivotLocalization();
      $this->_Command = new _PivotCommand();
      $this->Status = new _StatusBar();
      $this->ClientEvents = array();
      $this->EventHandler = new PivotEventHandler();
      $this->Appearance = new _Appearance();
      $this->ExportSettings = new _PivotTableExportSettings();
    }
    function AddDataField($_field) {
      $this->AddField($_field, _PV::_Data);
      return $this;
    }
    function AddFilterField($_field) {
      $this->AddField($_field, _PV::_Filter);
      return $this;
    }
    function AddRowField($_field) {
      $this->AddField($_field, _PV::_Row);
      return $this;
    }
    function AddColumnField($_field) {
      $this->AddField($_field, _PV::_Column);
      return $this;
    }
    function AddField($_field, $_PV_index) {
      $_FN = $_field->FieldName;
      $_FT = $_field->Text;
      $_FEx = $_FN;
      $_ds = $this->DataSource;
      $_selectFields = $_ds->_getSelectFields();
      if (!empty($_selectFields))
        foreach ($_selectFields as $_selectField)
          if ($_selectField["alias"] == $_FN)
            $_FEx = $_selectField["expression"];
      if ($_field instanceof PivotDateField) {
        $_display = $_field->getDateFields();
        foreach ($_display as $_dateType => $_show)
          if ($_show) {
            $_FN_k = $_FN . "_" . $_dateType;
            $_FEx_k = $_ds->_toSQLDate($_dateType) . "(" . $_FEx . ")";
            $_FT_k = $_FT . "'s " . ucfirst($_dateType);
            $_selectField = array("alias" => $_FN_k, "expression" => $_FEx_k);
            array_push($_selectFields, $_selectField);
            $_field_k = new PivotField($_FN_k);
            $_field_k->Text = $_FT_k;
            $_field_k->_setSqlExpression($_FEx_k);
            $this->_PVFields[$_PV_index]->_addField($_field_k);
          }
      } else {
        $_valueMap = $_field->getValueMap();
        if (isset($_valueMap)) {
          $_FNs = $_valueMap->getMapFields();
          foreach ($_FNs as $_type => $_FN_k) {
            if (empty($_FN_k) || $_FN_k == $_FN) {
              $_FN_k = $_FN;
              $_FT_k = $_FT;
            } else {
              $_FT_k = $_FN_k;
            }
            $_field_k = _PivotField::_new($_FN_k, $_type);
            $_field_k->setValueMap($_valueMap);
            $_field_k->Text = $_FT_k;
            $_field_k->_setSqlExpression($_FEx);
            $this->_PVFields[$_PV_index]->_addField($_field_k);
          }
        } else {
          $_field->_setSqlExpression($_FEx);
          $this->_PVFields[$_PV_index]->_addField($_field);
        }
      }
      return $this;
    }
    function GetFilterField($_FN) {
      return $this->_getField($_FN, _PV::_Filter);
    }
    function GetDataField($_FN) {
      return $this->_getField($_FN, _PV::_Data);
    }
    function GetRowField($_FN) {
      return $this->_getField($_FN, _PV::_Row);
    }
    function GetColumnField($_FN) {
      return $this->_getField($_FN, _PV::_Column);
    }
    function _getField($_FN, $_PV_index) {
      return $this->_PVFields[$_PV_index]->_getField($_FN);
    }
    function _getGroups($_PV_index, $_fieldName) {
      $_field = $this->_getField($_fieldName, $_PV_index);
      $_arr = array();
      foreach ($_field->_ExpandedParentGroups as $_group)
        if ($_group->_hasExpandedSubGroup()) {
          $_subGroups = $_group->_getSubGroups();
          foreach ($_subGroups as $_subGroup)
            array_push($_arr, $_subGroup);
        }
      return $_arr;
    }
    function _getGroupNames($_PV_index, $_fieldName) {
      $_field = $this->_getField($_fieldName, $_PV_index);
      $_arr = array();
      if (! isset($_field))
        return $_arr;
      foreach ($_field->_ExpandedParentGroups as $_group)
        if ($_group->_hasExpandedSubGroup()) {
          $_subGroups = $_group->_getSubGroups();
          foreach ($_subGroups as $_subGroup)
            array_push($_arr, $_subGroup->Value);
        }
      return $_arr;
    }
    function getGroupNames($_fieldName) {
      $arr = array();
      foreach (array(_PV::_Column, _PV::_Row) as $_PV_index)
        $arr = array_merge($arr, $this->_getGroupNames($_PV_index, $_fieldName));
      return $arr;
    }
    function getRowGroupNames($_fieldName) {
      $arr = $this->_getGroupNames(_PV::_Row, $_fieldName);
      return $arr;
    }
    function getColumnGroupNames($_fieldName) {
      $arr = $this->_getGroupNames(_PV::_Column, $_fieldName);
      return $arr;
    }    
    function SetDataFieldForSorting($_field) {
      $this->_DataFieldToSort = $_field;
      return $this;
    }
    function SetInitSortedGroup($_name, $_sort = _Key::_DESC) {
      $this->_initSortedGroup[_Key::_name] = $_name;
      $this->_initSortedGroup[_Key::_Direction] = $_sort;
      return $this;
    }
    function SetSortState($_value) {
      $this->_sortState = $_value;
      return $this;
    }
    function _Init() {
      $this->_ViewState->_Init($this);
      $_index = 0;
      foreach (_PV::$_index as $_PV_index)
        foreach ($this->_PVFields[$_PV_index]->_fieldArray() as $_field) {
          $_field->_Init($this, $_index++);
          $_field->_Type = $_PV_index;
        }
      if (isset($this->Pager))
        $this->Pager->_Init($this);
      $this->_Command->_Init($this);
      $this->Status->_Init($this);
    }
    function ClearViewState() {
      unset($_POST[$this->_PivotTable->_UniqueID . _Key::__viewstate]);
    }
    function _LoadViewState() {
      foreach (_PV::$_index as $_PV_index)
        foreach ($this->_PVFields[$_PV_index]->_fieldArray() as $i => $_field) {
          $_field->_LoadViewState();
        }
      if (isset($this->Pager))
        $this->Pager->_LoadViewState();
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $_PVField_ids = $_state[_Key::_PVField_Ids];
        $this->_ScrollTop = $_state[_Key::_ScrollTop];
        $this->_ScrollLeft = $_state[_Key::_ScrollLeft];
        $this->_CacheID = $_state[_Key::_CacheID];
        $this->_GroupsToSort = $_state[_Key::_GroupsToSort];
        $this->_sortState = $_state[_Key::_sortState];
        $_all_fields = array();
        foreach (_PV::$_index as $_PV_index)
          foreach ($this->_PVFields[$_PV_index]->_fieldArray() as $_field)
            $_all_fields[$_field->_UniqueID] = $_field;
        foreach (_PV::$_index as $_PV_index) {
          $this->_PVFields[$_PV_index] = _FieldList::_new();
          foreach ($_PVField_ids[$_PV_index] as $_UniqueID)
            $this->_PVFields[$_PV_index]->_addField($_all_fields[$_UniqueID]);
        }
      } else
        $this->_CacheID = uniqid();
    }
    function _SaveViewState() {
      $this->_ViewState->_Clear();
      foreach (_PV::$_index as $_PV_index) {
        $_PVField_ids[$_PV_index] = array();
        foreach ($this->_PVFields[$_PV_index]->_fieldArray() as $_field) {
          $_field->_SaveViewState();
          array_push($_PVField_ids[$_PV_index], $_field->_UniqueID);
        }
      }
      foreach ($this->_arrGroups[_PV::_Column] as $_group)
        $_group->_SaveViewState();
      foreach ($this->_arrGroups[_PV::_Row] as $_group)
        $_group->_SaveViewState();
      if (isset($this->Pager))
        $this->Pager->_SaveViewState();
      $this->_ViewState->_Data[$this->_UniqueID] = array(
        _Key::_PVField_Ids => $_PVField_ids,
        _Key::_CacheID => $this->_CacheID,
        _Key::_AllowReorder => $this->AllowReorder,
        _Key::_HorizontalScrolling => $this->HorizontalScrolling,
        _Key::_VerticalScrolling => $this->VerticalScrolling,
        _Key::_ScrollTop => $this->_ScrollTop,
        _Key::_ScrollLeft => $this->_ScrollLeft,
        _Key::_ClientEvents => $this->ClientEvents,
        _Key::_GroupsToSort => $this->_GroupsToSort,
        _Key::_sortState => $this->_sortState,
      );
      $_statevalue = json_encode($this->_ViewState->_Data);
      $this->EventHandler->OnSavingViewstate($this, array(
        'pivotId' => $this->id,
        'viewstate' => $_statevalue
      ));
    }
    function _GetHtmlTable($_start_row, $_row_count) {
      $_ExSt = $this->ExportSettings;
      $_config = $_ExSt->_getConfig();
      $_styles = $_ExSt->_getHtmlStyles();
      $_prt = $_ExSt->_getProperties();
      #Get width and depth of column and row grand groups
      $_col_grand = $this->_headGroups[_PV::_Column]->_getSubGroup(0);
      $_row_grand = $this->_headGroups[_PV::_Row]->_getSubGroup(0);
      $_col_grand->_GetProperties($_l_gc, $_d_gc, $_w_gc, $_len_gc, $_o_gc);
      $_row_grand->_GetProperties($_l_gr, $_d_gr, $_w_gr, $_len_gr, $_o_gr);
      $_d_gc = ($_d_gc > 0) ? $_d_gc : 1;
      $_d_gr = ($_d_gr > 0) ? $_d_gr : 1;
      $_c_df = $this->_PVFields[_PV::_Data]->_length();
      $_n_df = ($_c_df > 1) ? 1 : 0;
      $_ff = "Filter fields: " . $this->_PVFields[_PV::_Filter]->_fieldString();
      $_df = "Data fields: " . $this->_PVFields[_PV::_Data]->_fieldString();
      $_cf = "Column fields: " . $this->_PVFields[_PV::_Column]->_fieldString(1);
      $_rf = "Row fields: " . $this->_PVFields[_PV::_Row]->_fieldString(1);
      #Init table
      $_classes = "table";
      $_classes .= ($this->HorizontalScrolling ? " horizontalScroll" : "") . ($this->VerticalScrolling ? " verticalScroll" : "");
      $_mytable = _HtmlProvider::_newTable()->_class(_PV::_mapString($_classes))
          ->_property($_classes, $_prt)
          ->_setIStyle(_PV::_mapArrayKeys($_styles));
      #Build rows of filter fields
      $_span = _HtmlProvider::_newSpan()->_class(_PV::$_mapClass["fieldItem"])->_Content($_ff);
      $_div = _HtmlProvider::_newDiv()->_id($this->_UniqueID . "_filterzoneEx")->_class(_PV::$_mapClass["filterZone"])->_AddChild($_span);
      $_td = _HtmlProvider::_newCol()->_class(_PV::$_mapClass["cell"])->_property("cell", $_prt)->_Colspan($_w_gc * $_c_df + $_d_gr)->_AddChild($_div);
      $_tr = _HtmlProvider::_newRow()->_AddChild($_td);
      $_th = _HtmlProvider::_newElement('thead');
      if ($_config["showFilterZone"])
        $_th->_AddChild($_tr);
      #Build rows of data fields and column fields
      $_span = _HtmlProvider::_newSpan()->_class(_PV::$_mapClass["fieldItem"])->_Content($_df);
      $_div = _HtmlProvider::_newDiv()->_id($this->_UniqueID . "_datazoneEx")->_class(_PV::$_mapClass["dataZone"])->_AddChild($_span);
      $_td = _HtmlProvider::_newCol()->_class(_PV::$_mapClass["cell"])->_property("cell", $_prt)->_Colspan($_d_gr)->_AddChild($_div);
      $_tr = _HtmlProvider::_newRow()->_AddChild($_td);
      $_span = _HtmlProvider::_newSpan()->_class(_PV::$_mapClass["fieldItem"])->_Content($_cf);
      $_div = _HtmlProvider::_newDiv()->_id($this->_UniqueID . "_columnzoneEx")->_class(_PV::$_mapClass["columnZone"])->_AddChild($_span);
      $_td = _HtmlProvider::_newCol()->_class(_PV::$_mapClass["cell"])->_property("cell", $_prt)->_Colspan($_w_gc * $_c_df)->_AddChild($_div);
      $_tr->_AddChild($_td);
      if ($_config["showDataZone"])
        $_th->_AddChild($_tr);
      #Build rows of row fields and header column groups
      $_trs = array();
      for ($_i = 0; $_i < $_d_gc + 1; $_i++)
        $_trs[$_i] = _HtmlProvider::_newRow();
      $_span = _HtmlProvider::_newSpan()->_class(_PV::$_mapClass["fieldItem"])->_Content($_rf);
      $_div = _HtmlProvider::_newDiv()->_id($this->_UniqueID . "_rowzoneEx")->_class(_PV::$_mapClass["rowZone"])->_AddChild($_span);
      $_td = _HtmlProvider::_newCol()->_Colspan($_d_gr)->_Rowspan($_d_gc + $_n_df)->_AddChild($_div)->_class(_PV::$_mapClass["cell"])->_property("cell", $_prt);
      $_trs[0]->_SetChild($_td, 0);
      foreach ($this->_arrGroups[_PV::_Column] as $_group) {
        $_group->_GetProperties($_l, $_d, $_w, $_len, $_o);
        $_w = ($_w > 1) ? $_w - 1 : $_w;
        $_o = $_w_gc - 1 - $_o;
        $_l_2 = ($_l < 0) ? 0 : $_l;
        $_td = _HtmlProvider::_newCol();
        if ($_l >= 0)
          $_td->_Colspan($_w * $_c_df)->_Rowspan(($_d > 0) ? 1 : ($_d_gc - $_l_2))->_Content($_group->Value)->_class(_PV::_mapString("columnHeader cell"))->_property("columnHeader cell", $_prt)->_id($_group->_UniqueID . "Ex");
        $_td_total = _HtmlProvider::_newCol();
        if ($_group->_hasExpandedSubGroup() || $_l < 0) {
          if ($_l < 0)
            $_content = $_group->Value;
          else
            $_content = $_group->_RenderHeaderTotal();
          $_td_total->_Colspan($_c_df)->_Rowspan($_d_gc - $_l_2)->_Content($_content)->_class(_PV::_mapString("columnHeader columnHeaderTotal totalColumn expandedCell cell"))->_property("columnHeader columnHeaderTotal totalColumn expandedCell cell", $_prt);
        }
        $_trs[$_l_2]->_SetChildGroup(array($_td, $_td_total), $_o + 1);
      }
      if ($_n_df > 0)
        for ($_c = 0; $_c < $_w_gc; $_c++) {
          $_c_group = $this->_arrGroups[_PV::_Column][$_c];
          $_classes = (!$_c_group->_hasExpandedSubGroup()) ? "" : "totalColumn";
          $_arr = array();
          for ($_d = 0; $_d < $_c_df; $_d++)
            array_push($_arr, _HtmlProvider::_newCol()->_class(_PV::_mapString($_classes . " cell dataDesc"))->_property($_classes . " cell dataDesc", $_prt)->_Content($this->_PVFields[_PV::_Data]->_field($_d)->RenderHeaderTotal($this->_arrGroups[_PV::_Column][$_c]->Value, $this->_arrGroups[_PV::_Column][$_c])));
          $_trs[$_d_gc]->_SetChildGroup($_arr, $_c + 1);
        }
      $_trs[$_d_gc]->_class(_PV::$_mapClass["dataDesc"])->_property("dataDesc", $_prt);
      for ($_i = 0; $_i < $_d_gc + 1; $_i++)
        $_trs[$_i]->_SortChildGroupAsc();
      $_th->_AddChildGroup($_trs);
      $_mytable->_AddChild($_th);
      #Build rows of header row groups and data cells
      $_trs = array();
      $_buff = new _OrderedBuffer();
      $_arr_index = array();
      if ($_start_row < 0)
        $_start_row = 0;
      if ($_row_count < 0)
        $_row_count = $_w_gr - 1;
      for ($_r = $_start_row; $_r < $_start_row + $_row_count; $_r++)
        array_push($_arr_index, $_r);
      if ($_row_count >= 0)
        array_push($_arr_index, $_w_gr - 1);
      foreach ($_arr_index as $_r) {
        $_trs[$_r] = _HtmlProvider::_newRow();
        $_group = $this->_arrGroups[_PV::_Row][$_r];
        $_group->_GetProperties($_l, $_d, $_w, $_len, $_o);
        $_w = ($_w > 1) ? $_w - 1 : $_w;
        $_o = $_w_gr - 1 - $_o;
        if ($_l >= 0 && !$_group->_hasExpandedSubGroup()) {
          $_td = _HtmlProvider::_newCol()->_Colspan(($_d > 0) ? 1 : ($_d_gr - $_l))->_Rowspan($_w)->_Content($_group->Value)->_class(_PV::_mapString("cell rowHeader"))->_property("cell rowHeader", $_prt)->_id($_group->_UniqueID . "Ex");
          $_trs[$_o]->_SetChild($_td, $_l);
        }
        if ($_group->_hasExpandedSubGroup() || $_l < 0) {
          if ($_l < 0)
            $_content = $_group->Value;
          else
            $_content = $_group->_RenderHeaderTotal();
          $_l = ($_l < 0) ? 0 : $_l;
          $_td_total = _HtmlProvider::_newCol()->_Colspan($_d_gr - $_l)->_Content($_content)->_class(_PV::_mapString("cell rowHeader rowHeaderTotal"))->_property("cell rowHeader rowHeaderTotal", $_prt);
          $_trs[$_o]->_SetChild($_td_total, $_l)->_class(_PV::$_mapClass["totalRow"])->_property("totalRow", $_prt);
        }
        $_tmp = $_group;
        if ($_tmp !== $_row_grand)
          while ($_tmp->_Parent !== $_row_grand) {
            $_tmp = $_tmp->_Parent;
            $_buff->_put($_w_gr - 1 - $_tmp->_GetOrder());
          }
      }
      while (!$_buff->_empty()) {
        $_group = $this->_arrGroups[_PV::_Row][$_buff->_get()];
        $_group->_GetProperties($_l, $_d, $_w, $_len, $_o);
        $_w = ($_w > 1) ? $_w - 1 : $_w;
        $_1st_child = $_group;
        while (reset($_1st_child->_SubGroups))
          $_1st_child = reset($_1st_child->_SubGroups);
        $_o = $_w_gr - 1 - $_1st_child->_GetOrder();
        $_gap = 0;
        if ($_o < $_start_row) {
          $_gap = $_start_row - $_o;
          $_o = $_start_row;
        }
        $_w = min($_w - $_gap, $_start_row + $_row_count - $_o);
        $_td = _HtmlProvider::_newCol()->_Rowspan($_w)->_Content($_group->Value)->_class(_PV::_mapString("cell expandedCell"))->_property("cell expandedCell", $_prt)->_id($_group->_UniqueID . "Ex");
        $_trs[$_o]->_SetChild($_td, $_l);
      }
      foreach ($_arr_index as $_r) {
        $_arr = array();
        for ($_c = 0; $_c < $_w_gc; $_c++) {
          $_c_group = $this->_arrGroups[_PV::_Column][$_c];
          $_r_group = $this->_arrGroups[_PV::_Row][$_r];
          $this->_GetKeys($_c_group, $_r_group, $_fields_key, $_groups_key, $_key);
          $_classes = "cell";
          if ($_c_group->_hasExpandedSubGroup()) {
            $_classes .= " totalColumn";
          }
          if ($_r_group->_hasExpandedSubGroup()) {
            $_classes .= " totalRow";
          }
          for ($_d = 0; $_d < $_c_df; $_d++) {
            $_datafield = $this->_PVFields[_PV::_Data]->_field($_d);
            $_data_alias = $_datafield->_sqlAlias;
            if (!isset($this->_Values[$_fields_key][$_groups_key][$_key][$_data_alias])) {
              $_data = $_datafield->DisplayFormat($this->EmptyValue);
              $_classes .= " emptyDataCell";
            } else {
              $_data = $_datafield->DisplayFormat($this->_Values[$_fields_key][$_groups_key][$_key][$_data_alias]);
              $_classes .= " dataCell";
            }
            $_td = _HtmlProvider::_newCol()->_Content($_data)->_class(_PV::_mapString($_classes))->_property($_classes, $_prt);
            array_push($_arr, $_td);
          }
        }
        $_trs[$_r]->_SetChildGroup($_arr, $_d_gr);
      }
      foreach ($_arr_index as $_r)
        $_trs[$_r]->_SortChildGroupAsc();
      $_mytable->_AddChildGroup($_trs);
      return $_mytable;
    }
    function ExportToHTML() {
      $_ExSt = $this->ExportSettings;
      $_config = $_ExSt->_getConfig();
      $_html_template = $_config["template"];
      ob_end_clean();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"" . $_config["fileName"] . ".html\"");
      header("Content-Transfer-Encoding: binary");
      $_start_row = -1;
      $_row_count = -1;
      if (isset($this->Pager) && !$_ExSt->IgnorePaging) {
        $_start_row = $this->Pager->PageIndex * $this->Pager->PageSize;
        $_row_count = ($_start_row + $this->Pager->PageSize < $this->Pager->_TotalRows) ? $this->Pager->PageSize : ($this->Pager->_TotalRows - $_start_row);
      }
      $_mytable = $this->_GetHtmlTable($_start_row, $_row_count);
      $_table_html = $_mytable->_getIStyle() . $_mytable->_GetChangedHtml($_ExSt->_getChanges(), $_config["caseSensitive"]);
      $_output = _replace("{KoolPivotTable}", $_table_html, $_html_template);
      echo $_output;
      exit();
    }
    function ExportToPDF() {
      error_reporting(0);
      if (!class_exists("TCPDF", false)) {
        $_path = dirname(dirname(__FILE__));
        require_once $_path . "/library/tcpdf/config/lang/eng.php";
        require_once $_path . "/library/tcpdf/tcpdf.php";
      }
      $_ExSt = $this->ExportSettings;
      $_config = $_ExSt->_getConfig();
      $_html_template = $_config["template"];
      $_pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, TRUE, $this->CharSet, FALSE);
      $_font = $_config["pdf"]["font"];
      $_pdf->SetFont($_font["family"], $_font["style"], $_font["size"]);
      $_pdf->SetAutoPageBreak(TRUE);
      $_pdf->AddPage($_config["pdf"]["pageOrientation"], $_config["pdf"]["pageDimension"]);
      $_start_row = -1;
      $_row_count = -1;
      if (isset($this->Pager) && !$_ExSt->IgnorePaging) {
        $_start_row = $this->Pager->PageIndex * $this->Pager->PageSize;
        $_row_count = ($_start_row + $this->Pager->PageSize < $this->Pager->_TotalRows) ? $this->Pager->PageSize : ($this->Pager->_TotalRows - $_start_row);
      }
      $_mytable = $this->_GetHtmlTable($_start_row, $_row_count);
      $_table_html = $_mytable->_getIStyle() . $_mytable->_GetChangedHtml($_ExSt->_getChanges(), $_config["caseSensitive"]);
      $_output = _replace("{KoolPivotTable}", $_table_html, $_html_template);
      ob_end_clean();
      $_pdf->writeHTML($_output, TRUE, FALSE, FALSE, FALSE, '');
      $_pdf->Output($_config["fileName"] . ".pdf", "D");
      exit();
    }
    function ExportToExcel() {
      error_reporting(0);
      if (!class_exists("PHPExcel", false)) {
        $_path = dirname(dirname(__FILE__));
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel.php';
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel/Cell.php';
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel/IOFactory.php';
      }
      /** Include PHPExcel */
      $_ExSt = $this->ExportSettings;
      $_config = $_ExSt->_getConfig();
      $_workbook = new PHPExcel();
      $_workbook->setActiveSheetIndex(0);
      $_start_row = -1;
      $_row_count = -1;
      if (isset($this->Pager) && !$_ExSt->IgnorePaging) {
        $_start_row = $this->Pager->PageIndex * $this->Pager->PageSize;
        $_row_count = ($_start_row + $this->Pager->PageSize < $this->Pager->_TotalRows) ? $this->Pager->PageSize : ($this->Pager->_TotalRows - $_start_row);
      }
      $_mytable = $this->_GetHtmlTable($_start_row, $_row_count);
      $_arr = $_mytable->_GetArrayWithBlank();
      $_col_pos = 0;
      $_row_pos = 1;
      foreach ($_arr as $_cols) {
        foreach ($_cols as $_col) {
          $_text = $_col->_GetNonTagContent($_ExSt->_getChanges(), $_config["caseSensitive"]);
          $_text = $_col->_GetNonTagContent();
          if ($_text != "blank") {
            $_cs = $_col->_GetColspan();
            $_rs = $_col->_GetRowspan();
            $_start_cell = PHPExcel_Cell::stringFromColumnIndex($_col_pos) . $_row_pos;
            $_end_cell = PHPExcel_Cell::stringFromColumnIndex($_col_pos + $_cs - 1) . ($_row_pos + $_rs - 1);
            $_workbook->getActiveSheet()->setCellValue($_start_cell, $_text);
            if ($_start_cell != $_end_cell) {
              $_workbook->getActiveSheet()->mergeCells($_start_cell . ":" . $_end_cell);
              $_workbook->getActiveSheet()->getStyle($_start_cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
          }
          $_col_pos += 1;
        }
        $_row_pos++;
        $_col_pos = 0;
      }
      $_workbook->getActiveSheet()->setTitle($_config["fileName"]);
      $_workbook->setActiveSheetIndex(0);
      ob_end_clean();
      header("Content-Type: application/octet-stream");
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $_config["fileName"] . '.xls"');
      header('Cache-Control: max-age=0');
      header('Cache-Control: max-age=1');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
      header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header('Pragma: public'); // HTTP/1.0
      $_objWriter = PHPExcel_IOFactory::createWriter($_workbook, 'Excel5');
      $_objWriter->save('php://output');
      exit;
    }
    function ExportToWord() {
      error_reporting(0);
      $_ExSt = $this->ExportSettings;
      $_config = $_ExSt->_getConfig();
      $_html_template = $_config["template"];
      ob_end_clean();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Type: application/msword");
      header("Content-Disposition: attachment; filename=\"" . $_config["fileName"] . ".doc\"");
      header("Content-Transfer-Encoding: binary");
      $_start_row = -1;
      $_row_count = -1;
      if (isset($this->Pager) && !$_ExSt->IgnorePaging) {
        $_start_row = $this->Pager->PageIndex * $this->Pager->PageSize;
        $_row_count = ($_start_row + $this->Pager->PageSize < $this->Pager->_TotalRows) ? $this->Pager->PageSize : ($this->Pager->_TotalRows - $_start_row);
      }
      $_mytable = $this->_GetHTMLTable($_start_row, $_row_count);
      $_table_html = $_mytable->_getIStyle() . $_mytable->_GetChangedHtml($_ExSt->_getChanges(), $_config["caseSensitive"], "'");
      $_output = _replace("{KoolPivotTable}", $_table_html, $_html_template);
      echo $_output;
      exit();
    }
    function _GetKeys($_c_group, $_r_group, &$_fields_key, &$_groups_key, &$_key) {
      $_groups_key = $_c_group->_Parent->_UniqueID . $_r_group->_Parent->_UniqueID;
      $_key = $_c_group->_Key . $_r_group->_Key;
      $_fields_key = $_c_group->_Field->_key . $_r_group->_Field->_key;
    }
    function _startGroup($_PV_index, $_group) {
      $_group->_init($this);
      $_group->_LoadViewState();
      if ($this->_sortState == _Key::_initSort &&
          $_group->Value == $this->_initSortedGroup[_Key::_name]) {
        $_group->_SetSort($this->_initSortedGroup[_Key::_Direction]);
        $_group->_SetPivotSort();
      }
      $_group->_Process($this->_Command);
      $this->_idToGroup[$_PV_index][$_group->_UniqueID] = $_group;
      return $this;
    }
    /**
     * Add child groups for Column Header and Row Header groups starting from Grand groups.
     *
     * @access public
     * @param constant $_PV_index Indicate which kind of group to add, row or column group
     * @return object $_header_group Row or Column Header of KoolPivotTable
     */
    function _BuildHeaderGroup($_PV_index, &$_CachedHeaders) {
      $_ds = $this->DataSource;
      $_fieldList = $this->_PVFields[$_PV_index];
      $_grandField = $_fieldList->_field(0);
      $_value = $this->Localization->_Commands[_Key::_Grand_Total];
      $_grandGroup = _PivotGroup::_newGrandGroup($_value, $_grandField);
      $_headGroup = $this->_headGroups[$_PV_index];
      $_headGroup->_AddChild($_grandGroup);
      $this->_startGroup($_PV_index, $_grandGroup);
      $_buff = new _SimpleStack();
      $_buff->_put($_grandGroup);
      array_push($_grandField->_ExpandedParentGroups, $_headGroup);
      while (!$_buff->_empty()) {
        $_group = $_buff->_get();
        $_l = $_group->_GetLevel() + 2;
        if ($_l < $_fieldList->_length()) {
          $_field = $_fieldList->_field($_l);
          $_nextField = $_fieldList->_field($_l + 1);
          $_prevField = $_fieldList->_field($_l - 1);
          array_push($_field->_ExpandedParentGroups, $_group);
          $_field->_GetProperties($_FN, $_FId, $_FExp, $_FA, $_FOP);
          $_condition = $_group->_SQLCondition;
          $_nestedCondition = $_group->_nestedSQLCondition;
          $_subgroups_info = array();
          $_new_info = FALSE;
          if (!empty($_CachedHeaders[$_field->_key][$_group->_UniqueID][_Key::_SubGroupIds]) && !$this->_RecalHeader) {
            $_cache = $_CachedHeaders[$_field->_key];
            $_subgroup_ids = $_cache[$_group->_UniqueID][_Key::_SubGroupIds];
            foreach ($_subgroup_ids as $_subgroup_id) {
              $_cache2 = $_cache[$_subgroup_id];
              array_push($_subgroups_info, array(_Key::_value => urldecode($_cache2[_Key::_value]), _Key::_Expand => $_cache2[_Key::_Expand], _Key::_Sort => $_cache2[_Key::_Sort], _Key::_SortValue => $_cache2[_Key::_SortValue], _Key::_SQLCondition => $_cache2[_Key::_SQLCondition], _Key::_nestedSQLCondition => $_cache2[_Key::_nestedSQLCondition], _Key::_sqlValues => $_cache2[_Key::_sqlValues]));
            }
          } else if ($_field->_sqlExpression == $_prevField->_sqlExpression) {
            foreach ($_group->_sqlValues as $_value)
              array_push($_subgroups_info, array(_Key::_value => $_value, _Key::_SortValue => $_value));
            $_new_info = TRUE;
          } else {
            $_selectFields = array($_field->_select);
            $_allConditions = array(_Key::_allConditions => _SQLCondition::_newCondition($_condition), _Key::_allNestedConditions => _SQLCondition::_newCondition($_nestedCondition));
            $_query = $_ds->_distinct(TRUE)->_buildSelectQuery($_selectFields, $_allConditions, NULL);
            $_rows = $_ds->_queryAll($_query);
            foreach ($_rows as $_row)
              array_push($_subgroups_info, array(_Key::_value => $_row[$_FA], _Key::_SortValue => $_row[$_FA]));
            $_new_info = TRUE;
          }
          foreach ($_subgroups_info as $_sub_i) {
            $_value = $_sub_i[_Key::_value];
            if ($_new_info)
              $_value = $_field->_getMappedValue($_value);
            if ($_field->_DoFiltering($_value)) {
              $_subGroup = $_group->_getSubGroup($_value);
              if (empty($_subGroup)) {
                $_subGroup = _PivotGroup::_NewGroup($_value, $_field);
                $_group->_AddChild($_subGroup);
                if (isset($_sub_i[_Key::_Expand]))
                  $_subGroup->_SetExpand($_sub_i[_Key::_Expand]);
                if (isset($_sub_i[_Key::_Sort]))
                  $_subGroup->_SetSort($_sub_i[_Key::_Sort]);
                $_subGroup->_SetSortValue($_sub_i[_Key::_SortValue]);
                $this->_startGroup($_PV_index, $_subGroup);
                $_subGroup->_SQLCondition = _SQLCondition::_newCondition();
                $_subGroup->_nestedSQLCondition = _SQLCondition::_newCondition();
                if ($_l < $_fieldList->_length() - 1)
                  $_subGroup->_Expandable = TRUE;
                if ($_subGroup->Expand || $_field->_sqlExpression == $_nextField->_sqlExpression
                )
                  $_buff->_put($_subGroup);
              }
              if ($_new_info) {
                $_field_value = $_ds->_StrToSQLStrConstant($_ds->_HtmlToSqlStr($_sub_i[_Key::_value]));
                $_subGroup->_SQLCondition->_AddCondition($_FExp . _SQL::_Equal . $_field_value, _SQL::_OR);
                $_subGroup->_nestedSQLCondition->_AddCondition($_FN . _SQL::_Equal . $_field_value, _SQL::_OR);
                array_push($_subGroup->_sqlValues, $_sub_i[_Key::_value]);
              } else {
                $_subGroup->_SQLCondition = $_sub_i[_Key::_SQLCondition];
                $_subGroup->_nestedSQLCondition = $_sub_i[_Key::_nestedSQLCondition];
                $_subGroup->_sqlValues = $_sub_i[_Key::_sqlValues];
              }
            }
          }
          foreach ($_group->_SubGroups as $_subGroup) {
            if ($_subGroup->_SQLCondition instanceof _SQLCondition)
              $_subGroup->_SQLCondition = $_subGroup->_SQLCondition->_AddCondition($_condition)->_ToSQLExpression();
            if ($_subGroup->_nestedSQLCondition instanceof _SQLCondition)
              $_subGroup->_nestedSQLCondition = $_subGroup->_nestedSQLCondition->_AddCondition($_nestedCondition)->_ToSQLExpression();
          }
          if ($this->AllowCaching) {
            $_CachedHeaders[$_field->_key][$_group->_UniqueID] = $_group->_ExportProperties();
            foreach ($_group->_SubGroups as $_subGroup) {
              $_CachedHeaders[$_field->_key][$_subGroup->_UniqueID] = $_subGroup->_ExportProperties();
            }
          }
        }
      }
      return $_headGroup;
    }
    function Process() {
      $this->_Init();
      $this->_LoadViewState();
      if (!isset($this->_ViewState->_Data[$this->_UniqueID]))
        $this->_Rebind = TRUE;
      $_command = $this->_Command;
      {
        if (isset($_command->_Commands[$this->_UniqueID])) {
          $_c = $_command->_Commands[$this->_UniqueID];
          $_com = $_c[_Key::_Command];
          $_arg = $_c[_Key::_Args];
          switch ($_com) {
            case _Key::_MoveField:
              if ($this->EventHandler->OnBeforeFieldMove($this, array()) == TRUE) {
                $_old_PV_index = _PV::$_index[strtolower($_arg[_Key::_From])];
                $_new_PV_index = _PV::$_index[strtolower($_arg[_Key::_To])];
                $_field = $this->_PVFields[$_old_PV_index]->_removeField($_arg[_Key::_FromPosition]);
                $this->_PVFields[$_new_PV_index]->_addField($_field, $_arg[_Key::_ToPosition]);
                $this->EventHandler->OnFieldMove($this, array());
              }
              break;
            case _Key::_Refresh:
              if ($this->EventHandler->OnBeforeRefresh($this, array()) == TRUE) {
                $this->_Rebind = TRUE;
                $this->EventHandler->OnRefresh($this, array());
              }
              break;
            case _Key::_ChangeSortData:
              if ($this->EventHandler->OnBeforeChangeSortData($this, array()) == TRUE) {
                if ($_arg[_Key::_Check] == _Key::_checked)
                  $this->_DataFieldToSort = $this->_PVFields[_PV::_Data]->_field(0);
                $this->EventHandler->OnChangeSortData($this, array());
              }
              break;
          }
        }
      }
      $_ds = $this->DataSource;
      foreach (_PV::$_index as $_PV_index) {
        $this->_PVFields[$_PV_index]->_buildFieldKey()->_buildSelect();
        $_fields[$_PV_index] = $this->_PVFields[$_PV_index]->_fieldArray();
      }
      $_c_fields = $_fields[_PV::_Column];
      $_r_fields = $_fields[_PV::_Row];
      $_d_fields = $_fields[_PV::_Data];
      #Get cache and recalculatation conditions
      {
        $_tabledata = null;
        $this->_RawValues = array();
        $this->_Values = array();
        if ($this->AllowCaching) {
          $_cache = new _PivotCache($this->CacheFolder, $this->CacheTime);
          $_cache->_UniqueID = $this->_CacheID;
          if (!$this->_Rebind) {
            $_tabledata = $_cache->_Load(_Key::_tabledata);
            if (isset($_tabledata[_Key::_cacheFilters]) && (!$this->_RecalFilter)) {
              $_filter_items = $_tabledata[_Key::_cacheFilters];
              foreach (_PV::$_index as $_PV_index)
                if ($_PV_index != _PV::_Data)
                  foreach ($_fields[$_PV_index] as $_field)
                    $_field->_setItems($_filter_items[$_field->_UniqueID]);
            } else
              $this->_RecalFilter = TRUE;
            if (!$this->_RecalFilter) {
              if (isset($_tabledata[_Key::_cacheValues]))
                $this->_RawValues = $_tabledata[_Key::_cacheValues];
            }
          }
        } else
          $this->_Rebind = TRUE;
      }
      #Process field commands
      foreach (_PV::$_index as $_PV_index)
        foreach ($_fields[$_PV_index] as $_field)
          $_field->_Process($_command);
      #Build filters
      {
        if ($this->_Rebind) {
          $_ds->CharSet = $this->CharSet;
          $this->_RecalFilter = TRUE;
        }
        if ($this->_RecalFilter) {
          if (isset($_tabledata[_Key::_cacheHeaders]))
            $_tabledata[_Key::_cacheHeaders] = NULL;
          $this->_RawValues = NULL;
        }
      }
      $_end_time2 = round(microtime(TRUE) * 1000);
      #Build header groups
      {
        if (!isset($_tabledata[_Key::_cacheHeaders]))
          $_tabledata[_Key::_cacheHeaders] = array();
        foreach (array(_PV::_Column, _PV::_Row) as $_PV_index)
          $this->_BuildHeaderGroup($_PV_index, $_tabledata[_Key::_cacheHeaders]);
      }
      foreach (array(_PV::_Column, _PV::_Row) as $_PV_index) {
        $_grandGroups[$_PV_index] = $this->_headGroups[$_PV_index]->_getSubGroup(0);
      }
      $_c_grand = $_grandGroups[_PV::_Column];
      $_r_grand = $_grandGroups[_PV::_Row];
      #Retrieve data
      {
        $_selectFils = array();
        $_selectData = array();
        foreach (_PV::$_index as $_PV_index)
          foreach ($_fields[$_PV_index] as $_field)
            if ($_field->_hasFilter())
              array_push($_selectFils, $_field->_select);
        foreach ($_d_fields as $_field)
          array_push($_selectData, $_field->_select);
        foreach ($_c_fields as $_c => $_c_field)
          foreach ($_r_fields as $_r => $_r_field) {
            $_fields_key = $_c_field->_key . $_r_field->_key;
            $_selectCols = array();
            $_selectRows = array();
            for ($_i = 1; $_i <= $_c; $_i++)
              array_push($_selectCols, $_c_fields[$_i]->_select);
            for ($_i = 1; $_i <= $_r; $_i++)
              array_push($_selectRows, $_r_fields[$_i]->_select);
            $_groups = array();
            $_aliases = array();
            foreach (array($_selectFils, $_selectCols, $_selectRows) as $_selects)
              foreach ($_selects as $_select)
                if (!in_array($_select[_Key::_alias], $_aliases)) {
                  array_push($_aliases, $_select[_Key::_alias]);
                  array_push($_groups, $_select);
                }
            $_selectAll = array_merge($_groups, $_selectData);
            $_need_query = FALSE;
            $_all_conditions = _SQLCondition::_newCondition();
            $_allNestedCondition = _SQLCondition::_newCondition();
            foreach ($_c_field->_ExpandedParentGroups as $_c_p_group)
              foreach ($_r_field->_ExpandedParentGroups as $_r_p_group) {
                $_groups_key = $_c_p_group->_UniqueID . $_r_p_group->_UniqueID;
                if (!isset($this->_RawValues[$_fields_key][$_groups_key])) {
                  $_all_conditions->_AddCondition(_SQLCondition::_newCondition($_c_p_group->_SQLCondition)->_AddCondition($_r_p_group->_SQLCondition), _SQL::_OR);
                  $_allNestedCondition->_AddCondition(_SQLCondition::_newCondition($_c_p_group->_nestedSQLCondition)->_AddCondition($_r_p_group->_nestedSQLCondition), _SQL::_OR);
                  $_need_query = TRUE;
                }
              }
            if ($_need_query) {
              $_conditions = array(_Key::_allConditions => $_all_conditions, _Key::_allNestedConditions => $_allNestedCondition);
              $_query = $_ds->_distinct(FALSE)->_buildSelectQuery($_selectAll, $_conditions, $_groups);
              $_offset = 0;
              $_querySize = $_ds->getQuerySize();
              while (TRUE) {
                if ($_querySize == 0) {
                  $_rows = $_ds->_queryALL($_query);
                  $_querySize = PHP_INT_MAX;
                } else if ($_querySize > 0) {
                  $_rows = $_ds->_queryLimit($_query, $_offset, $_querySize);
                  $_offset += $_querySize;
                }
                foreach ($_rows as $_row) {
                  $_filter = true;
                  foreach ($_selectFils as $_selectFil) {
                    $_f = $_selectFil[_Key::_pivotField];
                    $_value = $_f->_getMappedValue($_row[$_selectFil[_Key::_alias]]);
                    if (! $_f->_DoFiltering($_value)) {
                      $_filter = FALSE;
                      break;
                    }
                  }
                  if ($_filter) {
                    $_c_group = $_c_grand;
                    foreach ($_selectCols as $_selectCol) {
                      $_f = $_selectCol[_Key::_pivotField];
                      $_value = $_f->_getMappedValue($_row[$_selectCol[_Key::_alias]]);
                      $_c_group = $_c_group->_getSubGroup($_value);
                    }
                    $_r_group = $_r_grand;
                    foreach ($_selectRows as $_selectRow) {
                      $_f = $_selectRow[_Key::_pivotField];
                      $_value = $_f->_getMappedValue($_row[$_selectRow[_Key::_alias]]);
                      $_r_group = $_r_group->_getSubGroup($_value);
                    }
                    if (isset($_c_group) && isset($_r_group)) {
                      $_c_groups = array($_c_group);
                      $_i = $_c + 1;
                      while (isset($_c_fields[$_i]) && $_c_fields[$_i]->_sqlExpression == $_c_field->_sqlExpression) {
                        $_select = end($_selectRows);
                        $_value = $_c_fields[$_i]->_getMappedValue($_row[$_select[_Key::_alias]]);
                        $_c_group = $_c_group->_getSubGroup($_value);
                        array_push($_c_groups, $_c_group);
                        $_i++;
                      }
                      $_r_groups = array($_r_group);
                      $_i = $_r + 1;
                      while (isset($_r_fields[$_i]) && $_r_fields[$_i]->_sqlExpression == $_r_field->_sqlExpression) {
                        $_select = end($_selectRows);
                        $_value = $_r_fields[$_i]->_getMappedValue($_row[$_select[_Key::_alias]]);
                        $_r_group = $_r_group->_getSubGroup($_value);
                        array_push($_r_groups, $_r_group);
                        $_i++;
                      }
                      foreach ($_c_groups as $_c_group)
                        foreach ($_r_groups as $_r_group) {
                          foreach ($_selectData as $_selectDatum) {
                            $_f = $_selectDatum[_Key::_pivotField];
                            $_value = $_f->_getMappedValue($_row[$_selectDatum[_Key::_alias]]);
                            $_field = $_selectDatum[_Key::_pivotField];
                            if (isset($_field->RelevantField)) {
                              if (strtolower(get_class($_field)) === 'pivotstringfield') 
                                $this->_initValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], '');
                              else 
                                $this->_initValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], 0);
                              if ($_field->RelevantField === $_r_group->_Field->FieldName) {
                                if (strtolower(get_class($_field)) === 'pivotstringfield') {
                                  $this->_setValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], $_value);
                                }
                                else
                                  $this->_addValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], $_value);
                              }
                              else {
                                $this->_setValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], 'NA');
                              }
                            }
                            else {
                              $this->_initValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], 0);
                              $this->_addValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], $_value);
                            }
                          }
                        }
                    }
                  }
                }
                if (count($_rows) < $_querySize)
                  break;
              }
            }
          }
        $this->_Values = $this->_RawValues;
        foreach ($_d_fields as $_datafield)
          $this->_Values = $_datafield->_PostDataProcess($this->_Values);
      }
      foreach (array(_PV::_Column, _PV::_Row) as $_PV_index)
        $this->_arrGroups[$_PV_index] = $_grandGroups[$_PV_index]->_GetGroupInSequence();
      #Sort fields and groups
      if ($this->AllowSorting || $this->AllowSortingData) {
        $_PV_changed_indexes = array(_PV::_Row => FALSE, _PV::_Column => FALSE);
        $_PV_sorted_indexes = array(_PV::_Row => FALSE, _PV::_Column => FALSE);
        if (isset($this->_ResetFieldType)) {
          $this->_ResetHeaderSortValue($this->_ResetFieldType);
          $_PV_changed_indexes[$this->_ResetFieldType] = TRUE;
        }
        if (!empty($this->_GroupsToSort))
          foreach ($this->_GroupsToSort as $_PV_index => $_group_to_sort)
            if (!empty($_group_to_sort)) {
              $_opposite_index = _PV::_Inverse($_PV_index);
              if (isset($this->_idToGroup[$_PV_index][$_group_to_sort[_Key::_UniqueID]])) {
                $_group = $this->_idToGroup[$_PV_index][$_group_to_sort[_Key::_UniqueID]];
                $this->_SetGroupsSortValue($_opposite_index, $_group);
                $_PV_changed_indexes[$_opposite_index] = TRUE;
              }
              $_PV_sorted_indexes[$_opposite_index] = TRUE;
              if (method_exists($_grandGroups[$_opposite_index], '_DoSortingGroups'))
                $_grandGroups[$_opposite_index]->_DoSortingGroups($_group_to_sort[_Key::_Direction]);
            }
        foreach ($_PV_sorted_indexes as $_PV_index => $_changed)
          if ($_changed == FALSE) {
            foreach ($_fields[$_PV_index] as $_field)
              $_field->_SortStatus = TRUE;
            $_grandGroups[$_PV_index]->_DoSortingFields();
          }
        foreach ($_c_fields as $_c => $_c_field)
          $_c_field->_filterData();
        foreach ($_r_fields as $_r => $_r_field)
          $_r_field->_filterData();
      }
      foreach (array(_PV::_Column, _PV::_Row) as $_PV_index)
        $this->_arrGroups[$_PV_index] = $_grandGroups[$_PV_index]->_GetGroupInSequence();
      foreach ($this->_arrGroups[_PV::_Column] as $_c_group)
        foreach ($this->_arrGroups[_PV::_Row] as $_r_group) {
          foreach ($_selectData as $_selectDatum) {
            if ($_c_group->_hasExpandedSubGroup()) {
              $_subGroups = $_c_group->_getSubGroups();
              foreach ($_subGroups as $_subGroup) {
                $_value = $this->_getValue($_subGroup, $_r_group, $_selectDatum[_Key::_alias]);
                $_field = $_selectDatum[_Key::_pivotField];
                if (isset($_field->RelevantField)) {
                    $this->_setValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], 'NA');
                }
              }
            } else if ($_r_group->_hasExpandedSubGroup()) {
              $_subGroups = $_r_group->_getSubGroups();
              foreach ($_subGroups as $_subGroup) {
                $_value = $this->_getValue($_c_group, $_subGroup, $_selectDatum[_Key::_alias]);
                $_field = $_selectDatum[_Key::_pivotField];
                if (isset($_field->RelevantField)) {
                    $this->_setValue($_c_group, $_r_group, $_selectDatum[_Key::_alias], 'NA');
                }
              }
            }
          }
          $this->_Values = $this->_RawValues;
          foreach ($_d_fields as $_datafield)
            $this->_Values = $_datafield->_PostDataProcess($this->_Values);
        }
      #Cache results for later uses
      if ($this->AllowCaching) {
        $_filter_items = array();
        foreach (_PV::$_index as $_PV_index)
          if ($_PV_index != _PV::_Data)
            foreach ($_fields[$_PV_index] as $_field)
              $_filter_items[$_field->_UniqueID] = $_field->_getItems();
        $_tabledata[_Key::_cacheFilters] = $_filter_items;
        foreach ($_c_fields as $_c_field)
          foreach ($_r_fields as $_r_field) {
            $_fields_key = $_c_field->_key . $_r_field->_key;
            if (isset($this->_RawValues[$_fields_key]))
              $_tabledata[_Key::_cacheValues][$_fields_key] = $this->_RawValues[$_fields_key];
          }
        $_cache->_Save(_Key::_tabledata, $_tabledata);
      }
      #Paging results 
      if (isset($this->Pager)) {
        $this->Pager->_TotalRows = $_grandGroups[_PV::_Row]->_GetWidth() - 1;
        $this->Pager->_Process($_command);
      }
      $this->_SaveViewState();
    }
    function _initValue($_c_group, $_r_group, $_dataAlias, $_value) {
      $this->_GetKeys($_c_group, $_r_group, $_fields_key, $_groups_key, $_key);
      if (!isset($this->_RawValues[$_fields_key][$_groups_key][$_key][$_dataAlias]))
        $this->_RawValues[$_fields_key][$_groups_key][$_key][$_dataAlias] = $_value;
      return $this;
    }
    function _setValue($_c_group, $_r_group, $_dataAlias, $_value) {
      $this->_GetKeys($_c_group, $_r_group, $_fields_key, $_groups_key, $_key);
      $this->_RawValues[$_fields_key][$_groups_key][$_key][$_dataAlias] = $_value;
      return $this;
    }
    function _addValue($_c_group, $_r_group, $_dataAlias, $_value) {
      $this->_GetKeys($_c_group, $_r_group, $_fields_key, $_groups_key, $_key);
        $this->_RawValues[$_fields_key][$_groups_key][$_key][$_dataAlias] += $_value;
      return $this;
    }
    function _getValue($_c_group, $_r_group, $_dataAlias) {
      $this->_GetKeys($_c_group, $_r_group, $_fields_key, $_groups_key, $_key);
      if (isset($this->_RawValues[$_fields_key][$_groups_key][$_key][$_dataAlias]))
        return $this->_RawValues[$_fields_key][$_groups_key][$_key][$_dataAlias];
      else
        return 0;
    }
    function _getValues($_r, $_c, $_d) {
      $_arr = array();
      $_r_groups = $this->_getGroups($_r[0], $_r[1]);
      $_c_groups = $this->_getGroups($_c[0], $_c[1]);
      $_df = $this->_getField($_d, _PV::_Data);
      $_s = $_df->_select;
      foreach ($_c_groups as $_c_group) {
        $_arr2 = array();
        foreach ($_r_groups as $_r_group) {
          $_value = $this->_getValue($_c_group, $_r_group, $_s[_Key::_alias]);
          array_push($_arr2, $_value);
        }
        array_push($_arr, $_arr2);
      }
      return $_arr;
    }
    function getValues($_r, $_c, $_d) {
      $_arr = array();
      $_r_groups = $this->_getGroups(_PV::_Row, $_r);
      $_c_groups = $this->_getGroups(_PV::_Column, $_c);
      $_df = $this->_getField($_d, _PV::_Data);
      $_s = $_df->_select;
      foreach ($_c_groups as $_c_group) {
        $_arr2 = array();
        foreach ($_r_groups as $_r_group) {
          $_value = $this->_getValue($_c_group, $_r_group, $_s[_Key::_alias]);
          array_push($_arr2, $_value);
        }
        array_push($_arr, $_arr2);
      }
      return $_arr;
    }
    function _ResetHeaderSortValue($_PV_Index) {
      foreach ($this->_arrGroups[$_PV_Index] as $_group)
        $_group->_SetSortValue($_group->Value);
    }
    function _SetGroupsSortValue($_PV_Index, $_group_to_sort) {
      foreach ($this->_arrGroups[$_PV_Index] as $_group) {
        if ($_PV_Index == _PV::_Column) {
          $_c_group = $_group;
          $_r_group = $_group_to_sort;
        } else if ($_PV_Index == _PV::_Row) {
          $_c_group = $_group_to_sort;
          $_r_group = $_group;
        } else
          return FALSE;
        $this->_GetKeys($_c_group, $_r_group, $_fields_key, $_groups_key, $_key);
        if (empty($this->_DataFieldToSort))
          $this->_DataFieldToSort = $this->_PVFields[_PV::_Data]->_field(0);
        $_data_alias = $this->_DataFieldToSort->_sqlAlias;
        if (isset($this->_Values[$_fields_key][$_groups_key][$_key][$_data_alias]))
          $_group->_SortValue = $this->_Values[$_fields_key][$_groups_key][$_key][$_data_alias];
        else
          $_group->_SortValue = 0;
      }
    }
    function _RenderFilterZone() {
      $_tpl_main = "<div id='{id}_filterzone' class='kptFilterZoneDiv'>{items}</div>";
      $_tpl_desc = "<span class='kptDesc'>{text}</span>";
      $_items = "";
      foreach ($this->_PVFields[_PV::_Filter]->_fieldArray() as $_field) {
        $_items.=$_field->_RenderField();
      }
      if ($_items != "") {
        $_main = _replace("{items}", $_items, $_tpl_main);
      } else {
        $_desc = _replace("{text}", $this->Localization->_Messages[_Key::_FilterZoneEmptyMessage], $_tpl_desc);
        $_main = _replace("{items}", $_desc, $_tpl_main);
      }
      $_main = _replace("{id}", $this->_UniqueID, $_main);
      return $_main;
    }
    function _RenderColumnZone() {
      $_tpl_main = "<div id='{id}_columnzone' class='kptColumnZoneDiv'>{items}</div>";
      $_tpl_desc = "<span class='kptDesc'>{text}</span>";
      $_items = "";
      foreach ($this->_PVFields[_PV::_Column]->_fieldArray(1) as $_field) {
        $_items.=$_field->_RenderField();
      }
      if ($_items != "") {
        $_main = _replace("{items}", $_items, $_tpl_main);
      } else {
        $_desc = _replace("{text}", $this->Localization->_Messages[_Key::_ColumnZoneEmptyMessage], $_tpl_desc);
        $_main = _replace("{items}", $_desc, $_tpl_main);
      }
      $_main = _replace("{id}", $this->_UniqueID, $_main);
      return $_main;
    }
    function _RenderRowZone() {
      $_tpl_main = "<div id='{id}_rowzone' class='kptRowZoneDiv'><table cellspacing='0'style='border:0px;'><tbody>{items}</tbody></table></div>";
      $_tpl_desc = "<span class='kptDesc'>{text}</span>";
      $_tpl_item = "<td>{field}</td>";
      $_items = "";
      foreach ($this->_PVFields[_PV::_Row]->_fieldArray(1) as $_field) {
        $_item = _replace("{field}", $_field->_RenderField(), $_tpl_item);
        $_items.=$_item;
      }
      if ($_items != "") {
        $_main = _replace("{items}", $_items, $_tpl_main);
      } else {
        $_desc = _replace("{text}", $this->Localization->_Messages[_Key::_RowZoneEmptyMessage], $_tpl_desc);
        $_main = _replace("{items}", $_desc, $_tpl_main);
      }
      $_main = _replace("{id}", $this->_UniqueID, $_main);
      return $_main;
    }
    function _RenderDataZone() {
      $_tpl_main = "<div id='{id}_datazone' class='kptDataZoneDiv'>{items}</div>";
      $_tpl_desc = "<span class='kptDesc'>{text}</span>";
      $_items = "";
      foreach ($this->_PVFields[_PV::_Data]->_fieldArray() as $_field) {
        $_items.=$_field->_RenderField(_PV::_Data);
      }
      if ($_items != "") {
        $_main = _replace("{items}", $_items, $_tpl_main);
      } else {
        $_desc = _replace("{text}", $this->Localization->_Messages[_Key::_DataZoneEmptyMessage], $_tpl_desc);
        $_main = _replace("{items}", $_desc, $_tpl_main);
      }
      $_main = _replace("{id}", $this->_UniqueID, $_main);
      return $_main;
    }
    function _RenderColumnHeaderZone() {
      $_tpl_main = "<div class='kptColumnHeaderDiv'><table class='kptTable' cellspacing='0' style='table-layout: auto;'><colgroup>{cols}</colgroup><tbody>{trs}</tbody></table></div>";
      $_tpl_tr = "<tr>{tds}</tr>";
      $_cssClasses = $this->CssClasses;
      $_tpl_td = "<td{id} class='kptColumnHeader{wraptext} {css}'{colspan}{rowspan}>{text}</td>";
      $_tpl_td = _replace("{css}", isset($_cssClasses['column header']) ? $_cssClasses['column header'] : "", $_tpl_td);
      $_tpl_td_total = "<td class='kptColumnHeader kptColumnHeaderTotal{wraptext} {css}'{colspan}{rowspan}>{text}</td>";
      $_tpl_td_total = _replace("{css}", isset($_cssClasses['column header']) ? $_cssClasses['column header'] : "", $_tpl_td_total);
      $_tpl_td_total_grand = "<td {id} class='kptColumnHeader kptColumnHeaderTotal{wraptext} {css}'{colspan}{rowspan}>{text}{sort}</td>";
      $_tpl_td_total_grand = _replace("{css}", isset($_cssClasses['column header']) ? $_cssClasses['column header'] : "", $_tpl_td_total_grand);
      $_tpl_col = "<col/>";
      $_tpl_dimension_tr = "<tr class='kptDimensionRow'>{tds}</tr>";
      $_tpl_dimension_td = "<td></td>";
      $_grand = $this->_headGroups[_PV::_Column]->_getSubGroup(0);
      $_total_depth = $_grand->_GetDepth();
      $_total_width = $_grand->_GetWidth();
      $_datafield_count = $this->_PVFields[_PV::_Data]->_length();
      $_trs = array();
      for ($i = 0; $i < $_total_depth; $i++) {
        array_push($_trs, $_tpl_tr);
      }
      $_tr_last = "";
      if ($_datafield_count > 1) {
        $_tr_last = $_tpl_tr;
      }
      $_array_columnheaders = $this->_arrGroups[_PV::_Column];
      $_cols = "";
      for ($i = 0; $i < count($_array_columnheaders); $i++) {
        if ($_array_columnheaders[$i]->_Field->FieldName == _Key::_grandConstant) {
          if ($this->ShowGrandColumn) {
            $_grand_td = _replace("{text}", $_array_columnheaders[$i]->_RenderHeader(), $_tpl_td_total_grand);
            $_grand_td = _replace("{rowspan}", ($_total_depth > 1) ? " rowspan='{rowspan}'" : "", $_grand_td);
            $_grand_td = _replace("{rowspan}", $_total_depth, $_grand_td);
            $_grand_td = _replace("{wraptext}", $_array_columnheaders[$i]->_Field->HeaderTextWrap ? "" : " kptNoWrap", $_grand_td);
            $_grand_td = _replace("{id}", " id='{id}'", $_grand_td);
            $_grand_td = _replace("{id}", $_grand->_UniqueID, $_grand_td);
            $_tpl_sort = "<span class='kptSortButton kptSort{direction}{status}' title='{tooltip}' onclick='pivot_group_sort_toggle(this)'></span>";
            if ($this->AllowSortingData) {
              switch (strtolower($_grand->_Sort)) {
                case _Key::_ASC:
                  $_sort = _replace("{direction}", "Asc", $_tpl_sort);
                  $_sort = _replace("{tooltip}", $this->Localization->_Messages[_Key::_Sorted_Asc], $_sort);
                  break;
                case _Key::_DESC:
                  $_sort = _replace("{direction}", "Desc", $_tpl_sort);
                  $_sort = _replace("{tooltip}", $this->Localization->_Messages[_Key::_Sorted_Desc], $_sort);
                  break;
                case _Key::_none:
                default:
                  $_sort = _replace("{direction}", "Asc", $_tpl_sort);
                  $_sort = _replace("{tooltip}", $this->Localization->_Messages[_Key::_Sorted_Asc], $_sort);
                  break;
              }
              $_status = "Off";
              if (!empty($this->_GroupsToSort))
                foreach ($this->_GroupsToSort as $_PV_index => $_group_to_sort)
                  if (!empty($_group_to_sort) && $_group_to_sort[_Key::_UniqueID] == $_grand->_UniqueID)
                    $_status = "On";
              $_sort = _replace("{status}", $_status, $_sort);
            } else
              $_sort = "";
            $_grand_td = _replace("{sort}", $this->AllowSortingData ? $_sort : "", $_grand_td);
            if ($_datafield_count > 1) {
              $_grand_td = _replace("{colspan}", " colspan='{colspan}'", $_grand_td);
              $_grand_td = _replace("{colspan}", $_datafield_count, $_grand_td);
            } else {
              $_grand_td = _replace("{colspan}", "", $_grand_td);
            }
            if (!isset($_trs[0])) {
              array_push($_trs, $_tpl_tr);
            }
            $_trs[0] = _replace("{tds}", $_grand_td, $_trs[0]);
          }
        } else {
          $_width = $_array_columnheaders[$i]->_GetWidth();
          $_depth = $_array_columnheaders[$i]->_GetDepth();
          $_level = $_array_columnheaders[$i]->_GetLevel();
          $_td = _replace("{id}", " id='{id}'", $_tpl_td);
          $_td = _replace("{id}", $_array_columnheaders[$i]->_UniqueID, $_td);
          $_td = _replace("{text}", $_array_columnheaders[$i]->_RenderHeader(), $_td);
          $_td = _replace("{wraptext}", $_array_columnheaders[$i]->_Field->HeaderTextWrap ? "" : " kptNoWrap", $_td);
          if ($_datafield_count > 1) {
            $_td = _replace("{colspan}", " colspan='{colspan}'", $_td);
            $_td = _replace("{colspan}", (($_width > 1) ? $_width - 1 : $_width) * $_datafield_count, $_td);
          } else {
            $_td = _replace("{colspan}", ($_width > 1) ? " colspan='{colspan}'" : "", $_td);
            $_td = _replace("{colspan}", $_width - 1, $_td);
          }
          if ($_level < $_total_depth - 1 & $_width <= 1) {
            $_td = _replace("{rowspan}", " rowspan='{rowspan}'", $_td);
            $_td = _replace("{rowspan}", $_total_depth - $_level, $_td);
          } else {
            $_td = _replace("{rowspan}", "", $_td);
          }
          $_trs[$_level] = _replace("{tds}", $_td . "{tds}", $_trs[$_level]);
          if ($_width > 1) {
            $_td_total = _replace("{text}", $_array_columnheaders[$i]->_RenderHeaderTotal(), $_tpl_td_total);
            $_td_total = _replace("{wraptext}", $_array_columnheaders[$i]->_Field->HeaderTextWrap ? "" : " kptNoWrap", $_td_total);
            $_td_total = _replace("{rowspan}", " rowspan='{rowspan}'", $_td_total);
            $_td_total = _replace("{rowspan}", $_total_depth, $_td_total);
            if ($_datafield_count > 1) {
              $_td_total = _replace("{colspan}", " colspan='{colspan}'", $_td_total);
              $_td_total = _replace("{colspan}", $_datafield_count, $_td_total);
            } else {
              $_td_total = _replace("{colspan}", "", $_td_total);
            }
            $_trs[$_level] = _replace("{tds}", $_td_total . "{tds}", $_trs[$_level]);
          }
        }
        if ($_datafield_count > 1) {
          $_tds = "";
          for ($j = 0; $j < $_datafield_count; $j++) {
            $_tpl_sort = "<span class='kptSortButton kptSort{direction}' title='{tooltip}' onclick='pivot_group_sort_toggle(this)'></span>";
            $_data_header = $this->_PVFields[_PV::_Data]->_field($j)->RenderHeaderTotal($_array_columnheaders[$i]->Value, $_array_columnheaders[$i]);
            $_td = _replace("{id}", " id='{id}'", $_tpl_td);
            $_td = _replace("{id}", $_array_columnheaders[$i]->_UniqueID . $_data_header, $_td);
            $_td = _replace("{colspan}", "", $_td);
            $_td = _replace("{rowspan}", "", $_td);
            $_td = _replace("{text}", $_data_header, $_td);
            $_td = _replace("{wraptext}", $_array_columnheaders[$i]->_Field->HeaderTextWrap ? "" : " kptNoWrap", $_td);
            $_tds.=$_td;
            $_cols.=$_tpl_col;
          }
          $_tr_last = _replace("{tds}", $_tds . "{tds}", $_tr_last);
        } else {
          $_cols.=$_tpl_col;
        }
      }
      $_dimension_tds = "";
      for ($i = 0; $i < $_total_width * $_datafield_count; $i++) {
        $_dimension_tds.=$_tpl_dimension_td;
      }
      $_dimension_tr = _replace("{tds}", $_dimension_tds, $_tpl_dimension_tr);
      for ($i = 0; $i < $_total_depth; $i++) {
        $_trs[$i] = _replace("{tds}", "", $_trs[$i]);
      }
      $_tr_last = _replace("{tds}", "", $_tr_last);
      $_main = _replace("{trs}", implode("", $_trs) . $_tr_last . $_dimension_tr, $_tpl_main);
      $_main = _replace("{cols}", $_cols, $_main);
      return $_main;
    }
    function _RenderRowHeaderZone() {
      $_tpl_main = "<div class='kptRowHeaderDiv'{minwidth}><table class='kptTable' cellspacing='0' ><colgroup>{cols}</colgroup><tbody>{trs}</tbody></table></div>";
      $_tpl_tr = "<tr>{tds}</tr>";
      $_cssClasses = $this->CssClasses;
      $_tpl_td = "<td id='{id}' class='kptRowHeader{wraptext} {css}'{colspan}{rowspan}>{text}</td>";
      $_tpl_td = _replace("{css}", isset($_cssClasses['row header']) ? $_cssClasses['row header'] : "", $_tpl_td);
      $_tpl_td_total = "<td class='{class}{wraptext} {css}' scope='col'{colspan}{rowspan}>{text}</td>";
      $_tpl_td_total = _replace("{css}", isset($_cssClasses['row header']) ? $_cssClasses['row header'] : "", $_tpl_td_total);
      $_tpl_td_total_grand = "<td id='{id}' class='kptRowHeader {class} {css}' scope='col'{colspan}{rowspan}>{text}{sort}</td>";
      $_tpl_td_total_grand = _replace("{css}", isset($_cssClasses['row header']) ? $_cssClasses['row header'] : "", $_tpl_td_total_grand);
      $_tpl_col = "<col/>";
      $_grand = $this->_headGroups[_PV::_Row]->_getSubGroup(0);
      $_total_depth = $_grand->_GetDepth();
      $_total_width = $_grand->_GetWidth();
      $_datafield_count = $this->_PVFields[_PV::_Data]->_length();
      $_array_rowheaders = $this->_arrGroups[_PV::_Row];
      $_start_row = 0;
      $_row_count = count($_array_rowheaders) - 1;
      if (isset($this->Pager)) {
        $_start_row = $this->Pager->PageIndex * $this->Pager->PageSize;
        $_row_count = ($_start_row + $this->Pager->PageSize < $this->Pager->_TotalRows) ? $this->Pager->PageSize : ($this->Pager->_TotalRows - $_start_row);
      }
      $_trs = array();
      for ($i = 0; $i < $_row_count; $i++) {
        array_push($_trs, $_tpl_tr);
      }
      $_runout = array();
      $_cols = "";
      for ($c = 0; $c < $_total_depth; $c++) {
        $_pointer = 0;
        for ($r = 0; $r < count($_array_rowheaders) - 1; $r++) { //-1 to avoid the grand   
          $_width = $_array_rowheaders[$r]->_GetWidth();
          $_depth = $_array_rowheaders[$r]->_GetDepth();
          $_level = $_array_rowheaders[$r]->_GetLevel();
          if ($_level == $c) {
            if ($_width > 1) {
              if ($_pointer >= $_start_row && $_pointer < $_start_row + $_row_count) {
                $_td = _replace("{id}", $_array_rowheaders[$r]->_UniqueID, $_tpl_td);
                $_td = _replace("{text}", $_array_rowheaders[$r]->_RenderHeader(), $_td);
                $_td = _replace("{wraptext}", $_array_rowheaders[$r]->_Field->HeaderTextWrap ? "" : " kptNoWrap", $_td);
                $_td = _replace("{colspan}", "", $_td); //No colspan
                $_td = _replace("{rowspan}", " rowspan='{rowspan}'", $_td);
                $_td = _replace("{rowspan}", ($_pointer + $_width - 1 < $_start_row + $_row_count) ? ($_width - 1) : ($_start_row + $_row_count - $_pointer), $_td);
                $_trs[$_pointer - $_start_row] = _replace("{tds}", $_td . "{tds}", $_trs[$_pointer - $_start_row]);
              } else if ($_pointer < $_start_row && $_pointer + $_width - 1 > $_start_row) {
                $_td = _replace("{id}", $_array_rowheaders[$r]->_UniqueID, $_tpl_td);
                $_td = _replace("{text}", $_array_rowheaders[$r]->_RenderHeader(), $_td);
                $_td = _replace("{wraptext}", $_array_rowheaders[$r]->_Field->HeaderTextWrap ? "" : " kptNoWrap", $_td);
                $_td = _replace("{colspan}", "", $_td); //No colspan
                $_td = _replace("{rowspan}", " rowspan='{rowspan}'", $_td);
                $_td = _replace("{rowspan}", ($_pointer + $_width - 1 < $_start_row + $_row_count) ? ($_pointer + $_width - 1 - $_start_row) : ($_row_count), $_td);
                $_trs[0] = _replace("{tds}", $_td . "{tds}", $_trs[0]);
              }
              $_pointer += ($_width - 1);
              if ($_pointer >= $_start_row && $_pointer < $_start_row + $_row_count) {
                $_td_total = _replace("{text}", $_array_rowheaders[$r]->_RenderHeaderTotal(), $_tpl_td_total);
                $_td_total = _replace("{wraptext}", $_array_rowheaders[$r]->_Field->HeaderTextWrap ? "" : " kptNoWrap", $_td_total);
                $_td_total = _replace("{rowspan}", "", $_td_total); //No row span
                $_td_total = _replace("{colspan}", " colspan='{colspan}'", $_td_total);
                $_td_total = _replace("{colspan}", $_total_depth, $_td_total);
                +
                    $_td_total = _replace("{class}", "kptRowHeaderTotal", $_td_total);
                $_trs[$_pointer - $_start_row] = _replace("{tds}", $_td_total, $_trs[$_pointer - $_start_row]);
              }
              $_runout[$_pointer] = 1;
              $_pointer++;
            } else {
              while (isset($_runout[$_pointer])) {
                $_pointer++;
              }
              if ($_pointer >= $_start_row && $_pointer < $_start_row + $_row_count) {
                $_td = _replace("{id}", $_array_rowheaders[$r]->_UniqueID, $_tpl_td);
                $_td = _replace("{text}", $_array_rowheaders[$r]->_RenderHeader(), $_td);
                $_td = _replace("{wraptext}", $_array_rowheaders[$r]->_Field->HeaderTextWrap ? "" : " kptNoWrap", $_td);
                $_td = _replace("{rowspan}", "", $_td); //No row span
                $_td = _replace("{colspan}", " colspan='{colspan}'", $_td);
                $_td = _replace("{colspan}", $_total_depth - $_level, $_td);
                $_trs[$_pointer - $_start_row] = _replace("{tds}", $_td . "{tds}", $_trs[$_pointer - $_start_row]);
              }
              $_runout[$_pointer] = 1;
              $_pointer++;
            }
          } elseif ($_level < $c) {//If the row group is at a 'higher' level than then current depth
            $_pointer++; //then skip this group.
          }
        }
        $_cols.=$_tpl_col;
      }
      for ($i = 0; $i < $_row_count; $i++) {
        $_trs[$i] = _replace("{tds}", "", $_trs[$i]);
      }
      $_tr_grand = _replace("{tds}", $_tpl_td_total_grand, $_tpl_tr);
      $_tr_grand = _replace("{rowspan}", "", $_tr_grand);
      $_tr_grand = _replace("{colspan}", " colspan='{colspan}'", $_tr_grand);
      $_tr_grand = _replace("{colspan}", $_total_depth, $_tr_grand);
      $_tr_grand = _replace("{class}", "kptRowHeaderGrandTotal", $_tr_grand);
      $_tr_grand = _replace("{text}", $_grand->_RenderHeader(), $_tr_grand);
      $_tr_grand = _replace("{id}", $_grand->_UniqueID, $_tr_grand);
      $_tpl_sort = "<span class='kptSortButton kptSort{direction}{status}' title='{tooltip}' onclick='pivot_group_sort_toggle(this)'></span>";
      if ($this->AllowSortingData) {
        switch (strtolower($_grand->_Sort)) {
          case _Key::_ASC:
            $_sort = _replace("{direction}", "Asc", $_tpl_sort);
            $_sort = _replace("{tooltip}", $this->Localization->_Messages[_Key::_Sorted_Asc], $_sort);
            break;
          case _Key::_DESC:
            $_sort = _replace("{direction}", "Desc", $_tpl_sort);
            $_sort = _replace("{tooltip}", $this->Localization->_Messages[_Key::_Sorted_Desc], $_sort);
            break;
          case _Key::_none:
          default:
            $_sort = _replace("{direction}", "Asc", $_tpl_sort);
            $_sort = _replace("{tooltip}", $this->Localization->_Messages[_Key::_Sorted_Asc], $_sort);
            break;
        }
        $_status = "Off";
        if (!empty($this->_GroupsToSort))
          foreach ($this->_GroupsToSort as $_PV_index => $_group_to_sort) {
            if (!empty($_group_to_sort) && $_group_to_sort[_Key::_UniqueID] == $_grand->_UniqueID)
              $_status = "On";
          }
        $_sort = _replace("{status}", $_status, $_sort);
      } else
        $_sort = "";
      $_tr_grand = _replace("{sort}", $this->AllowSortingData ? $_sort : "", $_tr_grand);
      $_main = $_tpl_main;
      $_main = _replace("{trs}", implode("", $_trs) . ($this->ShowGrandRow ? $_tr_grand : ""), $_main);
      $_main = _replace("{cols}", $_cols, $_main);
      $_main = _replace("{minwidth}", ($this->Appearance->RowHeaderMinWidth !== null) ? " style='min-width:" . $this->Appearance->RowHeaderMinWidth . "'" : "", $_main);
      return $_main;
    }
    function _RenderContentZone() {
      $_tpl_main = "<div class='kptContentDiv'><table cellspacing='0' class='kptTable' style='table-layout: auto;'><colgroup>{cols}</colgroup><tbody>{trs}</tbody></table></div>";
      $_tpl_tr = "<tr>{tds}</tr>";
      $_tpl_td = "<td class='kptDataCell {css}'>{text}</td>";
      $_tpl_col = "<col />";
      $_columnheaders = $this->_arrGroups[_PV::_Column];
      $_rowheaders = $this->_arrGroups[_PV::_Row];
      $_start_row = 0;
      $_row_count = count($_rowheaders) - 1;
      if (isset($this->Pager)) {
        $_start_row = $this->Pager->PageIndex * $this->Pager->PageSize;
        $_row_count = ($_start_row + $this->Pager->PageSize < $this->Pager->_TotalRows) ? $this->Pager->PageSize : ($this->Pager->_TotalRows - $_start_row);
      }
      $_cols = "";
      for ($c = 0; $c < count($_columnheaders); $c++) {
        if ($_columnheaders[$c]->_Field->FieldName != _Key::_grandConstant || $this->ShowGrandColumn
        ) {
          for ( $n=0; $n<$this->_PVFields[_PV::_Data]->_length(); $n++ )
            $_cols.=$_tpl_col;
        }
      }
      $_trs = "";
      for ($r = $_start_row; $r < $_start_row + $_row_count + ( $this->ShowGrandRow ? 1 : 0 ); $r++) {
        if ($r == $_start_row + $_row_count) {
          $r = count($_rowheaders) - 1; //grand
        }
        $_tds = "";
        for ($c = 0; $c < count($_columnheaders); $c++) {
          if ($_columnheaders[$c]->_Field->FieldName != _Key::_grandConstant || $this->ShowGrandColumn
          ) {
            $this->_GetKeys($_columnheaders[$c], $_rowheaders[$r], $_fields_key, $_groups_key, $_key);
            for ($i = 0; $i < $this->_PVFields[_PV::_Data]->_length(); $i++) {
              $_datafield = $this->_PVFields[_PV::_Data]->_field($i);
              $_data_alias = $_datafield->_sqlAlias;
              $_value = (isset($this->_Values[$_fields_key][$_groups_key][$_key][$_data_alias])) ? $_datafield->DisplayFormat($this->_Values[$_fields_key][$_groups_key][$_key][$_data_alias]) : $_datafield->DisplayFormat($this->EmptyValue);
              $_td = _replace("{text}", $_value, $_tpl_td);
              if (isset($this->CssClasses['data cell']))
                $_td = _replace("{css}", $this->CssClasses['data cell'] . " {css}", $_td);
              if ($_rowheaders[$r]->_Field->FieldName == _Key::_grandConstant) {
                $_td = _replace("{css}", " kptRowGrandTotalDataCell{css}", $_td);
              } else if ($_rowheaders[$r]->_GetDepth() > 0) {
                $_td = _replace("{css}", " kptRowTotalDataCell{css}", $_td);
              }
              if ($_columnheaders[$c]->_Field->FieldName == _Key::_grandConstant) {
                $_td = _replace("{css}", " kptColumnGrandTotalDataCell", $_td);
              } else if ($_columnheaders[$c]->_GetDepth() > 0) {
                $_td = _replace("{css}", " kptColumnTotalDataCell", $_td);
              } else {
                $_td = _replace("{css}", "", $_td);
              }
              $_tds.=$_td;
            }
          }
        }
        $_tr = _replace("{tds}", $_tds, $_tpl_tr);
        $_trs.=$_tr;
      }
      $_main = _replace("{trs}", $_trs, $_tpl_main);
      $_main = _replace("{cols}", $_cols, $_main);
      return $_main;
    }
    function _RenderVerticalScrollingZone() {
      $_tpl_main = "<div class='kptVerticalScrollDiv' style='width:17px;overflow-y: scroll; overflow-x: hidden;'><div style='width:17px'></div></div>";
      return $_tpl_main;
    }
    function _RenderHorizontalScrollingZone() {
      $_tpl_main = "<div class='kptHorizontalScrollDiv' style='height:17px;overflow-x: scroll; overflow-y: hidden;'><div style='height:17px'></div></div>";
      return $_tpl_main;
    }
    function _RenderStatusZone() {
      return $this->Status->_Render();
    }
    function RenderPivotTable() {
      $this->_positionStyle();
      $_trademark = "\n<!--KoolPivotTable version " . $this->_version . " - www.koolphp.net -->\n";
      $_tpl_main = "{trademark}<div id='{id}' class='{style}KPT' style='position:relative;display:inline-block;{width}{height}'>{table}{viewstate}{command}</div>";
      if (isset($this->_FilterPanelItem)) {
        $_table = $this->_FilterPanelItem->_RenderFilterPanel();
      } else {
        $_tpl_table = "<table class='kptTable{horizontalscrolling}{verticalscrolling}' cellspacing='0'><colgroup>{cols}</colgroup><tbody>{filter_zone}{data_and_column_zone}{row_and_columnheader_and_vertical_scrolling_zone}{rowheader_and_content_zone}{horizontal_scrolling_zone}{pager_zone}{status_zone}</tbody></table>";
        $_tpl_filter_zone = "<tr><td colspan='{total_colspan}' class='kptFilterZone'>{zone}</td></tr>";
        $_tpl_data_and_column_zone = "";
        if ($this->SeparateDataZone) {
          if ($this->ShowDataZone)
            $_tpl_data_and_column_zone .= "<tr><td colspan='{total_colspan}' class='kptDataZone'>{data_zone}</td></tr>";
          if ($this->ShowColumnZone)
            $_tpl_data_and_column_zone .= "<tr><td colspan='{total_colspan}' class='kptColumnZone'>{column_zone}</td></tr>";
        }
        else
          $_tpl_data_and_column_zone = "<tr><td colspan='{data_colspan}' class='kptDataZone'>{data_zone}</td><td class='kptColumnZone' colspan='{column_colspan}'>{column_zone}</td></tr>";
        $_tpl_data_zone = "{zone}";
        $_tpl_column_zone = "{zone}";
        if ($this->SeparateRowZone) {
          $_tpl_row_and_columnheader_and_vertical_scrolling_zone = "<tr><td colspan='2' class='kptRowZone'>{row_zone}</td></tr><tr><td colspan='{row_colspan}' class='' style='width:$this->RowZoneWidth; min-width:$this->RowZoneMinWidth'>&nbsp;</td><td colspan='{columnheader_colspan}' class='kptColumnHeaderZone'>{columnheader_zone}</td>{vertical_scrolling_zone}</tr>";
        }
        else {
          $_tpl_row_and_columnheader_and_vertical_scrolling_zone = "<tr><td colspan='{row_colspan}' class='kptRowZone' style='width:$this->RowZoneWidth; min-width:$this->RowZoneMinWidth'>{row_zone}</td><td colspan='{columnheader_colspan}' class='kptColumnHeaderZone'>{columnheader_zone}</td>{vertical_scrolling_zone}</tr>";
        }
        $_tpl_row_zone = "{zone}";
        $_tpl_columnheader_zone = "{zone}";
        $_tpl_vertical_scrolling_zone = "<td rowspan='2' class='kptVerticalScrollingZone' style='width:17px'>{zone}</td>";
        $_tpl_rowheader_and_content_zone = "<tr><td colspan='{rowheader_colspan}' class='kptRowHeaderZone'>{rowheader_zone}</td><td colspan='{content_colspan}' class='kptContentZone'>{content_zone}</td></tr>";
        $_tpl_rowheader_zone = "{zone}";
        $_tpl_content_zone = "{zone}";
        $_tpl_horizontal_scrolling_zone = "<tr><td colspan='{total_colspan}' class='kptHorizontalScrollingZone'>{zone}</td></tr>";
        $_tpl_pager_zone = "<tr><td colspan='{total_colspan}' class='kptPagerZone'>{zone}</td></tr>";
        $_tpl_status_zone = "<tr><td colspan='{total_colspan}' class='kptStatusZone'>{zone}</td></tr>";
        if ($this->VerticalScrolling) {
          $_total_colspan = 3;
          $_data_colspan = 1;
          $_column_colspan = 2;
          $_row_colspan = $_columnheader_colspan = 1;
          $_rowheader_colspan = $_content_colspan = 1;
          $_cols = "<col /><col /><col style='width:17px' />";
        } else {
          $_total_colspan = 2;
          $_data_colspan = $_column_colspan = 1;
          $_row_colspan = $_columnheader_colspan = 1;
          $_rowheader_colspan = $_content_colspan = 1;
          $_cols = "<col /><col />";
        }
        $_table = $_tpl_table;
        $_status_zone = "";
        if ($this->ShowStatus) {
          $_status_zone = _replace("{zone}", $this->_RenderStatusZone(), $_tpl_status_zone);
          $_status_zone = _replace("{total_colspan}", $_total_colspan, $_status_zone);
        }
        $_pager_zone = "";
        if ($this->Pager !== null) {
          $_pager_zone = _replace("{zone}", $this->Pager->Render(), $_tpl_pager_zone);
          $_pager_zone = _replace("{total_colspan}", $_total_colspan, $_pager_zone);
        }
        $_horizontal_scrolling_zone = "";
        if ($this->HorizontalScrolling) {
          $_horizontal_scrolling_zone = _replace("{zone}", $this->_RenderHorizontalScrollingZone(), $_tpl_horizontal_scrolling_zone);
          $_horizontal_scrolling_zone = _replace("{total_colspan}", $_total_colspan, $_horizontal_scrolling_zone);
        }
        $_content_zone = _replace("{zone}", $this->_RenderContentZone(), $_tpl_content_zone);
        $_rowheader_zone = _replace("{zone}", $this->_RenderRowHeaderZone(), $_tpl_rowheader_zone);
        $_rowheader_and_content_zone = _replace("{rowheader_zone}", $_rowheader_zone, $_tpl_rowheader_and_content_zone);
        $_rowheader_and_content_zone = _replace("{content_zone}", $_content_zone, $_rowheader_and_content_zone);
        $_rowheader_and_content_zone = _replace("{rowheader_colspan}", $_rowheader_colspan, $_rowheader_and_content_zone);
        $_rowheader_and_content_zone = _replace("{content_colspan}", $_content_colspan, $_rowheader_and_content_zone);
        $_vertical_scrolling_zone = "";
        if ($this->VerticalScrolling) {
          $_vertical_scrolling_zone = _replace("{zone}", $this->_RenderVerticalScrollingZone(), $_tpl_vertical_scrolling_zone);
        }
        $_columnheader_zone = _replace("{zone}", $this->_RenderColumnHeaderZone(), $_tpl_columnheader_zone);
        $_row_zone = "";
        if ($this->ShowRowZone) {
          $_row_zone = _replace("{zone}", $this->_RenderRowZone(), $_tpl_row_zone);
        }
        $_row_and_columnheader_and_vertical_scrolling_zone = _replace("{vertical_scrolling_zone}", $_vertical_scrolling_zone, $_tpl_row_and_columnheader_and_vertical_scrolling_zone);
        $_row_and_columnheader_and_vertical_scrolling_zone = _replace("{columnheader_zone}", $_columnheader_zone, $_row_and_columnheader_and_vertical_scrolling_zone);
        $_row_and_columnheader_and_vertical_scrolling_zone = _replace("{row_zone}", $_row_zone, $_row_and_columnheader_and_vertical_scrolling_zone);
        $_row_and_columnheader_and_vertical_scrolling_zone = _replace("{columnheader_colspan}", $_columnheader_colspan, $_row_and_columnheader_and_vertical_scrolling_zone);
        $_row_and_columnheader_and_vertical_scrolling_zone = _replace("{row_colspan}", $_row_colspan, $_row_and_columnheader_and_vertical_scrolling_zone);
        $_column_zone = "";
        if ($this->ShowColumnZone) {
          $_column_zone = _replace("{zone}", $this->_RenderColumnZone(), $_tpl_column_zone);
        }
        $_data_zone = "";
        if ($this->ShowDataZone) {
          $_data_zone = _replace("{zone}", $this->_RenderDataZone(), $_tpl_data_zone);
        }
        $_data_and_column_zone = "";
        if ($_data_zone != "" && $_column_zone != "" || $this->SeparateDataZone) {
          $_data_and_column_zone = _replace("{data_zone}", $_data_zone, $_tpl_data_and_column_zone);
          $_data_and_column_zone = _replace("{column_zone}", $_column_zone, $_data_and_column_zone);
          $_data_and_column_zone = _replace("{data_colspan}", $_data_colspan, $_data_and_column_zone);
          $_data_and_column_zone = _replace("{column_colspan}", $_column_colspan, $_data_and_column_zone);
          $_data_and_column_zone = _replace("{total_colspan}", $_total_colspan, $_data_and_column_zone);
        }
        $_filter_zone = "";
        if ($this->ShowFilterZone) {
          $_filter_zone = _replace("{zone}", $this->_RenderFilterZone(), $_tpl_filter_zone);
          $_filter_zone = _replace("{total_colspan}", $_total_colspan, $_filter_zone);
        }
        $_table = _replace("{filter_zone}", $_filter_zone, $_tpl_table);
        $_table = _replace("{data_and_column_zone}", $_data_and_column_zone, $_table);
        $_table = _replace("{row_and_columnheader_and_vertical_scrolling_zone}", $_row_and_columnheader_and_vertical_scrolling_zone, $_table);
        $_table = _replace("{rowheader_and_content_zone}", $_rowheader_and_content_zone, $_table);
        $_table = _replace("{horizontal_scrolling_zone}", $_horizontal_scrolling_zone, $_table);
        $_table = _replace("{pager_zone}", $_pager_zone, $_table);
        $_table = _replace("{status_zone}", $_status_zone, $_table);
        $_table = _replace("{cols}", $_cols, $_table);
        $_table = _replace("{horizontalscrolling}", $this->HorizontalScrolling ? " kptHorizontalScrolling" : "", $_table);
        $_table = _replace("{verticalscrolling}", $this->VerticalScrolling ? " kptVerticalScrolling" : "", $_table);
      }
      $_main = _replace("{id}", $this->id, $_tpl_main);
      if (true) {
        $_main = _replace("{width}", ($this->Width === null) ? "" : "width:" . $this->Width . ";", $_main);
        $_main = _replace("{height}", ($this->Height === null) ? "" : "height:" . $this->Height . ";", $_main);
        $_main = _replace("{style}", $this->_style, $_main);
        $_main = _replace("{trademark}", $_trademark, $_main);
        $_main = _replace("{table}", $_table, $_main);
        $_main = _replace("{viewstate}", $this->_ViewState->_Render(), $_main);
        $_main = _replace("{command}", $this->_Command->_Render(), $_main);
        $_main = _replace("{version}", $this->_version, $_main);
      }
      return $_main;
    }
    function Render() {
      $_script = $this->RegisterCss();
      $_script.= $this->RenderPivotTable();
      $_is_callback = isset($_POST["__koolajax"]) || isset($_GET["__koolajax"]);
      $_script.= ($_is_callback) ? "" : $this->RegisterScript();
      $_script.="<script type='text/javascript'>";
      $_script.= $this->StartupScript();
      $_script.="</script>";
      if ($this->AjaxEnabled && class_exists("UpdatePanel")) {
        $_pivot_updatepanel = new UpdatePanel($this->id . "_updatepanel");
        $_pivot_updatepanel->content = $_script;
        $_pivot_updatepanel->cssclass = $this->_style . "KPT_UpdatePanel";
        if ($this->AjaxLoadingImage) {
          $_pivot_updatepanel->setLoading($this->AjaxLoadingImage);
        }
        $_script = $_pivot_updatepanel->Render();
      }
      return $_script;
    }
    function _positionStyle() {
      $this->styleFolder = _replace("\\", "/", $this->styleFolder);
      $_styleFolder = trim($this->styleFolder, "/");
      $_lastpos = strrpos($_styleFolder, "/");
      $this->_style = substr($_styleFolder, ($_lastpos ? $_lastpos : -1) + 1);
    }
    function RegisterCss() {
      $this->_positionStyle();
      $_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KPT')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KPT';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
      $_script = _replace("{style}", $this->_style, $_tpl_script);
      $_script = _replace("{stylepath}", $this->_getStylePath(), $_script);
      return $_script;
    }
    function RegisterScript() {
      $_tpl_script = "<script type='text/javascript'>if(typeof _libKPT=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKPT=1;}</script>";
      $_script = _replace("{src}", _replace(".php", ".js", $this->_getComponentURI()), $_tpl_script); //Do comment to obfuscate
      return $_script;
    }
    function StartupScript() {
      $_tpl_script = "var {id}; function {id}_init(){ {id} = new KoolPivotTable('{id}',{AjaxEnabled},'{AjaxHandlePage}');}";
      $_tpl_script .= "if (typeof(KoolPivotTable)=='function'){{id}_init();}";
      $_tpl_script .= "else{if(typeof(__KPTInits)=='undefined'){__KPTInits=new Array();} __KPTInits.push({id}_init);{register_script}}";
      $_tpl_register_script = "if(typeof(_libKPT)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKPT=1;}";
      $_register_script = _replace("{src}", _replace(".php", ".js", $this->_getComponentURI()), $_tpl_register_script); //Do comment to obfuscate
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
