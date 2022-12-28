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



if (!class_exists("KoolTabs",false))
{

class KoolTabsItem
{
	var $id;
	var $text;
	var $link;
	var $enabled = true;
	var $selected = false;
	var $width = "";
	var $height = "";
	var $children;
	var $_parent = null;
	var $_level = -1;
	var $_break = false;
	
	function __construct($_id,$_text)
	{
		$this->id = $_id;
		$this->text = $_text;
		$this->children = array();
	}
	function addChild($_child)
	{
		$_child->_parent = $this;
		$_child->_level = $this->_level+1;
		array_push($this->children,$_child);
	}
}


class KoolTabs
{
	var $_version = "1.8.0.0";
	
	var $id;
	
	var $styleFolder="";
	var $_style;
	
	var $scriptFolder="";
		
	var $width ="auto";
	/*
	 * width: The width of tabs, setting width may make single tab longer
	 */
	var $height ="auto";	
	/*
	 * height: The height of tabs
	 */
	var $position = "top";
	/*
	 * The position of the tabs
	 * "top":"left":"bottom":"right"
	 */
	var $align = "left";
	/*
	 * "left":"right":"center":"justify"
	 */
	//var $overflow = "";//no overflow
	/*
	 * "":"hidden":"auto":"scroll"
	 */
	var $scroll = "hidden";
	/*
	 * "hidden":"left":"right":"middle"
	 */

	//var $keepState = "none";
	/*
	 * "none":"onpage":"crosspage"
	 */
	
	var $_root;
	var $_list;
	
	function __construct($_id)
	{
		$this->id = $_id;
		$this->_root = new KoolTabsItem("root","");
		$this->_root->selected = true;
		$this->_list = array();
		$this->_list["root"] = $this->_root;
	}
	
	function addTab($_parentid,$_id,$_text="New tab",$_link="",$_selected=false,$_enabled=true,$_width="",$_height="")
	{
		$_parenttab = $this->_root;
		if (isset($this->_list[$_parentid]))
		{
			$_parenttab = $this->_list[$_parentid];
		}
		$_newtab = new KoolTabsItem($_id,$_text);
		if ($_selected)
		{
			foreach($_parenttab->children as $_sibling)
			{
				$_sibling->selected = false;
			}		
		}
		$_newtab->link = ($_link==null)?"":$_link;
		$_newtab->selected = $_selected;
		$_newtab->enabled = $_enabled;
		$_newtab->width = $_width;
		$_newtab->height = $_height;		
		$_parenttab->addChild($_newtab);
		$this->_list[$_id] = $_newtab;
		return $_newtab;
	}
	
	function addBreak($_parentid)
	{
		$_parenttab = $this->_root;
		if (isset($this->_list[$_parentid]))
		{
			$_parenttab = $this->_list[$_parentid];
		}
		$_break = new KoolTabsItem("","");
		$_break->_break = true;
		$_parenttab->addChild($_break);
	}
	function getTab($_id)
	{
		return $this->_list[$_id];
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
		
		//width
		$_width = $_xml->getAttribute("width");
		if($_width!="") $this->width = $_width;


		//height
		$_height = $_xml->getAttribute("height");
		if($_height!="") $this->height = $_height;


		//position
		$_position = $_xml->getAttribute("position");
		if($_position!="") $this->position = $_position;
		
		//align
		$_align = $_xml->getAttribute("align");
		if($_align!="") $this->align = $_align;
		
		//scroll
		$_scroll = $_xml->getAttribute("scroll");
		if($_scroll!="") $this->scroll = $_scroll;
		
		
		$this->_buildTabs($_xml);
		
	}
	
	function _buildTabs($_parent)
	{
		foreach($_parent->childNodes as $_parent_subnode)
		{
			switch(strtolower($_parent_subnode->nodeName))
			{
				case "tab":
						$_id = $_parent_subnode->getAttribute("id");
						$_text = $_parent_subnode->getAttribute("text");
						$_link = $_parent_subnode->getAttribute("link");						
						$_selected = (strtolower($_parent_subnode->getAttribute("selected"))=="true")?true:false;
						$_enabled = (strtolower($_parent_subnode->getAttribute("enabled"))=="false")?false:true;
						$_width = $_parent_subnode->getAttribute("width");
						$_height = $_parent_subnode->getAttribute("height");						
						$_parentid = (strtolower($_parent->nodeName)=="tab")?$_parent->getAttribute("id"):"root";	
						$this->addTab($_parentid,$_id,$_text,$_link,$_selected,$_enabled,$_width,$_height);
						$this->_buildTabs($_parent_subnode);
					break;
				case "break":
						$_parentid = (strtolower($_parent->nodeName)=="tab")?$_parent->getAttribute("id"):"root";	
						$this->addBreak($_parentid);						
					break;
			}
							
		}		
	}
	
	function Render()
	{
		//global $_version;
		$_script="\n<!--KoolTabs version ".$this->_version." - www.koolphp.net -->\n";		
		$_script.= $this->RegisterCss();
		$_script.= $this->RenderTabs();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;	
	}
	function RenderTabs()
	{
		$this->_positionStyle();
		//global $_showExpireEncodeString;
		$tpl_bound = '{boundcontent}';
		$tpl_level = '{levelcontent}';
		$tpl_tab ='<span class="ktsOut"><span class="ktsIn">{tabcontent} </span></span>';

		//include _getRoot()._getAbsolutePath($this->styleFolder)."/".$this->_style.".tpl";
		//include $this->styleFolder."/".$this->_style.".tpl";
		include "styles"."/".$this->_style."/".$this->_style.".tpl";
		
		//$_tpl_main = "{0}{1}<div id='{id}' class='{style}KTS {style}{position}KTS' style='width:{width};height:{height};'>{tpl_bound}<div style='clear:both;'></div>{selected}{tabtemplate}</div>{2}";
		$_tpl_div_level = "<div class='ktsLevel ktsLevel{level} kts{align} {scroll}'>{tpl_level}{nextarrow}{prevarrow}</div>";
		$_tpl_input_selected = "<input id='{id}_selected' name='{id}_selected' type='hidden' />";
		
		$_tpl_arrow = "<a class='kts{arrow}'> </a>";
		
		$_tpl_template = "<div id='{id}.template' style='display:none'>{template}</div>";
		
		//$_expiredString=_encode($this->_showExpireEncodeString,-50);
		//$_tpl_reminder = "<div style='font-family:Arial;font-size:10pt;background-color:#FEFFDF;color:black;display:inline;visibility:visible;'><span style='font-family:Arial;font-size:10pt;font-weight:bold;color:black;display:inline;visibility:visible;'>KoolTabs</span> - Trial version {version} - Copyright (C) KoolPHP .Inc - <a style='font-family:Arial;font-size:10pt;display:inline;visibility:visible;' href='http://www.koolphp.net'>www.koolphp.net</a>. <span style='font-family:Arial;color:black;font-size:10pt;display:inline;visibility:visible;'>To remove</span> this message, please <a style='font-family:Arial;font-size:10pt;display:inline;visibility:visible;' href='http://www.koolphp.net/?mod=purchase'>purchase a license</a>.</div>";
		
		
		$_main = _replace("{id}",$this->id,"<div id='{id}' class='{style}KTS {style}{position}KTS' style='width:{width};height:{height};'>{tpl_bound}<div style='clear:both;'></div>{selected}{tabtemplate}</div>");
		$_main = _replace("{style}",$this->_style,$_main);
		$_main = _replace("{width}",$this->width,$_main);
		$_main = _replace("{height}",$this->height,$_main);
		$_main = _replace("{position}",$this->position,$_main);
		$_main = _replace("{tpl_bound}",$tpl_bound,$_main);

		$_boundcontent = "";
		$_levels = array();		
		$this->_buildTabLevel($this->_root,$_levels);	
		
		
		for($i=0;$i<sizeof($_levels);$i++)
		{
			//If position is bottom then render from bottom up
			$pos = ($this->position=="bottom")?sizeof($_levels)-$i-1:$i;			
			$_div_level = _replace("{level}",$pos,$_tpl_div_level);
			
			
			if($this->scroll!="hidden")
			{
				$_div_level = _replace("{scroll}","kts".$this->scroll."Scroll",$_div_level);
				$_prevarrow = _replace("{arrow}","Prev",$_tpl_arrow);
				$_div_level = _replace("{prevarrow}",$_prevarrow,$_div_level);
				$_nextarrow = _replace("{arrow}","Next",$_tpl_arrow);				
				$_div_level = _replace("{nextarrow}",$_nextarrow,$_div_level);
			}
			else
			{
				$_div_level = _replace("{scroll}","",$_div_level);
				$_div_level = _replace("{nextarrow}{prevarrow}","",$_div_level);
			}
			
			
			$_div_level = _replace("{tpl_level}",$tpl_level,$_div_level);
			$_div_level = _replace("{levelcontent}",$_levels[$pos],$_div_level);
			$_div_level = _replace("{align}",$this->align,$_div_level);
			
			$_boundcontent.=$_div_level;
		}
		
		$_selected = _replace("{id}",$this->id,$_tpl_input_selected);
		
		$_tab_template = _replace("{id}",$this->id.".tab",$_tpl_template);
		$_tab_template = _replace("{template}",$tpl_tab,$_tab_template);
		
		$_main = _replace("{boundcontent}",$_boundcontent,$_main);
		$_main = _replace("{selected}",$_selected,$_main);
		
			$_main = _replace("{tabtemplate}",$_tab_template,$_main);
		
		$_main = _replace("{version}",$this->_version,$_main);

		return $_main;
	}
	
	function _buildTabLevel($_tab,&$_levels)
	{
		$tpl_bound = '{boundcontent}';
		$tpl_level = '{levelcontent}';		
		$tpl_tab ='<span class="ktsOut"><span class="ktsIn">{tabcontent}</span></span>';
		include "styles"."/".$this->_style."/".$this->_style.".tpl";
		
		$_tpl_ul = "<ul id='{ktabid}.{id}.sub' class='ktsUL' style='display:{display}'>{lis}</ul>";
		$_tpl_li = "<li id='{id}' class='ktsLI {place} {enable}' style='width:{width};height:{height}'>{a}</li>";
		$_tpl_li_break = "<li class='ktsBreak'></li>";

		$_tpl_a = "<a class='ktsA {select}' {href} >{tpl_tab} </a>";
		$_tpl_text = "<span class='ktsText'>{text} </span>";
		
		//Break;
		if ($_tab->_break)
		{
			return $_tpl_li_break;
		}
		
		$_place = "";
		$_pos = 0;
		if($_tab!==$this->_root)
		{
			for($i=0;$i<sizeof($_tab->_parent->children);$i++)
				if($_tab===$_tab->_parent->children[$i])
					$_pos = $i;

			if ($_pos==0 || $_tab->_parent->children[$_pos-1]->_break)
			{
				$_place .= " ktsFirst";
			}
			
			if($_pos==sizeof($_tab->_parent->children)-1 || $_tab->_parent->children[$_pos+1]->_break)
			{
				$_place .= " ktsLast";
			}			
		}		
		if ($_place == "") $_place = "ktsMid";
		
		
		$_tabhtml = _replace("{id}",$_tab->id,$_tpl_li);
		$_tabhtml = _replace("{place}",$_place,$_tabhtml);
		$_tabhtml = _replace("{enable}",($_tab->enabled)?"":"ktsDisable",$_tabhtml);
		$_tabhtml = _replace("{width}",$_tab->width,$_tabhtml);
		$_tabhtml = _replace("{height}",$_tab->height,$_tabhtml);
		
		$_tabhtml = _replace("{a}",$_tpl_a,$_tabhtml);
		$_tabhtml = _replace("{href}",($_tab->link!="")?"href='".$_tab->link."'":"",$_tabhtml);
		
		
		if ($_tab->selected)
		{
			$_tabhtml = _replace("{select}","ktsSelected",$_tabhtml);	
		}
		if ($_tab!==$this->_root)
		{
			//$_prev = $_tab->_parent->children[$_pos-1];
			if (isset($_tab->_parent->children[$_pos-1])&& $_tab->_parent->children[$_pos-1]->selected)
				$_tabhtml = _replace("{select}","ktsAfter",$_tabhtml);
			//$_next = $_tab->_parent->children[$_pos+1];
			if (isset($_tab->_parent->children[$_pos+1])&& $_tab->_parent->children[$_pos+1]->selected)
				$_tabhtml = _replace("{select}","ktsBefore",$_tabhtml);
		}
		$_tabhtml = _replace("{select}","",$_tabhtml);
		
		$_tabhtml = _replace("{tpl_tab}",$tpl_tab,$_tabhtml);
		$_tabhtml = _replace("{tabcontent}",$_tpl_text,$_tabhtml);
		$_tabhtml = _replace("{text}",$_tab->text,$_tabhtml);

		if (sizeof($_tab->children)>0)
		{
			$_ul = _replace("{id}",$_tab->id,$_tpl_ul);
			$_ul = _replace("{ktabid}",$this->id,$_ul);
			$_ul = _replace("{display}",($_tab->selected)?"":"none",$_ul);			
			
			$_lis = "";
			foreach($_tab->children as $_childtab)
			{
				$_lis.=$this->_buildTabLevel($_childtab,$_levels);
			}
			$_ul = _replace("{lis}",$_lis,$_ul);
			
			if(!isset($_levels[$_tab->_level+1]))
			{
				
				$_levels[$_tab->_level+1] = "";	
			}
			$_levels[$_tab->_level+1].=$_ul;
		}
		
		return $_tabhtml;
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
		/*
		 * Register Css
		 */
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KTS')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KTS';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;		

	}
	function RegisterScript()
	{
		/*
		 * Register javascript
		 */
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKTS=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKTS=1;}</script>";
		
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script);
		
		return $_script;		
		
	}
	function StartupScript()
	{
		/*
		 * Generate startup script
		 */
		
		$_tpl_script  = "var {id}; function {id}_init(){ {id}=new KoolTabs('{id}','{position}');}";
		$_tpl_script .= "if (typeof(KoolTabs)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KTSInits)=='undefined'){__KTSInits=new Array();} __KTSInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKTS)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKTS=1;}";
		
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script);
		
		$_script = _replace("{id}",$this->id,$_tpl_script);
		$_script = _replace("{position}",$this->position,$_script);
		$_script = _replace("{register_script}",$_register_script,$_script);
		
		return $_script;
	}
	function _getComponentURI()
	{
		if ($this->scriptFolder=="")
		{
			//In case the scriptFolder is not specified, use the absolute path
			$_rootfolder = _getRoot();
			$_file = substr(_replace("\\","/",__FILE__),strlen($_rootfolder));
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