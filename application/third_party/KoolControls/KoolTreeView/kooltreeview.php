<?php
	$_version = "3.0.0.2";
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
if (!class_exists("KoolTreeView",false))
{
class _TreeNode
{
	var $id;
	var $text;
	var $image;
	var $children;
	var $expand =false;
	var $subTreeUrl;
	var $visible = true;
	var $showPlusMinus = true;
	var $data;
	var $parent;//not obfuscated
	function __construct($_id,$_text="",$_expand=false,$_image="",$_subTreeUrl="")
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
		array_push($this->children,$_child);
	}
	function addData($_name,$_data)
	{
		$this->data[$_name] = $_data;
	}
}
class KoolTreeView
{
	var $_version = "3.0.0.2";
	var $id;
	var $root;
	var $_style;
	var $_list;
	var $width="";
	var $height="";
	var $overflow="";	
	var $styleFolder;
	var $imageFolder;
	var $selectedIds;
	var $selectEnable = true;
	var $selectDisableIds;
	var $multipleSelectEnable = false;
	var $DragAndDropEnable = false;
	var $dragDisableIds;
	var $dropDisableIds;
	var $EditNodeEnable=false;
	var $editDisableIds;	
	var $isSubTree = false;
	var $singleExpand = false;
	var $keepState="none";
	var $keepStateHours=24;
	/*
	 * Option for keep state of treeview
	 * "none": No state saved in cookie
	 * "onpage": State save for particular page only
	 * "crosspage": Cross-page state
	 * The state only can be maintain if the id of treeview remain the same
	 */
	var $showLines =false;
	var $scriptFolder="";
	function __construct($_id)
	{
		$this->id = $_id;
		$this->root = new _TreeNode("root");
		$this->_list = array();
		$this->_list["root"] = $this->root;		
	}
	function loadXML($xml)
	{
		if (gettype($xml)=="string")
		{
			$xmlDoc = new DOMDocument();
			$xmlDoc->loadXML($xml);
			$xml = $xmlDoc->documentElement;
		}
		$id = $xml->getAttribute("id");
		if($id!="") $this->id = $id;
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
		if($_scriptFolder!="") $this->scriptFolder = $_scriptFolder;		
		$tmp = $xml->getAttribute("selectEnable");
		$this->selectEnable = ($tmp=="")?false:(($tmp=="true")?true:false);
		$tmp = $xml->getAttribute("multipleSelectEnable");
		$this->multipleSelectEnable = ($tmp=="")?false:(($tmp=="true")?true:false);
		$tmp = $xml->getAttribute("DragAndDropEnable");
		$this->DragAndDropEnable = ($tmp=="")?false:(($tmp=="true")?true:false);
		$tmp = $xml->getAttribute("EditNodeEnable");
		$this->EditNodeEnable = ($tmp=="")?false:(($tmp=="true")?true:false);
		$tmp = $xml->getAttribute("isSubTree");
		$this->isSubTree = ($tmp=="")?false:(($tmp=="true")?true:false);
		$tmp = $xml->getAttribute("showOnExpand");
		$this->showOnExpand = ($tmp=="")?false:(($tmp=="true")?true:false);
		$tmp = $xml->getAttribute("keepState");
		if($tmp!="") $this->keepState = $tmp;
		$tmp = $xml->getAttribute("keepStateHours");
		if($tmp!="") $this->keepStateHours = intval($tmp);
		$tmp = $xml->getAttribute("singleExpand");
		$this->singleExpand = ($tmp=="")?false:(($tmp=="true")?true:false);
		foreach($xml->childNodes as $treeview_subnode)
		{
			switch(strtolower($treeview_subnode->nodeName))
			{
				case "rootnode":
					$this->root->text = $treeview_subnode->getAttribute("text");
					$this->root->image = $treeview_subnode->getAttribute("image");
					$this->root->subTreeUrl = $treeview_subnode->getAttribute("subTreeUrl");					
					$tmp = $treeview_subnode->getAttribute("expand");
					$this->root->expand = ($tmp=="")?false:(($tmp=="true")?true:false);
					$tmp = $treeview_subnode->getAttribute("visible");
					$this->root->visible = ($tmp=="")?true:(($tmp=="true")?true:false);
					$tmp = $treeview_subnode->getAttribute("showPlusMinus");
					$this->root->showPlusMinus = ($tmp=="")?true:(($tmp=="true")?true:false);
					$this->buildChildren($this->root,$treeview_subnode);
					break;
				case "templates":
					break;				
			}
		}
	}
	function buildChildren($node,$xmlnode)
	{
		foreach($xmlnode->childNodes as $xmlchildnode)
		{
			if ($xmlchildnode->nodeName=="node")
			{
				$id = $xmlchildnode->getAttribute("id");
				$childnode = new _TreeNode($id);
				$childnode->text = $xmlchildnode->getAttribute("text");
				$childnode->image = $xmlchildnode->getAttribute("image");
				$childnode->subTreeUrl = $xmlchildnode->getAttribute("subTreeUrl");
				$tmp = $xmlchildnode->getAttribute("expand");
				$childnode->expand = ($tmp=="")?false:(($tmp=="true")?true:false);
				$this->buildChildren($childnode,$xmlchildnode);
				$node->addChild($childnode);				
			}
		}
	}
	function Render()
	{
		$script="";
		if ($this->isSubTree)
		{
			$this->_positionStyle();
			for($i=0;$i<sizeof($this->root->children);$i++)
				$script.=$this->RenderNode($this->root->children[$i]);			
		}
		else
		{
			$script="\n<!--KoolTreeView version ".$this->_version." - www.koolphp.net -->\n";	
			$script.= $this->RegisterCSS();
			$script.= $this->RenderTree();	
			$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
			$script.= ($_is_callback)?"":$this->RegisterScript();
			$script.="<script type='text/javascript'>";
			$script.= $this->StartupScript();
			$script.="</script>";
		}
		return $script;
	}
	function Add($_parentid,$_id,$_text="",$_expand=false,$_image="",$_subTreeUrl="")
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
		$this->styleFolder = _replace("\\","/",$this->styleFolder);
		$_styleFolder = trim($this->styleFolder,"/");
		$_lastpos = strrpos($_styleFolder,"/");
		$this->_style = substr($_styleFolder,($_lastpos?$_lastpos:-1)+1);
	}
	function RegisterCss()
	{
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KTV')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KTV';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}
	function RenderTree()
	{
		$this->_positionStyle();
		$_tpl_main = "<div id='{id}' class='{style}KTV' style='{width}{height}{overflow}'><ul class='ktvUL {nopadding} {lines}'>{subnodes}</ul>{clientstate}</div>";
		$tpl_clientstate = "<input type='hidden' id='{id}.clientState' name='{id}.clientState' />";
		$_main = _replace("{id}",$this->id,$_tpl_main);
		$_main = _replace("{style}",$this->_style,$_main);
		$_main = _replace("{nopadding}",(!$this->root->visible || !$this->root->showPlusMinus)?"ktvNoPadding":"",$_main);		
		$_main = _replace("{subnodes}",$this->RenderNode($this->root),$_main);
		$_main = _replace("{lines}",(($this->showLines)?"ktvLines":""),$_main);
		$clientstate = _replace("{id}",$this->id,$tpl_clientstate);
		if (true)
		{
			$_main = _replace("{clientstate}",$clientstate,$_main);
		}
		$_main = _replace("{width}",(($this->width!="")?"width:".$this->width.";":""),$_main);
		$_main = _replace("{height}",(($this->height!="")?"height:".$this->height.";":""),$_main);
		$_main = _replace("{overflow}",(($this->overflow!="")?"overflow:".$this->overflow.";":""),$_main);
		$_main = _replace("{version}",$this->_version,$_main);
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
		$tmp = _replace("{text}",$node->text,$tpl_text);
		$nodecontent = _replace("{text}",$tmp,$nodecontent);
		$subnodes = "";
		if ($node->image!="")
		{
			$tmp = _replace("{image}",(($this->imageFolder!="")?$this->imageFolder."/":"").$node->image,$tpl_image);			
			$nodecontent = _replace("{image}",$tmp,$nodecontent);
		}
		else
		{
			$nodecontent = _replace("{image}","",$nodecontent);
		}
		if (sizeof($node->children)>0)
		{
			$tmp = _replace("{plusminus}",($node->expand)?"Minus":"Plus",$tpl_plusminus);
			$nodecontent = _replace("{plusminus}",$tmp,$nodecontent);	
			$subnodes = "";
			for($i=0;$i<sizeof($node->children);$i++)
			{
				$subnodes.=$this->RenderNode($node->children[$i]);
			}
			$subnodes = _replace("{subnodes}",$subnodes,$tpl_subnodes);
			$subnodes = _replace("{display}",($node->expand)?"block":"none",$subnodes);						
		}
		else
		{
			if ($node->subTreeUrl!="")
			{
				$tmp = _replace("{plusminus}","Plus",$tpl_plusminus);
				$nodecontent = _replace("{plusminus}",$tmp,$nodecontent);
			}
			else
			{
				$nodecontent = _replace("{plusminus}","",$nodecontent);	
			}
		}
		if ($node->subTreeUrl!="" || sizeof($node->data)>0)
		{
			$_esc_subTreeUrl = _esc($node->subTreeUrl);
			$_esc_data = array();
			foreach($node->data as $_k=>$_v)
			{
				$_esc_data[$_k] = _esc($_v);
			}
			$data = array("url"=>$_esc_subTreeUrl,"data"=>$_esc_data);
			$nodedata = _replace("{nodeid}",$this->id.".".$node->id,$tpl_nodedata);
			$nodedata = _replace("{value}",json_encode($data),$nodedata);
			$nodecontent = _replace("{nodedata}",$nodedata,$nodecontent);			
		}
		else
		{
			$nodecontent = _replace("{nodedata}","",$nodecontent);
		}
		$class_singlenode = "ktvLI";
		if (( isset($node->parent->children[0]) && $node->parent->children[0]===$node) || $node===$this->root)
		{
			$class_singlenode .= " ktvFirst";
		}
		if ((isset($node->parent->children) && isset($node->parent->children[sizeof($node->parent->children)-1]) && $node->parent->children[sizeof($node->parent->children)-1]===$node)  || $node===$this->root)
		{
			$class_singlenode .= " ktvLast";
		}
		$class_nodecontent = "";
		if ($node===$this->root)
		{
			$class_nodecontent = "ktvTop";
			if (!$node->visible) $class_nodecontent.=" ktvInv";
			if (!$node->showPlusMinus) $class_nodecontent.=" ktvNoPM";				
		}
		else
		{
			if ($node->parent->children[0]===$node)
			{
				$class_nodecontent = "ktvTop";			
			}
			if ($node->parent->children[sizeof($node->parent->children)-1]===$node)
			{
				$class_nodecontent = "ktvBot";
			}
			if ($class_nodecontent=="")
			{
				$class_nodecontent = "ktvMid";
			}			
		}
		$sIds =  "[".str_replace(",","][",$this->selectedIds)."]";
		if (strpos($sIds,"[".$node->id."]")!==false) $class_nodecontent .= " ktvSelected";
		$nodecontent = _replace("{class}",$class_nodecontent,$nodecontent);
		$singlenode = _replace("{nodeid}",$this->id.".".$node->id,$singlenode);
		$singlenode = _replace("{class}",$class_singlenode,$singlenode);
		$singlenode = _replace("{nodecontent}",$nodecontent,$singlenode);
		$singlenode = _replace("{subnodes}",$subnodes,$singlenode);				
		return $singlenode;		
	}
	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKTV=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKTV=1;}</script>";
		$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script); //Do comment to obfuscate
		return $_script;
	}
	function StartupScript()
	{
		$_tpl_script  = "var {id}; function {id}_init(){ {id} = new KoolTreeView(\"{id}\",{singleExpand},{selectEnable},{multipleSelectEnable},{DragAndDropEnable},{EditNodeEnable},'{keepState}',{keepStateHours},\"{cs}\");}";
		$_tpl_script .= "if (typeof(KoolTreeView)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KTVInits)=='undefined'){__KTVInits=new Array();} __KTVInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKTV)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKTV=1;}";
		$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script); //Do comment to obfuscate
		$tpl_cs = "{'selectedIds':[{selectedIds}],'selectDisableIds':[{selectDisableIds}],'dragDisableIds':[{dragDisableIds}],'dropDisableIds':[{dropDisableIds}],'editDisableIds':[{editDisableIds}]}";
		$script = _replace("{id}",$this->id,$_tpl_script);		
		$sIds = ($this->selectedIds!="")?"'"._replace(",","','",$this->selectedIds)."'":"";
		$clientstate = _replace("{selectedIds}",$sIds,$tpl_cs);
		$sIds = ($this->selectDisableIds!="")?"'"._replace(",","','",$this->selectDisableIds)."'":"";
		$clientstate = _replace("{selectDisableIds}",$sIds,$clientstate);
		$sIds = ($this->dragDisableIds!="")?"'"._replace(",","','",$this->dragDisableIds)."'":"";
		$clientstate = _replace("{dragDisableIds}",$sIds,$clientstate);
		$sIds = ($this->dropDisableIds!="")?"'"._replace(",","','",$this->dropDisableIds)."'":"";
		$clientstate = _replace("{dropDisableIds}",$sIds,$clientstate);
		$sIds = ($this->editDisableIds!="")?"'"._replace(",","','",$this->editDisableIds)."'":"";
		$clientstate = _replace("{editDisableIds}",$sIds,$clientstate);
		$script = _replace("{singleExpand}",($this->singleExpand)?"1":"0",$script);				
		$script = _replace("{selectEnable}",($this->selectEnable)?"1":"0",$script);		
		$script = _replace("{multipleSelectEnable}",($this->multipleSelectEnable)?"1":"0",$script);				
		$script = _replace("{DragAndDropEnable}",($this->DragAndDropEnable)?"1":"0",$script);				
		$script = _replace("{EditNodeEnable}",($this->EditNodeEnable)?"1":"0",$script);				
		$script = _replace("{keepState}",$this->keepState,$script);				
		$script = _replace("{keepStateHours}",$this->keepStateHours,$script);				
		$script = _replace("{cs}",$clientstate,$script);		
		$script = _replace("{register_script}",$_register_script,$script);
		return $script;
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
