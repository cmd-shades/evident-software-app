<?php
$_version = "1.0.0.0";
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
if (!class_exists("KoolTreeGrid", false))
{
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
    class _HtmlIStyle
    {
        private $_selectors = array();
        private function __construct()
        {
        }
        public static function _new()
        {
            $_style = new _HtmlIStyle();
            return $_style;
        }
        function _style($_var)
        {
            if (is_array($_var))
            {
                foreach ($_var as $_selector => $_properties)
                    if (!empty($_properties))
                    {
                        if (!isset($this->_selectors[$_selector]))
                            $this->_selectors[$_selector] = "";
                        if (!strpos($this->_selectors[$_selector], $_properties))
                            $this->_selectors[$_selector] .= $_properties . ";";
                    }
            }
            return $this;
        }
        function _getStyleScript()
        {
            $_s = "";
            foreach ($this->_selectors as $_selector => $_properties)
            {
                $_s .= "." . $_selector . "\n { " . $_properties . " } \n";
            }
            return '<style type="text/css">' . $_s . "</style>";
        }
    }
    class _HtmlProperty
    {
        #code
        private $_n;
        private $_v;
        private function __construct()
        {
        }
        public static function _new($_name = NULL, $_value = NULL)
        {
            $_prt = new _HtmlProperty();
            if (isset($_name))
                $_prt->_n = $_name;
            if (isset($_value))
                $_prt->_v = $_value;
            return $_prt;
        }
        function _getExpression($_quote = "'")
        {
            if (isset($this->_n) && isset($this->_v))
                return $this->_n . '=' . $_quote . $this->_v . $_quote;
            else
                return "";
        }
        function _getName()
        {
            if (isset($this->_n))
                return $this->_n;
            else
                return "";
        }
        function _getValue()
        {
            if (isset($this->_v))
                return $this->_v;
            else
                return "";
        }
    }
    class _HtmlElement
    {
        #code
        protected $_template = "<{tag} {properties}>{content}</{tag}>";
        protected $_tag;
        protected $_con = "";
        protected $_iStyle;
        protected $_Properties = array();
        protected $_ChildGroups = array();
        function __construct()
        {
            $this->_iStyle = _HtmlIStyle::_new();
        }
        public static function _new($_var)
        {
            $_e = new _HtmlElement();
            $_e->_tag($_var);
            return $_e;
        }
        function _setIStyle($_var)
        {
            if (is_array($_var))
                $this->_iStyle->_style($_var);
            return $this;
        }
        function _getIStyle()
        {
            return $this->_iStyle->_getStyleScript();
        }
        function _tag($_var)
        {
            $this->_tag = $_var;
            $this->_template = _replace("{tag}", $_var, $this->_template);
            return $this;
        }
        function _colspan($_var)
        {
            $_prt = _HtmlProperty::_new("colspan", $_var);
            $this->_Properties["cs"] = $_prt;
            return $this;
        }
        function _getColspan()
        {
            if (isset($this->_Properties["cs"]))
                return $this->_Properties["cs"]->_getValue();
            else
                return 1;
        }
        function _rowspan($_var)
        {
            $_prt = _HtmlProperty::_new("rowspan", $_var);
            $this->_Properties["rs"] = $_prt;
            return $this;
        }
        function _getRowspan()
        {
            if (isset($this->_Properties["rs"]))
                return $this->_Properties["rs"]->_getValue();
            else
                return 1;
        }
        function _align($_var)
        {
            $_prt = _HtmlProperty::_new("align", $_var);
            $this->_Properties["al"] = $_prt;
            return $this;
        }
        function _width($_var)
        {
            $_prt = _HtmlProperty::_new("width", $_var);
            $this->_Properties["w"] = $_prt;
            return $this;
        }
        function _style($_var)
        {
            $_prt = _HtmlProperty::_new("style", $_var);
            $this->_Properties["st"] = $_prt;
            return $this;
        }
        function _class()
        {
            $_class = "";
            $_numargs = func_num_args();
            $_arg_list = func_get_args();
            for ($i = 0; $i < $_numargs; $i++)
                $_class .= $_arg_list[$i] . " ";
            $_prt = _HtmlProperty::_new("class", $_class);
            $this->_Properties["cl"] = $_prt;
            return $this;
        }
        function _id($_var)
        {
            $_prt = _HtmlProperty::_new("id", $_var);
            $this->_Properties["id"] = $_prt;
            return $this;
        }
        function _content($_var)
        {
            $this->_con = $_var;
            return $this;
        }
        function _property($_classStr, $_prt)
        {
            $_classes = explode(" ", trim($_classStr));
            foreach ($_classes as $_class)
            {
                if (isset($_prt[$_class]))
                {
                    foreach ($_prt[$_class] as $_n => $_v)
                    {
                        $_htmlPrt = _HtmlProperty::_new($_n, $_v);
                        array_push($this->_Properties, $_htmlPrt);
                    }
                }
            }
            return $this;
        }
        function _addProperties($_var)
        {
            if ($_var instanceof _HtmlProperty)
                array_push($this->_Properties, $_var);
            else if (is_array($_var))
            {
                foreach ($_var as $_n => $_v)
                {
                    $_prt = _HtmlProperty::_new($_n, $_v);
                    array_push($this->_Properties, $_prt);
                }
            }
            return $this;
        }
        function _setProperties($_var)
        {
            $this->_Properties = $_var;
            return $this;
        }
        function _getProperties()
        {
            return $this->_Properties;
        }
        function _getPropertiesExpression($_quote = '"')
        {
            $_s = "";
            foreach ($this->_Properties as $_prt)
                if (isset($_prt))
                {
                    $_s .= $_prt->_getExpression($_quote) . " ";
                }
            return $_s;
        }
        function _addChild($_var, $_position = -1)
        {
            return $this->_addChildGroup(array($_var), $_position);
        }
        function _addChildGroup($_var, $_position = -1)
        {
            $_len = count($this->_ChildGroups);
            if (($_position < 0) || ($_position > $_len))
                $_position = $_len;
            array_splice($this->_ChildGroups, $_position, 0, array($_var));
            return $this;
        }
        function _setChild($_var, $_position)
        {
            return $this->_setChildGroup(array($_var), $_position);
        }
        function _setChildGroup($_var, $_position)
        {
            if ($_position > -1)
                $this->_ChildGroups[$_position] = $_var;
            return $this;
        }
        function _sortChildGroupAsc()
        {
            ksort($this->_ChildGroups);
        }
        function _getChildArray()
        {
            $_arr = array();
            foreach ($this->_ChildGroups as $_childs)
                foreach ($_childs as $_child)
                    if (isset($_child) && $_child->_getContent() != "")
                    {
                        array_push($_arr, $_child);
                    }
            return $_arr;
        }
        function _getContent()
        {
            $_s = $this->_con;
            foreach ($this->_ChildGroups as $_childs)
                foreach ($_childs as $_child)
                    if (isset($_child))
                        $_s .= $_child->_getHtml();
            return $_s;
        }
        function _getHtml($_quote = "'")
        {
            $_content = $this->_getContent();
            if (!empty($_content) || $this->_tag == "input")
            {
                $_s = $this->_template;
                $_properties = $this->_getPropertiesExpression($_quote);
                $_s = _replace("{properties}", $_properties, $_s);
                $_s = _replace("{content}", $_content, $_s);
                return $_s;
            }
            else
                return "";
        }
        function _getNonTagContent($_changes = array(), $_caseSensitive = TRUE)
        {
            $_s = $this->_replaceAll($_changes, $this->_con, $_caseSensitive);
            foreach ($this->_ChildGroups as $_childs)
                foreach ($_childs as $_child)
                    if (isset($_child))
                        $_s .= $_child->_getNonTagContent($_changes, $_caseSensitive);
            return $_s;
        }
        function _getChangedContent($_changes, $_caseSensitive = TRUE)
        {
            $_s = $this->_replaceAll($_changes, $this->_con, $_caseSensitive);
            foreach ($this->_ChildGroups as $_childs)
                foreach ($_childs as $_child)
                    if (isset($_child))
                        $_s .= $_child->_getChangedHtml($_changes, $_caseSensitive);
            return $_s;
        }
        function _getChangedHtml($_changes, $_caseSensitive = TRUE, $_quote = "'")
        {
            $_content = $this->_getChangedContent($_changes, $_caseSensitive);
            if (!empty($_content))
            {
                $_s = $this->_template;
                $_properties = $this->_getPropertiesExpression($_quote);
                $_s = _replace("{properties}", $_properties, $_s);
                $_s = _replace("{content}", $_content, $_s);
                return $_s;
            }
            else
                return "";
        }
        function _replaceAll($_changes, $_str, $_caseSensitive = TRUE)
        {
            foreach ($_changes as $_key => $_rep)
            {
                if ($_caseSensitive)
                    $_str = _replace($_key, $_rep, $_str);
                else
                    $_str = _ireplace($_key, $_rep, $_str);
            }
            return $_str;
        }
    }
    class _HtmlProvider
    {
        #code
        public static function _newDiv()
        {
            $_e = _HtmlElement::_new("div");
            return $_e;
        }
        public static function _newSpan()
        {
            $_e = _HtmlElement::_new("span");
            return $_e;
        }
        public static function _newInput()
        {
            $_e = _HtmlElement::_new("input");
            return $_e;
        }
        public static function _newHiddenInput($_id, $_value)
        {
            $_e = _HtmlElement::_new("input");
            $_e->_addProperties(array(
                "id" => $_id,
                "name" => $_id,
                "type" => "hidden",
                "autocomplete" => "off",
                "value" => $_value,
            ));
            return $_e;
        }
        public static function _newJStag()
        {
            $_e = _HtmlElement::_new("script");
            $_e->_addProperties(array(
                "type" => "text/javascript",
            ));
            return $_e;
        }
    }
    class _TreeNode
    {
        var $id;
        var $text;
        var $image;
        var $children;
        var $expand = false;
        var $subTreeUrl;
        var $visible = true;
        var $showPlusMinus = true;
        var $data;
        var $parent; //not obfuscated
        function __construct($_id, $_text = "", $_expand = false, $_image = "", $_subTreeUrl = "")
        {
            $this->id = $_id;
            $this->text = $_text;
            $this->image = $_image;
            $this->expand = $_expand;
            $this->subTreeUrl = $_subTreeUrl;
            $this->children = array();
            $this->data = array();
        }
        function addChild($_child)
        {
            $_child->parent = $this;
            array_push($this->children, $_child);
        }
        function addData($_name, $_data)
        {
            $this->data[$_name] = $_data;
        }
    }
    class _KoolTreeView
    {
        var $_version = "1.0.0.0";
        var $id;
        var $root;
        var $_style;
        var $_list;
        var $width = "";
        var $height = "";
        var $overflow = "";
        var $styleFolder;
        var $imageFolder;
        var $selectedIds;
        var $selectEnable = true;
        var $selectDisableIds;
        var $multipleSelectEnable = false;
        var $DragAndDropEnable = false;
        var $dragDisableIds;
        var $dropDisableIds;
        var $EditNodeEnable = false;
        var $editDisableIds;
        var $isSubTree = false;
        var $singleExpand = false;
        var $keepState = "none";
        var $keepStateHours = 24;
        /*
         * Option for keep state of treeview
         * "none": No state saved in cookie
         * "onpage": State save for particular page only
         * "crosspage": Cross-page state
         * The state only can be maintain if the id of treeview remain the same
         */
        var $showLines = false;
        var $scriptFolder = "";
        function __construct($_id)
        {
            $this->id = $_id;
            $this->root = new _TreeNode("root");
            $this->_list = array();
            $this->_list["root"] = $this->root;
        }
        function loadXML($xml)
        {
            if (gettype($xml) == "string")
            {
                $xmlDoc = new DOMDocument();
                $xmlDoc->loadXML($xml);
                $xml = $xmlDoc->documentElement;
            }
            $id = $xml->getAttribute("id");
            if ($id != "")
                $this->id = $id;
            $this->width = $xml->getAttribute("width");
            $this->height = $xml->getAttribute("height");
            $this->overflow = $xml->getAttribute("overflow");
            $this->styleFolder = $xml->getAttribute("styleFolder");
            $this->imageFolder = $xml->getAttribute("imageFolder");
            $this->selectedIds = $xml->getAttribute("selectedIds");
            $this->selectDisableIds = $xml->getAttribute("selectDisableIds");
            $this->dragDisableIds = $xml->getAttribute("dragDisableIds");
            $this->dropDisableIds = $xml->getAttribute("dropDisableIds");
            $this->editDisableIds = $xml->getAttribute("editDisableIds");
            $_scriptFolder = $_xml->getAttribute("scriptFolder");
            if ($_scriptFolder != "")
                $this->scriptFolder = $_scriptFolder;
            $tmp = $xml->getAttribute("selectEnable");
            $this->selectEnable = ($tmp == "") ? false : (($tmp == "true") ? true : false);
            $tmp = $xml->getAttribute("multipleSelectEnable");
            $this->multipleSelectEnable = ($tmp == "") ? false : (($tmp == "true") ? true : false);
            $tmp = $xml->getAttribute("DragAndDropEnable");
            $this->DragAndDropEnable = ($tmp == "") ? false : (($tmp == "true") ? true : false);
            $tmp = $xml->getAttribute("EditNodeEnable");
            $this->EditNodeEnable = ($tmp == "") ? false : (($tmp == "true") ? true : false);
            $tmp = $xml->getAttribute("isSubTree");
            $this->isSubTree = ($tmp == "") ? false : (($tmp == "true") ? true : false);
            $tmp = $xml->getAttribute("showOnExpand");
            $this->showOnExpand = ($tmp == "") ? false : (($tmp == "true") ? true : false);
            $tmp = $xml->getAttribute("keepState");
            if ($tmp != "")
                $this->keepState = $tmp;
            $tmp = $xml->getAttribute("keepStateHours");
            if ($tmp != "")
                $this->keepStateHours = intval($tmp);
            $tmp = $xml->getAttribute("singleExpand");
            $this->singleExpand = ($tmp == "") ? false : (($tmp == "true") ? true : false);
            foreach ($xml->childNodes as $treeview_subnode)
            {
                switch (strtolower($treeview_subnode->nodeName))
                {
                    case "rootnode":
                        $this->root->text = $treeview_subnode->getAttribute("text");
                        $this->root->image = $treeview_subnode->getAttribute("image");
                        $this->root->subTreeUrl = $treeview_subnode->getAttribute("subTreeUrl");
                        $tmp = $treeview_subnode->getAttribute("expand");
                        $this->root->expand = ($tmp == "") ? false : (($tmp == "true") ? true : false);
                        $tmp = $treeview_subnode->getAttribute("visible");
                        $this->root->visible = ($tmp == "") ? true : (($tmp == "true") ? true : false);
                        $tmp = $treeview_subnode->getAttribute("showPlusMinus");
                        $this->root->showPlusMinus = ($tmp == "") ? true : (($tmp == "true") ? true : false);
                        $this->buildChildren($this->root, $treeview_subnode);
                        break;
                    case "templates":
                        break;
                }
            }
        }
        function buildChildren($node, $xmlnode)
        {
            foreach ($xmlnode->childNodes as $xmlchildnode)
            {
                if ($xmlchildnode->nodeName == "node")
                {
                    $id = $xmlchildnode->getAttribute("id");
                    $childnode = new _TreeNode($id);
                    $childnode->text = $xmlchildnode->getAttribute("text");
                    $childnode->image = $xmlchildnode->getAttribute("image");
                    $childnode->subTreeUrl = $xmlchildnode->getAttribute("subTreeUrl");
                    $tmp = $xmlchildnode->getAttribute("expand");
                    $childnode->expand = ($tmp == "") ? false : (($tmp == "true") ? true : false);
                    $this->buildChildren($childnode, $xmlchildnode);
                    $node->addChild($childnode);
                }
            }
        }
        function Render()
        {
            $script = "";
            if ($this->isSubTree)
            {
                $this->_positionStyle();
                for ($i = 0; $i < sizeof($this->root->children); $i++)
                    $script.=$this->RenderNode($this->root->children[$i]);
            }
            else
            {
                $script = "\n<!--KoolTreeView version " . $this->_version . " - www.koolphp.net -->\n";
                $script.= $this->RegisterCSS();
                $script.= $this->RenderTree();
                $_is_callback = isset($_POST["__koolajax"]) || isset($_GET["__koolajax"]);
                $script.= ($_is_callback) ? "" : $this->RegisterScript();
                $script.="<script type='text/javascript'>";
                $script.= $this->StartupScript();
                $script.="</script>";
            }
            return $script;
        }
        function getStartupScript()
        {
            $script = '';
            $script .="<script type='text/javascript'>";
            $script .= $this->StartupScript();
            $script .="</script>";
            return $script;
        }
        function Add($_parentid, $_id, $_text = "", $_expand = false, $_image = "", $_subTreeUrl = "")
        {
            $newNode = new _TreeNode($_id);
            $newNode->text = $_text;
            $newNode->expand = $_expand;
            $newNode->image = $_image;
            $newNode->subTreeUrl = $_subTreeUrl;
            $this->_list[$_parentid]->addChild($newNode);
            $this->_list[$_id] = $newNode;
            return $newNode;
            /*
             * 2008-09-26: Comment by Nghiem Anh Tuan
             */
        }
        function getRootNode()
        {
            return $this->root;
        }
        function getNode($_nodeid)
        {
            /*
              if ($node->id==$nodeid) return $node;
              for($i=0;$i<sizeof($node->children);$i++)
              {
              $result = $this->getNode($nodeid,$node->children[$i]);
              if ($result!=NULL) return $result;
              }
              return NULL;
             */
            return $this->_list[$_nodeid];
        }
        function _positionStyle()
        {
            $this->styleFolder = _replace("\\", "/", $this->styleFolder);
            $_styleFolder = trim($this->styleFolder, "/");
            $_lastpos = strrpos($_styleFolder, "/");
            $this->_style = substr($_styleFolder, ($_lastpos ? $_lastpos : -1) + 1);
        }
        function RegisterCss($styles = array())
        {
            $this->_positionStyle();
            if (!in_array($this->_style, $styles))
                array_push ($styles, $this->_style);
            $styleArrStr = '';
            foreach ($styles as $style)
                $styleArrStr .= "'" . $style . "',";
            $styleArrStr = '[' . trim($styleArrStr, ',') . ']';
            $_tpl_script = "<script type='text/javascript'>\n"
                . "var _head = document.getElementsByTagName('head')[0];\n "
                . "var styles={styleArray};\n "
                . "for (var i=0; i<styles.length; i+=1) {\n "
                . "if (document.getElementById('__{style}KTG')==null){\n "
                . "var _link = document.createElement('link'); _link.id = '__'+styles[i]+ 'KTG';\n "
                . "_link.rel='stylesheet'; _link.href='{stylepath}/'+styles[i]+'/'+styles[i]+'.css';\n "
                . "_head.appendChild(_link);\n "
                . "}}</script>";
            $_script = _replace("{styleArray}", $styleArrStr, $_tpl_script);
            $_script = _replace("{stylepath}", $this->_getStylePath(), $_script);
            return $_script;
        }
        function RenderTree()
        {
            $this->_positionStyle();
            $_tpl_main = "<div id='{id}' class='{style}KTV' style='{width}{height}{overflow}'><ul class='ktvUL {nopadding} {lines}'>{subnodes}</ul>{clientstate}</div>";
            $tpl_clientstate = "<input type='hidden' id='{id}.clientState' name='{id}.clientState' />";
            $_main = _replace("{id}", $this->id, $_tpl_main);
            $_main = _replace("{style}", $this->_style, $_main);
            $_main = _replace("{nopadding}", (!$this->root->visible || !$this->root->showPlusMinus) ? "ktvNoPadding" : "", $_main);
            $_main = _replace("{subnodes}", $this->RenderNode($this->root), $_main);
            $_main = _replace("{lines}", (($this->showLines) ? "ktvLines" : ""), $_main);
            $clientstate = _replace("{id}", $this->id, $tpl_clientstate);
            if (true)
            {
                $_main = _replace("{clientstate}", $clientstate, $_main);
            }
            $_main = _replace("{width}", (($this->width != "") ? "width:" . $this->width . ";" : ""), $_main);
            $_main = _replace("{height}", (($this->height != "") ? "height:" . $this->height . ";" : ""), $_main);
            $_main = _replace("{overflow}", (($this->overflow != "") ? "overflow:" . $this->overflow . ";" : ""), $_main);
            $_main = _replace("{version}", $this->_version, $_main);
            return $_main;
        }
        function RenderNode($node)
        {
            $tpl_subnodes = "<ul class='ktvUL' style='display:{display}'>{subnodes}</ul>";
            $tpl_singlenode = "<li id='{nodeid}' class='{class}'>{nodecontent}{subnodes}</li>";
            $tpl_nodecontent = "<div class='{class}'>{plusminus}{image}{text}{nodedata}</div>";
            $tpl_plusminus = "<span class='ktvPM ktv{plusminus}'> </span>";
            $tpl_image = "<img src='{image}' class='ktvImage' alt=''/>";
            $tpl_text = "<span class='ktvText'>{text}</span>";
            $tpl_nodedata = "<input id='{nodeid}_data' type='hidden' value='{value}'/>";
            $singlenode = $tpl_singlenode;
            $nodecontent = $tpl_nodecontent;
            $tmp = _replace("{text}", $node->text, $tpl_text);
            $nodecontent = _replace("{text}", $tmp, $nodecontent);
            $subnodes = "";
            if ($node->image != "")
            {
                $tmp = _replace("{image}", (($this->imageFolder != "") ? $this->imageFolder . "/" : "") . $node->image, $tpl_image);
                $nodecontent = _replace("{image}", $tmp, $nodecontent);
            }
            else
            {
                $nodecontent = _replace("{image}", "", $nodecontent);
            }
            if (sizeof($node->children) > 0)
            {
                $tmp = _replace("{plusminus}", ($node->expand) ? "Minus" : "Plus", $tpl_plusminus);
                $nodecontent = _replace("{plusminus}", $tmp, $nodecontent);
                $subnodes = "";
                for ($i = 0; $i < sizeof($node->children); $i++)
                {
                    $subnodes.=$this->RenderNode($node->children[$i]);
                }
                $subnodes = _replace("{subnodes}", $subnodes, $tpl_subnodes);
                $subnodes = _replace("{display}", ($node->expand) ? "block" : "none", $subnodes);
            }
            else
            {
                if ($node->subTreeUrl != "")
                {
                    $tmp = _replace("{plusminus}", "Plus", $tpl_plusminus);
                    $nodecontent = _replace("{plusminus}", $tmp, $nodecontent);
                }
                else
                {
                    $nodecontent = _replace("{plusminus}", "", $nodecontent);
                }
            }
            if ($node->subTreeUrl != "" || sizeof($node->data) > 0)
            {
                $_esc_subTreeUrl = _esc($node->subTreeUrl);
                $_esc_data = array();
                foreach ($node->data as $_k => $_v)
                {
                    $_esc_data[$_k] = _esc($_v);
                }
                $data = array("url" => $_esc_subTreeUrl, "data" => $_esc_data);
                $nodedata = _replace("{nodeid}", (($node === $this->root) ? $this->id . "." : "") . $node->id, $tpl_nodedata);
                $nodedata = _replace("{value}", json_encode($data), $nodedata);
                $nodecontent = _replace("{nodedata}", $nodedata, $nodecontent);
            }
            else
            {
                $nodecontent = _replace("{nodedata}", "", $nodecontent);
            }
            $class_singlenode = "ktvLI";
            if (( isset($node->parent->children[0]) && $node->parent->children[0] === $node) || $node === $this->root)
            {
                $class_singlenode .= " ktvFirst";
            }
            if ((isset($node->parent->children) && isset($node->parent->children[sizeof($node->parent->children) - 1]) && $node->parent->children[sizeof($node->parent->children) - 1] === $node) || $node === $this->root)
            {
                $class_singlenode .= " ktvLast";
            }
            $class_nodecontent = "";
            if ($node === $this->root)
            {
                $class_nodecontent = "ktvTop";
                if (!$node->visible)
                    $class_nodecontent.=" ktvInv";
                if (!$node->showPlusMinus)
                    $class_nodecontent.=" ktvNoPM";
            }
            else
            {
                if ($node->parent->children[0] === $node)
                {
                    $class_nodecontent = "ktvTop";
                }
                if ($node->parent->children[sizeof($node->parent->children) - 1] === $node)
                {
                    $class_nodecontent = "ktvBot";
                }
                if ($class_nodecontent == "")
                {
                    $class_nodecontent = "ktvMid";
                }
            }
            $sIds = "[" . str_replace(",", "][", $this->selectedIds) . "]";
            if (strpos($sIds, "[" . $node->id . "]") !== false)
                $class_nodecontent .= " ktvSelected";
            $nodecontent = _replace("{class}", $class_nodecontent, $nodecontent);
            $singlenode = _replace("{nodeid}", (($node === $this->root) ? $this->id . "." : "") . $node->id, $singlenode);
            $singlenode = _replace("{class}", $class_singlenode, $singlenode);
            $singlenode = _replace("{nodecontent}", $nodecontent, $singlenode);
            $singlenode = _replace("{subnodes}", $subnodes, $singlenode);
            return $singlenode;
        }
        function RegisterScript()
        {
            $_tpl_script = "<script type='text/javascript'>if(typeof _libKTV=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKTV=1;}</script>";
            $_script= _replace("{src}",$this->_getComponentURI()."?".md5("js"),$_tpl_script);
            return $_script;
        }
        function StartupScript()
        {
            $_tpl_script = "var {id}; function {id}_init(){ {id} = new KoolTreeView(\"{id}\",{singleExpand},{selectEnable},{multipleSelectEnable},{DragAndDropEnable},{EditNodeEnable},'{keepState}',{keepStateHours},\"{cs}\");}";
            $_tpl_script .= "if (typeof(KoolTreeView)=='function'){{id}_init();}";
            $_tpl_script .= "else{if(typeof(__KTVInits)=='undefined'){__KTVInits=new Array();} __KTVInits.push({id}_init);{register_script}}";
            $_tpl_register_script = "if(typeof(_libKTV)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKTV=1;}";
            $_register_script= _replace("{src}",$this->_getComponentURI()."?".md5("js"),$_tpl_register_script);
            $tpl_cs = "{'selectedIds':[{selectedIds}],'selectDisableIds':[{selectDisableIds}],'dragDisableIds':[{dragDisableIds}],'dropDisableIds':[{dropDisableIds}],'editDisableIds':[{editDisableIds}]}";
            $script = _replace("{id}", $this->id, $_tpl_script);
            $sIds = ($this->selectedIds != "") ? "'" . _replace(",", "','", $this->selectedIds) . "'" : "";
            $clientstate = _replace("{selectedIds}", $sIds, $tpl_cs);
            $sIds = ($this->selectDisableIds != "") ? "'" . _replace(",", "','", $this->selectDisableIds) . "'" : "";
            $clientstate = _replace("{selectDisableIds}", $sIds, $clientstate);
            $sIds = ($this->dragDisableIds != "") ? "'" . _replace(",", "','", $this->dragDisableIds) . "'" : "";
            $clientstate = _replace("{dragDisableIds}", $sIds, $clientstate);
            $sIds = ($this->dropDisableIds != "") ? "'" . _replace(",", "','", $this->dropDisableIds) . "'" : "";
            $clientstate = _replace("{dropDisableIds}", $sIds, $clientstate);
            $sIds = ($this->editDisableIds != "") ? "'" . _replace(",", "','", $this->editDisableIds) . "'" : "";
            $clientstate = _replace("{editDisableIds}", $sIds, $clientstate);
            $script = _replace("{singleExpand}", ($this->singleExpand) ? "1" : "0", $script);
            $script = _replace("{selectEnable}", ($this->selectEnable) ? "1" : "0", $script);
            $script = _replace("{multipleSelectEnable}", ($this->multipleSelectEnable) ? "1" : "0", $script);
            $script = _replace("{DragAndDropEnable}", ($this->DragAndDropEnable) ? "1" : "0", $script);
            $script = _replace("{EditNodeEnable}", ($this->EditNodeEnable) ? "1" : "0", $script);
            $script = _replace("{keepState}", $this->keepState, $script);
            $script = _replace("{keepStateHours}", $this->keepStateHours, $script);
            $script = _replace("{cs}", $clientstate, $script);
            $script = _replace("{register_script}", $_register_script, $script);
            return $script;
        }
        function _getComponentURI()
        {
            if ($this->scriptFolder == "")
            {
                $_root = _getRoot();
                $_file = substr(_replace("\\", "/", __FILE__), strlen($_root));
                return $_file;
            }
            else
            {
                $_file = _replace("\\", "/", __FILE__);
                $_file = $this->scriptFolder . substr($_file, strrpos($_file, "/"));
                return $_file;
            }
        }
        function _getStylePath()
        {
            $_com_uri = $this->_getComponentURI();
            $_styles_folder = _replace(strrchr($_com_uri, "/"), "", $_com_uri) . "/styles";
            return $_styles_folder;
        }
    }
    class _SimpleTree
    {
        public $Parent;
        public $Children;
        public $Id;
        public $text;
        public $expand;
        public $html;
        public $width = 1;
        public $order;
        public $level;
        public $row = array();
        public $meta = array();
        public static $rowClass = array(0 => "tgrRow", 1 => "tgrAltRow");
        function AddChild($_tree)
        {
            $_tree->Parent = $this;
            $this->Children[$_tree->Id] = $_tree;
            $_added_width = $_tree->width;
            $_child = $_tree;
            $_parent = $this;
            while ($_parent != NULL)
            {
                $_parent->width += $_added_width;
                $_child = $_parent;
                $_parent = $_parent->Parent;
            }
        }
        function GetExpandedWidth()
        {
            $w = 1;
            if (isset($this->Children))
            {
                foreach ($this->Children as $subTree)
                {
                    if ($subTree->expand)
                        $w += $subTree->GetExpandedWidth();
                    else
                        $w++;
                }
            }
            return $w;
        }
        function printTree()
        {
            for ($i = 1; $i < $this->_Level; $i++)
                echo "**";
            echo $this->Id . "<br>";
            if ($this->expand)
                if (isset($this->Children))
                    foreach ($this->Children as $subTree)
                        $subTree->printTree();
        }
        function getHtml()
        {
            return $this->html;
        }
        function getAllHtml()
        {
            $html = "";
            if ($this->Id != "root")
                $html = $this->html;
            if (isset($this->Children))
                foreach ($this->Children as $subTree)
                    $html .= $subTree->getAllHtml();
            return $html;
        }
        public function exportDataArray()
        {
            $_arr = array(
                'id' => $this->Id,
                'row' => $this->row,
                'meta' => $this->meta,
                'parent' => isset($this->Parent->Id) ?
                    $this->Parent->Id : null,
                'children' => array(),
            );
            if (isset($this->Children))
                foreach ($this->Children as $subTree)
                    array_push($_arr['children'], $subTree->exportDataArray());
            return $_arr;
        }
        function setOrderAndLevel($o = 0, $l = 1)
        {
            $this->order = $o;
            $this->level = $l;
            if (isset($this->Children))
                foreach ($this->Children as $subTree)
                    $o = $subTree->setOrderAndLevel($o + 1, $l + 1);
            return $o++;
        }
    }
    class TreeGridDataSource
    {
        private $_connection;
        private $_selectCommand;
        private $_settings = array(
        );
        function _new($_info)
        {
            $_ds = new TreeGridDataSource();
            $_ds->addSetting($_info);
            $_ms = array(
                'databaseConnection' => 'setConnection',
                'selectCommand' => 'setSelectCommand'
            );
            _setMethods($_ds, $_ms, $_info);
            return $_ds;
        }
        public function setConnection($_con)
        {
            $this->_connection = $_con;
        }
        public function setSelectCommand($s)
        {
            $this->_selectCommand = $s;
        }
        public function set($_st)
        {
            $this->_settings = _array_merge_replace($this->_settings, $_st);
        }
        public function addSetting($_st)
        {
            $this->_settings = _array_merge_replace($this->_settings, $_st);
        }
        public function getData()
        {
            $_rows = array();
            $_result = mysql_query($this->_selectCommand, $this->_connection);
            while ($_row = mysql_fetch_assoc($_result))
                array_push($_rows, $_row);
            return $_rows;
        }
    }
    class KoolTreeGrid
    {
        public $_version = "1.0.0.0";
        public $Id;
        public $DataSource;
        public $TreeArray;
        public $ArrayData;
        public $idField;
        public $parentField;
        public $expandField;
        public $viewstate;
        public $_treeView;
        public $_tree;
        private $_settings = array(
            'id' => 'KoolTreeGrid1',
            'divId' => 'div_KoolTreeGrid1',
            'ktvId' => 'KoolTreeGrid1_ktv',
            'width' => '600px',
            'style' => 'default',
            'styles' => array(
                'default', 'office2010blue', 
                'outlook', 'lightsky', 'sunset'),
            'columns' => array(),
        );
        public static function newTreeGrid($_info)
        {
            $_treeGrid = new KoolTreeGrid();
            $_treeGrid->set($_info);
            return $_treeGrid;
        }
        public static function csvToArray($filename, $delimiter = ',', $rowLength = 2000)
        {
            $file = fopen($filename, 'r');
            if ($file !== FALSE)
            {
                $header = NULL;
                $data = array();
                while (($row = fgetcsv($file, $rowLength, $delimiter)) !== FALSE)
                {
                    if ($header == NULL || count($row) == count($header))
                    {
                        foreach ($row as & $e)
                        {
                            $e = trim($e);
                        }
                        if ($header == NULL)
                            $header = $row;
                        else
                            array_push($data, array_combine($header, $row));
                    }
                }
                fclose($file);
                return $data;
            }
            return FALSE;
        }
        function process()
        {
            $_ms = array(
                'id' => 'setId',
                'dataSource' => 'setDataSource',
                'columns' => 'setColumns',
                'TreeArray' => 'setTreeArray',
                'ArrayData' => 'setArrayData',
                'parameterValues' => 'setParameterValues'
            );
            _setMethods($this, $_ms, $this->_settings);
            $divId = 'div_' . $this->Id;
            $this->_settings['divId'] = $divId;
            $treeViewId = $this->Id . '_ktv';
            $this->_settings['ktvId'] = $treeViewId;
            $myKoolTreeView = new _KoolTreeView($treeViewId);
            if (!isset($this->_settings['style']))
                $this->_settings['style'] = 'default';
            $myKoolTreeView->styleFolder = $this->_settings['style'];
            $myKoolTreeView->showLines = true;
            $myRoot = $myKoolTreeView->getRootNode();
            $treeRoot = new _SimpleTree();
            $treeRoot->Id = "root";
            $treeRoot->Parent = NULL;
            $treeRoot->expand = TRUE;
            $nodeList = array();
            $nodeList["root"] = $treeRoot;
            $this->_treeView = $myKoolTreeView;
            $this->_tree = $treeRoot;
            $tpl_span = "<span id='{id}' style='display:inline-block'>{content}</span>";
            $columns = $this->getColumns();
            if (isset($this->TreeArray))
            {
                $treeRoot = $this->addTree($treeRoot, $this->TreeArray, $columns);
            }
            else
            {
                $idField = isset($this->_settings['idField']) ?
                    trim($this->_settings['idField']) : "";
                $parentField = isset($this->_settings['parentField']) ?
                    trim($this->_settings['parentField']) : "";
                $metaField = isset($this->_settings['metaField']) ?
                    trim($this->_settings['metaField']) : "";
                $rows = array();
                $rows = isset($this->ArrayData) ?
                    $this->ArrayData : $this->DataSource->getData();
                foreach ($rows as $row)
                {
                    if (empty($row[$parentField]))
                    {
                        $treeNode = $treeRoot;
                        $span = str_replace("{id}", $treeViewId . ".root_text", $tpl_span);
                        $span = str_replace("{content}", $row[$columns[0]['field']], $span);
                        $myRoot->Id = $row[$idField];
                        $myRoot->text = $span;
                        $nodeList[$myRoot->Id] = $treeNode;
                        $rowData = array();
                        foreach ($columns as $col)
                        {
                            $f = $col['field'];
                            if (trim($row[$f]) == '')
                                $rowData[$f] = " ";
                            else
                                $rowData[$f] = $row[$f];
                            if (trim($rowData[$f]) == '')
                            {
                                $rowData[$f] = str_replace(' ', '&nbsp;', $rowData[$f]);
                                $rowData[$f] = html_entity_decode($rowData[$f]);
                            }
                        }
                        $treeNode->row = $rowData;
                        if (!empty($metaField))
                        {
                            $value = isset($row[$metaField]) ? 
                                $row[$metaField] : "";
                            $treeNode->meta = $value;
                            if (empty($treeNode->meta))
                                $treeNode->meta = array();
                        }
                        if (!isset($treeNode->meta['expand']))
                            $treeNode->meta['expand'] = true;
                        $treeNode->expand = $treeNode->meta['expand'];
                        $myRoot->expand = $treeNode->expand;
                    }
                    else //For rows which are not the root, i.e having a parent_ID
                    {
                        if (isset($nodeList[$row[$idField]]))
                        {
                            $treeNode = $nodeList[$row[$idField]];
                        }
                        else //If there hasn't, create a new node with the ID and add it to the array
                        {
                            $treeNode = new _SimpleTree();
                            $treeNode->Id = $row[$idField];
                            $nodeList[$row[$idField]] = $treeNode;
                        }
                        $treeNode->text = $row[$columns[0]['field']];
                        $rowData = array();
                        foreach ($columns as $col)
                        {
                            $f = $col['field'];
                            if (trim($row[$f]) == '')
                                $rowData[$f] = " ";
                            else
                                $rowData[$f] = $row[$f];
                            if (trim($rowData[$f]) == '')
                            {
                                $rowData[$f] = str_replace(' ', '&nbsp;', $rowData[$f]);
                                $rowData[$f] = html_entity_decode($rowData[$f]);
                            }
                        }
                        $treeNode->row = $rowData;
                        if (!empty($metaField))
                        {
                            $value = isset($row[$metaField]) ? 
                                $row[$metaField] : "";
                            $treeNode->meta = $value;
                            if (empty($treeNode->meta))
                                $treeNode->meta = array();
                        }
                        if (!isset($treeNode->meta['expand']))
                            $treeNode->meta['expand'] = true;
                        $treeNode->expand = $treeNode->meta['expand'];
                        if (isset($nodeList[$row[$parentField]]))
                        {
                            $parentNode = $nodeList[$row[$parentField]];
                        }
                        else //If there hasn't, create a new node with the ID and add it to the array
                        {
                            $parentNode = new _SimpleTree();
                            $parentNode->Id = $row[$parentField];
                            $nodeList[$row[$parentField]] = $parentNode;
                        }
                        if (!isset($parentNode->Children[$treeNode->Id]))
                            $parentNode->AddChild($treeNode);
                    }
                }
            }
            $treeRoot->setOrderAndLevel();
            $this->addKoolTree($myKoolTreeView, $treeRoot);
            $keepState = $this->getProperty('keepSate', false);
            if ($keepState===true) $myKoolTreeView->keepState = "crosspage";
            $myKoolTreeView->selectEnable = false; //Disable select node feature 
        }
        function getProperty($s, $default = null)
        {
            if (isset($this->_settings[$s]))
                return $this->_settings[$s];
            else
                return $default;
        }
        function addTree($tree, $arr, $columns)
        {
            $row = $arr['row'];
            if (!isset($tree->Parent))
            {
                $tpl_span = "<span id='{id}' style='display:inline-block'>{content}</span>";
                $span = str_replace("{id}", $this->_settings['ktvId'] . ".root_text", $tpl_span);
                $span = str_replace("{content}", $row[$columns[0]['field']], $span);
                $myRoot = $this->_treeView->getRootNode();
                $myRoot->text = $span;
                $myRoot->expand = isset($arr['meta']['expand']) ? $arr['meta']['expand'] : true;
                $rowData = array();
                foreach ($columns as $col)
                {
                    $f = $col['field'];
                    if (empty($row[$f]))
                        $rowData[$f] = " ";
                    else
                        $rowData[$f] = $row[$f];
                    if (trim($rowData[$f]) == '')
                    {
                        $rowData[$f] = str_replace(' ', '&nbsp;', $rowData[$f]);
                        $rowData[$f] = html_entity_decode($rowData[$f]);
                    }
                }
                $tree->row = $rowData;
                $tree->meta['expand'] = $tree->expand;
            }
            else
            {
                $tree->Id = $tree->Parent->Id . '_' . $row[$columns[0]['field']];
                $tree->expand = isset($arr['meta']['expand']) ? $arr['meta']['expand'] : true;
                $tree->text = $row[$columns[0]['field']];
                $rowData = array();
                foreach ($columns as $col)
                {
                    $f = $col['field'];
                    if (empty($row[$f]))
                        $rowData[$f] = " ";
                    else
                        $rowData[$f] = $row[$f];
                    if (trim($rowData[$f]) == '')
                    {
                        $rowData[$f] = str_replace(' ', '&nbsp;', $rowData[$f]);
                        $rowData[$f] = html_entity_decode($rowData[$f]);
                    }
                }
                $tree->row = $rowData;
                $tree->meta['expand'] = $tree->expand;
            }
            $_meta = !empty($arr['meta']) ? $arr['meta'] : array();
            $tree->meta = _array_merge_replace($tree->meta, $_meta);
            if (isset($arr['children']))
            {
                foreach ($arr['children'] as $childArr)
                {
                    $newTree = new _SimpleTree();
                    $newTree->Parent = $tree;
                    $newTree = $this->addTree($newTree, $childArr, $columns);
                    $tree->AddChild($newTree);
                }
            }
            return $tree;
        }
        function addKoolTree($myKoolTreeView, $treeNode)
        {
            $tpl_span = "<span id='{id}' level='{level}' style='display:inline-block'>{content}</span>";
            if (isset($treeNode->Children))
                foreach ($treeNode->Children as $subTreeNode)
                {
                    $span = str_replace("{id}", $this->Id . "_" . $subTreeNode->Id . "_text", $tpl_span);
                    $span = str_replace("{level}", $subTreeNode->level, $span);
                    $span = str_replace("{content}", $subTreeNode->text, $span);
                    $myKoolTreeView->Add($treeNode->Id, $subTreeNode->Id, $span, $subTreeNode->expand);
                    $this->addKoolTree($myKoolTreeView, $subTreeNode);
                }
        }
        public function set($_st)
        {
            $this->_settings = _array_merge_replace($this->_settings, $_st);
            return $this;
        }
        public function addSetting($_st)
        {
            $this->_settings = _array_merge_replace($this->_settings, $_st);
            return $this;
        }
        public function exportSetting()
        {
            $exportArr = array();
            $export = array(
                'id',
                'divId',
                'ktvId',
                'style',
                'styles',
                'width',
                'columns',
                'rootIndent',
                'treeIndent',
            );
            foreach ($this->_settings as $k => $v)
                if (in_array($k, $export))
                    $exportArr[$k] = $v;
            $exportArr['stylePath'] = $this->_treeView->_getStylePath();
            return $exportArr;
        }
        function getSetting()
        {
            return $this->_settings;
        }
        public function setId($_id)
        {
            $this->Id = $_id;
        }
        public function getId()
        {
            return $this->Id;
        }
        public function setDataSource($_info)
        {
            $this->DataSource = TreeGridDataSource::_new($_info);
            return $this;
        }
        public function setArrayData($_arr)
        {
            $this->ArrayData = $_arr;
            return $this;
        }
        public function setTreeArray($_arr)
        {
            $this->TreeArray = $_arr;
            return $this;
        }
        public function setColumns($columns)
        {
            foreach ($columns as & $col)
            {
                if (!isset($col['visible']))
                    $col['visible'] = TRUE;
            }
            $this->_settings['columns'] = $columns;
            return $this;
        }
        public function getColumns()
        {
            $columns = $this->_settings['columns'];
            return $columns;
        }
        public function setParameterValues()
        {
        }
        private function loadViewstate()
        {
            if (isset($_POST[$this->getId() . "_viewstate"]))
            {
                $s = $_POST[$this->getId() . "_viewstate"];
                $this->viewstate = json_decode($s, TRUE);
            }
        }
        private function getViewstateInput()
        {
            $_id = $this->getId() . "_viewstate";
            $_value = json_encode($this->viewstate);
            $_input = _HtmlProvider::_newHiddenInput($_id, $_value);
            return $_input->_getHtml();
        }
        public function exportData()
        {
            $treeViewStr = str_replace("\\", "", $this->_treeView->RenderTree());
            $treeData = $this->_tree->exportDataArray();
            $treeData = str_replace('"', '\"', $treeData);
            $data = array(
                'setting' => $this->exportSetting(),
                'treeViewHtml' => $treeViewStr,
                'tree' => $treeData,
            );
            return $data;
        }
        public function getDataInput()
        {
            $_id = $this->getId() . "_data";
            $_value = json_encode(
                $this->exportData(), JSON_HEX_TAG | JSON_HEX_APOS |
                JSON_HEX_QUOT | JSON_HEX_AMP |
                JSON_UNESCAPED_UNICODE);
            $_input = _HtmlProvider::_newHiddenInput($_id, $_value);
            return $_input->_getHtml();
        }
        public function render()
        {
            $s = '<div id="{id}" class="{class}KTG"></div>';
            $s = str_replace("{id}", $this->_settings['divId'], $s);
            $s = str_replace("{class}", $this->_settings['style'], $s);
            $s .= $this->_treeView->RegisterCSS($this->_settings['styles']);
            $s .= $this->getDataInput();
            $s .= $this->getViewstateInput();
            $s .= $this->registerJS();
            $s .= $this->renderJS();
            $s .= $this->_treeView->getStartupScript();
            $s .= $this->startUpJS();
            echo $s;
        }
        private function registerJS()
        {
            $_tpl_script = "<script type='text/javascript'>if (typeof(KoolTreeGridJS)=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));}</script>\n";
            $_script = _replace("{src}", _replace(".php", ".js", $this->getComponentURI()), $_tpl_script);
            return $_script;
        }
        private function renderJS()
        {
            $_tpl_script = "var KoolTreeGrid_{id} = KoolTreeGridJS.newTreeGrid('{id}'); KoolTreeGrid_{id}.render();";
            $_script = _replace("{id}", $this->getId(), $_tpl_script);
            $_tag = _HtmlProvider::_newJStag()
                ->_content($_script);
            return $_tag->_getHtml();
        }
        private function startUpJS()
        {
            $_tpl_script = "KoolTreeGridJS.getTreeGrid('{id}').init();";
            $_script = _replace("{id}", $this->getId(), $_tpl_script);
            $_tag = _HtmlProvider::_newJStag()
                ->_content($_script);
            return $_tag->_getHtml();
        }
        private function getComponentURI()
        {
            {
                $_root = _getRoot();
                $_file = substr(_replace("\\", "/", __FILE__), strlen($_root));
                return $_file;
            }
        }
        private function getStylePath()
        {
            $_com_uri = $this->getComponentURI();
            $_styles_folder = _replace(strrchr($_com_uri, "/"), "", $_com_uri);
            return $_styles_folder;
        }
    }
}
