/**
 * @author Administrator
 */
function _obj(_id)
{
	return document.getElementById(_id);
}
function _exist(_theObj)
{
    return (_theObj!=null);
}
function _getClass(_theObj)
{
    return _theObj.className;
}
function _getElements(_tag,_class,_parent)
{
	_parent = _exist(_parent)?_parent:document.body;
	var _elements = _parent.getElementsByTagName(_tag);
	var _result = new Array();
	for(var i=0;i<_elements.length;i++)
		if (_elements[i].className.indexOf(_class)>=0)
		{
			_result.push(_elements[i]);
		}
	return _result;
}
function _addEvent(_ob, _evType, _fn, _useCapture)
{
        if (_ob.addEventListener) {
            _ob.addEventListener(_evType, _fn, _useCapture);
            return true;
        }
        else if (_ob.attachEvent) {
            if (_useCapture) {
                //alert('IE does not support event capturing!');
                return false;
            }
            else {
                var _tmp = function() { _fn.apply(_ob, [window.event]); }
                //-- check if handler is not already attached
                if (!_ob['ref'+_evType]) _ob['ref'+_evType] = [];
                else {
                    for (var _ref in _ob['ref'+_evType]) {
                        if (_ob['ref'+_evType][_ref]._fn === _fn) return false;
                    }
                }
                var _r = _ob.attachEvent('on'+_evType, _tmp);
                //-- store references
                if (_r) _ob['ref'+_evType].push({_fn:_fn, _tmp:_tmp});
                return _r;
            }
        }
        else {
            //alert('Handler could not be attached');
            return false;
        }
}
function _goParentNode(_theObj,_level)
{
	if(!_exist(_level)) _level=1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.parentNode;
    return _theObj;
}
//-----------------------------------------------------//
//-----------------------------------------------------//

function KoolSocialShare(_id,_url_to_share,_title_to_share)
{
	this._id = _id;
	this._url_to_share = _url_to_share;
	this._title_to_share = _title_to_share;	
	this._init();
}
KoolSocialShare.prototype = 
{
	_init:function()
	{
		var _this = _obj(this._id);
		var _links = _getElements("a","kssLink",_this);
		for(i=0;i<_links.length;i++)
		{
			_addEvent(_links[i],"click",_link_click,false);
		}
	},
	_handle_link_click:function(_link)
	{
		var _type=_getClass(_link);
		_type=_type.replace("kssLink kss","").toLowerCase();
		var _data = this._get_link_width_height(_type);
		if(_type=="mailto")
		{
			location.href=_data["link"];
		}
		else
		{
			window.open(_data["link"],"_koolsocialshare_on_"+_type,"toolbar=no,status=no,menubar=no,resizeable=no,scrollbars=no,top=200,left=200,width="+_data["width"]+",height="+_data["height"]);
		}
	},
	_get_link_width_height:function(_type)
	{
		var _link="";
		var _width = 600;
		var _height = 300;
		
		var _url_to_share = this._url_to_share;
		var _title_to_share = this._title_to_share;
		
		
		switch(_type)
		{
			case "facebook":
				_link_template = "http://www.facebook.com/sharer/sharer.php?u={url}&t={title}";
				_width=660;
				_height=370;
				break;
			case "twitter":
				_link_template = "https://twitter.com/intent/tweet?original_referer={referer}&source=tweetbutton&text={title}&url={url}";
				_width=570;
				_height=344;
				break;
			case "blogger":
				_link_template = "http://www.blogger.com/blog_this.pyra?t=&u={url}&n={title}";
				_width=736;
				_height=646;
				break;
			case "delicious":
				_link_template = "http://www.delicious.com/save?url={url}&title={title}&notes=&v=6";
				_width=683;
				_height=627;
				break;
			case "linkedin":
				_link_template = "http://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}&ro=false&summary=&source=";
				_width=600;
				_height=405;
				break;
			case "myspace":
				_link_template = "http://www.myspace.com/Modules/PostTo/Pages/?u={url}";
				_width=586;
				_height=400;
				break;
			case "reddit":
				_link_template = "http://www.reddit.com/submit?url={url}";
				_width=586;
				_height=400;
				break;
			case "stumbleupon":
				_link_template = "http://www.stumbleupon.com/badge/?url={url}";
				_width=426;
				_height=365;
				break;
			case "tumblr":
				_link_template = "http://www.tumblr.com/share?v=3&u={url}&t={title}&s=";
				_url_to_share = _url_to_share.toLowerCase().replace("http://","").replace("https://","");
				_width=500;
				_height=440;
				break;
			case "mailto":
				_link_template = "mailto: ?subject={title}&body={url}";
				break;
		}
		
		
		
		if(_url_to_share=="")
		{
			_link = _link_template.replace("{url}",encodeURIComponent(location.href));
			_link = _link.replace("{title}",(_title_to_share=="")?encodeURIComponent(document.title):_title_to_share);			
		}
		else
		{
			_link = _link_template.replace("{url}",_url_to_share);
			_link = _link.replace("{title}",_title_to_share);						
		}
		_link = _link.replace("{referer}",encodeURIComponent(location.href));
		
		return {"link":_link,"width":_width,"height":_height};
	}
}

function _link_click(_e)
{
	var _div_ssh = _goParentNode(this,4);
	var _ssh = eval("__="+_div_ssh.id);
	_ssh._handle_link_click(this);
}

if(typeof(__KSSInits)!='undefined' && _exist(__KSSInits))
{	
	for(var i=0;i<__KSSInits.length;i++)
	{
		__KSSInits[i]();
	}
}

