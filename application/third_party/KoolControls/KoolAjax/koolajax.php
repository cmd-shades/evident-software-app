<?php
//$_version = "2.8.0.0";

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


if (!class_exists("KoolAjax",false))
{


function getParam($paramName)
{
	if (isset($_POST[$paramName]))
		return $_POST[$paramName];
	if (isset($_GET[$paramName]))
		return $_GET[$paramName];
	return NULL;
}



function resultProcess($result)
	{
		$pRes="null";
		$type= gettype($result);
		switch($type)
		{
			case "integer":
			case "double":
				$pRes=$result;
				break;
			case "boolean":
				$pRes=($result)?"true":"false";
				break;
			case "string":
				$pRes="\""._esc($result)."\"";
				break;
			case "array":
			case "object":
				$pRes="{";
				if ($type == "object")
					$result = get_object_vars($result);
				foreach ($result as $k=>$v)
					$pRes.= ((is_numeric($k))?$k:"\""._esc($k)."\"").":".resultProcess($v).",";
				if (count($result))
					$pRes=substr($pRes,0,-1);

				$pRes.="}";
				break;
		}
		return $pRes;
	}
class KoolUpdatePanelTrigger
{
	var $elementid;
	var $event;
	function __construct($elementid,$event)
	{
		$this->elementid = $elementid;
		$this->event = $event;		
	}
}

class KoolUpdatePanelLoading
{
	var $_backColor="white";
	var $_opacity=50;
	var $_image;
}

class UpdatePanel
{
	var $id;
	var $content;
	var $rendermode = "block"; //"inline"/"block"
	var $cssclass;
	var $triggers;
	var $_loading=null;
	static $koolajax; //Fix 27/09 starrynighthn	
	
	function __construct($id)
	{
		$this->id = $id;
		$this->triggers = array();
	}
	
	function LoadXMLFile($filename)
	{
		//Loading xml
	}
	function LoadXML($xml)
	{
		if (gettype($xml)=="string")
		{
			$xmlDoc = new DOMDocument();
			$xmlDoc->loadXML($xml);
			$xml = $xmlDoc->documentElement;
		}
		//id
		$id = $xml->getAttribute("id");
		if($id!="") $this->id = $id;
		//class
		$this->cssclass = $xml->getAttribute("cssclass");
		if ($this->cssclass=="")
		{
			$this->cssclass = $xml->getAttribute("class");	
		}
		//RenderMode
		$rm = $xml->getAttribute("rendermode");
		$this->rendermode = ($rm!="")?$rm:"block";
		
		foreach($xml->childNodes as $updatepanel_subnode)
		{
			switch(strtolower($updatepanel_subnode->nodeName))
			{
				case "content":
					
					$_innerXML = _getInnerXML($updatepanel_subnode,$xml->parentNode);
					$_innerXML = trim($_innerXML);
					if (substr($_innerXML,0,9)=="<![CDATA[")
					{
						$_innerXML = substr($_innerXML,9);
					}
					if (substr($_innerXML,-3)=="]]>")
					{
						$_innerXML = substr($_innerXML,0,-3);
					}					
					$this->content = $_innerXML;
					break;
				case "triggers":
					foreach($updatepanel_subnode->childNodes as $triggers_subnode)
					{
						if (strtolower($triggers_subnode->nodeName)=="trigger")
						{
							$this->addTrigger($triggers_subnode->getAttribute("elementid"),$triggers_subnode->getAttribute("event"));
						}
					}
					break;
				
				case "loading":
					$this->_loading = new KoolUpdatePanelLoading();
					$this->_loading->_image = $updatepanel_subnode->getAttribute("image");
					$_backColor = $updatepanel_subnode->getAttribute("backColor");
					if ($_backColor!="")$this->_loading->_backColor = $_backColor;
					$_opacity = $updatepanel_subnode->getAttribute("opacity");
					if ($_opacity!="") $this->_loading->_opacity = intval($_opacity);
					break;
			}
		}
	}
	function setLoading($_image,$_backColor="white",$_opacity=50)
	{
		$this->_loading = new KoolUpdatePanelLoading();
		$this->_loading->_image = $_image;
		$this->_loading->_backColor = $_backColor;
		$this->_loading->_opacity = $_opacity;		
	}
	function addTrigger($elementid,$event)
	{
		array_push($this->triggers,new KoolUpdatePanelTrigger($elementid,$event));
	}
	function Render()
	{
		//global $koolajax;
		$koolajax = UpdatePanel::$koolajax; //Fix 27/09 starrynighthn
		
		if ($koolajax->isCallback && getParam("__updatepanel")==$this->id)
		{
				$_n = 0;
				while(ob_get_level()>0 && $_n<10)
				{
					ob_end_clean();
					$_n++;	
				}			
				//Echo content as well as the client script if there is
				echo "<updatepanel>".$this->content."</updatepanel>".(($koolajax->_clientscript=="")?"":"[!@s>".$koolajax->_clientscript);
				exit();
		}
		else
		{
			$tpl_body_block = "<div id='{id}' class='_kup {class}' style='position:relative;'><div>{content}</div>{loading}</div>";
			$tpl_body_inline = "<span id='{id}' {class}>{content}</span>";
			$tpl_loading = "<div id='{id}_loading' style='position:absolute;display:none;background:url({image}) no-repeat 50% 50%;background-color:{backColor};filter:alpha(opacity={opacity});-moz-opacity:{opacity/100};opacity:{opacity/100};'><img src='{image}' style='display:none' alt='' /></div>";	
			$tpl_jscript = "<script type='text/javascript'>var {id} = new KoolUpdatePanel('{id}',{loading});{triggers}</script>";
			$tpl_trigger = "{id}.addTrigger();";
			$script = ($this->rendermode=="inline")?$tpl_body_inline:$tpl_body_block;
			$script = _replace("{id}",$this->id,$script);
			$script = _replace("{content}",$this->content,$script);		
			$script = _replace("{class}",($this->cssclass!="")?$this->cssclass:"",$script);
			
			$jscript = $tpl_jscript;
			$jscript = _replace("{id}",$this->id,$jscript);			
			
			if ($this->_loading!=null)
			{
				$_loading = _replace("{id}",$this->id,$tpl_loading);
				$_loading = _replace("{image}",$this->_loading->_image,$_loading);
				$_loading = _replace("{opacity}",$this->_loading->_opacity,$_loading);
				$_loading = _replace("{opacity/100}",$this->_loading->_opacity/100,$_loading);				
				$_loading = _replace("{backColor}",$this->_loading->_backColor,$_loading);
				$script = _replace("{loading}",$_loading,$script);
				$jscript = _replace("{loading}","1",$jscript);
			}
			else
			{
				$script = _replace("{loading}","",$script);
				$jscript = _replace("{loading}","0",$jscript);	
			}
			
			$triggers = "";
			for($i=0;$i<sizeof($this->triggers);$i++)
			{
				$triggers.=$this->id.".addTrigger('".$this->triggers[$i]->elementid."','".$this->triggers[$i]->event."');";
			}
			
			$jscript = _replace("{triggers}",$triggers,$jscript);
			$script.=$jscript;		
			return $script;			
		}
	}
}

class KoolAjax
{	
	//This function will take user-definded data then return HTML string as result to build Component.
	var $_version = "2.8.0.0";
	var $funcList;
	var $panelIds;
	var $isCallback=false;
	var $results;
	var $_clientscript="";
	var $scriptFolder= "";
	var $CharSet;
	function __construct()
	{
		$this->funcList = array();
		$this->panelIds = array();
		if(getParam("__koolajax")!=NULL)
		{
			$this->isCallback = true;
		}
		$this->results = array();
	}
	
	function enableFunction($sFunc)
	{
		array_push($this->funcList,$sFunc);
	}
	
	function registerClientScript($_script)
	{
		$this->_clientscript.=$_script.";";
	}
	function Render()
	{
		
		if ($this->isCallback)
		{
			if (getParam("__func")!=NULL)
			{
				$_n = 0;
				while(ob_get_level()>0 && $_n<10)
				{
					ob_end_clean();
					$_n++;	
				}	
				//Execute and return result from function
				$func = getParam("__func");
				$args = getParam("__args");			
				$result = "null";
				$error = "null";
				try
				{
					$result = resultProcess(call_user_func_array($func, ($args!==null)?$args:array()));					
				}
				catch(Exception $ex)
				{
					$error = "\"".$ex.getMessage()."\"";
				}
				$response = "<callback>{\"r\":{result},\"e\":{error}}</callback>{js}";
				$_tpl_js="[!@s>{js}";
				$response = _replace("{result}",$result,$response);
				$response = _replace("{error}",$error,$response);
				$response = _replace("{js}",($this->_clientscript=="")?"":_replace("{js}",$this->_clientscript,$_tpl_js),$response);
				echo $response;
				exit();
			}
		}		
		else
		{
			//global $_version;
			$script="";
			//First render
			//Register library
			$script="\n<!--KoolAjax version ".$this->_version." - www.koolphp.net -->\n";		
			
			$script.="<script type='text/javascript' src='"._replace(".php",".js",$this->_getComponentURI())."'> </script>";
			
			
			if ($this->CharSet!==null)
			{
				$script.="<script type='text/javascript'>koolajax.charset='".$this->CharSet."';</script>";
			}
			//Initiation code
			if(sizeof($this->funcList)>0 || sizeof($this->panelIds)>0)
			{
				$script.="\n<script type='text/javascript'>\n";
				for($i=0;$i<sizeof($this->funcList);$i++)
				{
					$script.="function ".$this->funcList[$i]."()\n";
					$script.="{\n";
					$script.="return koolajax.funcRequest('".$this->funcList[$i]."',arguments);\n";			
					$script.="}\n";
				}
				$script.="</script>\n";		
			}
			if ($this->_clientscript!="")
			{
				$script.="\n<script type='text/javascript'>\n";
				$script.=$this->_clientscript.";";
				$script.="\n</script>\n";				
			}
			return $script;
		}
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
}

if (!isset($koolajax))
{
	$koolajax= new KoolAjax();
	if ($koolajax->isCallback)
	{
		ob_start();
	}
	UpdatePanel::$koolajax = $koolajax;//Fix 27/09 starrynighthn
}
	
}
?>