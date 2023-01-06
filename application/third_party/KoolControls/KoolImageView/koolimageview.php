<?php
$_version = "1.4.1.0";
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
if (!class_exists("KoolImageView",false))
{
/*
function _getAbsolutePath($_relativePath)
{
	if ($_relativePath!="" && substr($_relativePath,0,1)=="/") 
		return $_relativePath;
	$_root = _getRoot();
	$_path = substr(_replace("\\","/",realpath($_relativePath)),strlen($_root));
	return $_path;	
}
*/
class KoolImageView
{
	var $_version = "1.4.1.0";
	var $id;
	var $styleFolder="";
	var $_style;
	var $scriptFolder="";
	var $imageUrl="";
	var $cssClass = "";
	var $bigImageUrl="";
	var $effect="zooming";//"fading":"zooming"
	var $backgroundOpacity=25;//true,false
	var $openTime=200;
	/*
	 * openTime: The milliseconds that ImageView will use to open panel
	 */
	var $frameNumber = 15;
	/*
	 * $frameNumber: the number of frames will be drawn when panel is open, the higher the value, the smoother panel will open.
	 */
	var $showLoading = true;
	var $description="";
	var $zIndex = 1000;
	var $position = "SCREEN_CENTER";// "SCREEN_CENTER"|"IMAGE_CENTER"|"RELATIVE"
	var $relativeLeft=0;
	var $relativeTop=0;
	var $alternative = "";
    var $thumbnailWidth = null;
    var $imageWidth = null;
    var $thumbnailHeight = null;
    var $imageHeight = null;
	function __construct($_id)
	{
		$this->id = $_id;
	}
	function LoadXML($_xml)
	{
		if (gettype($_xml)=="string")
		{
			$_xmlDoc = new DOMDocument();
			$_xmlDoc->loadXML($_xml);
			$_xml = $_xmlDoc->documentElement;
		}
		$_id = $_xml->getAttribute("id");
		if($_id!="") $this->id = $_id;
		$_styleFolder = $_xml->getAttribute("styleFolder");
		if($_styleFolder!="") $this->styleFolder = $_styleFolder;
	}
	function Render()
	{
		$_script="\n<!--KoolImageView version ".$this->_version." - www.koolphp.net -->\n";		
		$_script.= $this->RegisterCss();
		$_script.= $this->RenderImageView();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;	
	}
	function RenderImageView()
	{
		$this->_positionStyle();
		$tpl_zoompanel = "{BigImage}";
		$tpl_loading = "Loading...";
		$tpl_background = "";
		$tpl_effectpanel = "";
		$_tpl_main = "<img id='{id}' src='{imageUrl}' alt='{alternative}' class='{style}ZoomOut {cssClass}' style='{width};{height}'/><div class='{style}KIV' style='display:inline;'>{loading}{background}{effectpanel}{effectimage}{zoompanel}</div>";
		$_tpl_zoompanel = "<div id='{id}.zoompanel' class='kivZoomPanel' style='display:none;z-index:{zIndex};'>{tpl_zoompanel}</div>";
		$_tpl_loading = "<div id='{id}.loading' class='kivLoading' style='position:absolute;display:none;z-index:{zIndex};'>{tpl_loading}</div>";
		$_tpl_background = "<div id='{id}.background' class='kivBackground' style='display:none;z-index:{zIndex};'>{tpl_background}</div>";
		$_tpl_effectpanel = "<div id='{id}.effectpanel' class='kivEffectPanel' style='display:none;position:absolute;z-index:{zIndex};'>{tpl_effectpanel}</div>";
		$_tpl_image = "<img id='{id}.bigimage' class='kivBigImage' alt='' style='{width};{height}'/>";
		$_tpl_effectimage = "<img id='{id}.effectimage' class='kivEffectImage' style='display:none;position:absolute;z-index:{zIndex};' alt=''/>";
		$_tpl_description = "<span class='kivDescription'>{description}</span>";
		$_tpl_closebutton = "<a class='kivCloseButton'> </a>";
		$_tpl_movebutton = "<a class='kivMoveButton'> </a>";
		$_xmlDoc = new DOMDocument();
		$_xmlDoc->load((($this->scriptFolder=="")?_getRoot():"").$this->_getStylePath()."/".$this->_style."/".$this->_style.".xml");
		$_nodelist = $_xmlDoc->getElementsByTagName("zoompanel");
		if($_nodelist->length>0)
		{
			$tpl_zoompanel = _getInnerXML($_nodelist->item(0),$_xmlDoc);		
		}
		$_nodelist = $_xmlDoc->getElementsByTagName("loading");
		if($_nodelist->length>0)
		{
			$tpl_loading = _getInnerXML($_nodelist->item(0),$_xmlDoc);		
		}
		$_nodelist = $_xmlDoc->getElementsByTagName("background");
		if($_nodelist->length>0)
		{
			$tpl_background = _getInnerXML($_nodelist->item(0),$_xmlDoc);		
		}
		$_nodelist = $_xmlDoc->getElementsByTagName("effectpanel");
		if($_nodelist->length>0)
		{
			$tpl_effectpanel = _getInnerXML($_nodelist->item(0),$_xmlDoc);		
		}
		$_loading = _replace("{id}",$this->id,$_tpl_loading);
		$_loading = _replace("{tpl_loading}",$tpl_loading,$_loading);
		$_loading = _replace("{zIndex}",$this->zIndex,$_loading);
		$_background = _replace("{id}",$this->id,$_tpl_background);
		$_background = _replace("{tpl_background}",$tpl_background,$_background);
		$_background = _replace("{zIndex}",$this->zIndex+1,$_background);
		$_effectpanel = _replace("{id}",$this->id,$_tpl_effectpanel);
		$_effectpanel = _replace("{tpl_effectpanel}",$tpl_effectpanel,$_effectpanel);
		$_effectpanel = _replace("{zIndex}",$this->zIndex+2,$_effectpanel);
		$_effectimage = _replace("{id}",$this->id,$_tpl_effectimage);
		$_effectimage = _replace("{zIndex}",$this->zIndex+3,$_effectimage);
		$_bigimage = _replace("{id}",$this->id,$_tpl_image);
		$_zoompanel = _replace("{id}",$this->id,$_tpl_zoompanel);
		$_zoompanel = _replace("{tpl_zoompanel}",$tpl_zoompanel,$_zoompanel);
        $_zoompanel = _replace("{BigImage}",$_bigimage,$_zoompanel);
        $_description = _replace("{description}",$this->description,$_tpl_description);
        $_zoompanel = _replace("{Description}",$_description,$_zoompanel);
        $_zoompanel = _replace("{CloseButton}",$_tpl_closebutton,$_zoompanel);
        $_zoompanel = _replace("{MoveButton}",$_tpl_movebutton,$_zoompanel);			
		$_zoompanel = _replace("{zIndex}",$this->zIndex+4,$_zoompanel);		
        $_zoompanel = _replace("{width}",isset($this->imageWidth) ? "width:$this->imageWidth" : "",$_zoompanel);
        $_zoompanel = _replace("{height}",isset($this->imageHeight) ? "height:$this->imageHeight" : "",$_zoompanel);	
		$_main = _replace("{id}",$this->id,$_tpl_main);
		$_main = _replace("{style}",$this->_style,$_main);
		$_main = _replace("{alternative}",$this->alternative,$_main);
		$_main = _replace("{cssClass}",$this->cssClass,$_main);
		$_main = _replace("{imageUrl}",$this->imageUrl,$_main);
		$_main = _replace("{zoompanel}",$_zoompanel,$_main);
		$_main = _replace("{loading}",$_loading,$_main);
		$_main = _replace("{background}",$_background,$_main);
		$_main = _replace("{effectpanel}",$_effectpanel,$_main);
		$_main = _replace("{effectimage}",$_effectimage,$_main);
        $_main = _replace("{width}",isset($this->thumbnailWidth) ? "width:$this->thumbnailWidth" : "",$_main);
        $_main = _replace("{height}",isset($this->thumbnailHeight) ? "height:$this->thumbnailHeight" : "",$_main);
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
		/*
		 * Register Css
		 */
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KIV')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KIV';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;		
	}
	function RegisterScript()
	{
		/*
		 * Register javascript
		 */
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKIV=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKIV=1;} </script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script); //Do comment to obfuscate		
		return $_script;		
	}
	function StartupScript()
	{
		/*
		 * Generate startup script
		 */
		$this->_positionStyle();
		$_tpl_script  = "var {id}; function {id}_init(){ {id}=new KoolImageView('{id}','{bigImageUrl}',{showLoading},{backgroundOpacity},'{effect}',{openTime},{frameNumber},'{position}',{relativeLeft},{relativeTop});}";
		$_tpl_script .= "if (typeof(KoolImageView)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KIVInits)=='undefined'){__KIVInits=new Array();} __KIVInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKIV)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKIV=1;}";
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script); //Do comment to obfuscate
		$_script = _replace("{id}",$this->id,$_tpl_script);
		$_script = _replace("{style}",$this->_style,$_script);
		$_script = _replace("{bigImageUrl}",$this->bigImageUrl,$_script);
		$_script = _replace("{showLoading}",($this->showLoading)?"1":"0",$_script);
		$_script = _replace("{backgroundOpacity}",$this->backgroundOpacity,$_script);
		$_script = _replace("{effect}",$this->effect,$_script);
		$_script = _replace("{openTime}",$this->openTime,$_script);
		$_script = _replace("{frameNumber}",$this->frameNumber,$_script);
		$_script = _replace("{position}",$this->position,$_script);
		$_script = _replace("{relativeLeft}",$this->relativeLeft,$_script);
		$_script = _replace("{relativeTop}",$this->relativeTop,$_script);
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
}
?>
