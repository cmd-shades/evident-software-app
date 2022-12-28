<?php
//$_version = "1.6.0.0";
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



if (!class_exists("KoolSlideMenu",false))
{

class SlideMenuParent
{
	var $id;
	var $text;
	var $link;
	var $target;
	var $title;
	var $expand = false;
	var $parent;
	
	var $_children = array();
	var $_level=-1;//The depth level in tree.
	
	function __construct($_id)
	{
		$this->id = $_id;
	}
	function addChild($_child)
	{
		array_push($this->_children,$_child);
		$_child->parent = $this;
		if (strtolower(get_class($_child))=="slidemenuparent")
		{
			$_child->_level = $this->_level+1;
		}
	}
}
class SlideMenuChild
{
	var $id;
	var $text;
	var $link;
	var $target;
	var $parent;
	var $title;	
	var $_selected = false;
	
	function __construct($_id)
	{
		$this->id = $_id;
	}
}
class SlideMenuPanel
{
	var $id;
	var $content;
	function __construct($_id)
	{
		$this->id = $_id;
	}
}

class KoolSlideMenu
{
	var $_version = "1.6.0.0";
	
		
	var $id;
	var $styleFolder;
	var $scriptFolder="";
	var $singleExpand;
	var $boxHeight = -1;
	var $slidingSpeed =5;
	var $scrollEnable = false;
	var $width="auto";
	var $selectedId;
	var $selectEnable = true;
	var $_root;
	var $_list;

	var $_style;
	
	function __construct($_id)
	{
		$this->id = $_id;
		$this->_root = new SlideMenuParent("root");
		$this->_list = array();
		$this->_list["root"] = $this->_root;
	}

	function loadXML($_xml)
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
		//singleExpand
		$_singleExpand = strtolower($_xml->getAttribute("singleExpand"));
		if($_singleExpand!="") $this->singleExpand = ($_singleExpand=="true")?true:false;
		//boxHeight
		$_boxHeight = $_xml->getAttribute("boxHeight");
		if($_boxHeight!="") $this->boxHeight = intval($_boxHeight);
		//boxHeight
		$_slidingSpeed = $_xml->getAttribute("slidingSpeed");
		if($_slidingSpeed!="") $this->slidingSpeed = intval($_slidingSpeed);
		//scrollEnable
		$_scrollEnable = strtolower($_xml->getAttribute("scrollEnable"));
		if($_scrollEnable!="") $this->scrollEnable = ($_scrollEnable=="true")?true:false;
		//width
		$_width = $_xml->getAttribute("width");
		if($_width!="") $this->width = $_width;
		//styleFolder
		$_selectedId = $_xml->getAttribute("selectedId");
		if($_selectedId!="") $this->selectedId = $_selectedId;

		$this->_buildChildren($this->_root,$_xml,$_xml->parentNode);

	}
	function _buildChildren($_parent,$_xmlnode,$_xmlDoc)
	{
		foreach($_xmlnode->childNodes as $_xmlchildnode)
		{
			switch(strtolower($_xmlchildnode->nodeName))
			{
				case "parent":
						$_id = $_xmlchildnode->getAttribute("id");
						$_text = $_xmlchildnode->getAttribute("text");
						$_link = $_xmlchildnode->getAttribute("link");
						$_target = $_xmlchildnode->getAttribute("target");
						$_title = $_xmlchildnode->getAttribute("title");
						
						$_expand = (strtolower($_xmlchildnode->getAttribute("expand"))=="true")?true:false;
						//add to root
						$_subparent = $this->addParent($_parent->id,$_id,$_text,$_link,$_expand);
						$_subparent->target = $_target;
						$_subparent->title = $_title;
						$this->_buildChildren($_subparent,$_xmlchildnode,$_xmlDoc);	
					break;
				case "child":
						$_id = $_xmlchildnode->getAttribute("id");
						$_text = $_xmlchildnode->getAttribute("text");
						$_link = $_xmlchildnode->getAttribute("link");
						$_target = $_xmlchildnode->getAttribute("target");
						$_title = $_xmlchildnode->getAttribute("title");
						//add to root
						$_subchild = $this->addChild($_parent->id,$_id,$_text,$_link);
						$_subchild->target = $_target;
						$_subchild->title = $_title;
					break;
				case "panel":
						$_id = $_xmlchildnode->getAttribute("id");
						$_content = _getInnerXML($_xmlchildnode,$_xmlDoc);
						//add to root
						$this->addPanel($_parent->id,$_id,$_content);
					break;				
			}			
		}
	}

	
	function addParent($_parentid,$_id,$_text="",$_link="",$_expand=false)
	{
		$_item = new SlideMenuParent($_id);
		$_item->text = $_text;
		$_item->expand = $_expand;
		$_item->link = ($_link==null||$_link=="")?"javascript:void 0":$_link;
		$this->_list[$_parentid]->addChild($_item);
		$this->_list[$_id] = $_item;
		return $_item;
	}
	function addChild($_parentid,$_id,$_text="",$_link="")
	{
		$_item = new SlideMenuChild($_id);
		$_item->text = $_text;
		$_item->link = ($_link==null||$_link=="")?"javascript:void 0":$_link;
		$this->_list[$_parentid]->addChild($_item);
		$this->_list[$_id] = $_item;
		return $_item;
	}
	
	function addPanel($_parentid,$_id,$_content)
	{
		$_item = new SlideMenuPanel($_id);
		$_item->content = $_content;
		$this->_list[$_parentid]->addChild($_item);
		$this->_list[$_id] = $_item;
		return $_item;		
	}
	
	function getItem($_id)
	{
		//Return any item object with $_id regardless it's parent, child or panel.
		return $this->_list[$_id];
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
		//$_script="<link rel='stylesheet' href='".$this->styleFolder."/".$this->_style.".css' />";
		//$_tpl_script = "<script type='text/javascript'>if(typeof __{style}KSM=='undefined'){document.write(unescape(\"%3Clink rel='stylesheet' href='{stylepath}/{style}/{style}.css' /%3E\"));__{style}KSM=1;}</script>";
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KSM')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KSM';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}
	
	function Render()
	{
		$_script="\n<!--KoolSlideMenu version ".$this->_version." - www.koolphp.net -->\n";
		$_script.= $this->RegisterCss();
		$_script.= $this->RenderSlideMenu();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;
	}
	
	function RenderSlideMenu()
	{
		//Default
		$tpl_bound = "{boundcontent}";
		$tpl_parent = "<div class='ksmIn'>{parentcontent}</div>";
		$tpl_childbox = "{childboxcontent}";
		$tpl_child = "<span class='ksmIn'>{childcontent}</span>";
		$tpl_panel = "<div class='ksmIn'>{panelcontent}</div>";
		//Include template
		$this->_positionStyle();
		include "styles"."/".$this->_style."/".$this->_style.".tpl";
		
		$_tpl_boundcontent = "<ul class='ksmUL {boxHeight}'>{parents}</ul>";
		$_tpl_boxHeight ="<style rel='stylesheet'> .{style}KSM .ksmBoxHeight .ksmChildBox {height:{boxHeight}px;overflow:{overflow};} </style>";
		$_main= _replace("{tpl_bound}",$tpl_bound,"{boxHeight}<div id='{id}' class='{style}KSM' style='width:{width};' > {tpl_bound} <input id='{id}.clientState' name='{id}.clientState' type='hidden' /></div>");
		$_main= _replace("{id}",$this->id,$_main);
		$_main= _replace("{width}",$this->width,$_main);		
		$_main= _replace("{style}",$this->_style,$_main);
		$_item = $this->_root;
		$_parents = "";
		for($i=0;$i<sizeof($_item->_children);$i++)
		{
			$_parents.=	$this->_RenderSlideMenuItem($_item->_children[$i]);
		}
		$_boundcontent = _replace("{parents}",$_parents,$_tpl_boundcontent);
		if($this->boxHeight<0)
		{
			$_boxHeight ="";
			$_boundcontent= _replace("{boxHeight}","",$_boundcontent);
		}
		else
		{
			$_boxHeight = _replace("{style}",$this->_style,$_tpl_boxHeight);
			$_boxHeight = _replace("{boxHeight}",$this->boxHeight,$_boxHeight);
			$_boxHeight = _replace("{overflow}",($this->scrollEnable)?"auto":"hidden",$_boxHeight);			
			$_boundcontent= _replace("{boxHeight}","ksmBoxHeight",$_boundcontent);	
		}
		
		$_main= _replace("{boundcontent}",$_boundcontent,$_main);
		$_main = _replace("{version}",$this->_version,$_main);
		
		$_main= _replace("{boxHeight}",$_boxHeight,$_main);		
		
		return $_main;
	}	
	
	function _RenderSlideMenuItem($_item)
	{
		//Default
		$tpl_bound = "{boundcontent}";
		$tpl_parent = "<div class='ksmIn'>{parentcontent}</div>";
		$tpl_childbox = "{childboxcontent}";
		$tpl_child = "<span class='ksmIn'>{childcontent}</span>";
		$tpl_panel = "<div class='ksmIn'>{panelcontent}</div>";
				
		include "styles"."/".$this->_style."/".$this->_style.".tpl";
		
		$_script = "";
		//Get the position of item with its siblings
		$_pos="";
		if($_item===$_item->parent->_children[0])
		{
			$_pos="ksmFirst";
		}
		else if ($_item===$_item->parent->_children[sizeof($_item->parent->_children)-1])
		{
			$_pos="ksmLast";
		}
				
		switch(strtolower(get_class($_item)))
		{
			case "slidemenuparent":
				//Templates:		
				$_tpl_parent = "<li id='{id}' class='ksmLI ksmLevel{level} {collapse} {pos}'>{parentcontent}{childbox}</li>";
				$_tpl_parentcontent = "<a class='ksmA ksmParent' href='{link}' {target} {title} >{tpl_parent}</a>";
				$_tpl_childbox = "<div class='ksmChildBox'>{tpl_childbox}</div>";
				$_tpl_subchildren = "<ul class='ksmUL'>{children}</ul>";
				//Parent content
				$_parentcontent = _replace("{tpl_parent}",$tpl_parent,$_tpl_parentcontent);
				$_parentcontent = _replace("{parentcontent}",$_item->text,$_parentcontent);
				$_parentcontent = _replace("{link}",$_item->link,$_parentcontent);
				$_parentcontent = _replace("{target}",($_item->target!=null)?"target='".$_item->target."'":"",$_parentcontent);
				$_parentcontent = _replace("{title}",($_item->title!=null)?"title='".$_item->title."'":"",$_parentcontent);
				


				//Sub content:
				$_children = "";
				for($i=0;$i<sizeof($_item->_children);$i++)
				{
					$_children.= $this->_RenderSlideMenuItem($_item->_children[$i]);
				}
				$_childbox = "";
				if ($_children!="")
				{
					//If there is children;
					$_subchidlren = _replace("{children}",$_children,$_tpl_subchildren);
					$_childbox = _replace("{tpl_childbox}",$tpl_childbox,$_tpl_childbox);			
					$_childbox = _replace("{childboxcontent}",$_subchidlren,$_childbox);
				}

				//Join all;
				$_script = _replace("{parentcontent}",$_parentcontent,$_tpl_parent);
				
				$_script = _replace("{childbox}",$_childbox,$_script);
				$_script = _replace("{id}",$_item->id,$_script);
				$_script = _replace("{level}",$_item->_level,$_script);
				$_script = _replace("{pos}",$_pos,$_script);
				$_script = _replace("{collapse}",($_item->expand)?"":"ksmCollapse",$_script);
				

				break;
			case "slidemenuchild":
					$_tpl_child = "<li id='{id}' class='ksmLI {pos}'><a class='ksmA ksmChild {selected}' href='{link}' {target} {title} >{tpl_child}</a></li>";
					$_script = _replace("{tpl_child}",$tpl_child,$_tpl_child);
					$_script = _replace("{childcontent}",$_item->text,$_script);
					$_script = _replace("{link}",$_item->link,$_script);
					$_script = _replace("{target}",($_item->target!=null)?"target='".$_item->target."'":"",$_script);
					$_script = _replace("{title}",($_item->title!=null)?"title='".$_item->title."'":"",$_script);
					
					$_script = _replace("{id}",$_item->id,$_script);
					$_script = _replace("{pos}",$_pos,$_script);					
					$_script = _replace("{selected}",($this->selectedId==$_item->id)?"ksmSelected":"",$_script);


				break;
			case "slidemenupanel":
					$_tpl_panel = "<li id='{id}' class='ksmLI ksmPanel {pos}'>{tpl_panel}</li>";
					$_script = _replace("{tpl_panel}",$tpl_panel,$_tpl_panel);
					$_script = _replace("{panelcontent}",$_item->content,$_script);
					$_script = _replace("{id}",$_item->id,$_script);
					$_script = _replace("{pos}",$_pos,$_script);					
				break;								
		}
		
		return $_script;
	}
	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKSM=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKSM=1;}</script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script);
		
		return $_script;
	}	
	
	function StartupScript()
	{
		$_tpl_script  = "var {id}; function {id}_init(){ {id} = new KoolSlideMenu('{id}',{selectEnable},{slidingSpeed},{singleExpand},{boxHeight},\"{clientState}\");}";
		$_tpl_script .= "if (typeof(KoolSlideMenu)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KSMInits)=='undefined'){__KSMInits=new Array();} __KSMInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKSM)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKSM=1;}";
		
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script);

		$_tpl_clientState = "{'selectedId':'{selectedId}'}";
		$_script = _replace("{id}",$this->id,$_tpl_script);
		$_script = _replace("{singleExpand}",($this->singleExpand)?"1":"0",$_script);
		$_script = _replace("{selectEnable}",($this->selectEnable)?"1":"0",$_script);
		$_script = _replace("{slidingSpeed}",$this->slidingSpeed,$_script);
		$_script = _replace("{boxHeight}",$this->boxHeight,$_script);
		$_clientState = _replace("{selectedId}",$this->selectedId,$_tpl_clientState);
		$_script = _replace("{clientState}",$_clientState,$_script);
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