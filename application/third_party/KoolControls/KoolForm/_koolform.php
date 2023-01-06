<?php
//$_version = "1.6.0.0";

if(!defined('KOOLPHPCOMMON'))
{
	function _replace($key,$rep,$str)
	{
		return str_replace($key,$rep,$str);
	}
	function _getRoot()
	{
		//---Root-----	
		$_php_self = _replace("\\","/",strtolower($_SERVER["SCRIPT_NAME"]));// /koolphpsuite/koolajax/example_callback.php		
		$_php_self = _replace(strrchr($_php_self,"/"),"",$_php_self);		
		$_realpath = _replace("\\","/",realpath("."));// D:\xampplite\htdocs\KoolPHPSuite\KoolAjax		
		$_root = _replace($_php_self,"",strtolower($_realpath));
		//---Root-----
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

if (!class_exists("KoolForm",false))
{

/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
	function _slash_encode($_val)
	{
		return addslashes($_val);		
	}
	function _md5($_text)
	{
		return md5($_text);
	}
	function _urlencode($_val)
	{
		return _replace("+"," ",urlencode($_val));
	}	
	

class _FormViewState
{
	var $_Control;
	var $_Data;
	var $_Encode = true;
	var $_Available = false;
	function _Init($_control)
	{
		$this->_Control = $_control;
		$_string = (isset($_POST[$this->_Control->_UniqueID."_viewstate"]))?$_POST[$this->_Control->_UniqueID."_viewstate"]:"";
		
		if($_string!="")
		{
			
			$this->_Available = true;
			if($this->_Encode)
			{
				$_string = base64_decode($_string);				
			}
			
		}
		
		$_string = _replace("\\","",$_string);
		//echo ($this->_Control->_UniqueID=="txtPassword")?$_string:"";
		$this->_Data = json_decode($_string,true);
		//echo ($this->_Control->_UniqueID=="txtPassword")?$this->_Available:"";
		
	}
	function _Render()
	{
		$_statevalue = json_encode($this->_Data);
		if($this->_Encode) $_statevalue = base64_encode($_statevalue);
		$_tpl_viewstate = "<input id='{id}' name='{id}' type='hidden' value='{value}' autocomplete='off' />";
		$_viewstate = _replace("{id}",$this->_Control->_UniqueID."_viewstate",$_tpl_viewstate);
		$_viewstate = _replace("{value}",$_statevalue,$_viewstate);
		return $_viewstate;
	}
}

class _KoolFormElement
{
	var $_UniqueID;
	var $_Form;
	var $_ViewState;
	var $ClientEvents;
	var $StatePersistent;
	var $Enabled;
	var $CssClass;
	var $ToolTip;
	var $Validate =false;
	function __construct($_id)
	{
		$this->_UniqueID = $_id;
		$this->ClientEvents = array();
		$this->_ViewState = new _FormViewState();
	}
	
	function _Init($_form)
	{
		$this->_Form = $_form;
		$this->_ViewState->_Init($this);
		if($this->StatePersistent===null) $this->StatePersistent = $this->_Form->StatePersistent;
		if($this->StatePersistent)
		{
			$this->_LoadViewState();			
		}
	}
	function _LoadViewState()
	{
		if($this->_ViewState->_Available)
		{
			$_state = $this->_ViewState->_Data;
		}
	}
	
	function _SaveViewState()
	{
		$this->_ViewState->_Data = array(	"ObjectClass"=>$this->_GetClass(),
															"ClientEvents"=>$this->ClientEvents,
															"Form"=>$this->_Form->_UniqueID,
															"Enabled"=>$this->Enabled,
															"CssClass"=>$this->CssClass,
															"ToolTip"=>$this->ToolTip,															
															"StatePersistent"=>$this->StatePersistent,
															"Validate"=>$this->Validate,															
		);
	}
	function _RenderViewState()
	{
		$this->_SaveViewState();
		return $this->_ViewState->_Render();
	}
	function _GetClass()
	{
		return "";
	}
}



class _KoolValidator extends _KoolFormElement
{
	var $TargetId;
	var $ErrorMessage;
	var $Validate = true;

	function _Init($_form)
	{
		parent::_Init($_form);
		if($this->_UniqueID===null) $this->_UniqueID = $this->TargetId."validator";
	}
	
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["TargetId"] = $this->TargetId;
		$_state["ErrorMessage"] = $this->ErrorMessage;
	}
	function Render()
	{
		$_tpl_span = "<span id='{id}'> </span> {viewstate}";
		$_span = _replace("{id}",$this->_UniqueID,$_tpl_span);
		$_span = _replace("{viewstate}",$this->_RenderViewState(),$_span);		
		return $_span;
	}
}

class KoolRequiredFieldValidator extends _KoolValidator
{
	var $ErrorMessage = "*";
	function _GetClass()
	{
		return "KoolRequiredFieldValidator";
	}
}
class KoolRangeValidator extends _KoolValidator
{
	var $MinValue;
	var $MaxValue;
	
	var $Type="Integer";//"Integer"|"Date"
	
	function __construct($_id=null,$_min=null,$_max=null)
	{
		parent::__construct($_id);
		if($_min!==null) $this->MinValue = $_min;
		if($_max!==null) $this->MaxValue = $_max;
	}
	
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;
		$_state["MinValue"] = $this->MinValue;
		$_state["MaxValue"] = $this->MaxValue;
		$_state["Type"] = $this->Type;
	}
	function _GetClass()
	{
		return "KoolRangeValidator";
	}
}
class KoolRegularExpressionValidator extends _KoolValidator
{
	var $Expression;
	function __construct($_id=null,$_expression=null)
	{
		parent::__construct($_id);
		if($_expression!==null) $this->Expression = $_expression;		
	}
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["Expression"] = $this->Expression;
	}
	function _GetClass()
	{
		return "KoolRegularExpressionValidator";
	}
}
class KoolCustomValidator extends _KoolValidator
{
	var $ClientValidationFunction;
	function __construct($_id=null,$_function=null)
	{
		parent::__construct($_id);
		$this->ClientValidationFunction = $_function;		
	}
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["ClientValidationFunction"] = $this->ClientValidationFunction;
	}
	function _GetClass()
	{
		return "KoolCustomValidator";
	}
}





class _KoolText extends _KoolFormElement
{
	var $Value="";
	var $EmptyMessage="";
	var $AutoComplete=false;
	var $Width;
	var $Height;	
	

	
	function _LoadViewState()
	{
		parent::_LoadViewState();
		// + Base on UniqueID, get viewstate and assign correctly.
		if ($this->_ViewState->_Available)
		{
			$_state = $this->_ViewState->_Data;
			$this->Value = urldecode($_state["Value"]) ;
		}
	}	
	
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["Value"] = _urlencode($this->Value);
		$_state["EmptyMessage"] = $this->EmptyMessage;
		$_state["AutoComplete"] = $this->AutoComplete;
		$_state["Width"] = $this->Width;
		$_state["Height"] = $this->Height;		
	}
	function Render()
	{
		$_tpl_textbox = "<input type='text' id='{id}' name='{id}' value='{value}' style='{width}{height}' /> {viewstate}";
		$_textbox = _replace("{id}",$this->_UniqueID,$_tpl_textbox);
		$_textbox = _replace("{value}",$this->Value,$_textbox);
		$_textbox = _replace("{width}",($this->Width)?"width:".$this->Width.";":"",$_textbox);
		$_textbox = _replace("{height}",($this->Height)?"height:".$this->Height.";":"",$_textbox);
		$_textbox = _replace("{viewstate}",$this->_RenderViewState(),$_textbox);
		return $_textbox;
	}
}

class KoolTextBox extends _KoolText
{
	var $Mode;
	var $MaxLength;
	function _GetClass()
	{
		return "KoolTextBox";
	}

	
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["Mode"] = $this->Mode;
		$_state["MaxLength"] = $this->MaxLength;
	}

	function Render()
	{
		if(strtolower($this->Mode)=="multiline")
		{
			$_tpl_textbox = "<textarea id='{id}' name='{id}' style='{width}{height}'>{value}</textarea> {viewstate}";
			$_textbox = _replace("{id}",$this->_UniqueID,$_tpl_textbox);
			$_textbox = _replace("{value}",$this->Value,$_textbox);
			$_textbox = _replace("{width}",($this->Width)?"width:".$this->Width.";":"",$_textbox);
			$_textbox = _replace("{height}",($this->Height)?"height:".$this->Height.";":"",$_textbox);
			$_textbox = _replace("{viewstate}",$this->_RenderViewState(),$_textbox);			
			return $_textbox;			
		}
		else
		{
			return parent::Render();
		}
	}


}


class _CNumberFormat
{
	var $AllowRounding = false;
	var $KeepNotRoundedValue = false;
	var $KeepTrailingZeroOnFocus = false;
	var $DecimalDigits;
	var $DecimalSeparator;
	var $GroupSeparator;
	var $GroupSize;
	var $NegativePattern;
	var $PositivePattern;
	
}
class _CIncrementSettings
{
	var $InterceptArrowKeys = true;
	var $InterceptMouseWheel = true;
	var $Step = 1;	
}

class KoolNumericTextBox extends _KoolText
{
	var $Type; //"Number"|"Currency"|"Percent"
	var $Culture;//"en-us"|"en-en";
	var $NumberFormat;
	var $ShowSpinButton = false;
	var $SpinButtonPosition = "Right";
	var $IncrementSettings;
	var $MaxValue;
	var $MinValue;
	var $DefaultValue;
	var $EmptyMessage;

	function _Init($_form)
	{
		parent::_Init($_form);
		if($this->NumberFormat->NegativePattern===null) $this->NumberFormat->NegativePattern = "-n";
		if($this->NumberFormat->PositivePattern===null) $this->NumberFormat->PositivePattern = "n";
	}
	
	function _GetClass()
	{
		return "KoolNumericTextBox";
	}
	
	function __construct($_id)
	{
		parent::__construct($_id);
		$this->NumberFormat = new _CNumberFormat();
		$this->IncrementSettings = new _CIncrementSettings();
	}
	
	
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["Type"] = $this->Type;
		$_state["Culture"] = $this->Culture;
		$_state["ShowSpinButton"] = $this->ShowSpinButton;
		$_state["SpinButtonPosition"] = $this->SpinButtonPosition;
		$_state["MaxValue"] = $this->MaxValue;
		$_state["MinValue"] = $this->MinValue;
		$_state["DefaultValue"] = $this->DefaultValue;
		$_state["NumberFormat"] = array("AllowRounding"=>$this->NumberFormat->AllowRounding,
										"KeepNotRoundedValue"=>$this->NumberFormat->KeepNotRoundedValue,
										"KeepTrailingZeroOnFocus"=>$this->NumberFormat->KeepTrailingZeroOnFocus,
										"DecimalDigits"=>$this->NumberFormat->DecimalDigits,
										"DecimalSeparator"=>$this->NumberFormat->DecimalSeparator,
										"GroupSeparator"=>$this->NumberFormat->GroupSeparator,
										"GroupSize"=>$this->NumberFormat->GroupSize,
										"NegativePattern"=>$this->NumberFormat->NegativePattern,
										"PositivePattern"=>$this->NumberFormat->PositivePattern,
										);
		$_state["IncrementSettings"] = array(	"InterceptArrowKeys"=>$this->IncrementSettings->InterceptArrowKeys,
												"InterceptMouseWheel"=>$this->IncrementSettings->InterceptMouseWheel,
												"Step"=>$this->IncrementSettings->Step,
											);
	}
}

class KoolPasswordTextBox extends _KoolText
{
	var $ShowIndicator = false;
	var $IndicatorElementId;
	var $IndicatorWidth="70px";
	var $PreferredPasswordLength;
	var $MinimumNumericCharacters;
	var $MinimumUpperCaseCharacters;
	var $MinimumLowerCaseCharacters;
	var $MinimumSymbolCharacters;
	var $CalculationWeightings="50;15;15;20";
	var $RequiredUpperAndLowerCaseCharacters;
	var $TextStrengthDescriptions = "Very Weak;Weak;Medium;Strong;Very Strong";
	var $TextStrengthDescriptionStyles;
	function _GetClass()
	{
		return "KoolPasswordTextBox";
	}
		

	function _SaveViewState()
	{
		
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["ShowIndicator"] = $this->ShowIndicator;
		$_state["IndicatorElementId"] = $this->IndicatorElementId;
		$_state["IndicatorWidth"] = $this->IndicatorWidth;
		$_state["PreferredPasswordLength"] = $this->PreferredPasswordLength;
		$_state["MinimumNumericCharacters"] = $this->MinimumNumericCharacters;
		$_state["MinimumUpperCaseCharacters"] = $this->MinimumUpperCaseCharacters;
		$_state["MinimumLowerCaseCharacters"] = $this->MinimumLowerCaseCharacters;
		$_state["MinimumSymbolCharacters"] = $this->MinimumSymbolCharacters;
		$_state["CalculationWeightings"] = $this->CalculationWeightings;
		$_state["RequiredUpperAndLowerCaseCharacters"] = $this->RequiredUpperAndLowerCaseCharacters;
		$_state["TextStrengthDescriptions"] = $this->TextStrengthDescriptions;
		$_state["TextStrengthDescriptionStyles"] = $this->TextStrengthDescriptionStyles;

	}
	function Render()
	{
		$_tpl_textbox = "<input type='password' id='{id}' name='{id}' value='{value}' style='{width}{height}' />{viewstate}";
		$_textbox = _replace("{id}",$this->_UniqueID,$_tpl_textbox);
		$_textbox = _replace("{value}",$this->Value,$_textbox);
		$_textbox = _replace("{width}",($this->Width)?"width:".$this->Width.";":"",$_textbox);
		$_textbox = _replace("{height}",($this->Height)?"height:".$this->Height.";":"",$_textbox);
		$_textbox = _replace("{viewstate}",$this->_RenderViewState(),$_textbox);
		return $_textbox;
	}
}

class KoolDateTextBox extends _KoolText
{
	var $DateFormat;
	var $DisplayDateFormat;
	var $Culture;
	var $ShortYearCenturyStart;
	var $ShortYearCenturyEnd;
	var $IncrementSettings;
	var $MaxDate;
	var $MinDate;
	function _GetClass()
	{
		return "KoolDateTextBox";
	}
		
	function __construct($_id)
	{
		parent::__construct($_id);
		$this->IncrementSettings = new _CIncrementSettings();
	}


	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["DateFormat"] = $this->DateFormat;
		$_state["DisplayDateFormat"] = $this->DisplayDateFormat;
		$_state["Culture"] = $this->Culture;
		$_state["ShortYearCenturyStart"] = $this->ShortYearCenturyStart;
		$_state["ShortYearCenturyEnd"] = $this->ShortYearCenturyEnd;
		$_state["MaxDate"] = $this->MaxDate;
		$_state["MinDate"] = $this->MinDate;
		$_state["IncrementSettings"] = array(	"InterceptArrowKeys"=>$this->IncrementSettings->InterceptArrowKeys,
												"InterceptMouseWheel"=>$this->IncrementSettings->InterceptMouseWheel,
												"Step"=>$this->IncrementSettings->Step,
											);
	}
}


class KoolMaskedTextBox extends _KoolText
{
	var $Culture;	
	var $Mask;
	var $DisplayMask;
	var $PromptChar="_";
	var $SelectionOnFocus="None";//"None"|"CaretToBeginning"|"CaretToEnd"|"SelectAll"
	
	var $ValueWithLiterals;	
	var $ValueWithPrompt;
	var $ValueWithPromptAndLiterals;
	
	var $_UserInput;
	function _GetClass()
	{
		return "KoolMaskedTextBox";
	}
	
	function __construct($_id,$_mask="")
	{
		parent::__construct($_id);
		if($_mask!="") $this->Mask = $_mask;
	}
	
	function _LoadViewState()
	{
		// + Base on UniqueID, get viewstate and assign correctly.
		if ($this->_ViewState->_Available)
		{
			$_state = $this->_ViewState->_Data;
			$this->ValueWithLiterals = urldecode($_state["ValueWithLiterals"]) ;
			$this->ValueWithPrompt = urldecode($_state["ValueWithPrompt"]);
			$this->ValueWithPromptAndLiterals = urldecode($_state["ValueWithPromptAndLiterals"]);
			$this->_UserInput = $_state["UserInput"];
		}
	}
		
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["Culture"] = $this->Culture;
		$_state["Mask"] = $this->Mask;
		$_state["DisplayMask"] = $this->DisplayMask;
		$_state["PromptChar"] = $this->PromptChar;
		$_state["SelectionOnFocus"] = $this->SelectionOnFocus;	
	
		$_state["ValueWithLiterals"] = _urlencode($this->ValueWithLiterals);	
		$_state["ValueWithPrompt"] = _urlencode($this->ValueWithPrompt);	
		$_state["ValueWithPromptAndLiterals"] = _urlencode($this->ValueWithPromptAndLiterals);	

		$_state["UserInput"] = $this->_UserInput;
	}
}




class KoolDropDownList extends _KoolFormElement
{
	var $SelectedText;
	var $SelectedValue;
	var $SelectedIndex;
	var $Width;
	var $Height;
	var $_Items = array();
	function _GetClass()
	{
		return "KoolDropDownList";
	}
	
	function _LoadViewState()
	{
		parent::_LoadViewState();
		if ($this->_ViewState->_Available)
		{
			$_state = $this->_ViewState->_Data;
			$this->SelectedText = $_state["SelectedText"];
			$this->SelectedValue = $_state["SelectedValue"];
			$this->SelectedIndex = $_state["SelectedIndex"];
		}
	}
	
	function _SaveViewState()
	{
		parent::_SaveViewState();	
		$_state = &$this->_ViewState->_Data;		
		$_state["SelectedText"] = $this->SelectedText;
		$_state["SelectedValue"] = $this->SelectedValue;
		$_state["SelectedIndex"] = $this->SelectedIndex;
		$_state["Width"] = $this->Width;
		$_state["Height"] = $this->Height;
	}

	function AddItem($_text,$_value=null)
	{
		if ($_value===null) $_value = $_text;
		array_push($this->_Items,array($_text,$_value));		
	}
	function Render()
	{
		$_tpl_select = "<select id='{id}' name='{id}' style='{width}{height}'>{options} </select> {viewstate}";
		$_tpl_option = "<option value='{value}' {selected} >{text}</option>";
		
		$_options = "";
		for($i=0;$i<sizeof($this->_Items);$i++)
		{
			$_option = _replace("{text}",$this->_Items[$i][0],$_tpl_option);
			$_option = _replace("{value}",$this->_Items[$i][1],$_option);
			$_option = _replace("{selected}",($this->SelectedIndex==$i)?"selected='selected'":"",$_option);			
			$_options.=$_option;			
		}
		$_select = _replace("{id}",$this->_UniqueID,$_tpl_select);
		$_select = _replace("{options}",$_options,$_select);
		$_select = _replace("{width}",($this->Width)?"width:".$this->Width.";":"",$_select);
		$_select = _replace("{height}",($this->Height)?"height:".$this->Height.";":"",$_select);		
		$_select = _replace("{viewstate}",$this->_RenderViewState(),$_select);
		return $_select;
	}
}

class _KoolButton extends _KoolFormElement
{
	var $LeftImage;
	var $RightImage;
	var $LeftImageCss;
	var $RightImageCss;
	var $ButtonCss;	
	var $Text;
	var $OnClick;
	var $Width;
	var $Height;
	
	function _Init($_form)
	{
		parent::_Init($_form);
		if($this->OnClick!==null) $this->ClientEvents["OnClick"] = $this->OnClick;
	}
	
	function _SaveViewState()
	{
		parent::_SaveViewState();	
		$_state = &$this->_ViewState->_Data;		
		$_state["LeftImage"] = $this->LeftImage;
		$_state["RightImage"] = $this->RightImage;
		$_state["LeftImageCss"] = $this->LeftImageCss;
		$_state["RightImageCss"] = $this->RightImageCss;
		$_state["ButtonCss"] = $this->ButtonCss;
		$_state["Text"] = $this->Text;		
	}
}

class KoolButton extends _KoolButton
{
	var $AutoPostback=false;

	function _GetClass()
	{
		return "KoolButton";
	}
	
	function __construct($_id,$_text="")
	{
		parent::__construct($_id);
		$this->Text = $_text;	
	}

	function Render()
	{
		$_tpl_button = "<input type='{type}' id='{id}' name='{id}' value='{value}' style='{width}{height}' {onclick} /> {viewstate}";
		$_button = _replace("{id}",$this->_UniqueID,$_tpl_button);
		$_button = _replace("{value}",$this->Text,$_button);
		$_button = _replace("{type}",$this->AutoPostback?"submit":"button",$_button);
		$_button = _replace("{onclick}",$this->AutoPostback?"submit":"button",$_button);
		
		$_button = _replace("{width}",($this->Width!==null)?"width:".$this->Width.";":"",$_button);
		$_button = _replace("{height}",($this->Height!==null)?"height:".$this->Height.";":"",$_button);
		$_button = _replace("{viewstate}",$this->_RenderViewState(),$_button);
		return $_button;
	}
}

class KoolLinkButton extends _KoolButton
{
	var $Link;
	var $Target;
	function _GetClass()
	{
		return "KoolLinkButton";
	}
	function __construct($_id,$_text="",$_link="")
	{
		parent::__construct($_id);
		$this->Text = $_text;	
		$this->Link = $_link;			
	}	
	function _SaveViewState()
	{
		parent::_SaveViewState();	
		$_state = &$this->_ViewState->_Data;		
		$_state["Link"] = $this->Link;	
	}
	function Render()
	{
		$_tpl_button = "<a id='{id}' href='{link}' {target} {onclick}>{text} </a> {viewstate}";
		$_button = _replace("{id}",$this->_UniqueID,$_tpl_button);
		$_button = _replace("{text}",$this->Text,$_button);
		$_button = _replace("{link}",$this->Link,$_button);
		$_button = _replace("{target}",($this->Target!=null)?"target='".$this->Target."'":"",$_button);
		$_button = _replace("{onclick}",($this->OnClick!=null)?"onclick='".$this->OnClick."'":"",$_button);
		$_button = _replace("{viewstate}",$this->_RenderViewState(),$_button);
		return $_button;
	}
}

class KoolToggleButton extends _KoolButton
{
	var $_ToggleStates;
	var $SelectedIndex=0;
	var $SelectedValue;
	var $SelectedText;
	var $OnClick;
	
	function _Init($_form)
	{
		parent::_Init($_form);
		if($this->OnClick!==null) $this->ClientEvents["OnClick"] = $this->OnClick;
	}
	
	function _GetClass()
	{
		return "KoolToggleButton";
	}	
	
	function __construct($_id)
	{
		parent::__construct($_id);
		$this->_ToggleStates = array();
	}
			
	function AddOption($_option)
	{
		$_model = array("Text"=>null,
						"Value"=>null,
						"LeftImage"=>null,
						"RightImage"=>null,
						"LeftImageCss"=>null,
						"RightImageCss"=>null,
						"ButtonCss"=>null,
						"ToolTip"=>null);
						
		foreach($_model as $k=>$v)
		{
			if(isset($_option[$k]))
			{
				$_model[$k] = $_option[$k];				
			}
		}
		array_push($this->_ToggleStates,$_model);
	}
	
	function _LoadViewState()
	{
		parent::_LoadViewState();
		if ($this->_ViewState->_Available)
		{
			$_state = $this->_ViewState->_Data;
			$this->SelectedText = $_state["SelectedText"];
			$this->SelectedValue = $_state["SelectedValue"];
			$this->SelectedIndex = $_state["SelectedIndex"];
		}
	}	
	function _SaveViewState()
	{
		parent::_SaveViewState();	
		$_state = &$this->_ViewState->_Data;		
		$_state["SelectedIndex"] = $this->SelectedIndex;	
		$_state["SelectedValue"] = $this->SelectedValue;	
		$_state["SelectedText"] = $this->SelectedText;
		$_state["ToggleStates"] = $this->_ToggleStates;
		$_state["TotalStates"] = count($this->_ToggleStates);
	}	
	function Render()
	{
		$_tpl_button = "<input type='button' id='{id}' name='{id}' value='{value}' style='{width}{height}' /> {viewstate}";
		$_button = _replace("{id}",$this->_UniqueID,$_tpl_button);
		$_button = _replace("{value}",$this->_ToggleStates[$this->SelectedIndex]["Text"],$_button);
		$_button = _replace("{width}",($this->Width)?"width:".$this->Width.";":"",$_button);
		$_button = _replace("{height}",($this->Height)?"height:".$this->Height.";":"",$_button);
		$_button = _replace("{viewstate}",$this->_RenderViewState(),$_button);
		return $_button;		
	}
}

class KoolSplitButton extends KoolToggleButton
{
	function _GetClass()
	{
		return "KoolSplitButton";
	}
}



class KoolScrollBar extends _KoolFormElement
{
	function _GetClass()
	{
		return "KoolScrollBar";
	}
	
}

class KoolCheckBox extends _KoolFormElement
{
	var $Text;
	var $Selected = false;
	function _GetClass()
	{
		return "KoolCheckBox";
	}

	function __construct($_id,$_text="")
	{
		parent::__construct($_id);
		$this->Text = $_text;
	}
	
	function _LoadViewState()
	{
		// + Base on UniqueID, get viewstate and assign correctly.
		if ($this->_ViewState->_Available)
		{
			$_state = $this->_ViewState->_Data;
			$this->Selected = $_state["Selected"];
			$this->Text = $_state["Text"];
		}
	}	
	
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["Text"] = $this->Text;
		$_state["Selected"] = $this->Selected;
	}
	function Render()
	{
		$_tpl_checkbox = "<input type='checkbox' id='{id}' name='{id}'/><label id='{id}_label' for='{id}'>{text}</label>";
		$_checkbox = _replace("{id}",$this->_UniqueID,$_tpl_checkbox);
		$_checkbox = _replace("{text}",$this->Text,$_checkbox);
		return $_checkbox;
	}
}


class KoolRadioButton extends _KoolFormElement
{
	var $Text;
	var $Name;
	var $Selected = false;
	function _GetClass()
	{
		return "KoolRadioButton";
	}
	
	function __construct($_id,$_name,$_text="")
	{
		parent::__construct($_id);
		$this->Name = $_name;
		$this->Text = $_text;
	}
	
	function _LoadViewState()
	{
		// + Base on UniqueID, get viewstate and assign correctly.
		if ($this->_ViewState->_Available)
		{
			$_state = $this->_ViewState->_Data;
			$this->Selected = $_state["Selected"];
		}
	}	
	
	function _SaveViewState()
	{
		parent::_SaveViewState();
		$_state = &$this->_ViewState->_Data;		
		$_state["Text"] = $this->Text;
		$_state["Selected"] = $this->Selected;
	}
	function Render()
	{
		$_tpl_radio = "<input type='radio' id='{id}' name='{name}'/><label id='{id}_label' for='{id}'>{text}</label>";
		$_radio = _replace("{id}",$this->_UniqueID,$_tpl_radio);
		$_radio = _replace("{name}",$this->Name,$_radio);
		$_radio = _replace("{text}",$this->Text,$_radio);
		return $_checkbox;
	}
	
}

class _Decoration
{
	var $Button = true;
	var $TextBox = true;
	var $RadioButton = true;
	var $CheckBox = true;
	var $TextArea = true;
	var $FieldSet = true;
	var $DropDownList = true;
	var $ListBox = true;
	var $Headings = true;
	var $Label = true;
}


class KoolForm
{
	var $_version = "1.6.0.0";

	var $id;
	var $_UniqueID;

	var $_ViewState;
	
	var $scriptFolder;
	var $styleFolder;
	var $_style;

	var $Controls;
	var $Validate = true;
	var $IsPostBack = false;
	var $StatePersistent = true;
	
	var $Decoration;
	var $DecorationEnabled = true;
	
	var $RenderWithExistingMarkup = false;
	
	
	function __construct($_id)
	{
		$this->id = $_id."_manager";
		$this->_UniqueID = $this->id;
		$this->_ViewState = new _FormViewState();
		$this->Controls = array();
		$this->Decoration = new _Decoration();
	}

	function Init()
	{
		// 1.Init viewstate
		$this->_ViewState->_Init($this);
		$this->IsPostBack = $this->_ViewState->_Available;
		// 2.Init all the sub-controls.
		foreach($this->Controls as $_control)
		{
			$_control->_Init($this);
		}
	}
	
	function AddControl($_control)
	{
		$this->Controls[$_control->_UniqueID] = $_control;
		return $_control;
	}
	function AddTextBox($_id)
	{
		$_control = new KoolTextBox($_id);
		$this->AddControl($_control);
		return $_control;
	}
	function AddNumericTextBox($_id)
	{
		$_control = new KoolNumericTextBox($_id);
		$this->AddControl($_control);
		return $_control;
	}
	function AddPasswordTextBox($_id)
	{
		$_control = new KoolPasswordTextBox($_id);
		$this->AddControl($_control);
		return $_control;
	}	
	function AddDateTextBox($_id)
	{
		$_control = new KoolDateTextBox($_id);
		$this->AddControl($_control);
		return $_control;
	}
	function AddMaskedTextBox($_id)
	{
		$_control = new KoolMaskedTextBox($_id);
		$this->AddControl($_control);
		return $_control;
	}
	function AddDropDownList($_id)
	{
		$_control = new KoolDropDownList($_id);
		$this->AddControl($_control);
		return $_control;
	}
	function AddCheckBox($_id,$_text="")
	{
		$_control = new KoolCheckBox($_id,$_text);
		$this->AddControl($_control);
		return $_control;
	}
	function AddRadioButton($_id,$_name,$_text="")
	{
		$_control = new KoolRadioButton($_id,$_name,$_text);
		$this->AddControl($_control);
		return $_control;
	}
	function AddButton($_id,$_text)
	{
		$_control = new KoolButton($_id,$_text);
		$this->AddControl($_control);
		return $_control;
	}
	
	
	
	
	function _LoadViewState()
	{
		// + Base on UniqueID, get viewstate and assign correctly.
		if (isset($this->_ViewState->_Available))
		{
		}		
	}
	
	function _SaveViewState()
	{
		$this->_positionStyle();
		$_control_classes = array();
		foreach($this->Controls as $_control)
		{
			$_control->_SaveViewState();
			$_control_classes[$_control->_UniqueID] = $_control->_GetClass();
		}
		$this->_ViewState->_Data = array(	"Id"=>$this->_UniqueID,
											"ControlClasses"=>$_control_classes,
											"Validate"=>$this->Validate,
											"Style"=>$this->_style,
											"DecorationEnabled"=>$this->DecorationEnabled,
											"Decoration"=>array(
														"Button"=>$this->Decoration->Button,
														"TextBox"=>$this->Decoration->TextBox,
														"RadioButton"=>$this->Decoration->RadioButton,
														"CheckBox"=>$this->Decoration->CheckBox,
														"TextArea"=>$this->Decoration->TextArea,
														"FieldSet"=>$this->Decoration->FieldSet,
														"DropDownList"=>$this->Decoration->DropDownList,
														"ListBox"=>$this->Decoration->ListBox,
														"Headings"=>$this->Decoration->Headings,
														"Label"=>$this->Decoration->Label													
											)
											
		);		
	}
	
	

	
	function Render()
	{
		//global $_version;
		$_script= $this->RegisterCss();
		$_script.= $this->RenderForm();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;		
	}
	
	function RenderForm()
	{
		$this->_SaveViewState();


		$_trademark = "\n<!--KoolForm version ".$this->_version." - www.koolphp.net -->\n";
		$_main = _replace("{trademark}", $_trademark, "{trademark}{content}");
		
			$_content = $this->_ViewState->_Render();
			if($this->RenderWithExistingMarkup)
			{
				foreach($this->Controls as $_control)
				{
					$_content.=$_control->_RenderViewState();
				}			
			}
			$_main = _replace("{content}",$_content,$_main);				
			$_main = _replace("{version}",$this->_version,$_main);				
	
		return $_main;
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
		//Generate CSS
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KFR')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KFR';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}


	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKFR=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKFR=1;}</script>";
		
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script);
		
		return $_script;
	}	

	
	function StartupScript()
	{
		$_tpl_script  = "var {id}; function {id}_init(){ {id} = new KoolForm('{id}');}";
		$_tpl_script .= "if (typeof(KoolForm)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KFRInits)=='undefined'){__KFRInits=new Array();} __KFRInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKFR)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKFR=1;}";
		
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script);

		$_script = _replace("{id}",$this->id,$_tpl_script);				
		$_script = _replace("{register_script}",$_register_script,$_script);

		return $_script;
	}	
	
	function _getComponentURI()
	{
		if ($this->scriptFolder=="")
		{
			//In case the scriptFolder is not specified, use the absolute path
			$_root = _getRoot();
			$_file = substr(_replace("\\","/",__FILE__),strlen($_root));
			return $_file;			
		}
		else
		{
			//Use the relative path provided in scriptFolder by user
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

/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */

	
}
?>