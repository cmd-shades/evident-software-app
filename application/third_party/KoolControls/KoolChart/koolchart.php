<?php
$_version = "2.6.0.3";
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
if (!class_exists("KoolChart", false)) {
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  if(!defined('KOOLPHPCOMMON2'))
  {
    function _array_merge_replace($_arr1, $_arr2)
    {
        foreach ($_arr2 as $_k => $_v)
            if (is_array($_v))
            {
                if (!isset($_arr1[$_k]))
                    $_arr1[$_k] = array();
                $_arr1[$_k] = _array_merge_replace($_arr1[$_k], $_v);
            }
            else
                $_arr1[$_k] = $_v;
        return $_arr1;
    }
    function _setProperties($_obj, $_ps, $_info)
    {
        foreach ($_ps as $_k => $_p)
            if (isset($_info[$_k]))
            {
                if (is_string($_p))
                    $_obj->{$_p} = $_info[$_k];
                else if (is_array($_p))
                    _setProperties($_obj->{$_k}, $_p, $_info[$_k]);
            }
    }
    function _setMethods($_obj, $_ms, $_info)
    {
        foreach ($_ms as $_k => $_m)
            if (isset($_info[$_k]))
            {
                if (is_string($_m))
                    $_obj->{$_m}($_info[$_k]);
                else if (is_array($_m))
                    _setMethods($_obj->{$_k}, $_m, $_info[$_k]);
            }
    }
    @define('KOOLPHPCOMMON2', true);
  }
  class _ColorArray {
    var $_Colors;
    public static function getColorArray() {
      return array("Green", "Blue", "Orange", "Maroon", "Purple", "Aqua", "Navy", "Fuchsia",
        "Yellow", "Gray", "Silver", "Teal", "Lime", "Black", "Olive");
    }
    function __construct() {
      $this->_Colors = self::getColorArray();
    }
  }
  class _ChartTitleAppearance {
    var $Visible = true;
    var $Position; //"Top"|"Bottom"
    var $Align; //"Left"|"Center"|"Right"
    var $BackgroundColor;
    var $FontColor;
    var $FontFamily;
    var $FontSize;
    var $FontStyle;
    var $FontWeight;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array("Visible" => $this->Visible,
        "Position" => $this->Position,
        "Align" => $this->Align,
        "BackgroundColor" => $this->BackgroundColor,
        "FontColor" => $this->FontColor,
        "FontFamily" => $this->FontFamily,
        "FontSize" => $this->FontSize,
        "FontStyle" => $this->FontStyle,
        "FontWeight" => $this->FontWeight,
      );
    }
  }
  class _ChartLegendAppearance {
    var $Visible;
    var $Position; //"Top"|"Left"|"Right"|"Bottom"
    var $BackgroundColor;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array("Visible" => $this->Visible,
        "Position" => $this->Position,
        "BackgroundColor" => $this->BackgroundColor
      );
    }
  }
  class _SeriesLabelsAppearance {
    var $Visible = true;
    var $DataFormatString;
    var $Position; //For Column and Bar: "Center"|"InsideEnd"|"InsideBase"|"OutsideEnd"
    var $RotationAngle;
    var $FontColor;
    var $FontFamily;
    var $FontSize;
    var $FontStyle;
    var $FontWeight;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array(
        "Visible" => $this->Visible,
        "DataFormatString" => encodeUrl($this->DataFormatString),
        "Position" => $this->Position,
        "RotationAngle" => $this->RotationAngle,
        "FontColor" => $this->FontColor,
        "FontFamily" => $this->FontFamily,
        "FontSize" => $this->FontSize,
        "FontStyle" => $this->FontStyle,
        "FontWeight" => $this->FontWeight,
      );
    }
  }
  class _SeriesAppearance {
    var $BackgroundColor;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array(
        "BackgroundColor" => $this->BackgroundColor
      );
    }
  }
  class _PlotAreaAppearance {
    var $BackgroundColor;
    function _Init($_chart) {
      if ($this->BackgroundColor === null)
        $this->BackgroundColor = $_chart->BackgroundColor;
    }
    function _Serialize() {
      return array(
        "BackgroundColor" => $this->BackgroundColor
      );
    }
  }
  class _SeriesTooltipsAppearance {
    var $Visible = true;
    var $BackgroundColor;
    var $DataFormatString;
    var $FontColor;
    var $FontFamily;
    var $FontSize;
    var $FontStyle;
    var $FontWeight;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array("Visible" => $this->Visible,
        "DataFormatString" => encodeUrl($this->DataFormatString),
        "BackgroundColor" => $this->BackgroundColor,
        "FontColor" => $this->FontColor,
        "FontFamily" => $this->FontFamily,
        "FontSize" => $this->FontSize,
        "FontStyle" => $this->FontStyle,
        "FontWeight" => $this->FontWeight,
      );
    }
  }
  class _SeriesMarkersAppearance {
    var $BackgroundColor;
    var $MarkersType; //"Circle"|"Square"|"Triangle"
    var $Visible = true;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array("Visible" => $this->Visible,
        "MarkersType" => strtolower($this->MarkersType),
        "BackgroundColor" => $this->BackgroundColor
      );
    }
  }
  class _AxisTitleAppearance {
    var $FontFamily;
    var $FontColor;
    var $FontSize;
    var $FontStyle;
    var $FontWeight;
    var $RotationAngle;
    var $Position; //"Top"|"Middle"|"Bottom"|"Left"|"Center"|"Right"
    var $Visible = TRUE;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array("FontFamily" => $this->FontFamily,
        "FontColor" => $this->FontColor,
        "FontStyle" => $this->FontStyle,
        "FontWeight" => $this->FontWeight,
        "RotationAngle" => $this->RotationAngle,
        "FontSize" => $this->FontSize,
        "Position" => $this->Position,
        "Visible" => $this->Visible
      );
    }
  }
  class _AxisLabelsAppearance {
    var $Visible = true;
    var $DataFormatString;
    var $FontColor;
    var $FontFamily;
    var $FontSize;
    var $FontStyle;
    var $FontWeight;
    var $RotationAngle;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array("Visible" => $this->Visible,
        "DataFormatString" => encodeUrl($this->DataFormatString),
        "FontColor" => $this->FontColor,
        "FontFamily" => $this->FontFamily,
        "FontSize" => $this->FontSize,
        "RotationAngle" => $this->RotationAngle,
        "FontStyle" => $this->FontStyle,
        "FontWeight" => $this->FontWeight,
      );
    }
  }
  class _AxisGridLines {
    var $Color;
    var $Visible = true;
    var $Width = 1;
    function _Init($_chart) {
    }
    function _Serialize() {
      return array("Visible" => $this->Visible,
        "Color" => $this->Color,
        "Width" => $this->Width
      );
    }
  }
  class ChartAxis {
    var $Items; //List of ChartAxisItem.
    var $Title;
    var $TitleAppearance;
    var $LabelsAppearance;
    var $Color;
    var $Name;
    var $MajorTickSize;
    var $MajorTickType; //"None"|"Outside"
    var $MajorStep;
    var $MaxValue;
    var $MinValue;
    var $MinorTickSize;
    var $MinorTickType; //"None"|"Outside"
    var $MinorStep;
    var $MajorGridLines;
    var $MinorGridLines;
    var $Reversed = false;
    var $Width = 1;
    var $Visible = true;
    function __construct($_array = null) {
      $this->Items = array();
      $this->TitleAppearance = new _AxisTitleAppearance();
      $this->LabelsAppearance = new _AxisLabelsAppearance();
      $this->MajorGridLines = new _AxisGridLines();
      $this->MinorGridLines = new _AxisGridLines();
      if ($_array !== null) {
        $this->Set($_array);
      }
    }
    function _Init($_chart) {
      $this->MajorGridLines->_Init($_chart);
      $this->MinorGridLines->_Init($_chart);
      $this->TitleAppearance->_Init($_chart);
      $this->LabelsAppearance->_Init($_chart);
    }
    function AddItem($_item) {
      array_push($this->Items, $_item);
      return $_item;
    }
    function Set($_array) {
      for ($i = 0; $i < count($_array); $i++) {
        $this->AddItem(new ChartAxisItem($_array[$i]));
      }
    }
    function _Serialize() {
      $_serialized_items = array();
      for ($i = 0; $i < count($this->Items); $i++) {
        array_push($_serialized_items, $this->Items[$i]->_Serialize());
      }
      return array("Visible" => $this->Visible,
        "Color" => $this->Color,
        "Name" => $this->Name,
        "MajorStep" => $this->MajorStep,
        "MajorTickSize" => $this->MajorTickSize,
        "MajorTickType" => $this->MajorTickType,
        "MajorGridLines" => $this->MajorGridLines->_Serialize(),
        "MaxValue" => $this->MaxValue,
        "MinValue" => $this->MinValue,
        "MinorStep" => $this->MinorStep,
        "MinorTickSize" => $this->MinorTickSize,
        "MinorTickType" => $this->MinorTickType,
        "MinorGridLines" => $this->MinorGridLines->_Serialize(),
        "Reversed" => $this->Reversed,
        "Width" => $this->Width,
        "LabelsAppearance" => $this->LabelsAppearance->_Serialize(),
        "TitleAppearance" => $this->TitleAppearance->_Serialize(),
        "Title" => $this->Title,
        "Items" => $_serialized_items
      );
    }
  }
  class ChartAxisItem {
    var $Text;
    function __construct($_text = null) {
      $this->Text = $_text;
    }
    function _Serialize() {
      return array("Text" => $this->Text
      );
    }
  }
  class SeriesItem {
    var $YValue;
    var $_settings = array();
    function __construct($_yvalue = null) {
      $this->YValue = $_yvalue;
    }
    function _Serialize() {
      return array(
        "YValue" => $this->YValue
      );
    }
    public function set($_st) {
      $this->_settings = _array_merge_replace($this->_settings, $_st);
    }
    public function getSetting() {
      return $this->_Serialize();
    }
  }
  class ChartSeries {
    var $Items; // List of SeriesItem
    var $Name; //Show in legend
    var $LabelsAppearance;
    var $MarkersAppearance;
    var $TooltipsAppearance;
    var $Appearance;
    var $AxisName;
    var $MissingValue; //"Zero"|"Interpolated"|"Gap"
    var $_ChartType;
    var $_settings = array();
    function __construct($_name = null, $_array = null) {
      $this->LabelsAppearance = new _SeriesLabelsAppearance();
      $this->MarkersAppearance = new _SeriesMarkersAppearance();
      $this->TooltipsAppearance = new _SeriesTooltipsAppearance();
      $this->Appearance = new _SeriesAppearance();
      if ($_name != null) {
        $this->Name = $_name;
      }
      $this->Items = array();
      if ($_array !== null) {
        $this->ArrayData($_array);
      }
    }
    function _Init($_chart) {
    }
    function AddItem($_item) {
      array_push($this->Items, $_item);
      return $_item;
    }
    function ArrayData($_array) {
      for ($i = 0; $i < count($_array); $i++) {
        $this->AddItem(new SeriesItem($_array[$i]));
      }
    }
    public function set($_st) {
      $this->_settings = _array_merge_replace($this->_settings, $_st);
    }
    public function addSetting($_st) {
      $this->_settings = _array_merge_replace($this->_settings, $_st);
    }
    public function getSetting() {
      return $this->_Serialize();
    }
    function _Serialize() {
      $_valuesToChange = array(
        "Name" => "encodeUrl",
        "MissingValue" => "toLower",
        "LabelsAppearance" => array(
          "DataFormatString" => "encodeUrl",
          "Position" => "toLower",
        ),
        "TooltipsAppearance" => array(
          "DataFormatString" => "encodeUrl",
          "Position" => "toLower",
        ),
        "MarkersAppearance" => array(
          "MarkersType" => "toLower",
        )
      );
      _changeValue($this->_settings, $_valuesToChange);
      $_serialized_items = array();
      for ($i = 0; $i < count($this->Items); $i++) {
        array_push($_serialized_items, $this->Items[$i]->_Serialize());
      }
      return _array_merge_replace(
          array(
        "Items" => $_serialized_items,
        "Name" => encodeUrl($this->Name),
        "LabelsAppearance" => $this->LabelsAppearance->_Serialize(),
        "MarkersAppearance" => $this->MarkersAppearance->_Serialize(),
        "TooltipsAppearance" => $this->TooltipsAppearance->_Serialize(),
        "Appearance" => $this->Appearance->_Serialize(),
        "AxisName" => $this->AxisName,
        "MissingValue" => strtolower($this->MissingValue),
        "ChartType" => $this->_ChartType
          ), $this->_settings
      );
    }
  }
  class ColumnSeries extends ChartSeries {
    var $Stacked = false;
    var $_ChartType = "Column";
    function __construct($_name = null, $_array = null) {
      parent::__construct($_name, $_array);
      $this->_settings = array_merge(
          $this->_settings, array(
          )
      );
    }
    function _Serialize() {
      return array_merge(parent::_Serialize(), array("Stacked" => $this->Stacked
      ));
    }
  }
  class PieItem extends SeriesItem {
    var $Name;
    var $Tooltip;
    var $Exploded = false;
    var $BackgroundColor;
    function __construct($_value, $_name = "Pie item", $_background_color = null, $_exploded = false) {
      parent::__construct($_value);
      $this->Name = $_name;
      $this->BackgroundColor = $_background_color;
      $this->Exploded = $_exploded;
    }
    function _Serialize() {
      $_serialization = array_merge(
          parent::_Serialize(), array(
        "Name" => encodeUrl($this->Name),
        "Tooltip" => $this->Tooltip,
        "Exploded" => $this->Exploded,
        "BackgroundColor" => $this->BackgroundColor
      ));
      $_settings = _array_merge_replace(
          $_serialization, $this->_settings
      );
      return $_settings;
    }
  }
  class PieSeries extends ChartSeries {
    var $StartAngle;
    var $ShowRealValue;
    var $DecimalNumber;
    var $_ChartType = "pie";
    function __construct($_name = null, $_array = null) {
      parent::__construct($_name, $_array);
    }
    function _Serialize() {
      return array_merge(
        parent::_Serialize(), array(
          "StartAngle" => $this->StartAngle,
          "ShowRealValue" => $this->ShowRealValue,
          "DecimalNumber" => $this->DecimalNumber,
        )
      );
    }
    function AddItem($_item) {
      array_push($this->Items, $_item);
      return $_item;
    }
    function ArrayData($_array) {
      for ($i = 0; $i < count($_array); $i++) {
        $_pieItem = new PieItem($_array[$i][0]);
        if (isset($_array[$i][1]))
          $_pieItem->Name = $_array[$i][1];
        if (isset($_array[$i][2]))
          $_pieItem->BackgroundColor = $_array[$i][2];
        if (isset($_array[$i][3]))
          $_pieItem->Exploded = $_array[$i][3];
        $this->AddItem($_pieItem);
      }
    }
  }
  class BarSeries extends ChartSeries {
    var $Stacked;
    var $_ChartType = "bar";
    function __construct($_name = null, $_array = null) {
      parent::__construct($_name, $_array);
    }
    function _Serialize() {
      return array_merge(parent::_Serialize(), array("Stacked" => $this->Stacked
      ));
    }
  }
  class LineSeries extends ChartSeries {
    var $_ChartType = "line";
    function __construct($_name = null, $_array = null) {
      parent::__construct($_name, $_array);
    }
  }
  class AreaSeries extends ChartSeries {
    var $_ChartType = "area";
    function __construct($_name = null, $_array = null) {
      parent::__construct($_name, $_array);
    }
  }
  class ScatterSeries extends ChartSeries {
    var $_ChartType = "scatter";
    var $ItemConnected;
    function __construct($_name = null, $_array = null) {
      parent::__construct($_name, $_array);
    }
    function _Serialize() {
      return array_merge(parent::_Serialize(), array("ItemConnected" => $this->ItemConnected
      ));
    }
  }
  class ScatterLineSeries extends ScatterSeries {
    var $_ChartType = "scatterline";
    var $ItemConnected;
    var $MissingValue;
  }
  class ScatterItem extends SeriesItem {
    var $XValue;
    function __construct($_xvalue, $_yvalue) {
      $this->XValue = $_xvalue;
      $this->YValue = $_yvalue;
    }
    function _Serialize() {
      return array_merge(parent::_Serialize(), array("XValue" => $this->XValue
      ));
    }
  }
  class _ChartPlotArea {
    var $Appearance;
    var $XAxis;
    var $YAxis;
    var $_ListOfSeries; //List of series.
    var $_ExtraYAxis;
    var $SeriesOrder;
    function __construct() {
      $this->Appearance = new _PlotAreaAppearance();
      $this->_ListOfSeries = array();
      $this->_ExtraYAxis = array();
      $this->XAxis = new ChartAxis();
      $this->YAxis = new ChartAxis();
    }
    function _Init($_chart) {
    }
    function AddSeries($_series) {
      array_push($this->_ListOfSeries, $_series);
      return $_series;
    }
    function AddYAxis($_yaxis) {
      array_push($this->_ExtraYAxis, $_yaxis);
      return $_yaxis;
    }
    function _Serialize() {
      $_serialized_listofseries = array();
      for ($i = 0; $i < count($this->_ListOfSeries); $i++) {
        array_push($_serialized_listofseries, $this->_ListOfSeries[$i]->_Serialize());
      }
      $_serialized_extrayaxis = array();
      for ($i = 0; $i < count($this->_ExtraYAxis); $i++) {
        array_push($_serialized_extrayaxis, $this->_ExtraYAxis[$i]->_Serialize());
      }
      return array(
        "Appearance" => $this->Appearance->_Serialize(),
        "XAxis" => $this->XAxis->_Serialize(),
        "YAxis" => $this->YAxis->_Serialize(),
        "ListOfSeries" => $_serialized_listofseries,
        "ExtraYAxis" => $_serialized_extrayaxis,
        'SeriesOrder' => $this->SeriesOrder
      );
    }
  }
  class _ChartLegend {
    var $Appearance;
    var $SeriesOrder;
    function __construct() {
      $this->Appearance = new _ChartLegendAppearance();
    }
    function _Init($_chart) {
    }
    function _Serialize() {
      return array(
        "Appearance" => $this->Appearance->_Serialize(),
        'SeriesOrder' => $this->SeriesOrder
      );
    }
  }
  class _ChartTitle {
    var $Text;
    var $Appearance;
    function __construct() {
      $this->Appearance = new _ChartTitleAppearance();
    }
    function _Init($_chart) {
    }
    function _Serialize() {
      return array(
        "Text" => encodeUrl($this->Text),
        "Appearance" => $this->Appearance->_Serialize()
      );
    }
  }
  function toLower($str) {
    return $str;
  }
  function encodeUrl($str) {
    if (isset($str) && is_string($str))
      return str_replace("+", " ", urlencode($str));
    else
      return null;
  }
  function _changeValue(& $_settings, $_valuesToChange) {
    foreach ($_valuesToChange as $k => $v)
      if (isset($_settings[$k])) {
        if (is_string($v))
          $_settings[$k] = $v($_settings[$k]);
        else if (is_array($v))
          _changeValue($_settings[$k], $v);
      }
  }
  class KoolChart {
    var $_version = "2.6.0.3";
    var $id;
    var $scriptFolder;
    var $styleFolder;
    var $_style;
    var $PlotArea;
    var $Title;
    var $Legend;
    var $Height;
    var $Width;
    var $ClientEvents;
    var $Transitions; //false or true: Get/set whether an animation is played when the chart is rendered.
    var $FontColor;
    var $BackgroundColor;
    var $FontFamily;
    var $FontSize;
    var $FontStyle;
    var $FontWeight;
    var $NumberFormat;
    var $DecimalNumber;
    var $ThousandSeparator;
    var $DecimalSeparator;
    var $Padding;
    var $_settings = array(
    );
    function __construct($_id = 'KoolChart1') {
      $this->id = $_id;
      $this->Title = new _ChartTitle();
      $this->Legend = new _ChartLegend();
      $this->PlotArea = new _ChartPlotArea();
      $this->ClientEvents = array();
    }
    public static function newChart($_info) {
      if (!isset($_info["Id"]))
        $_info["Id"] = 'KoolChart1';
      $_chart = new KoolChart($_info["Id"]);
      $_chart->set($_info);
      return $_chart;
    }
    public static function newItem($_info) {
      $item = new SeriesItem();
      $item->set($_info);
      return $item;
    }
    public static function newSeries($_info) {
      $_type = isset($_info["type"]) ? $_info["type"] : "BarSeries";
      switch ($_type) {
        case "Column":
          $series = new ColumnSeries();
        case "Pie":
          $series = new PieSeries();
        case "Line":
          $series = new LineSeries();
        case "Area":
          $series = new AreaSeries();
        case "Scatter":
          $series = new ScatterSeries();
        case "ScatterLine":
          $series = new ScatterLineSeries();
        case "Bar":
        default:
          $series = new BarSeries();
      }
      $_ps = array(
        "Name" => "Name",
        "Stacked" => "Stacked",
        "MissingValue" => "MissingValue",
        "StartAngle" => "StartAngle",
        "ShowRealValue" => "ShowRealValue",
        "DecimalNumber" => "DecimalNumber",
        "ItemConnected" => "ItemConnected",
        "Appearance" => array(
          "BackgroundColor" => "BackgroundColor",
        ),
        "LabelsAppearance" => array(
          "DataFormatString" => "DataFormatString",
          "Visible" => "Visible",
          "Position" => "Position",
          "RotationAngle" => "RotationAngle",
          "FontColor" => "FontColor",
          "FontFamily" => "FontFamily",
          "FontSize" => "FontSize",
          "FontStyle" => "FontStyle",
          "FontWeight" => "FontWeight"
        ),
        "TooltipsAppearance" => array(
          "DataFormatString" => "DataFormatString",
          "Visible" => "Visible",
          "Position" => "Position",
          "RotationAngle" => "RotationAngle",
          "FontColor" => "FontColor",
          "FontFamily" => "FontFamily",
          "FontSize" => "FontSize",
          "FontStyle" => "FontStyle",
          "FontWeight" => "FontWeight"
        ),
        "MarkersAppearance" => array(
          "Visible" => "Visible",
          "MarkersType" => "MarkersType",
          "BackgroundColor" => "BackgroundColor",
        )
      );
      _setProperties($series, $_ps, $_info);
      $_ms = array(
        "Items" => "ArrayData",
        "setting" => "setting"
      );
      _setMethods($series, $_ms, $_info);
      return $series;
    }
    function _Init() {
    }
    public function set($_st) {
      $this->_settings = _array_merge_replace($this->_settings, $_st);
      return $this;
    }
    public function addSetting($_st) {
      $this->_settings = _array_merge_replace($this->_settings, $_st);
      return $this;
    }
    public function getSetting() {
      return $this->_Serialize();
    }
    function _Serialize() {
      $_valuesToChange = array(
        'Title' => array(
          'Text' => 'encodeUrl',
          'Appearance' => array(
            'Position' => 'toLower',
            'Align' => 'toLower'
          ),
        ),
        'Legend' => array(
          'Appearance' => array(
            'Position' => 'toLower'
          ),
        ),
        'PlotArea' => array(
          'XAxis' => array(
            'MajorTickType' => 'toLower',
            'MinorTickType' => 'toLower',
            'LabelsAppearance' => array(
              'DataFormatString' => 'encodeUrl'
            ),
          ),
          'YAxis' => array(
            'MajorTickType' => 'toLower',
            'MinorTickType' => 'toLower',
            'LabelsAppearance' => array(
              'DataFormatString' => 'encodeUrl'
            ),
          ),
        ),
      );
      _changeValue($this->_settings, $_valuesToChange);
      $_settings = _array_merge_replace(
        array(
          "Title" => $this->Title->_Serialize(),
          "Legend" => $this->Legend->_Serialize(),
          "Height" => $this->Height,
          "Width" => $this->Width,
          "Transitions" => $this->Transitions,
          "Padding" => $this->Padding,
          "FontColor" => $this->FontColor,
          "BackgroundColor" => $this->BackgroundColor,
          "FontFamily" => $this->FontFamily,
          "FontSize" => $this->FontSize,
          "FontStyle" => $this->FontStyle,
          "FontWeight" => $this->FontWeight,
          "NumberFormat" => $this->NumberFormat,
          "DecimalNumber" => $this->DecimalNumber,
          "DecimalSeparator" => $this->DecimalSeparator,
          "ThousandSeparator" => $this->ThousandSeparator,
          "PlotArea" => $this->PlotArea->_Serialize(),
          "ClientEvents" => $this->ClientEvents,
          "ColorArray" => _ColorArray::getColorArray(),
        ), $this->_settings
      );
      return $_settings;
    }
    function Render() {
      if (isset($this->_settings['Id']))
        $this->id = $this->_settings["Id"];
      else if (!isset($this->id))
        $this->id = 'KoolChart1';
      $_script = $this->RenderChart();
      $_is_callback = isset($_POST["__koolajax"]) || isset($_GET["__koolajax"]);
      $_script.= ($_is_callback) ? "" : $this->RegisterScript();
      $_script.="<script type='text/javascript'>";
      $_script.= $this->StartupScript();
      $_script.="</script>";
      return $_script;
    }
    function RenderChart() {
      $this->_positionStyle();
      $_trademark = "\n<!--KoolChart version " . $this->_version . " - www.koolphp.net -->\n";
      $_tpl_main = "{trademark}<div id='{id}' class='koolchart' style='width:{width}px;height:{height}px;position:relative;' data-role='chart'>{settings}</div>";//Uncomment for source code
      $_tpl_viewstate = "<input id='{id}_viewstate' name='{id}_viewstate' type='hidden' />";
      $_tpl_settings = "<input id='{id}_settings' type='hidden' autocomplete='off' value='{value}' />";
      $_settings = _replace("{id}", $this->id, $_tpl_settings);
      $_settings = _replace("{value}", base64_encode(json_encode($this->_Serialize())), $_settings);
      $_main = _replace("{id}", $this->id, $_tpl_main);
      if (true) {
        $_main = _replace("{style}", $this->_style, $_main);
        $_main = _replace("{trademark}", $_trademark, $_main);
        $_main = _replace("{settings}", $_settings, $_main);
        $_main = _replace("{width}", $this->Width, $_main);
        $_main = _replace("{height}", $this->Height, $_main);
        $_main = _replace("{version}", $this->_version, $_main);
      }
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
      $_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KCH')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KCH';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
      $_script = _replace("{style}", $this->_style, $_tpl_script);
      $_script = _replace("{stylepath}", $this->_getStylePath(), $_script);
      return $_script;
    }
    function RegisterScript() {
      $_tpl_script = "<script type='text/javascript'>if(typeof _libKCH=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKCH=1;}</script>";
      $_script = _replace("{src}", _replace(".php", ".js", $this->_getComponentURI()), $_tpl_script); //Do comment to obfuscate
      return $_script;
    }
    function StartupScript() {
      $_tpl_script = "var {id}; function {id}_init(){ {id} = KoolChartJS.newChart('{id}');}";
      $_tpl_script .= "if (typeof(KoolChartJS) !== 'undefined'){{id}_init();}";
      $_tpl_script .= "else{if(typeof(__KCHInits)=='undefined'){__KCHInits=new Array();} __KCHInits.push({id}_init);{register_script}}";
      $_tpl_register_script = "if(typeof(_libKCH)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKCH=1;}";
      $_register_script = _replace("{src}", _replace(".php", ".js", $this->_getComponentURI()), $_tpl_register_script); //Do comment to obfuscate
      $_script = _replace("{id}", $this->id, $_tpl_script);
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
