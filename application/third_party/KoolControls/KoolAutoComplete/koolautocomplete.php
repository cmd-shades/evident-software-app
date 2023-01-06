<?php
$_version = "1.7.0.1";
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
if (!class_exists("KoolAutoComplete", false)) {
  class KoolAutoCompleteItem {
    /*
     * id of item: Thinking of auto-id for item
     */
    /*
     * enabled: Indicate whether a item is enabled.
     */
    /*
     * selected: Indicate whether a item is selected.
     */
    var $data;
    /*
     * Contain data for item.
     */
    function __construct() {
      $this->data = array("text" => "KoolAutoComplete Item");
    }
  }
  class KoolAutoComplete {
    var $_version = "1.7.0.1";
    var $id;
    var $styleFolder = "";
    var $_style;
    var $scriptFolder = "";
    var $openDirection = "down";
    /* "down":"up":"auto"
     * "auto": choose the "down" if ok, if not choose "up"
     */
    var $attachTo = "";
    /*
     * attachTo: specify the id of the textbox that auto-suggest will attach to
     */
    var $saveTo = "";
    var $defaultSave = "";
    var $saveTemplate = "";
    var $ClientEvents = array('null' => 'null');
    var $delayTime = 100;
    /*
     * The millisecond that autocomplete will wait before open
     */
    var $verticalOffset = "0px";
    /*
     * Set vertical offset of open box
     */
    var $horizontalOffset = "0px";
    /*
     * Set horizontal offset of open box
     */
    var $superAbove = true;
    /*
     * If true the AutoComplete will use the iframe "shim" technique to make opened box above all
     */
    var $effect = "none";
    /*
     * "none":"linear":"ease"
     */
    var $boxWidth = "auto";
    /*
     * Set the width for opened div of AutoComplete.
     * By default, without setting width, the width of AutoComplete div will
     * be equal to width of AutoComplete
     */
    var $boxHeight = "auto";
    /*
     * Set the height for opened div of AutoComplete.
     * By default, without setting height, the will expand within limit of maxheight
     */
    var $maxBoxHeight = "200px";
    /*
     * The maximum height that the AutoComplete will expand.
     */
    var $minBoxHeight = "50px";
    /*
     * The minimum height that the AutoComplete will expand.
     */
    var $highLight = false;
    /*
     * The result is highlighted with typed string.
     */
    var $searchFilter = "startwith";
    /*
     * "startwith":"contain"
     */
    /*
     * cols: The number of collums in the AutoComplete div
     */
    var $align = "left";
    /*
     * "left":"right";
     * Align of open div to combobox
     */
    var $headerTemplate = "";
    var $itemTemplate = "{text}";
    var $footerTemplate = "";
    /*
     * Those templates is for customizing the combobox
     */
    var $serviceFunction = "";
    var $servicePage = "";
    var $_items;
    function __construct($_id) {
      $this->id = $_id;
      $this->_items = array();
    }
    function addItem($_text = "", $_data = array()) {
      $_item = new KoolAutoCompleteItem();
      if ($_text != "")
        $_item->data["text"] = $_text;
      if (isset($_data)) {
        foreach ($_data as $_k => $_v) {
          $_item->data[$_k] = $_v;
        }
      }
      array_push($this->_items, $_item);
      return $_item;
    }
    function LoadXML($_xml) {
      if (gettype($_xml) == "string") {
        $_xmlDoc = new DOMDocument();
        $_xmlDoc->loadXML($_xml);
        $_xml = $_xmlDoc->documentElement;
      }
      $_id = $_xml->getAttribute("id");
      if ($_id != "")
        $this->id = $_id;
      $_styleFolder = $_xml->getAttribute("styleFolder");
      if ($_styleFolder != "")
        $this->styleFolder = $_styleFolder;
      $_scriptFolder = $_xml->getAttribute("scriptFolder");
      if ($_scriptFolder != "")
        $this->scriptFolder = $_scriptFolder;
      $_attachTo = $_xml->getAttribute("attachTo");
      if ($_attachTo != "")
        $this->attachTo = $_attachTo;
      $_searchFilter = $_xml->getAttribute("searchFilter");
      if ($_searchFilter != "")
        $this->searchFilter = searchFilter;
      $_boxHeight = $_xml->getAttribute("boxHeight");
      if ($_boxHeight != "")
        $this->boxHeight = $_boxHeight;
      $_maxBoxHeight = $_xml->getAttribute("maxBoxHeight");
      if ($_maxBoxHeight != "")
        $this->maxBoxHeight = $_maxBoxHeight;
      $_minBoxHeight = $_xml->getAttribute("minBoxHeight");
      if ($_minBoxHeight != "")
        $this->minBoxHeight = $_minBoxHeight;
      $_openDirection = $_xml->getAttribute("openDirection");
      if ($_openDirection != "")
        $this->openDirection = $_openDirection;
      $_effect = $_xml->getAttribute("effect");
      if ($_effect != "")
        $this->effect = $_effect;
      $_boxWidth = $_xml->getAttribute("boxWidth");
      if ($_boxWidth != "")
        $this->boxWidth = $_boxWidth;
      $_serviceFunction = $_xml->getAttribute("serviceFunction");
      if ($_serviceFunction != "")
        $this->serviceFunction = $_serviceFunction;
      $_servicePage = $_xml->getAttribute("servicePage");
      if ($_servicePage != "")
        $this->servicePage = $_servicePage;
      $_align = $_xml->getAttribute("align");
      if ($_align != "")
        $this->align = $_align;
      $_highLight = strtolower($_xml->getAttribute("highLight"));
      if ($_highLight != "")
        $this->highLight = ($_highLight == "true") ? true : false;
      $_delayTime = strtolower($_xml->getAttribute("delayTime"));
      if ($_delayTime != "")
        $this->delayTime = intval($_delayTime);
      $_verticalOffset = strtolower($_xml->getAttribute("verticalOffset"));
      if ($_verticalOffset != "")
        $this->verticalOffset = $_verticalOffset;
      $_horizontalOffset = strtolower($_xml->getAttribute("horizontalOffset"));
      if ($_horizontalOffset != "")
        $this->horizontalOffset = $_horizontalOffset;
      $_superAbove = strtolower($_xml->getAttribute("superAbove"));
      if ($_superAbove != "")
        $this->superAbove = ($_superAbove == "true") ? true : false;
      foreach ($_xml->childNodes as $_koolautocomplete_subnode) {
        switch (strtolower($_koolautocomplete_subnode->nodeName)) {
          case "items":
            foreach ($_koolautocomplete_subnode->childNodes as $_items_subnode) {
              if (strtolower($_items_subnode->nodeName) == "item") {
                $_data = array("text" => "");
                foreach ($_items_subnode->attributes as $_attributes) {
                  $_data[$_attributes->name] = $_attributes->value;
                }
                $this->addItem($_data["text"], $_data);
              }
            }
            break;
          case "templates":
            foreach ($_koolautocomplete_subnode->childNodes as $_templates_subnode) {
              switch (strtolower($_templates_subnode->nodeName)) {
                case "headertemplate":
                  $this->headerTemplate = _getInnerXML($_templates_subnode, $_xml->parentNode);
                  break;
                case "itemtemplate":
                  $this->itemTemplate = _getInnerXML($_templates_subnode, $_xml->parentNode);
                  break;
                case "footertemplate":
                  $this->footerTemplate = _getInnerXML($_templates_subnode, $_xml->parentNode);
                  break;
              }
            }
            break;
        }
      }
    }
    function _positionStyle() {
      $this->styleFolder = _replace("\\", "/", $this->styleFolder);
      $_styleFolder = trim($this->styleFolder, "/");
      $_lastpos = strrpos($_styleFolder, "/");
      $this->_style = substr($_styleFolder, ($_lastpos ? $_lastpos : -1) + 1);
    }
    function Render() {
      $_script = "\n<!--KoolAutoComplete version " . $this->_version . " - www.koolphp.net -->\n";
      $_script.= $this->RegisterCss();
      $_script.= $this->RenderAutoComplete();
      $_is_callback = isset($_POST["__koolajax"]) || isset($_GET["__koolajax"]);
      $_script.= ($_is_callback) ? "" : $this->RegisterScript();
      $_script.="<script type='text/javascript'>";
      $_script.= $this->StartupScript();
      $_script.="</script>";
      return $_script;
    }
    function RenderAutoComplete() {
      $this->_positionStyle();
      $tpl_box = "{boxcontent}";
      $tpl_item = "{itemcontent}";
      include "styles" . "/" . $this->_style . "/" . $this->_style . ".tpl";
      $_tpl_main = "<div id='{id}' class='{style}KAC'>{box}{iframe}{itemTemplate}</div>";
      $_tpl_box = "<div class='kacBox'>{tpl_box}</div>";
      $_tpl_iframe = "<iframe class='kacIframe' src='javascript:false;'> </iframe>";
      $_tpl_box_core = "{header}{item}{footer}";
      $_tpl_header = "<div class='kacHeader'>{headercontent} </div>";
      $_tpl_footer = "<div class='kacFooter'>{footercontent} </div>";
      $_tpl_item_box = "<div class='kacItemBox' style='height:{boxHeight}'>{itemscontent}</div>";
      $_tpl_itemcontent = "<ul class='kacUL'>{items}</ul>";
      $_tpl_item = "<li class='kacLI kacItem'>{item_data}<a href='javascript:void 0' class='kacA'><div class='kacIn'>{tpl_item}</div></a></li>";
      $_tpl_item_data = "<input type='hidden' value=\"{data}\">";
      $_tpl_itemtemplate = "<div id='{id}' style='display:none;'>{itemTemplate}</div>";
      $_box = _replace("{tpl_box}", $tpl_box, $_tpl_box);
      $_box = _replace("{boxcontent}", $_tpl_box_core, $_box);
      $_headersection = "";
      if ($this->headerTemplate != "") {
        $_headersection = _replace("{headercontent}", $this->headerTemplate, $_tpl_header);
      }
      $_box = _replace("{header}", $_headersection, $_box);
      $_footersection = "";
      if ($this->footerTemplate != "") {
        $_footersection = _replace("{footercontent}", $this->footerTemplate, $_tpl_footer);
      }
      $_box = _replace("{footer}", $_footersection, $_box);
      $_items = "";
      foreach ($this->_items as $_item) {
        $_itemtemp = $this->itemTemplate;
        foreach ($_item->data as $_key => $_value) {
          $_itemtemp = _replace("{" . $_key . "}", $_value, $_itemtemp);
        }
        $item_render = _replace("{tpl_item}", $tpl_item, $_tpl_item);
        $item_render = _replace("{itemcontent}", $_itemtemp, $item_render);
        $_itemdatavalue = "";
        foreach ($_item->data as $_key => $_value) {
          $_itemdatavalue.=",'" . $_key . "':'" . _esc($_value) . "'";
        }
        $_itemdatavalue = "{" . substr($_itemdatavalue, 1) . "}";
        $_item_data = _replace("{data}", $_itemdatavalue, $_tpl_item_data);
        $item_render = _replace("{item_data}", $_item_data, $item_render);
        $_items.= $item_render;
      }
      $_itemcontent = _replace("{items}", $_items, $_tpl_itemcontent);
      $_itemsection = _replace("{itemscontent}", $_itemcontent, $_tpl_item_box);
      $_itemsection = _replace("{boxHeight}", $this->boxHeight, $_itemsection);
      $_box = _replace("{item}", $_itemsection, $_box);
      $_itemTemplate = _replace("{id}", $this->id . ".itemTemplate", $_tpl_itemtemplate);
      $_itemTemplate = _replace("{itemTemplate}", $tpl_item, $_itemTemplate);
      $_itemTemplate = _replace("{itemcontent}", $this->itemTemplate, $_itemTemplate);
      $_main = _replace("{id}", $this->id, $_tpl_main);
      $_main = _replace("{style}", $this->_style, $_main);
      $_main = _replace("{iframe}", ($this->superAbove) ? $_tpl_iframe : "", $_main);
      if (true) {
        $_main = _replace("{box}", $_box, $_main);
      }
      $_main = _replace("{version}", $this->_version, $_main);
      $_main = _replace("{itemTemplate}", $_itemTemplate, $_main);
      return $_main;
    }
    function RegisterCss() {
      /*
       * Register Css
       */
      $this->_positionStyle();
      $_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KAC')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KAC';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
      $_script = _replace("{style}", $this->_style, $_tpl_script);
      $_script = _replace("{stylepath}", $this->_getStylePath(), $_script);
      return $_script;
    }
    function RegisterScript($_styleInit = true) {
      /*
       * Register javascript
       */
      if ($_styleInit) {
        $_tpl_script = "<script type='text/javascript'>if(typeof _libKAC=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKAC=1;}</script>";
      } else {
        $_tpl_script = "<script type='text/javascript'>if(typeof _libKAC=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKAC=1;}</script>";
      }
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script); //Do comment to obfuscate
      return $_script;
    }
    function StartupScript() {
      /*
       * Generate startup script
       */
      $_tpl_script = "var {id}; function {id}_init(){ {id}=new KoolAutoComplete('{id}','{attachTo}', '{saveTo}', '{defaultSave}', '{saveTemplate}', {delayTime},'{searchFilter}',{highLight},'{boxWidth}','{boxHeight}','{minBoxHeight}','{maxBoxHeight}','{horizontalOffset}','{verticalOffset}','{openDirection}','{align}','{serviceFunction}','{servicePage}', '{stylePath}', '{ClientEvents}');}";
      $_tpl_script .= "if (typeof(KoolAutoComplete)=='function'){{id}_init();}";
      $_tpl_script .= "else{if(typeof(__KACInits)=='undefined'){__KACInits=new Array();} __KACInits.push({id}_init);{register_script}}";
      $_tpl_register_script = "if(typeof(_libKAC)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKAC=1;}";
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script); //Do comment to obfuscate
      $_script = _replace("{id}", $this->id, $_tpl_script);
      $_script = _replace("{attachTo}", $this->attachTo, $_script);
      $_script = _replace("{defaultSave}", $this->defaultSave, $_script);
      $_script = _replace("{saveTo}", $this->saveTo, $_script);
      $_script = _replace("{saveTemplate}", $this->saveTemplate, $_script);
      $_script = _replace("{delayTime}", $this->delayTime, $_script);
      $_script = _replace("{searchFilter}", $this->searchFilter, $_script);
      $_script = _replace("{highLight}", ($this->highLight) ? "true" : "false", $_script);
      $_script = _replace("{boxWidth}", $this->boxWidth, $_script);
      $_script = _replace("{boxHeight}", $this->boxHeight, $_script);
      $_script = _replace("{minBoxHeight}", $this->minBoxHeight, $_script);
      $_script = _replace("{maxBoxHeight}", $this->maxBoxHeight, $_script);
      $_script = _replace("{horizontalOffset}", $this->horizontalOffset, $_script);
      $_script = _replace("{verticalOffset}", $this->verticalOffset, $_script);
      $_script = _replace("{openDirection}", $this->openDirection, $_script);
      $_script = _replace("{align}", $this->align, $_script);
      $_script = _replace("{serviceFunction}", $this->serviceFunction, $_script);
      $_script = _replace("{servicePage}", $this->servicePage, $_script);
      $_script = _replace("{register_script}", $_register_script, $_script);
      $_script = _replace("{stylePath}", $this->_getStylePath() . '/' . $this->_style, $_script);
      $_script = _replace("{ClientEvents}", json_encode($this->ClientEvents, true), $_script);
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
}
?>
