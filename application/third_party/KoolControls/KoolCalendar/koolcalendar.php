<?php
$_version = "1.9.0.1";
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
if (!class_exists("KoolCalendar",false))
{
function _addDays($_timestamp,$_day)
{
	$_arr = getdate($_timestamp);
	return mktime(0,0,0,$_arr["mon"],$_arr["mday"]+$_day,$_arr["year"]);
}
class _AnimationCalendar
{
	var $Type = "EaseBoth";
	var $Duration = 350;
}
class _Localization
{
	var $_Commands;
	var $_Months_Full;
	var $_Months_Short;
	var $_DaysOfWeek_Full;
	var $_DaysOfWeek_Short;
	var $_DaysOfWeek_Shortest;
	var $_DaysOfWeek_FirstLetter;
	var $_DaysOfWeek_FirstTwoLetters;
	function __construct()
	{
		$this->_Commands = array(
				"Today"=>"Today",
				"OK"=>"OK",
				"Cancel"=>"Cancel"		
		);
		$this->_Months_Full = array(
				"January"		=> "January",
				"February"		=> "February",
				"March"			=> "March",
				"April"			=> "April",
				"May"			=> "May",
				"June"			=> "June",
				"July"			=> "July",
				"August"		=> "August",
				"September"		=> "September",
				"October"		=> "October",
				"November"		=> "November",
				"December"		=> "December"	
		);
		$this->_Months_Short = array(
				"January"		=> "Jan",
				"February"		=> "Feb",
				"March"			=> "Mar",
				"April"			=> "Apr",
				"May"			=> "May",
				"June"			=> "Jun",
				"July"			=> "Jul",
				"August"		=> "Aug",
				"September"		=> "Sep",
				"October"		=> "Oct",
				"November"		=> "Nov",
				"December"		=> "Dec"	
		);
		$this->_DaysOfWeek_Full = array(
				"Sunday"		=> "Sunday",
				"Monday"		=> "Monday",
				"Tuesday"		=> "Tuesday",
				"Wednesday"		=> "Wednesday",
				"Thursday"		=> "Thursday",
				"Friday"		=> "Friday",
				"Saturday"		=> "Saturday"		
		);
		$this->_DaysOfWeek_Short = array(
				"Sunday"		=> "Sun",
				"Monday"		=> "Mon",
				"Tuesday"		=> "Tue",
				"Wednesday"		=> "Wed",
				"Thursday"		=> "Thu",
				"Friday"		=> "Fri",
				"Saturday"		=> "Sat"		
		);
		$this->_DaysOfWeek_Shortest = array(
				"Sunday"		=> "S",
				"Monday"		=> "M",
				"Tuesday"		=> "T",
				"Wednesday"		=> "W",
				"Thursday"		=> "T",
				"Friday"		=> "F",
				"Saturday"		=> "S"		
		);
		$this->_DaysOfWeek_FirstLetter = array();
		$this->_DaysOfWeek_FirstTwoLetters = array();			
		foreach($this->_DaysOfWeek_Full as $k=>$v)
		{
			$this->_DaysOfWeek_FirstLetter[$k] = substr($v,0,1);
			$this->_DaysOfWeek_FirstTwoLetters[$k] = substr($v,0,2);			
		}
	}
	function Load($_xml_path)
	{
		$_xmlDoc = new DOMDocument();
		$_xmlDoc->load($_xml_path);
		$_nodes = $_xmlDoc->getElementsByTagName("Commands");
		if($_nodes->length>0)
		{
			foreach($_nodes->item(0)->attributes as $_attributes)
			{								
				$this->_Commands[$_attributes->name] = $_attributes->value;
			}
		}
		$_nodes = $_xmlDoc->getElementsByTagName("Months_Full");
		if($_nodes->length>0)
		{
			foreach($_nodes->item(0)->attributes as $_attributes)
			{								
				$this->_Months_Full[$_attributes->name] = $_attributes->value;
			}
		}
		$_nodes = $_xmlDoc->getElementsByTagName("Months_Short");
		if($_nodes->length>0)
		{
			foreach($_nodes->item(0)->attributes as $_attributes)
			{								
				$this->_Months_Short[$_attributes->name] = $_attributes->value;
			}
		}
		$_nodes = $_xmlDoc->getElementsByTagName("DaysOfWeek_Full");
		if($_nodes->length>0)
		{
			foreach($_nodes->item(0)->attributes as $_attributes)
			{								
				$this->_DaysOfWeek_Full[$_attributes->name] = $_attributes->value;
			}
		}
		$_nodes = $_xmlDoc->getElementsByTagName("DaysOfWeek_Short");
		if($_nodes->length>0)
		{
			foreach($_nodes->item(0)->attributes as $_attributes)
			{								
				$this->_DaysOfWeek_Short[$_attributes->name] = $_attributes->value;
			}
		}
		$_nodes = $_xmlDoc->getElementsByTagName("DaysOfWeek_Shortest");
		if($_nodes->length>0)
		{
			foreach($_nodes->item(0)->attributes as $_attributes)
			{								
				$this->_DaysOfWeek_Shortest[$_attributes->name] = $_attributes->value;
			}
		}
		$this->_DaysOfWeek_FirstLetter = array();
		$this->_DaysOfWeek_FirstTwoLetters = array();			
		foreach($this->_DaysOfWeek_Full as $k=>$v)
		{
			$this->_DaysOfWeek_FirstLetter[$k] = substr($v,0,1);
			$this->_DaysOfWeek_FirstTwoLetters[$k] = substr($v,0,2);			
		}
	}
}
class _ViewState
{
	var $_Object;
	var $_Data;
	var $_SaveToSession = false;
	function _Init($_object)
	{
		$this->_Object = $_object;
		$this->_SaveToSession = $_object->KeepViewStateInSession;
		$_string = (isset($_POST[$this->_Object->id."_viewstate"]))?$_POST[$this->_Object->id."_viewstate"]:"";
		if ($this->_SaveToSession && $_string=="")
		{
			$_string = (isset($_SESSION[$this->_Object->id."_viewstate"]))?$_SESSION[$this->_Object->id."_viewstate"]:"";
		}
		$_string = _replace("\\","",$_string);
		$this->_Data = json_decode($_string,true);
	}
	function _Render()
	{
		$_statevalue = json_encode($this->_Data);
		if ($this->_SaveToSession)
		{
			$_SESSION[$this->_Object->id."_viewstate"] = $_statevalue;
		}
		$_tpl_viewstate = "<input id='{id}' name='{id}' type='hidden' value='{value}' autocomplete='off' />";
		$_viewstate = _replace("{id}",$this->_Object->id."_viewstate",$_tpl_viewstate);
		$_viewstate = _replace("{value}",$_statevalue,$_viewstate);
		return $_viewstate;
	}
}
class KoolCalendar
{
	var $id;
	var $_version = "1.9.0.1";
	var $styleFolder;
	var $scriptFolder;
	var $_style;	
	var $ClientMode = false;
	var $AjaxEnabled = false;
	var $AjaxLoadingImage;
	var $AjaxHandlePage="";
	var $Orientation = "Horizontal";
	var $MonthLayout = "7x6";//"7x6"|"14x3"|"21x2"
	var $ShowOtherMonthsDays = true;
	var $ShowDayCellToolTips = true;
	var $ShowToday = true;
	var $ShowRowHeader = true;
	var $UseRowHeadersAsSelectors = false;
	var $ShowColumnHeader = true;
	var $UseColumnHeadersAsSelectors = false;
	var $DayNameFormat = "Shortest"; //"Shortest"|"Short"|"FirstLetter"|"FirstTwoLetters"|"Full" 
	var $ShowViewSelector = true;
	var $ViewSelectorText="x";
	var $EnableSelect = true;
	var $EnableMultiSelect = false;
	var $SelectedDates;//Collection of selected timestamps.
	var $_Selected_Dates;
	var $MultiViewColumns = 1;
	var $MultiViewRows = 1;
	var $NavigateAnimation;
	var $TitleFormat; //Not yet.
	var $TitleStyle; // An array contain all the style for title	
	var $CalendarTableStyle;//Define the style for bound table in multiview
	var $DateRangeSeparator = " - ";
	var $FirstDayOfWeek = 0;
	var $FocusedDate; //Timestamp
	var $RangeMinDate;
	var $RangeMaxDate;
	var $ShowNavigation = true;	
	var $ShowFastNavigation = true;
	var $FastNavigationStep;
	var $Width;
	var $Height;
	var $EnableQuickMonthSelect = true;
	var $Localization;
	var $ClientEvents;	
	var $_SpecialDates;
	var $_ViewState;
	var $_ShowTrademark = true;
	function __construct($_id="kcd")
	{
		$this->id = $_id;
		$this->TitleStyle = array();
		$this->_ViewState = new _ViewState();
		$this->SelectedDates = array();
		$this->SpecialDates = array();
		$this->Localization = new _Localization();
		$this->NavigateAnimation = new _AnimationCalendar();
		$this->ClientEvents = array();
	}
	function Init()
	{
		if($this->MultiViewColumns*$this->MultiViewRows<1)
		{
			$this->MultiViewColumns = 1;
			$this->MultiViewRows = 1;
		}
		if($this->FocusedDate===null) $this->FocusedDate = time();
		if($this->SelectedDates===null) $this->SelectedDates = array();
		if($this->FastNavigationStep===null) $this->FastNavigationStep = 3*$this->MultiViewColumns*$this->MultiViewRows;
		$this->_LoadViewState();
	}
	function _LoadViewState()
	{
		$_string = (isset($_POST[$this->id."_viewstate"]))?$_POST[$this->id."_viewstate"]:"";
		$_string = _replace("\\","",$_string);
		$_viewstate = json_decode($_string,true);
		$_selected_dates = array();
		if(isset($_viewstate["SelectedDates"]))
		{
			foreach($_viewstate["SelectedDates"] as $_date_string=>$v)
			{
				array_push($_selected_dates, strtotime($_date_string));	
			}
		}
		$this->SelectedDates = $_selected_dates;
		if(isset($_viewstate["FocusedDate"]))
		{
			$this->FocusedDate = strtotime($_viewstate["FocusedDate"]);
		}
	}	
	function AddSpecialDate($_date,$_disabled = false,$_cssclass = null,$_tooltip = null)
	{
		$_arr = array(	"Disabled"=>$_disabled,
						"CssClass"=>$_cssclass,
						"ToolTip"=>$_tooltip,					
		);
		$this->_SpecialDates[date("n/j/Y",$_date)] = $_arr; 
	}
	function Render()
	{
		$_script="";
		$_script.= $this->RegisterCss();
		$_script.= $this->RenderCalendar();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		if($this->AjaxEnabled && class_exists("UpdatePanel"))
		{
			$_calendar_updatepanel = new UpdatePanel($this->id."_updatepanel");
			$_calendar_updatepanel->content = $_script;
			$_calendar_updatepanel->cssclass = $this->_style."KCD_UpdatePanel";
			if($this->AjaxLoadingImage)
			{
				$_calendar_updatepanel->setLoading($this->AjaxLoadingImage);				
			}
			$_script = $_calendar_updatepanel->Render();	
		}
		return $_script;		
	}
	function RenderCalendar()
	{
     $_tpl_main = "{trademark}<div id='{id}' style class='{style}KCD'>{view}{viewstate}{settings}{QMS}</div>";
		$_tpl_trademark = "\n<!--KoolCalendar version {version} - www.koolphp.net -->\n";
		$_main = _replace("{id}",$this->id,$_tpl_main);
		if(true)
		{
			$_main = _replace("{style}",$this->_style,$_main);
			$_main = _replace("{trademark}",$this->_ShowTrademark?$_tpl_trademark:"",$_main);
			$_main = _replace("{settings}",$this->_RenderSettings(),$_main);
			$_main = _replace("{viewstate}",$this->_RenderViewState(),$_main);
			$_main = _replace("{view}",($this->MultiViewColumns*$this->MultiViewRows>1)?$this->_RenderMultiView():$this->_RenderMonthView(),$_main);
			$_main = _replace("{QMS}",($this->EnableQuickMonthSelect)?$this->_RenderQuickMonthSelector():"",$_main);
			$_main = _replace("{version}",$this->_version,$_main);				
		}
		return $_main;		
	}
	function _RenderSettings()
	{
		/*
		$_settings =array(	"Orientation"=>$this->Orientation,
							"MonthLayout"=>$this->MonthLayout,
							"Width"=>$this->Width,
							"Height"=>$this->Height,
							"ShowToday"=>$this->ShowToday,
							"Today"=>date("n/j/Y"),
							"ShowOtherMonthsDays"=>$this->ShowOtherMonthsDays,
							"ShowDayCellToolTips"=>$this->ShowDayCellToolTips,
							"ShowRowHeader"=>$this->ShowRowHeader,
							"UseRowHeadersAsSelectors"=>$this->UseRowHeadersAsSelectors,
							"ShowColumnHeader"=>$this->ShowColumnHeader,
							"UseColumnHeadersAsSelectors"=>$this->UseColumnHeadersAsSelectors,
							"ShowViewSelector"=>$this->ShowViewSelector,
							"ViewSelectorText"=>$this->ViewSelectorText,
							"EnableSelect"=>$this->EnableSelect,
							"EnableMultiSelect"=>$this->EnableMultiSelect,
							"MultiViewColumns"=>$this->MultiViewColumns,
							"MultiViewRows"=>$this->MultiViewRows,
							"NavigateAnimation"=>$this->NavigateAnimation,
							"RangeMinDate"=>$this->RangeMinDate,
							"RangeMaxDate"=>$this->RangeMaxDate,
							"DateRangeSeparator"=>$this->DateRangeSeparator,
							"FastNavigationStep"=>$this->FastNavigationStep,
							"ClientMode"=>$this->ClientMode,								
							"AjaxEnabled"=>$this->AjaxEnabled,								
							"AjaxHandlePage"=>$this->AjaxHandlePage,								
							"FirstDayOfWeek"=>$this->FirstDayOfWeek,								
							"DayName"=>$_arr_dayname_format,
							"DayNameFull"=>$this->Localization->_DaysOfWeek_Full,
							"MonthsFull"=>$this->Localization->_Months_Full,							
							"ClientEvents"=>$this->ClientEvents,							
		);
		*/
		$_settings =array(	"Width"=>$this->Width,
							"Height"=>$this->Height,
							"Today"=>date("n/j/Y"),
							"EnableSelect"=>$this->EnableSelect,
							"EnableMultiSelect"=>$this->EnableMultiSelect,
							"UseRowHeadersAsSelectors"=>$this->UseRowHeadersAsSelectors,
							"UseColumnHeadersAsSelectors"=>$this->UseColumnHeadersAsSelectors,
							"MultiViewColumns"=>$this->MultiViewColumns,
							"MultiViewRows"=>$this->MultiViewRows,
							"RangeMinDate"=>($this->RangeMinDate!==null)?date("n/j/Y",$this->RangeMinDate):null,
							"RangeMaxDate"=>($this->RangeMaxDate!==null)?date("n/j/Y",$this->RangeMaxDate):null,
							"FastNavigationStep"=>$this->FastNavigationStep,
							"ClientMode"=>$this->ClientMode,								
							"AjaxEnabled"=>$this->AjaxEnabled,								
							"AjaxHandlePage"=>$this->AjaxHandlePage,								
							"ClientEvents"=>$this->ClientEvents,							
		);		
		if($this->ClientMode)
		{
			$_arr_dayname_format = $this->Localization->_DaysOfWeek_Full;
			switch(strtolower($this->DayNameFormat))
			{
				case "short":
					$_arr_dayname_format = $this->Localization->_DaysOfWeek_Short;
					break;
				case "firstletter":
					$_arr_dayname_format = $this->Localization->_DaysOfWeek_FirstLetter;
					break;
				case "firsttwoletters":
					$_arr_dayname_format = $this->Localization->_DaysOfWeek_FirstTwoLetters;
					break;
				case "shortest":
					$_arr_dayname_format = $this->Localization->_DaysOfWeek_Shortest;
					break;				
			}
			$_settings["ShowToday"] = $this->ShowToday;
			$_settings["Orientation"] = $this->Orientation;
			$_settings["MonthLayout"] = $this->MonthLayout;
			$_settings["ShowOtherMonthsDays"] = $this->ShowOtherMonthsDays;
			$_settings["ShowDayCellToolTips"] = $this->ShowDayCellToolTips;
			$_settings["ShowColumnHeader"] = $this->ShowColumnHeader;
			$_settings["ShowRowHeader"] = $this->ShowRowHeader;
			$_settings["ShowViewSelector"] = $this->ShowViewSelector;
			$_settings["ViewSelectorText"] = $this->ViewSelectorText;
			$_settings["NavigateAnimation"] = $this->NavigateAnimation;
			$_settings["DateRangeSeparator"] = $this->DateRangeSeparator;
			$_settings["FirstDayOfWeek"] = $this->FirstDayOfWeek;
			$_settings["DayName"] = $_arr_dayname_format;
			$_settings["DayNameFull"] =$this->Localization->_DaysOfWeek_Full;
			$_settings["MonthsFull"] = $this->Localization->_Months_Full;
		}
		$_tpl_input = "<input id='{id}_settings' type='hidden' value='{value}' autocomplete='off' />";
		$_input = _replace("{id}",$this->id,$_tpl_input);
		$_input = _replace("{value}",json_encode($_settings),$_input);
		return $_input;
	}
	function _RenderViewState()
	{
		$this->_Selected_Dates = array();
		for($i=0;$i<sizeof($this->SelectedDates);$i++)
		{
			$this->_Selected_Dates[date("n/j/Y",$this->SelectedDates[$i])] =1;
		}
		$_viewstate =array( "FocusedDate"=>date("n/j/Y",$this->FocusedDate),
							"SelectedDates"=>$this->_Selected_Dates															
		);
		$_tpl_input = "<input id='{id}_viewstate' name='{id}_viewstate' type='hidden' value='{value}' autocomplete='off' />";
		$_input = _replace("{id}",$this->id,$_tpl_input);
		$_input = _replace("{value}",json_encode($_viewstate),$_input);
		return $_input;
	}
	function _RenderQuickMonthSelector()
	{
		$_month_key = array();
		$_arr_focused_date =  getdate($this->FocusedDate);
		for($i=1;$i<13;$i++)
		{
			array_push($_month_key, date("F",mktime(0,0,0,$i,1,2000)));
		}
		$_tpl_table = "<div id='{id}' class='kcdQMS' style='display:none;'><table border='0' cellspacing='0' ><tbody>{trs}</tbody></table></div>";
		$_tpl_tr = "<tr>{tds}</tr>";
		$_tpl_td = "<td id='{id}' class='kcdMonth'><a>{text}</a></td>";
		$_tpl_td_sep = "<td id='{id}' class='kcdMonth kcdSeparate'><a>{text}</a></td>";
		$_tpl_td_year = "<td id='{id}' class='kcdYear'><a>{text}</a></td>";
		$_tpl_td_dir = "<td id='{id}_qms_{dir}'><a>{text}</a></td>";
		$_tpl_tr_footer = "<tr><td class='kcdButtons' colspan='4'>{today}{ok}{cancel}</td></tr>";
		$_tpl_button = "<input id='{id}_qms_{button}'type='button' value='{value}' class='kcdButton{button}' />";
		$_start_year = $_arr_focused_date["year"]-4;
		$_trs = "";
		for($r=0;$r<6;$r++)
		{
			$_tds = "";
			for($c=0;$c<2;$c++)
			{
				$_td = _replace("{id}",$this->id."_qms_".$_month_key[$r*2+$c],($c==1)?$_tpl_td_sep:$_tpl_td);
				$_td = _replace("{text}",$this->Localization->_Months_Short[$_month_key[$r*2+$c]],$_td);
				$_tds .=$_td;
			}
			if($r<5)
			{
					$_td = _replace("{id}",$this->id."_qms_".($_start_year+$r),$_tpl_td_year);
					$_td = _replace("{text}",$_start_year+$r,$_td);
					$_tds .=$_td;
					$_td = _replace("{id}",$this->id."_qms_".($_start_year+$r+5),$_tpl_td_year);
					$_td = _replace("{text}",$_start_year+$r+5,$_td);
					$_tds .=$_td;
			}
			else
			{
				$_td = _replace("{dir}","Prev",$_tpl_td_dir);
				$_td = _replace("{id}",$this->id,$_td);
				$_td = _replace("{text}","&lt;&lt;",$_td);
				$_tds .=$_td;
				$_td = _replace("{dir}","Next",$_tpl_td_dir);
				$_td = _replace("{id}",$this->id,$_td);
				$_td = _replace("{text}","&gt;&gt;",$_td);
				$_tds .=$_td;				
			}
			$_tr = _replace("{tds}",$_tds,$_tpl_tr);
			$_trs.=$_tr;
		}
		$_tpl_button = _replace("{id}",$this->id,$_tpl_button);	
		$_today_button = _replace("{value}",$this->Localization->_Commands["Today"],$_tpl_button);
		$_today_button = _replace("{button}","Today",$_today_button);
		$_ok_button = _replace("{value}",$this->Localization->_Commands["OK"],$_tpl_button);
		$_ok_button = _replace("{button}","OK",$_ok_button);
		$_cancel_button = _replace("{value}",$this->Localization->_Commands["Cancel"],$_tpl_button);
		$_cancel_button = _replace("{button}","Cancel",$_cancel_button);
		$_tr = _replace("{today}",$_today_button,$_tpl_tr_footer);
		$_tr = _replace("{ok}",$_ok_button,$_tr);
		$_tr = _replace("{cancel}",$_cancel_button,$_tr);
		$_trs.=$_tr;
		$_table = _replace("{id}",$this->id."_qms",$_tpl_table);
		$_table =_replace("{style}",$this->_style,$_table);
		$_table =_replace("{trs}",$_trs,$_table);
		return $_table;
	}
	function _RenderMultiView()
	{
		$_num_month = $this->MultiViewColumns*$this->MultiViewRows;
		$_timestamp = $this->FocusedDate; 
		$_arr = getdate($_timestamp);
		$_from_month = mktime(0,0,0,$_arr["mon"],1,$_arr["year"]);
		$_to_month = mktime(0,0,0,$_arr["mon"]+$_num_month-1,1,$_arr["year"]);
		$_arr_from_month = getdate($_from_month);
		$_arr_to_month = getdate($_to_month);
		$_tpl_table = "<table cellspacing='0' border='0' class='kcdMultiView' style='{width}{height}'>{head}<tbody><tr><td class='kcdMultiViewContainer' style='overflow:hidden;'>{subtable}</td></tr></tbody>{foot}</table>";
		$_tpl_head = "<thead>{trs}</thead>";
		$_tpl_head_nav_tr = "<tr><th colspan='{colspan}' class='kcdTopHeader'>{fastnav}{nav}<span class='kcdNavText {qms}'>{from_month}{sep}{to_month}</span></th></tr>";
		$_tpl_fastnav = "<span class='kcdFastPrev'><a>&lt;&lt;</a></span><span class='kcdFastNext'><a>&gt;&gt;</a></span>";
		$_tpl_nav = "<span class='kcdPrev'><a>&lt;</a></span><span class='kcdNext'><a>&gt;</a></span>";		
		$_tpl_subtable = "<table cellspacing='0' border='0' style='width:100%;'>{body}</table>";
		$_tpl_body = "<tbody>{trs}</tbody>";
		$_tpl_body_tr = "<tr>{tds}</tr>";
		$_tpl_body_td = "<td class='kcdMonthContainer {rowpos} {colpos}'>{monthview}</td>";
		$_head_nav_tr = _replace("{from_month}",$this->Localization->_Months_Full[$_arr_from_month["month"]]." ".$_arr_from_month["year"],$_tpl_head_nav_tr);
		$_head_nav_tr = _replace("{sep}",$this->DateRangeSeparator,$_head_nav_tr);
		$_head_nav_tr = _replace("{to_month}",$this->Localization->_Months_Full[$_arr_to_month["month"]]." ".$_arr_to_month["year"],$_head_nav_tr);
		$_head_nav_tr = _replace("{colspan}",$this->MultiViewColumns,$_head_nav_tr);
		$_head_nav_tr = _replace("{qms}",$this->EnableQuickMonthSelect?"kcdQMSNav":"",$_head_nav_tr);
		$_head_nav_tr = _replace("{fastnav}",($this->ShowFastNavigation)?$_tpl_fastnav:"",$_head_nav_tr);
		$_head_nav_tr = _replace("{nav}",($this->ShowNavigation)?$_tpl_nav:"",$_head_nav_tr);				
		$_head_trs= "";
		$_head_trs.=$_head_nav_tr;
		$_head = _replace("{trs}",$_head_trs,$_tpl_head);
		$_body_trs = "";
		for($r=0;$r<$this->MultiViewRows;$r++)
		{
			$_body_tds = "";
			for($c=0;$c<$this->MultiViewColumns;$c++)
			{
				$_month = mktime(0,0,0,$_arr_from_month["mon"]+$r*$this->MultiViewColumns+$c,1, $_arr_from_month["year"]);
				$_body_td = _replace("{monthview}",$this->_RenderMonthView($_month,false),$_tpl_body_td);
				$_body_td = _replace("{rowpos}",($r==0)?"kcdFirstRow {rowpos}":"{rowpos}",$_body_td);
				$_body_td = _replace("{rowpos}",($r==$this->MultiViewRows-1)?"kcdLastRow {rowpos}":"{rowpos}",$_body_td);
				$_body_td = _replace("{rowpos}","",$_body_td);
				$_body_td = _replace("{colpos}",($c==0)?"kcdFirstCol {colpos}":"{colpos}",$_body_td);
				$_body_td = _replace("{colpos}",($c==$this->MultiViewColumns-1)?"kcdLastCol {colpos}":"{colpos}",$_body_td);
				$_body_td = _replace("{colpos}","",$_body_td);
				$_body_tds.=$_body_td;
			}
			$_body_tr = _replace("{tds}",$_body_tds,$_tpl_body_tr);
			$_body_trs.=$_body_tr;	
		}
		$_body = _replace("{trs}",$_body_trs,$_tpl_body);
		$_subtable = _replace("{body}",$_body,$_tpl_subtable);
		$_foot = "";
		$_table = $_tpl_table;
		$_table = _replace("{width}",($this->Width)?"width:".$this->Width.";":"",$_table);
		$_table = _replace("{height}",($this->Height)?"height:".$this->Height.";":"",$_table);
		$_table = _replace("{head}",$_head,$_table);
		if($this->ClientMode)
		{
			$_table = _replace("{subtable}","<div><table class='kcdTableSlide' style='width:100%;' border='0' cellpadding='0' cellspacing='0'><tr><td>{subtable}</td></tr></table></div>",$_table);
		}		
		$_table = _replace("{subtable}",$_subtable,$_table);
		$_table = _replace("{foot}",$_foot,$_table);
		return $_table;
	}
	function _RenderMonthView($_timestamp = null,$_navigation = true)
	{
		if (!$_timestamp)
		{
			$_timestamp = $this->FocusedDate; 
		}
		$_arr_date = getdate($_timestamp);
		$_tpl_table = "<table cellspacing='0' cellpadding='0' border='0' class='kcdMonthView' style='{width}{height}'>{head}{body}{foot}</table>";
		$_tpl_head = "<thead>{trs}</thead>";
		$_tpl_top_nav = "<tr><th class='kcdTopHeader'>{fastnav}{nav} {text}</th></tr>";
		$_tpl_nav_text = "<span class='kcdNavText {qms}'>{text}</span>";
		$_tpl_fastnav = "<span class='kcdFastPrev'><a>&lt;&lt;</a></span><span class='kcdFastNext'><a>&gt;&gt;</a></span>";
		$_tpl_nav = "<span class='kcdPrev'><a>&lt;</a></span><span class='kcdNext'><a >&gt;</a></span>";		
		$_tpl_body = "<tbody><tr><td class='kcdMain' style='overflow:hidden'>{detail}</td></tr></tbody>";
		$_tpl_foot = "<tfoot>{trs}</tfoot>";
		$_tpl_foot_tr = "<tr>{tds}</tr>";
		$_tpl_foot_td = "<td>{ct}</td>";
		$_top_nav = $_tpl_top_nav;
		if($_navigation)
		{
			$_top_nav = _replace("{text}",$_tpl_nav_text,$_top_nav);
			$_top_nav = _replace("{text}","{text} ".$_arr_date["year"],$_top_nav);
			$_top_nav = _replace("{qms}",$this->EnableQuickMonthSelect?"kcdQMSNav":"",$_top_nav);
		}		
		$_top_nav = _replace("{text}",$this->Localization->_Months_Full[$_arr_date["month"]],$_top_nav);
		$_top_nav = _replace("{fastnav}",($_navigation && $this->ShowFastNavigation)?$_tpl_fastnav:"",$_top_nav);
		$_top_nav = _replace("{nav}",($_navigation && $this->ShowNavigation)?$_tpl_nav:"",$_top_nav);				
		$_head_trs= "";
		$_head_trs.=$_top_nav;
		$_head = _replace("{trs}",$_head_trs,$_tpl_head);
		$_body = $_tpl_body;
		if($_navigation)
		{
			$_body = _replace("{detail}","<div><table class='kcdTableSlide'  border='0' cellpadding='0' cellspacing='0' style='width:100%;'><tr><td>{detail}</td></tr></table></div>",$_body);
		}
		$_body = _replace("{detail}",$this->_RenderMonthDetail($_arr_date),$_body);
		$_foot = "";
		$_table = $_tpl_table;
		$_table = _replace("{width}",($this->Width)?"width:".$this->Width.";":"",$_table);
		$_table = _replace("{height}",($this->Height)?"height:".$this->Height.";":"",$_table);
		$_table = _replace("{head}",$_head,$_table);
		$_table = _replace("{body}",$_body,$_table);
		$_table = _replace("{foot}",$_foot,$_table);
		return $_table;
	}
	function _RenderMonthDetail($_arr_month)
	{
		$_week_key =array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		$_is_vertical_orientation = (strtolower($this->Orientation)=="vertical");
		$_col_num = 7;
		$_row_num = 6;
		switch($this->MonthLayout)
		{
			case "21x2":	
				$_col_num = 21;
				$_row_num = 2;				
				break;
			case "14x3":	
				$_col_num = 14;
				$_row_num = 3;				
				break;
			case "7x6":
				$_col_num = 7;
				$_row_num = 6;				
			default:
				break;
		}
		$_arr_dayname_format = $this->Localization->_DaysOfWeek_Full;
		switch(strtolower($this->DayNameFormat))
		{
			case "short":
				$_arr_dayname_format = $this->Localization->_DaysOfWeek_Short;
				break;
			case "firstletter":
				$_arr_dayname_format = $this->Localization->_DaysOfWeek_FirstLetter;
				break;
			case "firsttwoletters":
				$_arr_dayname_format = $this->Localization->_DaysOfWeek_FirstTwoLetters;
				break;
			case "shortest":
				$_arr_dayname_format = $this->Localization->_DaysOfWeek_Shortest;
				break;				
		}
		if($_is_vertical_orientation)
		{
			$_temp = $_col_num;
			$_col_num = $_row_num;
			$_row_num = $_temp;			
		}
		$_first_day_of_month = mktime(0,0,0,$_arr_month["mon"],1,$_arr_month["year"]);
		$_arr_first_day_of_month = getdate($_first_day_of_month);
		$_diff = $_arr_first_day_of_month["wday"] - $this->FirstDayOfWeek;
		if($_diff<0) $_diff+=7;
		$_first_day_monthview = _addDays($_first_day_of_month,- $_diff);
		$_tpl_table = "<table cellspacing='0' border='0' class='kcdMainTable'>{head}{body}</table>";
		$_tpl_head = "<thead><tr>{th_selector}{ths}</tr></thead>";
		$_tpl_head_th = "<th class='kcdColHeader' title='{title}'>{text}</th>";
		$_tpl_head_th_selector = "<th class='kcdColHeader kcdViewSelector'>{text}</th>";
		$_tpl_body = "<tbody>{trs}</tbody>";
		$_tpl_body_tr = "<tr>{th}{tds}</tr>";
		$_tpl_body_th = "<th class='kcdRowHeader' title='{title}'>{text}</th>";
		$_tpl_body_td = "<td abbr='{abbr}' class='{class}' {title}><a>{text}</a></td>";
		$_head_ths = "";
		for($c=0;$c<$_col_num;$c++)
		{
			$_head_th = "";
			if($_is_vertical_orientation)
			{
				$_day = _addDays($_first_day_monthview,$c*$_row_num);
				$_arr_day = getdate($_day);
				$_week_year = ceil($_arr_day["yday"]/7) +1;
				if ($_week_year>52)
				{
					$_week_year = 1;
				}
				$_head_th = _replace("{text}",$_week_year,$_tpl_head_th);
				$_head_th = _replace("{title}",$_week_year,$_head_th);
			}
			else
			{
				$_wday = ($this->FirstDayOfWeek+$c)%7;
				$_head_th = _replace("{title}",$this->Localization->_DaysOfWeek_Full[$_week_key[$_wday]],$_tpl_head_th);
				$_head_th = _replace("{text}",$_arr_dayname_format[$_week_key[$_wday]],$_head_th);				
			}
			$_head_ths.=$_head_th;
		}
		$_head_th_selector = "";
		if($this->ShowRowHeader)
		{
			if($this->ShowViewSelector)
			{
				$_head_th_selector = _replace("{text}",$this->ViewSelectorText,$_tpl_head_th_selector);
			}
			else
			{
				$_head_th_selector = _replace("{text}","",$_tpl_head_th);
				$_head_th_selector = _replace("{title}","",$_head_th_selector);
			}			
		}
		$_head = _replace("{ths}",$_head_ths,$_tpl_head);
		$_head = _replace("{th_selector}",$_head_th_selector,$_head);
		$_body_trs = "";
		for($r=0;$r<$_row_num;$r++)
		{
			$_body_tds = "";
			for($c=0;$c<$_col_num;$c++)
			{
				$_day = _addDays($_first_day_monthview,$r*$_col_num+$c);
				if($_is_vertical_orientation)
				{
					$_day = _addDays($_first_day_monthview,$c*$_row_num+$r);
				}
				$_arr_day = getdate($_day);
				$_is_show = ($_arr_day["mon"]!=$_arr_month["mon"])?($this->ShowOtherMonthsDays?true:false):true;
				$_body_td = _replace("{abbr}",$_is_show?date("n/j/Y",$_day):"",$_tpl_body_td);
				$_body_td = _replace("{text}",$_is_show?$_arr_day["mday"]:"",$_body_td);
				$_body_td = _replace("{class}",$_is_show?"kcdDay {class}":"",$_body_td);				
				$_body_td = _replace("{class}",($_arr_day["mon"]!=$_arr_month["mon"])?"kcdOtherMonth {class}":"{class}",$_body_td);
				$_body_td = _replace("{class}",($_arr_day["wday"]==0 || $_arr_day["wday"]==6)?"kcdWeekend {class}":"{class}",$_body_td);
				$_body_td = _replace("{class}",isset($this->_Selected_Dates[date("n/j/Y",$_day)])?"kcdSelected {class}":"{class}",$_body_td);
				$_body_td = _replace("{class}",($this->ShowToday && date("n/j/Y",$_day)==date("n/j/Y"))?"kcdToday {class}":"{class}",$_body_td);
				if($this->RangeMaxDate!==null)
				{
					if($_day>$this->RangeMaxDate)
					{
						$_body_td = _replace("{class}","kcdDisabled {class}",$_body_td);						
					}
				}				
				if($this->RangeMinDate!==null)
				{
					if($_day<$this->RangeMinDate)
					{
						$_body_td = _replace("{class}","kcdDisabled {class}",$_body_td);						
					}
				}				
				if(isset($this->_SpecialDates[date("n/j/Y",$_day)]))
				{
					$_prop = $this->_SpecialDates[date("n/j/Y",$_day)];
					$_body_td = _replace("{class}",($_prop["Disabled"])?"kcdDisabled {class}":"{class}",$_body_td);
					$_body_td = _replace("{class}",($_prop["CssClass"]!==null)?$_prop["CssClass"]." {class}":"{class}",$_body_td);
					$_body_td = _replace("{title}",($_prop["ToolTip"]!==null)?"title='".$_prop["ToolTip"]."'":"",$_body_td);
				}
				$_body_td = _replace("{class}","",$_body_td);
				$_body_td = _replace("{title}",$this->ShowDayCellToolTips?"title='".date("l, F d, Y",$_day)."'":"",$_body_td);
				$_body_tds.=$_body_td;
			}
			$_body_th = "";
			if($this->ShowRowHeader)
			{
				if($_is_vertical_orientation)
				{
					$_wday = ($this->FirstDayOfWeek+$r)%7;
					$_body_th = _replace("{title}",$this->Localization->_DaysOfWeek_Full[$_week_key[$_wday]],$_tpl_body_th);
					$_body_th = _replace("{text}",$_arr_dayname_format[$_week_key[$_wday]],$_body_th);				
				}
				else
				{
					$_day = _addDays($_first_day_monthview,$r*$_col_num);
					$_arr_day = getdate($_day);
					$_week_year = ceil($_arr_day["yday"]/7) +1;
					if ($_week_year>52)
					{
						$_week_year = 1;
					}
					$_body_th = _replace("{text}",$_week_year,$_tpl_body_th);
					$_body_th = _replace("{title}",$_week_year,$_body_th);
				}
			}
			$_body_tr = _replace("{tds}",$_body_tds,$_tpl_body_tr);
			$_body_tr = _replace("{th}",$_body_th,$_body_tr);
			$_body_trs.=$_body_tr;			
		}
		$_body = _replace("{trs}",$_body_trs,$_tpl_body);
		$_table = _replace("{head}",$this->ShowColumnHeader?$_head:"",$_tpl_table);
		$_table = _replace("{body}",$_body,$_table);
		return $_table;
	}
	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKCD=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKCD=1;}</script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script); //Do comment to obfuscate
		return $_script;
	}	
	function _positionStyle()
	{
		$this->styleFolder = _replace("\\","/",$this->styleFolder);
		$_styleFolder = trim($this->styleFolder,"/");
		$_lastpos = strrpos($_styleFolder,"/");
		$this->_style = substr($_styleFolder,($_lastpos?$_lastpos:-1)+1);
	}
	function RegisterCss()
	{
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KCD')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KCD';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}	
	function StartupScript()
	{
		$_tpl_script  = "var {id}; function {id}_init(){ {id}= new KoolCalendar('{id}');}";
		$_tpl_script .= "if (typeof(KoolCalendar)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KCDInits)=='undefined'){__KCDInits=new Array();} __KCDInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKCD)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKCD=1;}";
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script); //Do comment to obfuscate
		$_script = _replace("{id}",$this->id,$_tpl_script);
		$_script = _replace("{register_script}",$_register_script,$_script);
		return $_script;
	}	
	function _getComponentURI()
	{
		if ($this->scriptFolder=="")
		{
			$_root = _getRoot();
			$_file = substr(_replace("\\","/",__FILE__),strlen($_root));
			return $_file;			
		}
		else
		{
			$_file = _replace("\\","/",__FILE__);
			$_file = $this->scriptFolder.substr($_file,strrpos($_file,"/"));
			return $_file;
		}
	}
	function _getStylePath()
	{
		$_com_uri = $this->_getComponentURI();
		$_styles_folder = _replace(strrchr($_com_uri,"/"),"",$_com_uri)."/styles";
		return $_styles_folder;
	}			
}
class KoolTimeView
{
	var $id;
	var $_version = "1.9.0.0";
	var $styleFolder;
	var $scriptFolder;
	var $_style;	
	var $StartTime;
	var $EndTime;
	var $Interval;
	var $NumberOfColumns = 4;
	var $HeaderText = "Time View";
	var $Orientation="Horizontal"; //"Horizontal"|"Vertical"
	var $TimeFormat = "g:i A";
	var $ClientEvents;
	var $_ShowTrademark = true;
	function __construct($_id="ktview")
	{
		$this->id = $_id;
		$this->StartTime = mktime(0,0,0);
		$this->EndTime = mktime(23,0,0);
		$this->Interval = mktime(1,0,0);
		$this->ClientEvents = array();
	}
	function Render()
	{
		$_script="";
		$_script.= $this->RegisterCss();
		$_script.= $this->RenderTimeView();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";		
		return $_script;		
	}
	function _RenderSettings()
	{
		$_settings =array("ClientEvents"=>$this->ClientEvents
		);
		$_tpl_input = "<input id='{id}_settings' type='hidden' value='{value}' autocomplete='off' />";
		$_input = _replace("{id}",$this->id,$_tpl_input);
		$_input = _replace("{value}",json_encode($_settings),$_input);
		return $_input;		
	}
	function RenderTimeView()
	{
		 $_tpl_main = "{0}{trademark}<div id='{id}' style class='{style}KCD'>{table}{settings}{1}</div>{2}";		
		$_tpl_main = "{trademark}<div id='{id}' style class='{style}KCD'>{table}{settings}</div>";		
		$_tpl_trademark = "\n<!--KoolTimeView version {version} - www.koolphp.net -->\n";
		$_tpl_table = "<table class='ktmTable' border='0' cellspacing='0'>{head}{body}</table>";
		$_tpl_head = "<thead><tr><th class='ktmHeader' colspan='{colspan}'>{text}</th></tr></thead>";
		$_tpl_body = "<tbody>{trs}</tbody>";
		$_tpl_tr = "<tr>{tds}</tr>";
		$_tpl_td = "<td class='{time} {colpos}' abbr='{abbr}'><a>{text}</a></td>";
		$_head = _replace("{text}",$this->HeaderText,$_tpl_head);
		$_head = _replace("{colspan}",$this->NumberOfColumns,$_head);
		$_base = mktime(0,0,0);		
		$_total_time_cells = floor(($this->EndTime - $this->StartTime)/($this->Interval - $_base))+1;
		$_total_rows = ceil($_total_time_cells/$this->NumberOfColumns);
		$_trs = "";		
		for($r=0;$r<$_total_rows;$r++)
		{
			$_tds = "";
			for($c=0;$c<$this->NumberOfColumns;$c++)
			{
				$_time = $this->StartTime + ($r*$this->NumberOfColumns+$c)*($this->Interval - $_base);
				if(strtolower($this->Orientation)=="vertical")
				{
					$_time = $this->StartTime + ($c*$_total_rows+$r)*($this->Interval - $_base);
				}
				$_is_show = ($_time<=$this->EndTime);
				$_td = _replace("{text}",$_is_show?date($this->TimeFormat,$_time):"",$_tpl_td);
				$_td = _replace("{abbr}",$_is_show?date("H:i:s",$_time):"",$_td);
				$_td = _replace("{time}",$_is_show?"ktmTime":"ktmNoTime",$_td);
				if($c==0)
				{
					$_td = _replace("{colpos}","ktmFirst",$_td);
				}
				else if($c == $this->NumberOfColumns-1)
				{
					$_td = _replace("{colpos}","ktmLast",$_td);
				}
				else
				{
					$_td = _replace("{colpos}","",$_td);
				}
				$_tds.=$_td;
			}
			$_tr = _replace("{tds}",$_tds,$_tpl_tr);
			$_trs.=$_tr;
		}
		$_body = _replace("{trs}",$_trs,$_tpl_body);
		$_table = _replace("{head}",$_head,$_tpl_table);
		$_table = _replace("{body}",$_body,$_table);
		$_main = _replace("{id}",$this->id,$_tpl_main);
		$_main = _replace("{style}",$this->_style,$_main);
		$_main = _replace("{trademark}",$this->_ShowTrademark?$_tpl_trademark:"",$_main);
		$_main = _replace("{table}",$_table,$_main);
		if(true)
		{
			$_main = _replace("{settings}",$this->_RenderSettings(),$_main);			
		}
		$_main = _replace("{version}",$this->_version,$_main);
		return $_main;		
	}
	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKCD=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKCD=1;}</script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script); //Do comment to obfuscate
		return $_script;
	}	
	function _positionStyle()
	{
		$this->styleFolder = _replace("\\","/",$this->styleFolder);
		$_styleFolder = trim($this->styleFolder,"/");
		$_lastpos = strrpos($_styleFolder,"/");
		$this->_style = substr($_styleFolder,($_lastpos?$_lastpos:-1)+1);
	}
	function RegisterCss()
	{
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KCD')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KCD';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}	
	function StartupScript()
	{
		/*
		$_tpl_script = "var {id} = new KoolTimeView('{id}');";
		$_script = _replace("{id}",$this->id,$_tpl_script);				
		return $_script;
		*/
		$_tpl_script  = "var {id}; function {id}_init(){ {id}= new KoolTimeView('{id}');}";
		$_tpl_script .= "if (typeof(KoolTimeView)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KCDInits)=='undefined'){__KCDInits=new Array();} __KCDInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKCD)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}';_head.appendChild(_script);_libKCD=1;}";
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script); //Do comment to obfuscate
		$_script = _replace("{id}",$this->id,$_tpl_script);
		$_script = _replace("{register_script}",$_register_script,$_script);
		return $_script;		
	}	
	function _getComponentURI()
	{
		if ($this->scriptFolder=="")
		{
			$_root = _getRoot();
			$_file = substr(_replace("\\","/",__FILE__),strlen($_root));
			return $_file;			
		}
		else
		{
			$_file = _replace("\\","/",__FILE__);
			$_file = $this->scriptFolder.substr($_file,strrpos($_file,"/"));
			return $_file;
		}
	}
	function _getStylePath()
	{
		$_com_uri = $this->_getComponentURI();
		$_styles_folder = _replace(strrchr($_com_uri,"/"),"",$_com_uri)."/styles";
		return $_styles_folder;
	}			
}
class _CalendarSettings
{
	var $Orientation = "Horizontal";
	var $MonthLayout = "7x6";//"7x6"|"14x3"|"21x2"
	var $ShowOtherMonthsDays = true;
	var $ShowDayCellToolTips = true;
	var $ShowToday = true;
	var $ShowColumnHeader = true;
	var $ShowRowHeader = true;
	var $ShowNavigation = true;	
	var $ShowFastNavigation = true;
	var $FastNavigationStep = 3;
	var $MultiViewColumns = 1;
	var $MultiViewRows = 1;
	var $FirstDayOfWeek = 0;
	var $FocusedDate; //Timestamp	
	var $RangeMinDate;
	var $RangeMaxDate;
	var $NavigateAnimation;
	function __construct()
	{
		$this->NavigateAnimation = new _AnimationCalendar();
	}		
}
class _TimeViewSettings
{
	var $StartTime;
	var $EndTime;
	var $Interval;
	var $NumberOfColumns = 3;	
	var $HeaderText = "Time Picker";
	var $Orientation="Horizontal"; //"Horizontal"|"Vertical"
	var $TimeFormat = "g:i A";
	function __construct()
	{
		$this->StartTime = mktime(0,0,0);
		$this->EndTime = mktime(23,0,0);
		$this->Interval = mktime(1,0,0);		
	}
}
class KoolDateTimePicker
{
	var $id;
	var $_version = "1.9.0.0";
	var $styleFolder;
	var $scriptFolder;
	var $_style;	
	var $CalendarSettings;
	var $TimeViewSettings;
	var $_EnableDatePicker = true;
	var $_EnableTimePicker = true;		
	var $_Calendar;//KoolCalendar object 
	var $_TimeView;//KoolTimeView object
	var $Width = "160px";
	var $CssStyles;
	var $OffsetLeft = 0;
	var $OffsetTop = 0;
	var $DateFormat = "m/d/Y";
	var $TimeFormat = "g:i A";
	var $Value = "";
	var $ClientEvents;
	var $Localization;
	function __construct($_id="kdtp")
	{
		$this->id = $_id;
		$this->CssStyles = array();
		$this->CalendarSettings = new _CalendarSettings();
		$this->TimeViewSettings = new _TimeViewSettings();
		$this->ClientEvents = array();
		$this->Localization = new _Localization();		
	}	
	function Init()
	{
		if($this->_EnableDatePicker)
		{
			$this->_Calendar = new KoolCalendar($this->id."_calendar");
			$this->_Calendar->ClientMode = true;
			$this->_Calendar->styleFolder = $this->styleFolder;
			$this->_Calendar->scriptFolder = $this->scriptFolder;
			$this->_Calendar->_ShowTrademark = false;
			$this->_Calendar->ClientMode = true;
			$this->_Calendar->ShowViewSelector = false;
			$this->_Calendar->ViewSelectorText="";
			$this->_Calendar->Orientation = $this->CalendarSettings->Orientation;
			$this->_Calendar->MonthLayout = $this->CalendarSettings->MonthLayout;
			$this->_Calendar->ShowOtherMonthsDays = $this->CalendarSettings->ShowOtherMonthsDays;
			$this->_Calendar->ShowDayCellToolTips = $this->CalendarSettings->ShowDayCellToolTips;
			$this->_Calendar->ShowColumnHeader = $this->CalendarSettings->ShowColumnHeader;
			$this->_Calendar->ShowRowHeader = $this->CalendarSettings->ShowRowHeader;
			$this->_Calendar->ShowNavigation = $this->CalendarSettings->ShowNavigation;
			$this->_Calendar->ShowFastNavigation = $this->CalendarSettings->ShowFastNavigation;
			$this->_Calendar->FastNavigationStep = $this->CalendarSettings->FastNavigationStep;
			$this->_Calendar->FirstDayOfWeek = $this->CalendarSettings->FirstDayOfWeek;
			$this->_Calendar->FocusedDate = $this->CalendarSettings->FocusedDate;
			$this->_Calendar->RangeMinDate = $this->CalendarSettings->RangeMinDate;
			$this->_Calendar->RangeMaxDate = $this->CalendarSettings->RangeMaxDate;
			$this->_Calendar->ShowToday = $this->CalendarSettings->ShowToday;			
			$this->_Calendar->MultiViewColumns = $this->CalendarSettings->MultiViewColumns;			
			$this->_Calendar->MultiViewRows = $this->CalendarSettings->MultiViewRows;
			$this->_Calendar->NavigateAnimation = $this->CalendarSettings->NavigateAnimation;
			$this->_Calendar->Localization = $this->Localization;
			$this->_Calendar->Init();
		}
		if($this->_EnableTimePicker)
		{
			$this->_TimeView = new KoolTimeView($this->id."_timeview"); 
			$this->_TimeView->styleFolder = $this->styleFolder;
			$this->_TimeView->scriptFolder = $this->scriptFolder;
			$this->_TimeView->_ShowTrademark = false;
			$this->_TimeView->StartTime = $this->TimeViewSettings->StartTime;
			$this->_TimeView->EndTime = $this->TimeViewSettings->EndTime;
			$this->_TimeView->Interval = $this->TimeViewSettings->Interval;
			$this->_TimeView->NumberOfColumns = $this->TimeViewSettings->NumberOfColumns;
			$this->_TimeView->HeaderText = $this->TimeViewSettings->HeaderText;
			$this->_TimeView->Orientation = $this->TimeViewSettings->Orientation;
			$this->_TimeView->TimeFormat = $this->TimeViewSettings->TimeFormat;
		}
		if(isset($_POST[$this->id]))
		{
			$this->Value = $_POST[$this->id];
		}		
	}
	function Render()
	{
		$_script="";
		$_script.= $this->RegisterCss();
		$_script.= $this->RenderDateTimePicker();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;				
	}
	function _RenderSettings()
	{
		$_settings =array(	"OffsetLeft"=>$this->OffsetLeft,
							"OffsetTop"=>$this->OffsetTop,
							"DateFormat"=>$this->DateFormat,
							"TimeFormat"=>$this->TimeFormat,
							"ClientEvents"=>$this->ClientEvents									
		);
		$_tpl_input = "<input id='{id}_settings' type='hidden' value='{value}' autocomplete='off' />";
		$_input = _replace("{id}",$this->id,$_tpl_input);
		$_input = _replace("{value}",json_encode($_settings),$_input);
		return $_input;		
	}
	function RenderDateTimePicker()
	{
		 $_tpl_main = "{0}{trademark}<div id='{id}_bound' style='{stylecss}' class='{style}KCD'>{view}{datepicker}{timepicker}{settings}{1}</div>{2}";		
		$_tpl_main = "{trademark}<div id='{id}_bound' style='{stylecss}' class='{style}KCD'>{view}{datepicker}{timepicker}{settings}</div>";		
		$_tpl_trademark = "\n<!--KoolDateTimePicker version {version} - www.koolphp.net -->\n";
		$_tpl_datepicker = "<div id='{id}_datepicker' class='kcdDatePicker' style='display:none;position:absolute;'>{calendar}</div>";
		$_tpl_timepicker = "<div id='{id}_timepicker' class='kcdTimePicker' style='display:none;position:absolute;'>{timeview}</div>";
		$_tpl_view = "<table border='0' cellpadding='0' cellspacing='0' style='width:100%;'><tr><td class='kcdInput'><div><input id='{id}' name='{id}' value='{value}' style='width:100%;{style}' type='text' autocomplete='off'/></div></td>{dateopener}{timeopener}</tr></table>";
		$_datepicker = "";
		$_dateopener = "";
		if($this->_EnableDatePicker)
		{
			$_datepicker = _replace("{id}",$this->id,$_tpl_datepicker);
			$_datepicker = _replace("{calendar}",$this->_Calendar->Render(),$_datepicker);
			$_dateopener = "<td class='kcdPicker'><a id='{id}_dateopener' class='kcdDateOpener'></a></td>";
			$_dateopener = _replace("{id}",$this->id,$_dateopener);
		}
		$_timepicker = "";
		$_timeopener = "";
		if($this->_EnableTimePicker)
		{
			$_timepicker = _replace("{id}",$this->id,$_tpl_timepicker);
			$_timepicker = _replace("{timeview}",$this->_TimeView->Render(),$_timepicker);
			$_timeopener = "<td class='kcdPicker'><a id='{id}_timeopener' class='kcdTimeOpener'></a></td>";
			$_timeopener = _replace("{id}",$this->id,$_timeopener);
		}
		$_view = _replace("{id}",$this->id,$_tpl_view);
		$_view = _replace("{dateopener}",$_dateopener,$_view);
		$_view = _replace("{timeopener}",$_timeopener,$_view);
		foreach($this->CssStyles as $k=>$v)
		{
			$_view = _replace("{style}",$k.":".$v.";{style}",$_view);			
		}
		$_view = _replace("{style}","",$_view);
		$_view = _replace("{value}",$this->Value,$_view);
		$_main = _replace("{id}",$this->id,$_tpl_main);
		$_main = _replace("{style}",$this->_style,$_main);		
		$_main = _replace("{view}",$_view,$_main);		
		$_main = _replace("{datepicker}",$_datepicker,$_main);		
		$_main = _replace("{timepicker}",$_timepicker,$_main);	
		if(true)
		{
			$_main = _replace("{settings}",$this->_RenderSettings(),$_main);				
		}
		$_main = _replace("{stylecss}",($this->Width!==null)?"width:".$this->Width.";":"",$_main);
		$_main = _replace("{trademark}",$_tpl_trademark,$_main);
		$_main = _replace("{version}",$this->_version,$_main);
		return $_main;		
	}
	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKCD=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKCD=1;}</script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script); //Do comment to obfuscate
		return $_script;
	}	
	function _positionStyle()
	{
		$this->styleFolder = _replace("\\","/",$this->styleFolder);
		$_styleFolder = trim($this->styleFolder,"/");
		$_lastpos = strrpos($_styleFolder,"/");
		$this->_style = substr($_styleFolder,($_lastpos?$_lastpos:-1)+1);
	}
	function RegisterCss()
	{
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KCD')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KCD';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}	
	function StartupScript()
	{
		/*
		$_tpl_script = "var {id} = new KoolDateTimePicker('{id}',{EnableDatePicker},{EnableTimePicker});";
		$_script = _replace("{id}",$this->id,$_tpl_script);				
		$_script = _replace("{EnableDatePicker}",$this->_EnableDatePicker?"1":"0",$_script);				
		$_script = _replace("{EnableTimePicker}",$this->_EnableTimePicker?"1":"0",$_script);				
		return $_script;
		*/
		$_tpl_script  = "var {id}; function {id}_init(){ {id}= new KoolDateTimePicker('{id}',{EnableDatePicker},{EnableTimePicker});}";
		$_tpl_script .= "if (typeof(KoolDateTimePicker)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KCDInits)=='undefined'){__KCDInits=new Array();} __KCDInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKCD)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}';_head.appendChild(_script);_libKCD=1;}";
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script); //Do comment to obfuscate
		$_script = _replace("{id}",$this->id,$_tpl_script);
		$_script = _replace("{EnableDatePicker}",$this->_EnableDatePicker?"1":"0",$_script);				
		$_script = _replace("{EnableTimePicker}",$this->_EnableTimePicker?"1":"0",$_script);				
		$_script = _replace("{register_script}",$_register_script,$_script);
		return $_script;		
	}	
	function _getComponentURI()
	{
		if ($this->scriptFolder=="")
		{
			$_root = _getRoot();
			$_file = substr(_replace("\\","/",__FILE__),strlen($_root));
			return $_file;			
		}
		else
		{
			$_file = _replace("\\","/",__FILE__);
			$_file = $this->scriptFolder.substr($_file,strrpos($_file,"/"));
			return $_file;
		}
	}
	function _getStylePath()
	{
		$_com_uri = $this->_getComponentURI();
		$_styles_folder = _replace(strrchr($_com_uri,"/"),"",$_com_uri)."/styles";
		return $_styles_folder;
	}			
}
class KoolDatePicker extends KoolDateTimePicker
{
	var $_EnableTimePicker = false; //TimePicker ->Disable the time
	var $TimeFormat = "";
	function Init()
	{
		parent::Init();
		$this->TimeFormat = "";
	}		
}
class KoolTimePicker extends KoolDateTimePicker
{
	var $_EnableDatePicker = false; //TimePicker ->Disable the date
	var $DateFormat = "";
	function Init()
	{
		parent::Init();
		$this->DateFormat = "";
	}		
}
}
?>
