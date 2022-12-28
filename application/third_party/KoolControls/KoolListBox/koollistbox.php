<?php
$_version = "1.2.0.1";
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
if (!class_exists("KoolListBox",false))
{
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
class _ViewState
{
	var $_Control;
	var $_Data;
	var $_Encode = false;
	var $_Available = false;
	var $_Name;
	function __construct($_name="_viewstate",$_encode=false)
	{
		$this->_Name = $_name;
		$this->_Encode = $_encode;
	}
	function _Init($_control)
	{
		$this->_Control = $_control;
		$_string = (isset($_POST[$this->_Control->_UniqueID.$this->_Name]))?$_POST[$this->_Control->_UniqueID.$this->_Name]:"";
		if($_string!="")
		{
			$this->_Available = true;
			if($this->_Encode)
			{
				$_string = base64_decode($_string);				
			}
		}
		$_string = _replace("\\","",$_string);
		$this->_Data = json_decode($_string,true);		
	}
	function _Render()
	{
		$_statevalue = json_encode($this->_Data);
		if($this->_Encode) $_statevalue = base64_encode($_statevalue);
		$_tpl_viewstate = "<input id='{id}' name='{id}' type='hidden' value='{value}' autocomplete='off' />";
		$_viewstate = _replace("{id}",$this->_Control->_UniqueID.$this->_Name,$_tpl_viewstate);
		$_viewstate = _replace("{value}",$_statevalue,$_viewstate);
		return $_viewstate;
	}
}
class _ButtonSettings
{
	var $ShowDelete = false;
	var $ShowReorder = false;
	var $ShowTransfer = false;
	var $ShowTransferAll = false;
	var $Position = "Right";
	var $HorizontalAlign = "Left"; //[â€œLeftâ€,Rightâ€,â€Centerâ€]
	var $VerticalAlign = "Top"; //[â€œTopâ€,â€Bottomâ€,â€Middleâ€]
	var $RenderButtonWithText = false;//Later
}
class ListBoxItem
{
	var $id;
	var $Enabled=true;
	var $Text;
	var $Value;
	var $ToolTip;
	var $Checked;
	var $Checkable=true;
	var $Selected;
	var $ImageUrl;
	var $AllowDrag;//Not yet
	var $CssClass;
	var $Data;
	var $_ListBox;
	function __construct($_text="ListBoxItem",$_value=null)
	{
		$this->Text = $_text;
		if($_value===null)
		{
			$this->Value = $_text;
		}
		else
		{
			$this->Value = $_value; 
		}
		$this->Data = array();
	}
	function CloneMe()
	{
		$_item = new ListBoxItem($this->Text,$this->Value);
		$_item->Enabled = $this->Enabled;
		$_item->ToolTip = $this->ToolTip;
		$_item->Checked = $this->Checked;
		$_item->Checkable = $this->Checkable;
		$_item->Selected = $this->Selected;
		$_item->ImageUrl = $this->ImageUrl;
		$_item->AllowDrag = $this->AllowDrag;
		$_item->CssClass = $this->CssClass;
		$_item->Data = $this->Data;
		$_item->_ListBox = $this->_ListBox;
		return $_item;		
	}	
	function _Render()
	{
		$_tpl_item = "<li class='klbItem{selected}{cssclass}{disabled}' {tooltip}>{data}{display}</li>";
		$_tpl_data = "<input class='klbItemData' type='hidden' value='{value}' autocomplete='off' />";
		$_display="";
		if($this->_ListBox->ItemTemplate!==null)
		{
			$_display = $this->_ListBox->ItemTemplate;
			foreach($this->Data as $_k=>$_v)
			{
				$_display = _replace("{".$_k."}", $_v, $_display);
			}
			$_display = _replace("{Text}", $this->Text, $_display);
			$_display = _replace("{Value}", $this->Value, $_display);
		}
		else
		{
			$_tpl_text = "<span class='klbText'>{text}</span>";
			$_tpl_checkbox = "<input class='klbCheck' type='checkbox' {checked} {disabled}/>";
			$_tpl_image = "<img class='klbImage' src='{imageurl}' />";
			$_checkbox=($this->_ListBox->UseCheckBoxes)?_replace("{checked}",$this->Checked?"checked='true'":"",$_tpl_checkbox):"";
			$_checkbox=_replace("{disabled}",($this->Checkable)?"":"disabled='true'",$_checkbox);
			$_image=($this->ImageUrl!==null)?_replace("{imageurl}",$this->ImageUrl,$_tpl_image):"";
			$_text = _replace("{text}",$this->Text,$_tpl_text);						
			$_display = $_checkbox.$_image.$_text;
		}
		$_item_data = $this->Data;
		$_item_data["Text"]=$this->Text;
		$_item_data["Value"]=$this->Value;
		foreach($_item_data as $_k=>$_v)
		{
			$_item_data[$_k] = urlencode($_v);
		}
		if($this->ImageUrl!==null)
		{
			$_item_data["ImageUrl"]=$this->ImageUrl;			
		}
		$_data = _replace("{value}",json_encode($_item_data),$_tpl_data);
		$_item = _replace("{display}", $_display, $_tpl_item);
		$_item = _replace("{selected}", ($this->Selected)?" klbSelected klbActive":"", $_item);
		$_item = _replace("{tooltip}", ($this->ToolTip!==null)?"title='".$this->ToolTip."'":"", $_item);
		$_item = _replace("{cssclass}", ($this->CssClass)?$this->CssClass:"", $_item);
		$_item = _replace("{disabled}", ($this->Enabled)?"":" klbDisabledItem", $_item);
		$_item = _replace("{data}", $_data, $_item);
		return $_item;
	}
	function _Serialize()
	{
		$_serial = array();
		$_serial["Text"] = urlencode($this->Text);
		$_serial["Value"] = urlencode($this->Value);
		$_serial["Enabled"] = $this->Enabled;
		$_serial["ToolTip"] = $this->ToolTip;
		$_serial["Checked"] = $this->Checked;
		$_serial["Checkable"] = $this->Checkable;
		$_serial["Selected"] = $this->Selected;
		$_serial["ImageUrl"] = $this->ImageUrl;
		$_serial["AllowDrag"] = $this->AllowDrag;
		$_serial["CssClass"] = $this->CssClass;
		$_data = array();
		foreach($this->Data as $_k=>$_v)
		{
			$_data[$_k] = urlencode($_v);
		}
		$_serial["Data"] = $_data;
		return $_serial;
	}
	function _Revive($_serial)
	{
		$this->Text = urldecode($_serial["Text"]);
		$this->Value = urldecode($_serial["Value"]);
		$this->Enabled = $_serial["Enabled"];
		$this->ToolTip = $_serial["ToolTip"];
		$this->Checked = $_serial["Checked"];
		$this->Checkable = $_serial["Checkable"];
		$this->ImageUrl = $_serial["ImageUrl"];
		$this->AllowDrag = $_serial["AllowDrag"];
		$this->CssClass = $_serial["CssClass"];
		foreach($_serial["Data"] as $_k=>$_v)
		{
			$this->Data[$_k] = urlencode($_v);
		}
	}
}
class ListBoxEventHandler
{
	function OnBeforeReorder($sender,$args){return true;}
	function OnReorder($sender,$args){}
	function OnBeforeTransferIn($sender,$args){return true;}
	function OnTransferIn($sender,$args){}
	function OnBeforeDelete($sender,$args){return true;}
	function OnDelete($sender,$args){}
}
class KoolListBox
{
	var $_version = "1.2.0.1";
	var $id;
	var $_UniqueID;
	var $scriptFolder;
	var $styleFolder;
	var $_style;
	var $Height = "200px";
	var $Width = "200px";
	var $AllowMultiSelect = false;
	var $AllowSelect = true;
	var $AllowHover = true;
	var $UseCheckBoxes = false;
	var $EnableDragAndDrop = false;
	var $AllowReorder = false;
	var $AutoPostBackOnReorder = false;
	var $AllowTransfer = false;
	var $TransferMode = "Move"; //"Move"|"Copy"
	var $AutoPostBackOnTransfer=false;
	var $AutoPostBackOnDelete=false;
	var $AllowTransferOnDoubleClick = false;
	var $ButtonSettings;
	var $LoadOnDemand = false;//Later
	var $ItemTemplate;
	var $SelectedItems;
	var $CheckedItems;
	var $TransferToId;
	var $ClientEvents;
	var $EventHandler;
	var $UpdatePanel;
	var $Items;
	var $_ItemsData;
	var $_ViewState;
	var $_NotifyingUpdateIds;
	var $_LogEntries;
	var $_SelectedIndices;
	var $_CheckedIndices;
	var $_ScrollTop=0;
	function __construct($_id)
	{
		$this->id = $_id;
		$this->_UniqueID = $_id;
		$this->ButtonSettings = new _ButtonSettings();
		$this->Items = array();
		$this->_ViewState = new _ViewState();
		$this->_ItemsData = new _ViewState("_itemdata",true);
		$this->ClientEvents = array();
		$this->_LogEntries = array();
		$this->EventHandler = new ListBoxEventHandler();
	}
	function ClearAll()
	{
		$this->_Items = array();
	}
	function AddItem($_item)
	{
		$_item->_ListBox = $this;
		array_push($this->Items,$_item);
		return $_item;
	}
	function _LoadViewState()
	{
		if($this->_ViewState->_Available)
		{
			$this->_NotifyingUpdateIds=$this->_ViewState->_Data["NotifyingUpdateIds"];
			$this->_LogEntries=$this->_ViewState->_Data["LogEntries"];
			$this->_SelectedIndices=$this->_ViewState->_Data["SelectedIndices"];
			$this->_CheckedIndices=$this->_ViewState->_Data["CheckedIndices"];
			$this->_ScrollTop=$this->_ViewState->_Data["ScrollTop"];			
		}
		if($this->_ItemsData->_Available)
		{
			$this->Items = array();
			$_itemsdata = $this->_ItemsData->_Data;
			for($i=0;$i<count($_itemsdata);$i++)
			{
				$_item = new ListBoxItem();
				$_item->_Revive($_itemsdata[$i]);
				$_item->Selected = false;
				$_item->Checked = false;
				$this->AddItem($_item);
			}
		}
	}
	function _SaveViewState()
	{
		$this->_ViewState->_Data = array(
										"AllowMultiSelect"=>$this->AllowMultiSelect,
										"AllowHover"=>$this->AllowHover,
										"AllowSelect"=>$this->AllowSelect,
										"UseCheckBoxes"=>$this->UseCheckBoxes,
										"EnableDragAndDrop"=>$this->EnableDragAndDrop,
										"AllowReorder"=>$this->AllowReorder,
										"AutoPostBackOnReorder"=>$this->AutoPostBackOnReorder,
										"AutoPostBackOnDelete"=>$this->AutoPostBackOnDelete,
										"AllowTransfer"=>$this->AllowTransfer,
										"TransferMode"=>$this->TransferMode,
										"AutoPostBackOnTransfer"=>$this->AutoPostBackOnTransfer,
										"AllowTransferOnDoubleClick"=>$this->AllowTransferOnDoubleClick,
										"TransferToId"=>$this->TransferToId,
										"UseCheckBoxes"=>$this->UseCheckBoxes,
										"ClientEvents"=>$this->ClientEvents,
										"LogEntries"=>array(),
										"SelectedIndices"=>array(),
										"CheckedIndices"=>array(),
										"NotifyingUpdateIds"=>$this->_NotifyingUpdateIds,
										"ScrollTop"=>$this->_ScrollTop,
										"UpdatePanel"=>$this->UpdatePanel																	
		);
		$_data = array();
		for($i=0;$i<count($this->Items);$i++)
		{
			array_push($_data,$this->Items[$i]->_Serialize());
		}
		$this->_ItemsData->_Data = $_data;
	}
	function _Update()
	{
		if(count($this->_LogEntries)>0)
		foreach($this->_LogEntries as $_log_entry)
		{
			switch($_log_entry["Event"])
			{
				case "Delete":
					if($this->EventHandler->OnBeforeDelete($this, array("Position"=>$_log_entry["Data"]["Position"])))
					{
						$_arr = array();
						for($i=0;$i<count($this->Items);$i++)
						{
							if($i!=$_log_entry["Data"]["Position"])
							{
								array_push($_arr,$this->Items[$i]);
							}
						}
						$this->Items = $_arr;
						$this->EventHandler->OnDelete($this, array("Position"=>$_log_entry["Data"]["Position"]));						
					}
					break;
				case "Move":
					if($this->EventHandler->OnBeforeReorder($this, $_log_entry["Data"]))
					{
						$_direction = abs($_log_entry["Data"]["To"]-$_log_entry["Data"]["From"])/($_log_entry["Data"]["To"]-$_log_entry["Data"]["From"]);
						for($i=$_log_entry["Data"]["From"];$i!=$_log_entry["Data"]["To"];$i=$i+$_direction)
						{
							$_tmp = $this->Items[$i+$_direction];
							$this->Items[$i+$_direction] = $this->Items[$i];
							$this->Items[$i] = $_tmp;
						}
					}
					break;
				case "TransferIn":
					if($this->EventHandler->OnBeforeTransferIn($this, array("ItemData"=>$_log_entry["Data"])))
					{
						$_item = new ListBoxItem($_log_entry["Data"]["Text"],$_log_entry["Data"]["Value"]);
						$_item->Data = $_log_entry["Data"];
						$this->AddItem($_item);
						$this->EventHandler->OnTransferIn($this,array("ItemData"=>$_log_entry["Data"]));
					}
					break;
			}
		}
		$this->SelectedItems = array();
		for($i=0;$i<count($this->_SelectedIndices);$i++)
		{
			if(isset($this->Items[$this->_SelectedIndices[$i]]))
			{
				$this->Items[$this->_SelectedIndices[$i]]->Selected = true;
				array_push($this->SelectedItems,$this->Items[$this->_SelectedIndices[$i]]);				
			}
		}
		$this->CheckedItems = array();
		for($i=0;$i<count($this->_CheckedIndices);$i++)
		{
			if(isset($this->Items[$this->_CheckedIndices[$i]]))
			{
				$this->Items[$this->_CheckedIndices[$i]]->Checked = true;
				array_push($this->CheckedItems,$this->Items[$this->_CheckedIndices[$i]]);				
			}
		}
	}
	function Init()
	{
		$this->_ViewState->_Init($this);
		$this->_ItemsData->_Init($this);		
		$this->_LoadViewState();
		$this->_Update();		
	}
	function Render()
	{
		$this->_SaveViewState();
		$_script= $this->RegisterCss();
		$_script.= $this->RenderListBox();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;		
	}
	function RenderListBox()
	{
		$this->_positionStyle();
		$_trademark = "\n<!--KoolListBox version ".$this->_version." - www.koolphp.net -->\n";
		$_tpl_main = "{trademark}<div id='{id}' class='{style}KLB {style}KLB_Scrollable' style='width:{width};height:{height}'>{group}{button_area}{viewstate}{template}</div>";
		$_tpl_group = "<div class='klbGroup' style='{style}'><ul class='klbList'>{lis}</ul></div>";
		$_tpl_button_area = "<div class='klbButtonArea{position} klbAlign{align}' style='{style}'>{buttons}</div>";
		$_tpl_button_area_table = "<table cellpadding='0' cellspacing='0' class='klbButtonArea{position} klbAlign{align}' style='{style}'><tr><td>{buttons}</td></tr></table>";
		$_tpl_template = "<div id='{id}_template' style='display:none'>{itemtemplate}</div>";
		$_tpl_button = "<a class='klbButton {type}' tittle='{title}' href='javascript:void 0'><span class='klbButtonBL'><span class='klbButtonBR'><span class='klbButtonTR'><span class='klbButtonTL'><span class='klbButtonText'>{text}</span></span></span></span></span></a>";
		$_tpl_buttons = "";
		$_lis = "";
		foreach($this->Items as $_item)
		{
			$_lis.=$_item->_Render();
		}
		$_group = _replace("{lis}",$_lis,$_tpl_group);
		$_button_area = "";
		if($this->ButtonSettings->ShowDelete||$this->ButtonSettings->ShowTransfer||$this->ButtonSettings->ShowReorder||$this->ButtonSettings->ShowTransferAll)
		{
			$_button_delete = "";
			if($this->ButtonSettings->ShowDelete)
			{
				$_button_delete = _replace("{type}","klbDelete", $_tpl_button);
				$_button_delete = _replace("{title}","Delete", $_button_delete);
				$_button_delete = _replace("{text}","", $_button_delete);				
			}
			$_button_reorder = "";
			if($this->ButtonSettings->ShowReorder)
			{
				$_button_moveup = _replace("{type}","klbMoveUp", $_tpl_button);
				$_button_moveup = _replace("{title}","Move up", $_button_moveup);
				$_button_moveup = _replace("{text}","", $_button_moveup);				
				$_button_movedown = _replace("{type}","klbMoveDown", $_tpl_button);
				$_button_movedown = _replace("{title}","Move up", $_button_movedown);
				$_button_movedown = _replace("{text}","", $_button_movedown);
				$_button_reorder=$_button_moveup.$_button_movedown;
			}
			$_button_transfer="";
			if($this->ButtonSettings->ShowTransfer)
			{
				$_button_transferout = _replace("{type}","klbTransferOut", $_tpl_button);
				$_button_transferout = _replace("{title}","Transfer out", $_button_transferout);
				$_button_transferout = _replace("{text}","", $_button_transferout);				
				$_button_transferin = _replace("{type}","klbTransferIn", $_tpl_button);
				$_button_transferin = _replace("{title}","Transfer in", $_button_transferin);
				$_button_transferin = _replace("{text}","", $_button_transferin);
				$_button_transfer=$_button_transferout.$_button_transferin;
			}
			$_button_transfer_all="";
			if($this->ButtonSettings->ShowTransferAll)
			{
				$_button_transferout_all = _replace("{type}","klbTransferAllOut", $_tpl_button);
				$_button_transferout_all = _replace("{title}","Transfer all out ", $_button_transferout_all);
				$_button_transferout_all = _replace("{text}","", $_button_transferout_all);				
				$_button_transferin_all = _replace("{type}","klbTransferAllIn", $_tpl_button);
				$_button_transferin_all = _replace("{title}","Transfer all in", $_button_transferin_all);
				$_button_transferin_all = _replace("{text}","", $_button_transferin_all);
				$_button_transfer_all=$_button_transferout_all.$_button_transferin_all;
			}
			$_align="";
			$_tpl_button_area_final="";
			switch(strtolower($this->ButtonSettings->Position))
			{
				case "left":
				case "right":
					switch(strtolower($this->ButtonSettings->VerticalAlign))
					{
						case "top":
							$_align="Top";
							break;						
						case "bottom":
							$_align="Bottom";
							break;						
						case "middle":
							$_align="Middle";
							break;						
					}
					$_tpl_button_area_final=$_tpl_button_area_table;
					break;
				case "top":
				case "bottom":
					switch(strtolower($this->ButtonSettings->HorizontalAlign))
					{
						case "left":
							$_align="Left";
							break;						
						case "right":
							$_align="Right";
							break;						
						case "center":
							$_align="Center";
							break;						
					}
					$_tpl_button_area_final=$_tpl_button_area;
					break;					
			}
			$_buttons = $_button_reorder.$_button_transfer.$_button_transfer_all.$_button_delete;
			$_button_area = _replace("{buttons}", $_buttons, $_tpl_button_area_final);
			$_button_area = _replace("{position}", $this->ButtonSettings->Position, $_button_area);	
			$_button_area = _replace("{align}", $_align, $_button_area);	
		}
		if($_button_area!="")
		{
			switch(strtolower($this->ButtonSettings->Position))
			{
				case "left":
					$_group=_replace("{style}","margin-left:30px;",$_group);
					$_button_area=_replace("{style}","width:30px;",$_button_area);
					break;
				case "right":
					$_group=_replace("{style}","margin-right:30px;",$_group);
					$_button_area=_replace("{style}","width:30px;",$_button_area);
					break;
				case "top":
					$_group=_replace("{style}","margin-top:30px;",$_group);
					$_button_area=_replace("{style}","height:30px;",$_button_area);
					break;
				case "bottom":
					$_group=_replace("{style}","margin-bottom:30px;",$_group);
					$_button_area=_replace("{style}","position:absolute;height:30px;bottom:0px;",$_button_area);
					break;
			}
		}
		$_template = "";
		if($this->ItemTemplate!==null)
		{
			$_template = _replace("{itemtemplate}", $this->ItemTemplate, $_tpl_template);
			$_template = _replace("{id}", $this->id, $_template);
		}
		$_main = _replace("{id}",$this->id,$_tpl_main);
		if(true)
		{
			$_main = _replace("{style}", $this->_style, $_main);
			$_main = _replace("{width}", $this->Width, $_main);
			$_main = _replace("{height}", $this->Height, $_main);
			$_main = _replace("{viewstate}", $this->_ViewState->_Render().$this->_ItemsData->_Render(), $_main);
			$_main = _replace("{group}", $_group, $_main);
			$_main = _replace("{button_area}", $_button_area, $_main);
			$_main = _replace("{template}", $_template, $_main);		
			$_main = _replace("{trademark}", $_trademark, $_main);			
			$_main = _replace("{version}", $this->_version, $_main);			
		}
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
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KLB')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KLB';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}
	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKLB=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKLB=1;}</script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script);//Do comment to obfuscate
		return $_script;
	}	
	function StartupScript()
	{
		$_tpl_script  = "var {id}; function {id}_init(){ {id} = new KoolListBox('{id}');}";
		$_tpl_script .= "if (typeof(KoolListBox)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KLBInits)=='undefined'){__KLBInits=new Array();} __KLBInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKLB)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKLB=1;}";
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script);//Do comment to obfuscate
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
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
}
?>
