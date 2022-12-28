<?php
$_version = "1.5.0.0";
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
if (!class_exists("KoolMenu",false))
{
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
class _GroupSettings
{
	var $Height;
	var $Width;
	var $ExpandDirection;//"Auto"|"Left"|"Right"|"Up"|"Down"
	var $Flow;
	var $OffsetX;
	var $OffsetY;
	function _Init($_menu)
	{
		if($this->Height===null) $this->Height = $_menu->GroupSettings_Height;
		if($this->Width===null) $this->Width = $_menu->GroupSettings_Width;
		if($this->ExpandDirection===null) $this->ExpandDirection = $_menu->GroupSettings_ExpandDirection;
		if($this->Flow===null) $this->Flow = $_menu->GroupSettings_Flow;
		if($this->OffsetX===null) $this->OffsetX = $_menu->GroupSettings_OffsetX;
		if($this->OffsetY===null) $this->OffsetY = $_menu->GroupSettings_OffsetY;
	}
}
class KoolMenuItem
{
	var $GroupSettings;
	var $_ParentItem;
	var $_Menu;
	var $id;
	var $_ChildList;
	var $Text;
	var $Link;
	var $ImageUrl;
	var $ToolTip;
	var $Target;
	var $Enabled = true;
	var $Width;
	var $Template;
	var $_Level;
	function __construct()
	{
		$this->GroupSettings = new _GroupSettings();
		$this->_ChildList = array();
	}
	function _Init()
	{
		$this->GroupSettings->_Init($this->_Menu);
		if ($this->Target===null) $this->Target = $this->_Menu->Target;
		foreach($this->_ChildList as $_child)
		{
			$_child->_Init();
		}
	}
	function AddChild($_child_item)
	{
		$_child_item->_ParentItem = $this;
		$_child_item->_Level = $this->_Level+1;
		$_child_item->_Menu = $this->_Menu;
		array_push($this->_ChildList,$_child_item);
	}
	function AddSeparator()
	{
		array_push($this->_ChildList, new KoolMenuSeparator());
	}
	function _Render()
	{
		$_tpl_li = "<li id='{id}' class='kmuItem {template} {firstlast}' >{content}{slide}{setting}</li>";
		$_tpl_a = "<a class='kmuLink {enabled}' {href} title='{tooltip}' {target} {style} >{img} {text}</a>";
		$_tpl_img = "<img class='kmuImage' src='{src}' alt='' />";
		$_tpl_text = "<span class='kmuText {expand}'>{text}</span>";
		$_tpl_slide = "<div class='kmuSlide kmuPrem' style='{style}'>{ul}</div>";
		$_tpl_ul = "<ul id='{id}_group' class='kmuGroup {flow} {level}'>{lis}</ul>";
		$_tpl_template = "<div class='kmuText'>{template}</div>";
		$_tpl_setting = "<input id='{id}_setting' type='hidden' value='{value}' autocomplete='off' />";
		$_content = "";
		if(!$this->Template)
		{
			$_img = "";
			if ($this->ImageUrl)
			{
				$_img = _replace("{src}",$this->ImageUrl,$_tpl_img);
			}
			$_text = _replace("{text}",$this->Text,$_tpl_text);
			$_expand = "";
			if(sizeof($this->_ChildList)>0)
			{
				switch(strtolower($this->GroupSettings->ExpandDirection))
				{
					case "up":
						$_expand = "kmuExpandUp";
						break;					
					case "down":
						$_expand = "kmuExpandDown";
						break;					
					case "left":
						$_expand = "kmuExpandLeft";
						break;					
					case "right":
						$_expand = "kmuExpandRight";
						break;					
					case "auto":
					default:
						if(strtolower($this->_ParentItem->GroupSettings->Flow)=="horizontal")
						{
							$_expand = "kmuExpandDown";
						}
						else
						{
							$_expand = "kmuExpandRight";
						}
						break;					
				}				
			}
			$_text = _replace("{expand}",$_expand,$_text);			
			$_a = _replace("{img}",$_img,$_tpl_a);
			$_a = _replace("{text}",$_text,$_a);
			$_a = _replace("{href}",($this->Link!==null)?"href='".$this->Link."'":"",$_a);
			$_a = _replace("{tooltip}",$this->ToolTip,$_a);			
			$_a = _replace("{target}",($this->Target!==null)?"target='".$this->Target."'":"",$_a);			
			$_a = _replace("{enabled}",($this->Enabled)?"":"kmuDisabled",$_a);
			if($this->Width!==null)
			{
				$_a = _replace("{style}","style='width:".$this->Width.";'",$_a);
			}
			else
			{
				$_a = _replace("{style}","",$_a);
			}
			$_content = $_a;
		}
		else
		{
			$_template = _replace("{template}",$this->Template,$_tpl_template);
			$_content = $_template;			
		}
		$_slide = "";
		if(sizeof($this->_ChildList)>0)
		{
			$_lis = "";
			for($i=0;$i<sizeof($this->_ChildList);$i++)
			{
				$_child = $this->_ChildList[$i];
				$_li = $_child->_Render();
				if($i==sizeof($this->_ChildList)-1)
				{
					$_li = _replace("{firstlast}","kmuLast",$_li);
				}else if($i==0)
				{
					$_li = _replace("{firstlast}","kmuFirst",$_li);
				}else
				{
					$_li = _replace("{firstlast}","",$_li);
				}
				$_lis.=$_li;
			}
			$_ul = _replace("{id}",$this->id,$_tpl_ul);
			$_ul = _replace("{flow}",(strtolower($this->GroupSettings->Flow)=="vertical")?"kmuVertical":"kmuHorizontal",$_ul);
			$_ul = _replace("{level}","kmuLevel".$this->_Level,$_ul);
			$_ul = _replace("{lis}",$_lis,$_ul);
			$_slide = _replace("{ul}",$_ul,$_tpl_slide);
			if($this->GroupSettings->Width!==null)
			{
				$_slide = _replace("{style}","width:".$this->GroupSettings->Width.";{style}",$_slide);
			}
			if($this->GroupSettings->Height!==null)
			{
				$_slide = _replace("{style}","height:".$this->GroupSettings->Height.";{style}",$_slide);
			}
			$_slide = _replace("{style}","display:none;overflow:hidden;z-index:".$this->_Level*3,$_slide);
		}
		$_info_arr = array(	"OffsetX"=>$this->GroupSettings->OffsetX,
							"OffsetY"=>$this->GroupSettings->OffsetY,
							"ExpandDirection"=>$this->GroupSettings->ExpandDirection
		);
		$_setting = _replace("{value}",json_encode($_info_arr),$_tpl_setting);
		$_setting = _replace("{id}",$this->id,$_setting);
		$_li = _replace("{id}",$this->id,$_tpl_li);
		$_li = _replace("{template}",(!$this->Template)?"":"kmuTemplate",$_li);
		$_li = _replace("{content}",$_content,$_li);
		$_li = _replace("{setting}",$_setting,$_li);
		$_li = _replace("{slide}",$_slide,$_li);
		return $_li;
	}
}
class KoolMenuSeparator
{
	function _Render()
	{
		$_tpl_li = "<li class='kmuItem kmuSeparator'><span class='kmuSub'><span></span></span></li>";
		return $_tpl_li;		
	}
	function _Init()
	{
	}
}
class _KoolMenuRootItem
{
	var $GroupSettings;
	var $_ChildList;
	var $_Menu;
	var $_Level = 0;
	function __construct($_menu)
	{
		$this->_ChildList = array();
		$this->GroupSettings = new _GroupSettings();
		$this->_Menu = $_menu;
	}
	function _Init()
	{
		$this->GroupSettings->_Init($this->_Menu);
		$this->GroupSettings->Flow = $this->_Menu->Flow;
		$this->GroupSettings->ExpandDirection = $this->_Menu->ExpandDirection;
		foreach($this->_ChildList as $_child)
		{
			$_child->_Init();
		}
	}
	function AddChild($_child_item)
	{
		$_child_item->_ParentItem = $this;
		$_child_item->_Level = $this->_Level+1;
		$_child_item->_Menu = $this->_Menu;
		array_push($this->_ChildList,$_child_item);
	}
	function _Render()
	{
		$_tpl_ul = "<ul class='kmuRootGroup {flow}'>{lis}</ul>";
		$_lis = "";
		for($i=0;$i<sizeof($this->_ChildList);$i++)
		{
			$_child = $this->_ChildList[$i];
			$_li = $_child->_Render();
			if($i==sizeof($this->_ChildList)-1)
			{
				$_li = _replace("{firstlast}","kmuLast",$_li);
			}else if($i==0)
			{
				$_li = _replace("{firstlast}","kmuFirst",$_li);
			}else
			{
				$_li = _replace("{firstlast}","",$_li);
			}
			$_lis.=$_li;
		}
		$_ul = _replace("{flow}",(strtolower($this->_Menu->Flow)=="vertical")?"kmuVertical":"kmuHorizontal",$_tpl_ul);
		$_ul = _replace("{lis}",$_lis,$_ul);
		return $_ul;
	}
}
class _KoolContextMenuRootItem extends _KoolMenuRootItem
{
	var $_DumpItem;
	function __construct($_menu)
	{
		parent::__construct($_menu);
		$_dump_item = new KoolMenuItem();
		$_dump_item->id = $_menu->id."_ctmnu";
		parent::AddChild($_dump_item);
		$this->_DumpItem = $_dump_item;		
	}
	function _Init()
	{
		$this->_DumpItem->GroupSettings->Flow = $this->_Menu->Flow;
		$this->_DumpItem->GroupSettings->ExpandDirection = $this->_Menu->ExpandDirection;
		parent::_Init();
	}
	function AddChild($_child_item)
	{
		$this->_DumpItem->AddChild($_child_item);
	}
	function AddSeparator()
	{
		$this->_DumpItem->AddSeparator();
	}
}
class _AnimationMenu
{
	var $Duration = 200;
	var $Type = "EaseBoth";
}
class KoolMenu
{
	var $_version = "1.5.0.0";
	var $id;
	var $_RootItem;
	var $_style;
	var $_Items;
	var $styleFolder;
	var $scriptFolder;
	var $Target;
	var $ExpandAnimation;
	var $CollapseAnimation;
	var $Flow="Horizontal";//"Horizontal"|"Vertical"	
	var $ExpandDirection = "Auto"; 
	var $ClickToOpen = false;
	var $ExpandDelay = 210;
	var $CollapseDelay = 210;
	var $GroupSettings_Flow = "Vertical";
	var $GroupSettings_OffsetX = 0;
	var $GroupSettings_OffsetY = 0;
	var $GroupSettings_ExpandDirection = "Auto";
	var $GroupSettings_Width;
	var $GroupSettings_Height;
	var $Width;
	var $Height;
	var $PostBackOnSelect = false;
	var $SelectedId;
	var $_ContextMenu = false;
	function __construct($_id)
	{
		$this->id = $_id;
		$this->_RootItem = new _KoolMenuRootItem($this);
		$this->ExpandAnimation = new _AnimationMenu();
		$this->CollapseAnimation = new _AnimationMenu();
		if(isset($_POST[$_id."_select"]))
		{
			$this->SelectedId = $_POST[$_id."_select"]; 	
		}
		else if(isset($_GET[$_id."_select"]))
		{
			$this->SelectedId = $_GET[$_id."_select"];
		}
	}
	function Add($_parent_id,$_id,$_text="",$_link=null,$_image_url=null)
	{
		$_menu_item = new KoolMenuItem();
		$_menu_item->id = $_id;
		$_menu_item->Text = $_text;
		$_menu_item->Link = $_link;
		$_menu_item->ImageUrl = $_image_url;
		$_parent_item = null; 
		if (isset($this->_Items[$_parent_id]))
		{
			$_parent_item = $this->_Items[$_parent_id];
		}
		else
		{
			$_parent_item = $this->_RootItem;
		}
		$_parent_item->AddChild($_menu_item);
		$this->_Items[$_id] = $_menu_item;
		return $_menu_item;
	}
	function GetItem($_id)
	{
		return $this->_Items[$_id];
	}
	function AddSeparator($_parent_id)
	{
		$_parent_item = null; 
		if (isset($this->_Items[$_parent_id]))
		{
			$_parent_item = $this->_Items[$_parent_id];
		}
		else
		{
			$_parent_item = $this->_RootItem;
		}
		$_parent_item->AddSeparator();		
	}
	function _Init()
	{
		$this->_RootItem->_Init();
	}
	function Render()
	{
		$_script="\n<!--KoolMenu version ".$this->_version." - www.koolphp.net -->\n";
		$_script.= $this->RegisterCss();
		$_script.= $this->RenderMenu();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;		
	}
	function RenderMenu()
	{
		$this->_Init();
		$_tpl_main = "<div id='{id}' class='{style}KMU' style='z-index:5000;'>{root}  {setting} {select}</div>";
		$_tpl_setting = "<input id='{id}_setting' type='hidden' value='{value}' autocomplete='off' />";
		$_tpl_select = "<input id='{id}_select' name='{id}_select' type='hidden' autocomplete='off' />";
		$_value_arr = array("ExpandDelay"=>$this->ExpandDelay,
							"CollapseDelay"=>$this->CollapseDelay,
							"ClickToOpen"=>$this->ClickToOpen,
							"ExpandAnimation"=>$this->ExpandAnimation,
							"CollapseAnimation"=>$this->CollapseAnimation,
							"PostBackOnSelect"=>$this->PostBackOnSelect,
							"ContextMenu"=>$this->_ContextMenu
		);
		if($this->_ContextMenu)
		{
			$_tpl_main = "<div id='{id}' class='{style}KMU {style}KMU_ContextMenu' style='width:0px;height:0px;font-size:0pt;'>{root} {setting} {select}</div>";
			$_value_arr["AttachTo"] = explode(",",$this->AttachTo) ;
		}
		$_value = json_encode($_value_arr);		
		$_setting = _replace("{id}",$this->id,$_tpl_setting);
		$_setting = _replace("{value}",$_value,$_setting);
		$_select = _replace("{id}",$this->id,$_tpl_select);
		$_main = _replace("{id}",$this->id,$_tpl_main);
		$_main = _replace("{style}",$this->_style,$_main);
		$_main = _replace("{root}",$this->_RootItem->_Render(),$_main);
		$_main = _replace("{setting}",$_setting,$_main);
		if (true)
		{
			$_main = _replace("{select}",$_select,$_main);	
		}
		$_main = _replace("{version}",$this->_version,$_main);
		return $_main;		
	}
	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKMU=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKMU=1;}</script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script);//Do comment to obfuscate
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
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KMU')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KMU';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}	
	function StartupScript()
	{
		$_tpl_script  = "var {id}; function {id}_init(){ {id} = new KoolMenu('{id}');}";
		$_tpl_script .= "if (typeof(KoolMenu)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KMUInits)=='undefined'){__KMUInits=new Array();} __KMUInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKMU)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKMU=1;}";
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
class KoolContextMenu extends KoolMenu
{
	var $Flow="Vertical";
	var $_ContextMenu = true;
	var $AttachTo;
	function __construct($_id)
	{
		parent::__construct($_id);
		$this->_RootItem = new _KoolContextMenuRootItem($this);
	}
}
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
}
?>
