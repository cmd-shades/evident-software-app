<?php
$_version = "1.0.0.0";

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
function _md5($_text)
{
	return md5($_text);
}
function _replace($key,$rep,$str)
{
	return str_replace($key,$rep,$str);
}
/*----------------------------------------------------------*/
/*full_start -----------------------------------------------*/

function _js_header()
{
	header("Content-type: text/javascript");
}
function _js_footer()
{
	echo "var _miO1=0;";
}
function _exit()
{
	return exit();
}


/*full_end -------------------------------------------------*/
/*----------------------------------------------------------*/


if (isset($_GET[_md5("js")]))
{
	_js_header();
?>
#javascript#
<?php
	_js_footer();
	_exit();
}

if (!class_exists("KoolSocialShare",false))
{

/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */

	

class _SocialShareItem
{
	var $_KSS;
}

class CompactButton
{
	function Render()
	{
		
	}
}

class SocialLineBreak
{
	function Render()
	{
		$_item = "<li class='kssItem kssLineBreak'></li>";		
		return $_item;
	}
}



class SocialButton extends _SocialShareItem
{
	var $Width = "16px";
	var $Height = "16px";
	var $ImageSrc;
	var $Type;//"Facebook"|"Twitter"|"LinkedIn"|"Digg"|"StumbleOn"
	var $Text;
	var $Tooltip;
	var $CssClass;
	
	function __construct($_type,$_text=null,$_tooltip=null)
	{
		$this->Type = $_type;
		if($_text!==null) $this->Text = $_text;
		if($_tooltip!==null) $this->Tooltip = $_tooltip;
	}
	function Render()
	{
		$_tooltip = null;
		if($this->Tooltip!==null) $_tooltip = $this->Tooltip;
		if($_tooltip===null) $_tooltip = $this->Text;
		if($_tooltip===null) $_tooltip = $this->Type;
		
		$_tpl_item = "<li class='kssItem' title='{tooltip}'><a class='kssLink kss{type}' href='javascript: void 0'>{item_icon}{item_text}</a></li>";		
		$_tpl_item_icon = "<span class='kssIcon' {imagesrc}></span>";
		$_tpl_item_text = "<span class='kssText'>{text}</span>";
		$_tpl_imagesrc = "style='background-image:url({imagesrc});width:{width};height:{height}'";

		$_item = _replace("{item_icon}",$_tpl_item_icon,$_tpl_item);
		$_item = _replace("{item_text}",($this->Text!==null)?$_tpl_item_text:"",$_item);			
		
		$_item = _replace("{type}",$this->Type,$_item);
		$_item = _replace("{text}",$this->Text,$_item);
		$_item = _replace("{tooltip}",$_tooltip,$_item);

		if($this->ImageSrc!==null)
		{
			$_imagesrc = _replace("imagesrc",$this->ImageSrc,$_tpl_imagesrc);
			$_imagesrc = _replace("width",$this->$this->Width,$_imagesrc);
			$_imagesrc = _replace("height",$this->Height,$_imagesrc);
			$_item = _replace("{imagesrc}",$_imagesrc,$_item);	
		}
		else
		{
			$_item = _replace("{imagesrc}","",$_item);
		}
		return $_item;
	}
}

class FacebookButton extends _SocialShareItem
{
	var $SendButton = true;
	var $DarkColor = false;
	var $Font = "";
	var $Width = "";
	var $Layout = "standard";
	var $ShowFaces = true;
	var $Action = "like";
	function Render()
	{
		$_tpl_item = "<li class='kssItem kssFacebookButton'><div style='vertical-align:middle'>{facebook_register}{facebook_button}</div></li>";		
		$_tpl_facebook_register  = "<div id='fb-root'></div><script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = '//connect.facebook.net/en_US/all.js#xfbml=1';fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script>";
		
		$_tpl_facebook_button = "<fb:like action='{action}' layout='{layout}' {url} send='{sendbutton}' width='{width}' show_faces='{showfaces}' {darkcolor}></fb:like>";		


		$_facebook_button = _replace("{layout}",$this->Layout,$_tpl_facebook_button);
		$_facebook_button = _replace("{url}",($this->_KSS->UrlToShare!==null)?"href='".$this->_KSS->UrlToShare."'":"",$_facebook_button);
		$_facebook_button = _replace("{width}",$this->Width,$_facebook_button);
		$_facebook_button = _replace("{action}",$this->Action,$_facebook_button);
		$_facebook_button = _replace("{showfaces}",($this->ShowFaces)?"true":"false",$_facebook_button);
		$_facebook_button = _replace("{darkcolor}",($this->DarkColor)?"colorscheme='dark'":"",$_facebook_button);
		$_facebook_button = _replace("{sendbutton}",($this->SendButton)?"true":"false",$_facebook_button);
		
		
		$_item = _replace("{facebook_register}",$_tpl_facebook_register,$_tpl_item);
		$_item = _replace("{facebook_button}",$_facebook_button,$_item);
		
		return $_item;
	}
}

class FacebookLike extends FacebookButton
{
	var $SendButton = false;
	var $Layout = "standard";
	var $ShowFaces = false;
}

class FacebookLikeWithSend extends FacebookButton
{
	var $SendButton = true;
	var $Layout = "standard";
	var $ShowFaces = false;	
}


class FacebookLikeWithSendAndPeople extends FacebookButton
{
	var $SendButton = true;
	var $Layout = "standard";
	var $ShowFaces = true;	
}


class FacebookLikeWithCount extends FacebookButton
{
	var $SendButton = false;
	var $Layout = "button_count";
	var $ShowFaces = false;	
}

class FacebookLikeWithCountAndSend extends FacebookButton
{
	var $SendButton = true;
	var $Layout = "button_count";
	var $ShowFaces = false;	
}
class FacebookVertical extends FacebookButton
{
	var $SendButton = true;
	var $Layout = "box_count";
	var $ShowFaces = false;	
}


class GooglePlusButton extends _SocialShareItem
{
	var $Size="medium";//"small"|"medium"|"standard"|"tall"
	var $Annotation = "none";
	var $Width="";
	
	function Render()
	{
		$_tpl_item = "<li class='kssItem kssGooglePlusButton'><div style='vertical-align:middle'>{googleplus_register}{googleplus_button}</div></li>";		
		$_tpl_googleplus_register  = "<script type='text/javascript'>(function() {var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;po.src = 'https://apis.google.com/js/plusone.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);})();</script>";
		
		$_tpl_googleplus_button = "<g:plusone {url} width='{width}' size='{size}' annotation='{annotation}' ></g:plusone>";		

		$_googleplus_button = _replace("{size}",$this->Size,$_tpl_googleplus_button);
		$_googleplus_button = _replace("{url}",($this->_KSS->UrlToShare!==null)?"href='".$this->_KSS->UrlToShare."'":"",$_googleplus_button);
		$_googleplus_button = _replace("{annotation}",$this->Annotation,$_googleplus_button);
		$_googleplus_button = _replace("{width}",$this->Width,$_googleplus_button);
		
		$_item = _replace("{googleplus_register}",$_tpl_googleplus_register,$_tpl_item);
		$_item = _replace("{googleplus_button}",$_googleplus_button,$_item);
		
		return $_item;
	}
}

class GooglePlusWithCount extends GooglePlusButton
{
	var $Annotation = "bubble";
}


class GooglePlusWithCountAndPeople extends GooglePlusButton
{
	var $Annotation = "inline";
}

class GooglePlusVertical extends GooglePlusButton
{
	var $Size="tall";
	var $Annotation = "bubble";
}



class TwitterButton extends _SocialShareItem
{
	var $ShowCount = false;
	function Render()
	{
		$_tpl_item = "<li class='kssItem kssTwitterButton'><div style='vertical-align:middle'>{twitter_button}{twitter_register}</div></li>";		
		$_tpl_twitter_register  = "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>";
		
		$_tpl_twitter_button = "<a href='ttps://twitter.com/share' class='twitter-share-button' {url} {title} {showcount}>Tweet</a>";		

		$_twitter_button = _replace("{url}",($this->_KSS->UrlToShare!==null)?"data-url='".$this->_KSS->UrlToShare."'":"",$_tpl_twitter_button);
		$_twitter_button = _replace("{title}",($this->_KSS->TitleToShare!==null)?"data-text='".$this->_KSS->TitleToShare."'":"",$_twitter_button);
		$_twitter_button = _replace("{showcount}",($this->ShowCount)?"":"data-count='none'",$_twitter_button);
		
		$_item = _replace("{twitter_register}",$_tpl_twitter_register,$_tpl_item);
		$_item = _replace("{twitter_button}",$_twitter_button,$_item);
		
		return $_item;
	}
}

class TwitterWithCount extends TwitterButton
{
	var $ShowCount = true;
}

class TwitterFollow extends _SocialShareItem
{
	
	var $Username = "twitter";
	
	function __construct($_username)
	{
		$this->Username = $_username;
	}
	
	function Render()
	{
		$_tpl_item = "<li class='kssItem kssTwitterButton'><div style='vertical-align:middle'>{twitter_button}{twitter_register}</div></li>";		
		$_tpl_twitter_register  = "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>";
		
		$_tpl_twitter_button = "<a href='https://twitter.com/{username}' class='twitter-follow-button' data-show-count='false'>Follow @{username}</a>";		

		$_twitter_button = _replace("{username}",$this->Username,$_tpl_twitter_button);
		
		$_item = _replace("{twitter_register}",$_tpl_twitter_register,$_tpl_item);
		$_item = _replace("{twitter_button}",$_twitter_button,$_item);
		
		return $_item;
	}
}

class LinkedInButton extends _SocialShareItem
{
	var $_Count = "";//""|"top"|"right"
	function Render()
	{
		$_tpl_item = "<li class='kssItem kssLinkedInButton'><div style='vertical-align:middle'>{linkedin_button}{linkedin_register}</div></li>";		
		$_tpl_linkedin_register  = "<script src='//platform.linkedin.com/in.js' type='text/javascript'></script>";
		
		$_tpl_linkedin_button = "<script type='IN/Share' {count} {url}></script>";		

		$_linkedin_button = _replace("{url}",($this->_KSS->UrlToShare!==null)?"data-url='".$this->_KSS->UrlToShare."'":"",$_tpl_linkedin_button);

		$_linkedin_button = _replace("{count}",($this->_Count=="")?"":"data-counter='".$this->_Count."' data-showzero='true'",$_linkedin_button);			
		
		$_item = _replace("{linkedin_register}",$_tpl_linkedin_register,$_tpl_item);
		$_item = _replace("{linkedin_button}",$_linkedin_button,$_item);
		
		return $_item;
	}
}
class LinkedInWithCount extends LinkedInButton
{
	var $_Count = "right";
}
class LinkedInVertical extends LinkedInButton
{
	var $_Count = "top";
}



class KoolSocialShare
{
	var $_version = "1.0.0.0";

	var $id;

	var $scriptFolder;
	var $styleFolder;
	var $_style;

	var $_Items;

	var $Width;
	var	$UrlToShare;
	var	$TitleToShare;
	
	var $Vertical = false;
	var $Align = "left";
	//var $ShowButtonText = true;
	
	
	function __construct($_id)
	{
		$this->id = $_id;
		$this->_Items = array();
	}
	
	function ShowAllShareButtons()
	{
		$this->Add(new SocialButton("Facebook"));
		$this->Add(new SocialButton("Twitter"));
		$this->Add(new SocialButton("Blogger"));
		$this->Add(new SocialButton("Delicious"));
		$this->Add(new SocialButton("LinkedIn"));
		$this->Add(new SocialButton("MySpace"));
		$this->Add(new SocialButton("Reddit"));
		$this->Add(new SocialButton("StumbleUpon"));
		$this->Add(new SocialButton("Tumblr"));
		$this->Add(new SocialButton("MailTo"));
	}


	function Add($_item)
	{
		$_item->_KSS = $this;
		array_push($this->_Items,$_item);
		return $_item;
	}

	function Render()
	{
		//global $_version;
		$_script= $this->RegisterCss();
		$_script.= $this->RenderSocialShare();
		$_is_callback = isset($_POST["__koolajax"])||isset($_GET["__koolajax"]);		
		$_script.= ($_is_callback)?"":$this->RegisterScript();
		$_script.="<script type='text/javascript'>";
		$_script.= $this->StartupScript();
		$_script.="</script>";
		return $_script;		
	}
	
	function RenderSocialShare()
	{
		$this->_positionStyle();
		$_trademark = "\n<!--KoolSocialShare version ".$this->_version." - www.koolphp.net -->\n";
		$_tpl_main = "{trademark}<div id='{id}' class='{style}KSS' style='text-align:{align};{width}'><div class='kssInner'><ul class='kssUL {vertical}'>{items}</ul></div></div>";
		$_items = "";
		for($i=0;$i<count($this->_Items);$i++)
		{
				$_items.=$this->_Items[$i]->Render();
		}
		$_main = _replace("{id}", $this->id, $_tpl_main);
		$_main = _replace("{style}", $this->_style, $_main);
		$_main = _replace("{trademark}", $_trademark, $_main);
		$_main = _replace("{vertical}", ($this->Vertical)?"kssVertical":"kssHorizontal", $_main);		
		$_main = _replace("{align}",$this->Align, $_main);		
		$_main = _replace("{width}", ($this->Width!==null)?"width:".$this->Width.";":"", $_main);		
		$_main = _replace("{items}", $_items, $_main);
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
		//Generate CSS
		$this->_positionStyle();
		$_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KSS')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KSS';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);}</script>";
		$_script= _replace("{style}",$this->_style,$_tpl_script);
		$_script= _replace("{stylepath}",$this->_getStylePath(),$_script);
		return $_script;
	}


	function RegisterScript()
	{
		$_tpl_script = "<script type='text/javascript'>if(typeof _libKSS=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKSS=1;}</script>";
		
		$_script= _replace("{src}",$this->_getComponentURI()."?".md5("js"),$_tpl_script);
		//$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script);
		
		return $_script;
	}	

	
	function StartupScript()
	{
		$_tpl_script  = "var {id}; function {id}_init(){ {id} = new KoolSocialShare('{id}','{url_to_share}','{title_to_share}');}";
		$_tpl_script .= "if (typeof(KoolSocialShare)=='function'){{id}_init();}";
		$_tpl_script .= "else{if(typeof(__KSSInits)=='undefined'){__KSSInits=new Array();} __KSSInits.push({id}_init);{register_script}}";
		$_tpl_register_script = "if(typeof(_libKSS)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKSS=1;}";
		
		$_register_script= _replace("{src}",$this->_getComponentURI()."?".md5("js"),$_tpl_register_script);
		//$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script);

		$_script = _replace("{id}",$this->id,$_tpl_script);
		$_script = _replace("{url_to_share}",urldecode($this->UrlToShare),$_script);
		$_script = _replace("{title_to_share}",urldecode($this->TitleToShare),$_script);
		
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

/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */
/* ****************************************************************************************** */

	
}
?>