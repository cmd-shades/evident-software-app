<?php
//$_version = "1.8.0.0";
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


if (!class_exists("KoolCombobox",false))
{


class KoolComboboxItem
{
	//var $id;
	/*
	 * id of item: Thinking of auto-id for item
	 */
	var $enabled=true;
	/*
	 * enabled: Indicate whether a item is enabled.
	 */
	var $selected=false;
	/*
	 * selected: Indicate whether a item is selected.
	 */	
	var $data;
	/*
	 * Contain data for item.
	 */
	function __construct()
	{
		//$this->id = $_id;
		$this->data = array("text"=>"KoolCombobox Item","value"=>"");
	}
}

class KoolCombobox
{
	var $_version = "1.8.0.0";
	//var $_showExpireEncodeString="cejeghpqjk";//Date:12/7/2009
	
	var $id;
	
	var $styleFolder;
	var $_style;
	
	var $scriptFolder="";
	
	var $openDirection="down";
	/* "down":"up":"auto"
	 * "auto": choose the "down" if ok, if not choose "up"
	 */
	
	var $superAbove = true;
	/*
	 * If true the combobox will use the iframe "shim" technique to make opened box above all
	 */
	
	var $effect="none";
	/*
	 * "none":"linear":"ease"
	 */

	var $width="auto";
	/*
	 * This is width of combo
	 */
	var $boxWidth="auto";
	/*
	 * Set the width for opened div of combobox.
	 * By default, without setting width, the width of combobox div will
	 * be equal to width of combobox
	 */
		
	var $boxHeight="auto";
	/*
	 * Set the height for opened div of combobox.
	 * By default, without setting height, the will expand within limit of maxheight
	 */
	var $maxBoxHeight="200px";
	/*
	 * The maximum height that the combobox will expand.
	 */
	var $minBoxHeight="50px";
	/*
	 * The minimum height that the combobox will expand.
	 */
	
		
	//var $cols=1;
	/*
	 * cols: The number of collums in the combobox div
	 */
	
	var $mode="combobox";
	/*
	 * mode="combobox":"textbox": change appearance to be combobox or textbox
	 */
	var $align = "left";
	/*
	 * "left":"right";
	 * Align of open div to combobox
	 */
	
	var $headerTemplate="";
	var $itemTemplate="{text}";
	var $footerTemplate="";
	/*
	 * Those templates is for customizing the combobox
	 */

	var $inputValidate = true;
	
	//var $selectedText;
	/*
	 * selectedText: This is used to get the text of selected item
	 */
	//var $selectedValue;
	/*
	 * selectedValue: This is used to get the value of selected item
	 */
	
	var $serviceFunction;
	
	var $_items;
	
	
	
	function __construct($_id)
	{
		$this->id = $_id;
		$this->_items = array();
	}
	
	function addItem($_text="",$_value="",$_data=array(),$_selected=false,$_enabled=true)
	{
		$_item = new KoolComboboxItem();
		$_item->enabled = (isset($_enabled))?$_enabled:false;
		$_item->selected = (isset($_selected))?$_selected:false;
		if($_text!="") $_item->data["text"] = $_text;
		$_item->data["value"] = $_value;
		if (isset($_data))
		{
			foreach($_data as $_k=>$_v)
			{
				$_item->data[$_k] = $_v;	
			}			
		}				
		array_push($this->_items,$_item);
		return $_item;
	}
	
	
	function LoadXML($_xml)
	{
		if (gettype($_xml)=="string")
		{
			$_xmlDoc = new DOMDocument();
			$_xmlDoc->loadXML($_xml);
			$_xml = $_xmlDoc->documentElement;
		}
		//id
		$_id = $_xml->getAttribute("id");
		if($_id!="") $this->id = $_id;
		//styleFolder
		$_styleFolder = $_xml->getAttribute("styleFolder");
		if($_styleFolder!="") $this->styleFolder = $_styleFolder;
		//scriptFolder
		$_scriptFolder = $_xml->getAttribute("scriptFolder");
		if($_scriptFolder!="") $this->scriptFolder = $_scriptFolder;		
		//boxHeight
		$_boxHeight = $_xml->getAttribute("boxHeight");
		if($_boxHeight!="") $this->boxHeight = $_boxHeight;
		//maxBoxHeight
		$_maxBoxHeight = $_xml->getAttribute("maxBoxHeight");
		if($_maxBoxHeight!="") $this->maxBoxHeight = $_maxBoxHeight;
		//minBoxHeight
		$_minBoxHeight = $_xml->getAttribute("minBoxHeight");
		if($_minBoxHeight!="") $this->minBoxHeight = $_minBoxHeight;
		//openDirection
		$_openDirection = $_xml->getAttribute("openDirection");
		if($_openDirection!="") $this->openDirection = $_openDirection;
		//effect
		$_effect = $_xml->getAttribute("effect");
		if($_effect!="") $this->effect = $_effect;
		//width
		$_width = $_xml->getAttribute("width");
		if($_width!="") $this->width = $_width;
		//boxWidth
		$_boxWidth = $_xml->getAttribute("boxWidth");
		if($_boxWidth!="") $this->boxWidth = $_boxWidth;

		//selectedText
		//$_selectedText = $_xml->getAttribute("selectedText");
		//if($_selectedText!="") $this->selectedText = $_selectedText;
		//selectedValue
		//$_selectedValue = $_xml->getAttribute("selectedValue");
		//if($_selectedValue!="") $this->selectedValue = $_selectedValue;

		//serviceFunction
		$_serviceFunction = $_xml->getAttribute("serviceFunction");
		if($_serviceFunction!="") $this->serviceFunction = $_serviceFunction;

		//align
		$_align = $_xml->getAttribute("align");
		if($_align!="") $this->align = $_align;
		
		//mode
		$_mode = $_xml->getAttribute("mode");
		if($_mode!="") $this->mode = $_mode;
	
		//inputValidate
		$_inputValidate = strtolower($_xml->getAttribute("inputValidate"));
		if($_inputValidate!="") $this->inputValidate = ($_inputValidate=="true")?true:false;

		//superAbove
		$_superAbove = strtolower($_xml->getAttribute("superAbove"));
		if($_superAbove!="") $this->superAbove = ($_superAbove=="true")?true:false;



		foreach($_xml->childNodes as $_koolcombobox_subnode)
		{
			switch(strtolower($_koolcombobox_subnode->nodeName))
			{
				case "items":
					foreach($_koolcombobox_subnode->childNodes as $_items_subnode)
					{
						if (strtolower($_items_subnode->nodeName)=="item")
						{
							$_enabled = $_items_subnode->getAttribute("enabled");
							$_enabled = ($_enabled!="")?$_enabled:"true";
							$_selected = $_items_subnode->getAttribute("selected");
							$_selected = ($_selected!="")?$_selected:"false";
							
							$_data = array("text"=>"","value"=>"");
							
							foreach($_items_subnode->attributes as $_attributes)
							{
								
								if ($_attributes->name!="enabled" && $_attributes->name!="selected")
								{
									$_data[$_attributes->name] = $_attributes->value;
								}
							}
							$this->addItem($_data["text"],$_data["value"],$_data,($_selected=="true")?true:false,($_enabled=="true")?true:false);
							
						}
					}					
					break;
				case "templates":
					foreach($_koolcombobox_subnode->childNodes as $_templates_subnode)
					{
						switch(strtolower($_templates_subnode->nodeName))
						{
							case "headertemplate":
								$this->headerTemplate = _getInnerXML($_templates_subnode,$_xml->parentNode);
								break;
							case "itemtemplate":
								$this->itemTemplate = _getInnerXML($_templates_subnode,$_xml->parentNode);
								break;
							case "footertemplate":
								$this->footerTemplate = _getInnerXML($_templates_subnode,$_xml->parentNode);
								break;								
						}
					}
					break;				
			}			
		}		


	}
	
	function _positionStyle()
	{
		//$this->styleFolder = _getAbsolutePath($this->styleFolder);
		//$lastslashPosition = strrpos($this->styleFolder,"/")+1;
		$this->styleFolder = _replace("\\","/",$this->styleFolder);
		$_styleFolder = trim($this->styleFolder,"/");
		$_lastpos = strrpos($_styleFolder,"/");
		$this->_style = substr($_styleFolder,($_lastpos?$_lastpos:-1)+1);
	}
	
	function Render()
	{
		//global $_version;
		$_script="\n<!--KoolCombobox version ".$this->_version." - www.koolphp.net -->\n";		
		$_script.= $this->RegisterCss();
		$_script.= $this->RenderCombobox();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;	
	}
	function RenderCombobox()
	{
		$this->_positionStyle();
		//$_root = _getRoot();
		//global $_showExpireEncodeString;
		//Should declare before load
		$tpl_bound = "{boundcontent}";
		$tpl_box = "{boxcontent}";
		$tpl_item = "{itemcontent}";
		//include _getRoot()._getAbsolutePath($this->styleFolder)."/".$this->_style.".tpl";
		//include $this->styleFolder."/".$this->_style.".tpl";
		include "styles"."/".$this->_style."/".$this->_style.".tpl";
		
		//$_tpl_main = "{0}<div id='{id}' class='{style}KCB' style='width:{width};'><div style='position:relative;'>{combo}{box}</div>{1}<div id='{id}_itemtemplate' style='display:none'>{itemtemplate}</div></div>{2}";
		//Combo
		$_tpl_combo = "<div class='kcb{mode}'>{tpl_bound}</div>";
		$_tpl_combo_core = "<table><tr><td style='width:100%;'>{input}</td><td>{arrow}</td></tr></table>";
		$_tpl_input = "<input id='{id}_selectedText' name='{id}_selectedText' type='text' class='kcbInput nodecor' autocomplete='off' /><input type='hidden' id='{id}_selectedValue' name='{id}_selectedValue' />";
		$_tpl_arrow = "<img id='{id}_arrow' src='{stylefolder}/none.gif' class='kcbArrow' alt='' />";
		//$_expiredString=_encode($this->_showExpireEncodeString,-50);
	
		//Box
		/*
		$_tpl_box = "<div class='kcbBox'>{tpl_box}</div>";
		$_tpl_box_core = "<ul class='kcbUL'>{header}{item}{footer}</ul>";
		
		$_tpl_header = "<li class='kcbLI'><div class='kcbHeader'>{headercontent}</div></li>";
		$_tpl_footer = "<li class='kcbLI'><div class='kcbFooter'>{footercontent}</div></li>";
		$_tpl_item_box = "<li class='kcbLI'><div class='kcbItemBox' style='height:{boxHeight}'>{itemscontent}</div></li>";
		
		$_tpl_itemcontent = "<ul class='kcbUL'>{items}</ul>";
		$_tpl_item = "<li id='{id}' class='kcbLI kcbItem {disable}'>{item_data}<a href='javascript:void 0' class='kcbA'><div class='kcbIn'>{itemTemplate}</div></a></li>";
		$_tpl_item_data = "<input type='hidden' value=\"{data}\">";
		
		$_tpl_itemtext = "<span class='kcbText'>{text}</span>";
		$_tpl_itemimage = "<img class='kcbImage' src='{src}' alt=''/>";
		*/
		$_tpl_box = "<div class='kcbBox'>{tpl_box}</div>{box_iframe}";
		$_tpl_box_iframe = "<iframe class='kcbIframe' src='javascript:false;'> </iframe>";
		$_tpl_box_core = "{header}{item}{footer}";
		
		$_tpl_header = "<div class='kcbHeader'>{headercontent} </div>";
		$_tpl_footer = "<div class='kcbFooter'>{footercontent} </div>";
		$_tpl_item_box = "<div class='kcbItemBox' style='height:{boxHeight}'>{itemscontent}</div>";
		
		$_tpl_itemcontent = "<ul class='kcbUL'>{items}</ul>";
		$_tpl_item = "<li class='kcbLI kcbItem {disable} {selected}'>{item_data}<a href='javascript:void 0' class='kcbA'><span class='kcbIn'>{tpl_item}</span></a></li>";
		$_tpl_item_data = "<input type='hidden' value=\"{data}\" />";
		//$_tpl_itemtext = "<span class='kcbText'>{text}</span>";
		//$_tpl_itemimage = "<img class='kcbImage' src='{src}' alt=''/>";
		//$_tpl_reminder = "<div style='font-family:Arial;font-size:10pt;background-color:#FEFFDF;color:black;display:inline;visibility:visible;'><span style='font-family:Arial;font-size:10pt;font-weight:bold;color:black;display:inline;visibility:visible;'>KoolComboBox</span> - Trial version {version} - Copyright (C) KoolPHP .Inc - <a style='font-family:Arial;font-size:10pt;display:inline;visibility:visible;' href='http://www.koolphp.net'>www.koolphp.net</a>. <span style='font-family:Arial;color:black;font-size:10pt;display:inline;visibility:visible;'>To remove</span> this message, please <a style='font-family:Arial;font-size:10pt;display:inline;visibility:visible;' href='http://www.koolphp.net/?mod=purchase'>purchase a license</a>.</div>";

		
		//Render
		
		//Render - Box
		
		$_box = _replace("{tpl_box}",$tpl_box,$_tpl_box);
		$_box = _replace("{box_iframe}",($this->superAbove)?$_tpl_box_iframe:"",$_box);
		$_box = _replace("{boxcontent}",$_tpl_box_core,$_box);
		//Header
		$_header = "";
		if ($this->headerTemplate!="")
		{
			$_header = _replace("{headercontent}",$this->headerTemplate,$_tpl_header);
		}
		$_box = _replace("{header}",$_header,$_box);
		//Footer
		$_footer ="";
		if ($this->footerTemplate!="")
		{
			$_footer = _replace("{footercontent}",$this->footerTemplate,$_tpl_footer);
		}
		$_box = _replace("{footer}",$_footer,$_box);
		
		//ItemsContent
		$_items = "";
		foreach($this->_items as $_item)
		{		
			$_itemtemp = $this->itemTemplate;
			foreach ($_item->data as $_key => $_value)
			{
				$_itemtemp = _replace("{".$_key."}",$_value,$_itemtemp);
			}
			
			$item_render = _replace("{tpl_item}",$tpl_item,$_tpl_item);
			$item_render = _replace("{itemcontent}",$_itemtemp,$item_render);
			
			//$item_render = _replace("{id}",$this->id.".i".$_index,$item_render);
			$item_render = _replace("{disable}",($_item->enabled)?"":"kcbDisable",$item_render);	
			$item_render = _replace("{selected}",($_item->selected)?"kcbSelected":"",$item_render);	
			//Render data of item
			$_itemdatavalue = "";
			foreach($_item->data as $_key=>$_value)
			{
				$_itemdatavalue.=",'".$_key."':'"._esc($_value)."'";
			}
			$_itemdatavalue = "{".substr($_itemdatavalue,1)."}";
			$_item_data = _replace("{data}",$_itemdatavalue,$_tpl_item_data);
			
			$item_render = _replace("{item_data}",$_item_data,$item_render);
			
			$_items.= $item_render;
			//$_index++;
						
		}
		$_itemcontent = _replace("{items}",$_items,$_tpl_itemcontent);
		$_item_box = _replace("{itemscontent}",$_itemcontent,$_tpl_item_box);
		$_item_box = _replace("{boxHeight}",$this->boxHeight,$_item_box);
		$_box = _replace("{item}",$_item_box,$_box);
		
		//Render - Combobox
		$_input  = _replace("{id}",$this->id,$_tpl_input);
		$_arrow = _replace("{id}",$this->id,$_tpl_arrow);
		$_arrow = _replace("{stylefolder}",$this->_getStylePath()."/".$this->_style,$_arrow);
		

		$_combo =  _replace("{id}",$this->id,$_tpl_combo);
		$_combo =  _replace("{tpl_bound}",$tpl_bound,$_combo);
		$_combo =  _replace("{boundcontent}",$_tpl_combo_core,$_combo);
		$_combo =  _replace("{input}",$_input,$_combo);
		$_combo =  _replace("{arrow}",($this->mode=="combobox")?$_arrow:"",$_combo);
		$_combo =  _replace("{mode}",($this->mode=="combobox")?"Combobox":"Textbox",$_combo);

		
		
		//Render - All
		$_main = _replace("{id}",$this->id,"<div id='{id}' class='{style}KCB' style='z-index:4000;width:{width};'><div>{combo}{box}</div><div id='{id}_itemtemplate' style='display:none'>{itemtemplate}</div></div>");
		$_main = _replace("{style}",$this->_style,$_main);
		$_main = _replace("{width}",$this->width,$_main);
		$_main = _replace("{itemtemplate}",$tpl_item,$_main);
		$_main = _replace("{itemcontent}",$this->itemTemplate,$_main);
		

		$_main = _replace("{combo}",$_combo,$_main);			
		$_main = _replace("{version}",$this->_version,$_main);
		$_main = _replace("{box}",$_box,$_main);
		
		
		return $_main;
	}

	function RegisterCss()
	{
		/*
		 * Register Css
		 */
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KCB')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KCB';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;		
		
	}
	function RegisterScript()
	{
		/*
		 * Register javascript
		 */
		
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKCB=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKCB=1;}</script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script);
		
		return $_script;		
		
	}
	function StartupScript()
	{
		/*
		 * Generate startup script
		 */
		$_tpl_script  = "var {id}; function {id}_init(){ {id}=new KoolCombobox('{id}','{mode}','{boxWidth}','{boxHeight}','{minBoxHeight}','{maxBoxHeight}',{inputValidate},'{openDirection}','{align}','{serviceFunction}');}";
		$_tpl_script .= "if (typeof(KoolCombobox)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KCBInits)=='undefined'){__KCBInits=new Array();} __KCBInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKCB)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKCB=1;}";
		
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script);
		
		$_script = _replace("{id}",$this->id,$_tpl_script);
		$_script = _replace("{mode}",$this->mode,$_script);
		$_script = _replace("{boxWidth}",$this->boxWidth,$_script);
		$_script = _replace("{boxHeight}",$this->boxHeight,$_script);
		$_script = _replace("{minBoxHeight}",$this->minBoxHeight,$_script);						
		$_script = _replace("{maxBoxHeight}",$this->maxBoxHeight,$_script);				
		$_script = _replace("{inputValidate}",($this->inputValidate)?"1":"0",$_script);				
		$_script = _replace("{openDirection}",$this->openDirection,$_script);
		$_script = _replace("{align}",$this->align,$_script);
		$_script = _replace("{serviceFunction}",$this->serviceFunction,$_script);		

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
	
}

?>