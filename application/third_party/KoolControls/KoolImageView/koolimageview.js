/*
 * ImageZoom
 * Written by Nghiem Anh Tuan
 * Date: January 16, 2009
 */
function _obj(_oid)
{
	return document.getElementById(_oid);
}
function _exist(_theObj)
{
    return (_theObj!=null);
}
function _newNode(_sTag,_oParent)
{
    var _oNode = document.createElement(_sTag);
    _oParent.appendChild(_oNode);
    return _oNode;
}
function _replace(_old,_new,_str)
{
	return _str.replace(eval("/"+_old+"/g"),_new);
}
function _goParentNode(_theObj,_level)
{
	_level = (_exist(_level))?_level:1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.parentNode;
    return _theObj;
}
function _goFirstChild(_theObj,_level)
{
	_level = (_exist(_level))?_level:1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.firstChild;
    return _theObj;
}
function _goNextSibling(_theObj,_level)
{
	_level = (_exist(_level))?_level:1;	
    for(var i=0;i<_level;i++)
        _theObj = _theObj.nextSibling;
    return _theObj;
}
function _goPreviousSibling(_theObj,_level)
{
	_level = (_exist(_level))?_level:1;	
    for(var i=0;i<_level;i++)
        _theObj = _theObj.previousSibling;
    return _theObj;
}
/*
var _hLI4 = null;
*/
function _goPreviousSibling(_theObj,_level)
{
	_level = (_exist(_level))?_level:1;	
    for(var i=0;i<_level;i++)
        _theObj = _theObj.previousSibling;
    return _theObj;
}
function _setDisplay(_theObj,_val)
{
    _theObj.style.display=(_val)?"":"none";
}
function _getDisplay(_theObj)
{
    return (_theObj.style.display!="none");
}
function _setOpacity(_theObj,_val)
{
	_theObj.style.mozOpacity = _exist(_val)?_val/100:"";
	_theObj.style.filter = _exist(_val)?"alpha(opacity="+_val+")":"";;
	_theObj.style.opacity =  _exist(_val)?_val/100:"";
}
function _setHeight(_theObj,_val)
{
    _theObj.style.height=_exist(_val)?_val+"px":"";
}
function _getHeight(_theObj)
{
	return parseInt(_theObj.style.height);
}
function _setWidth(_theObj,_val)
{
    _theObj.style.width=_exist(_val)?_val+"px":"";
}
function _getWidth(_theObj)
{
	return parseInt(_theObj.style.width);
}
function _setTop(_theObj,_val)
{
    _theObj.style.top=_exist(_val)?_val+"px":"";
}
function _getTop(_theObj)
{
	return parseInt(_theObj.style.top);
}
function _setLeft(_theObj,_val)
{
    _theObj.style.left=_exist(_val)?_val+"px":"";
}
function _getLeft(_theObj)
{
	return parseInt(_theObj.style.left);
}
function _setzIndex(_theObj,_val)
{
    _theObj.style.zIndex=_exist(_val)?_val:null;
}
function _getzIndex(_theObj)
{
	return parseInt(_theObj.style.zIndex);
}
function _getClass(_theObj)
{
    return _theObj.className;
}
function _setClass(_theObj,_val)
{
    _theObj.className = _val;
}
function _index(_search,_original)
{
	return _original.indexOf(_search);
}
function _replaceClass(_search,_rep,_o)
{
	_setClass(_o,_getClass(_o).replace(_search,_rep));// Only the first
}
function _addClass(_theObj,_class)
{
	if (_theObj.className.indexOf(_class)<0)
	{
		var _listclass = _theObj.className.split(" ");	
		_listclass.push(_class);
		_theObj.className = _listclass.join(" ");
	}
}
function _removeClass(_theObj,_class)
{
	if (_theObj.className.indexOf(_class)>-1)
	{
		_replaceClass(_class,"",_theObj)
		var _listclass = _theObj.className.split(" ");
		_theObj.className = _listclass.join(" ");
	}
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
                return false;
            }
            else {
                var _tmp = function() { _fn.apply(_ob, [window.event]); }
                if (!_ob['ref'+_evType]) _ob['ref'+_evType] = [];
                else {
                    for (var _ref in _ob['ref'+_evType]) {
                        if (_ob['ref'+_evType][_ref]._fn === _fn) return false;
                    }
                }
                var _r = _ob.attachEvent('on'+_evType, _tmp);
                if (_r) _ob['ref'+_evType].push({_fn:_fn, _tmp:_tmp});
                return _r;
            }
        }
        else {
            return false;
        }
}
function _purge(d) {
    var a = d.attributes, i, l, n;
    if (a) {
        l = a.length;
        for (i = 0; i < l; i += 1) {
            n = a[i].name;
            if (typeof d[n] === 'function') {
                d[n] = null;
            }
        }
    }
    a = d.childNodes;
    if (a) {
        l = a.length;
        for (i = 0; i < l; i += 1) {
            _purge(d.childNodes[i]);
        }
    }
}
function _getWindowSize()
{
	var _width = 0, _height = 0;
	if( typeof( window.innerWidth ) == 'number' ) 
	{
		_width = window.innerWidth;
		_height = window.innerHeight;
	} 
	else if( document.documentElement && ( document.documentElement.clientWidth ||document.documentElement.clientHeight ) ) 
	{
		_width = document.documentElement.clientWidth;
		_height = document.documentElement.clientHeight;
	}
	else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) 
	{
		_width = document.body.clientWidth;
		_height = document.body.clientHeight;
	}
	return {_width:_width,_height:_height}; 
} 
function _getDocumentSize()
{
	var _width=(document.body.scrollWidth>document.documentElement.scrollWidth)?document.body.scrollWidth:document.documentElement.scrollWidth;
	var _height=(document.body.scrollHeight>document.documentElement.scrollHeight)?document.body.scrollHeight:document.documentElement.scrollHeight; 
	return {_width:_width,_height:_height};
}
function _getStyle(oElm, strCssRule)
{   
    var strValue = "";
    if(document.defaultView && document.defaultView.getComputedStyle)   
    {       
        var oStyle = document.defaultView.getComputedStyle(oElm, null);
        if(!oStyle) {
            try {
                if(oElm.style.display == "none") {
                    oElm.style.display = "";
                    oStyle = document.defaultView.getComputedStyle(oElm, null);
                    if(oStyle) {
                        strValue = oStyle.getPropertyValue(strCssRule);   
                    }
                    oElm.style.display = "none";
                }               
            }
            catch(ex) {}
        }
        if(oStyle && strValue == "") {
            strValue = oStyle.getPropertyValue(strCssRule);
        }
    }
    else if(oElm.currentStyle)
    {           
        try
        {               
            strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){return p1.toUpperCase();});           
            strValue = oElm.currentStyle[strCssRule];           
        }
        catch(ex){/*used to avoid an exception in IE 5.0*/}
    }
    return strValue;
};
function _advancePosTop(obj) 
{
    var pos = 0;
    var arrUsedElements = new Array();
    var oInitialObj = obj;    
    var _browser = _getBrowser();
    while(obj.offsetParent)
    {
        arrUsedElements.push(obj);   
        if ( this.isSafari && obj.nodeName=="TR")
            pos += obj.firstChild.offsetTop - obj.firstChild.scrollTop;
        else
            pos += obj.offsetTop - ((_browser=="opera" || obj.nodeName=="TR")?0:obj.scrollTop);                   
        obj = obj.offsetParent;
    }
    if(_browser=="safari")
        pos += document.body.offsetTop;
    if(_browser=="ie") 
        if(document.documentElement && document.documentElement.clientWidth)
            if(document.body.topMargin)
			{
				try
				{
					_styleTopMargin = _getStyle(document.body,"margin-top");
					if(_styleTopMargin=="auto") _styleTopMargin="0";					
					pos += parseInt((_styleTopMargin!="")?_styleTopMargin:document.body.topMargin);
				}
				catch(e){}				
			}
    if ((_browser=="safari" || _browser=="firefox" || _browser=="opera" ) && arrUsedElements.length>0)
    {
        obj = oInitialObj;
        while(obj.offsetParent)
        {
            var bAlreadyUsed = false;
            if (obj.nodeName.toLowerCase() == "div" && obj.style.position != "absolute" && obj.style.position != "relative" && obj.style.position != "fixed")
            {
                for(var j=0; j<arrUsedElements.length; j++) 
                {
                    if(arrUsedElements[j] == obj) 
                    {
                        bAlreadyUsed = true;
                        break;
                    }                
                }
                if(bAlreadyUsed == false)
                {
                    pos -= obj.scrollTop;
                }                
            }
            obj = obj.parentNode;
        }
    }
    return pos;
};
function _advancePosLeft(obj) 
{
    var pos = 0;
    var arrUsedElements = new Array();
    var oInitialObj = obj;    
	var _browser = _getBrowser();
    while(obj.offsetParent)
    {
        arrUsedElements.push(obj);   
        if ( this.isSafari && obj.nodeName=="TR")
            pos += obj.firstChild.offsetLeft - obj.firstChild.scrollLeft;
        else
            pos += obj.offsetLeft - ((_browser=="opera" || obj.nodeName=="TR")?0:obj.scrollLeft);                   
        obj = obj.offsetParent; 
    }
    if(_browser=="safari")
        pos += document.body.offsetLeft;
    if(_browser=="ie") 
        if(document.documentElement && document.documentElement.clientWidth)
            if(document.body.leftMargin)
			{
				try
				{
					_styleLeftMargin = _getStyle(document.body,"margin-left");
					if(_styleLeftMargin=="auto") _styleLeftMargin="0";
					pos += parseInt(((_styleLeftMargin!="")?_styleLeftMargin:document.body.leftMargin));
				}
				catch(e){}
			}
    if (( _browser=="safari" || _browser=="firefox" || _browser=="opera") && arrUsedElements.length>0)
    {
        obj = oInitialObj;
        while(obj.offsetParent)
        {
            var bAlreadyUsed = false;
            if (obj.nodeName.toLowerCase() == "div" && obj.style.position != "absolute" && obj.style.position != "relative" && obj.style.position != "fixed")
            {
                for(var j=0; j<arrUsedElements.length; j++) 
                {
                    if(arrUsedElements[j] == obj) 
                    {
                        bAlreadyUsed = true;
                        break;
                    }                
                }
                if(bAlreadyUsed == false)
                {
                    pos -= obj.scrollLeft;
                }                
            }
            obj = obj.parentNode;
        }
    }
    return pos;
};
function _getDocumentScrollLeft()
{
	return (document.body.scrollLeft + document.documentElement.scrollLeft);
}
function _getDocumentScrollTop()
{
	return (document.body.scrollTop + document.documentElement.scrollTop);
}
function _linearTween(_t, _b, _c, _d) 
{
	return _c*_t/_d + _b;
}
function _easeIn(_t, _b, _c, _d) 
{
	return _c*(_t/=_d)*_t + _b;
}
function _easeInOut(_t, _b, _c, _d) 
{
	if ((_t/=_d/2) < 1) return _c/2*_t*_t + _b;
	return -_c/2 * ((--_t)*(_t-2) - 1) + _b;
}
function _getBrowser()
{
	var _agent=navigator.userAgent.toLowerCase(); 
	if(_index("opera",_agent)!=-1)
	{
		return "opera";
	}else if (_index("firefox",_agent)!=-1)
	{
		return "firefox";
	}else if (_index("safari",_agent)!=-1)
	{
		return "safari";
	}
	else if ((_index("msie",_agent)!=-1) && (_index("opera",_agent)==-1))
	{
		return "ie";
	}
	else
	{
		return "firefox";
	}	
}
/*---------------------------------------------------------------------*/
/*---------------------------------------------------------------------*/
function KoolImageView(_id,_bigImageUrl,_showLoading,_background,_effect,_openTime,_frameNumber,_position,_relativeLeft,_relativeTop)
{	
	this._id = _id;
	this._bigImageUrl = _bigImageUrl;
	this._isLoading = _showLoading;
	this._background = _background;
	this._zoomDuration = _openTime;//ms
	this._steps = _frameNumber;
	this._effect = _effect;
	this._position = _position;
	this._relativeLeft = _relativeLeft;
	this._relativeTop = _relativeTop;	
	this._status = "closed";
	this._eventhandles = new Array();
	this._init();
}
KoolImageView.prototype = 
{
	_init:function()
	{
		var _div_main = _goParentNode(_obj(this._id+".zoompanel"));
		document.body.insertBefore(_div_main,document.body.firstChild);
		var _small_image = _obj(this._id);
		var _big_image = _obj(this._id+".bigimage");
		_addEvent(_small_image,"click",_small_image_click,false);
		_addEvent(_small_image,"mouseover",_small_image_mouseover,false);
		_addEvent(_small_image,"mouseout",_small_image_mouseout,false);
		_addEvent(_big_image,"load",_big_image_load,false);
		_addEvent(_big_image,"click",_big_image_click,false);
		_addEvent(window,"resize",eval("__=function(_e){"+this._id+".WRS(_e);}"),false);
		if (this._background>0)
		{
			var _div_background = _obj(this._id+".background");
			_addEvent(_div_background,"click",_background_click,false);
		}
		var _zoompanel = _obj(this._id+".zoompanel");
		_addEvent(_zoompanel,"mouseover",_zoompanel_mouseover,false);
		_addEvent(_zoompanel,"mouseout",_zoompanel_mouseout,false);
		var _closebuttons = _getElements("a","CloseButton",_zoompanel);
		for(var i in _closebuttons)
		if(typeof _closebuttons[i]!="function") //Mootools
			_addEvent(_closebuttons[i],"click",_close_button_click,false);
	},
	open:function()
	{
		if (!this._handleEvent("OnBeforeOpen",null)) return;
		this._status="loading";
		if (this._isLoading)
		{
			this._showLoading(1);
		}
		var _big_image = _obj(this._id+".bigimage");
		_big_image.src = this._bigImageUrl;		
	},
	_zoomin_prepare:function()
	{
		var _zoompanel = _obj(this._id+".zoompanel");
		var _big_image = _obj(this._id+".bigimage");
		var _doc_scroll_left = _getDocumentScrollLeft();
		var _doc_scroll_top = _getDocumentScrollTop();
		var _winsize = _getWindowSize();
		_addClass(_zoompanel,"kivTransparent");
		_setTop(_zoompanel,0);
		_setLeft(_zoompanel,0);		
		_setDisplay(_zoompanel,1);
		var _zoompanel_width = _zoompanel.offsetWidth;
		var _zoompanel_height = _zoompanel.offsetHeight;
		/* Check if zoompanel is larger than viewport. 
		 * If it is resize to fit screen.
		 * Resize the image accordingly
		 * 
		 */
		var _small_image = _obj(this._id);
		var _small_image_left = _advancePosLeft(_small_image);
		var _small_image_top = _advancePosTop(_small_image);
		var _small_image_width = _small_image.offsetWidth;
		var _small_image_height = _small_image.offsetHeight;		
		switch(this._position)
		{
			case "RELATIVE":
				var _zoompanel_left = _small_image_left + this._relativeLeft;
				var _zoompanel_top = _small_image_top + this._relativeTop;				
				break;
			case "IMAGE_CENTER":
				var _zoompanel_left = _small_image_left - (_zoompanel_width - _small_image_width)/2;
				var _zoompanel_top =  _small_image_top - (_zoompanel_height - _small_image_height)/2;
				break;
			case "SCREEN_CENTER":
			default:
				var _zoompanel_left = (_winsize._width - _zoompanel_width)/2 + _doc_scroll_left;
				var _zoompanel_top = (_winsize._height - _zoompanel_height)/2 + _doc_scroll_top;				
				break;
		}		
		_setLeft(_zoompanel,_zoompanel_left);
		_setTop(_zoompanel,_zoompanel_top);			
		this._image_relative_postion = this._get_relative_pos_between_image_and_zoompanel();
		this._info_prepare = new Object();
		this._info_prepare._big_image_width = _big_image.width;
		this._info_prepare._big_image_height = _big_image.height;
		this._info_prepare._zoompanel_width = _zoompanel_width;
		this._info_prepare._zoompanel_height = _zoompanel_height;
		_setDisplay(_zoompanel,0);
		_removeClass(_zoompanel,"kivTransparent");
		if (_getBrowser() != "safari" && _getBrowser() != "ie")
		{
			var _opacity_render_elements = _getElements("span","OpacityRender",_zoompanel);
			for(var i in _opacity_render_elements)
			if(typeof _opacity_render_elements[i]!="function") //Mootools
			{
				_setOpacity(_opacity_render_elements[i],0);
			}			
		}
	},
	_get_relative_pos_between_image_and_zoompanel:function()
	{
		var _big_image = _obj(this._id+".bigimage");
		var _zoompanel = _obj(this._id+".zoompanel");
		var _top = 0;
		var _left = 0;
		var _parent = _big_image;
		while(_parent!=_zoompanel)
		{
			_top+=_parent.offsetTop;
			_left+=_parent.offsetLeft;
			_parent = _parent.offsetParent;
		}
		return {_left:_left,_top:_top};
	},
	_zoomin_end:function()
	{
		var _zoompanel = _obj(this._id+".zoompanel");
		_setOpacity(_zoompanel,null);//Clear oapcity if previously used by effect
		_setDisplay(_zoompanel,1);
		if (this._background>0) 
		{
			var _div_background = _obj(this._id+".background");
			_setOpacity(_div_background,this._background);			
			this._show_background(1);			
		}
		this._status="opened";
		this._handleEvent("OnOpen",null);
		if (_getBrowser() != "safari" && _getBrowser() != "ie")
		{
			this.ORD(0, 1);
		}
	},
	close:function()
	{
		if (!this._handleEvent("OnBeforeClose",null)) return;
		this._status="closing";
		this.DZM(this._steps,-1);		
	},
	getStatus:function()
	{
		return this._status;
	},
	_zoomout_end:function()
	{
		var _zoompanel = _obj(this._id+".zoompanel");
		var _big_image = _obj(this._id+".bigimage");
		_setDisplay(_zoompanel,0);
		_big_image.src = "";
		/*Fix for opera: Opera does not put onload event if the image has been loaded with image. */
		if(_getBrowser()=="opera")
		{
			var _parent = _goParentNode(_big_image);
			_purge(_big_image);
			var _new_big_image = _newNode("img",_parent);
			_parent.insertBefore(_new_big_image,_big_image);
			_new_big_image.id = this._id+".bigimage";
			_setClass(_new_big_image,"kivBigImage");
			_addEvent(_new_big_image,"load",_big_image_load,false);
			_addEvent(_new_big_image,"click",_big_image_click,false);
			_parent.removeChild(_big_image);
		}
		/*End Fix for opera -------------------------------------------------------*/
		if(this._background>0)
		{
			var _div_background = _obj(this._id+".background");
			_setOpacity(_div_background,this._background);			
			this._show_background(0);	
		}
		this._status="closed";
		this._handleEvent("OnClose",null);
	},
	DZM:function(_step,_direction)
	{
		if (_step<=0 && _direction<0)
		{
			this._render_effect(_step,1);
			this._zoomout_end();
			return;
		}
		if (_step>=this._steps && _direction>0)
		{
			this._render_effect(_step,1);
			this._zoomin_end();
			return;
		}
		this._render_effect(_step,0);
		setTimeout(this._id+".DZM("+(_step+_direction)+","+_direction+")",this._zoomDuration/this._steps);
	},
	_render_effect:function(_step,_clear_effect)
	{
		switch(this._effect)
		{
			case "fading":
				var _zoompanel = _obj(this._id+".zoompanel");
				var _value = _linearTween(_step,0,100-0,this._steps);
				_setOpacity(_zoompanel,_value);
				_setDisplay(_zoompanel,1);
				if(this._background>0)
				{
					var _value = _linearTween(_step,0,this._background-0,this._steps);
					var _div_background = _obj(this._id+".background");
					_setOpacity(_div_background,_value);
					this._show_background(1);
				}				
				break;
			case "zooming":
				var _effectpanel = _obj(this._id +".effectpanel");
				var _effectimage = _obj(this._id +".effectimage");
				var _zoompanel = _obj(this._id +".zoompanel");
				var _small_image = _obj(this._id);
				var _big_image = _obj(this._id+".bigimage");
				if (_clear_effect)
				{
					_setDisplay(_effectpanel,0);
					_setDisplay(_effectimage,0);
					_effectimage.src = "";
					if (_step==0)
					{
						_setOpacity(_small_image,null);
					}
					return;
				}
				if (_step==0)
				{
					_effectimage.src = this._bigImageUrl;
					_setOpacity(_small_image,0);
				}
				if (_step==this._steps)
				{
					_effectimage.src = this._bigImageUrl;
					_setDisplay(_zoompanel,0);
					if (this._background>0)
					{
						var _div_background = _obj(this._id + ".background");
						_setDisplay(_div_background,0);						
					}
				}
				var _small_image_left = _advancePosLeft(_small_image);
				var _small_image_top = _advancePosTop(_small_image);
				var _small_image_width = _small_image.width;
				var _small_image_height = _small_image.height;
				var _zoompanel_left = _getLeft(_zoompanel);
				var _zoompanel_top = _getTop(_zoompanel);
				var _zoompanel_width = this._info_prepare._zoompanel_width;
				var _zoompanel_height = this._info_prepare._zoompanel_height;
				var _big_image_width = this._info_prepare._big_image_width;
				var _big_image_height = this._info_prepare._big_image_height;
				var _effectpanel_start_width = (_small_image_width/_big_image_width)*_zoompanel_width;
				var _effectpanel_start_height = (_small_image_height/_big_image_height)*_zoompanel_height;
				var _effectpanel_width = _easeInOut(_step,_effectpanel_start_width,_zoompanel_width-_effectpanel_start_width,this._steps);				
				var _effectpanel_height = _easeInOut(_step,_effectpanel_start_height,_zoompanel_height-_effectpanel_start_height,this._steps);				
				var _relative_position_start_left = (_small_image_width/_big_image_width) * this._image_relative_postion._left;
				var _relative_position_start_top = (_small_image_width/_big_image_width) * this._image_relative_postion._top;
				var _effectpanel_start_left = _small_image_left - _relative_position_start_left;
				var _effectpanel_start_top = _small_image_top - _relative_position_start_top;
				var _effectpanel_left = _easeInOut(_step,_effectpanel_start_left,_zoompanel_left-_effectpanel_start_left,this._steps);				
				var _effectpanel_top = _easeInOut(_step,_effectpanel_start_top,_zoompanel_top-_effectpanel_start_top,this._steps);				
				var _effectimage_end_left = _zoompanel_left+this._image_relative_postion._left;
				var _effectimage_end_top = _zoompanel_top+this._image_relative_postion._top;
				var _effectimage_left = _easeInOut(_step,_small_image_left,_effectimage_end_left-_small_image_left,this._steps);
				var _effectimage_top = _easeInOut(_step,_small_image_top,_effectimage_end_top-_small_image_top,this._steps);
				var _effectimage_width = _easeInOut(_step,_small_image_width,_big_image_width-_small_image_width,this._steps);
				var _effectimage_height = _easeInOut(_step,_small_image_height,_big_image_height-_small_image_height,this._steps);
				_setLeft(_effectpanel,_effectpanel_left);
				_setTop(_effectpanel,_effectpanel_top);
				_setWidth(_effectpanel,_effectpanel_width);
				_setHeight(_effectpanel,_effectpanel_height);
				_setDisplay(_effectpanel,1);
				_setLeft(_effectimage,_effectimage_left);
				_setTop(_effectimage,_effectimage_top);
				_setWidth(_effectimage,_effectimage_width);
				_setHeight(_effectimage,_effectimage_height);
				_setDisplay(_effectimage,1);
				break;			
		}
	},
	ORD:function(_step,_direction)
	{
		var _zoompanel = _obj(this._id+".zoompanel");
		var _opacity_render_elements = _getElements("span","OpacityRender",_zoompanel);
		if(_opacity_render_elements.length>0)
		{
			if (_step<=0 && _direction<0)
			{
				return;
			}
			if (_step>=this._steps && _direction>0)
			{
				for(var i in _opacity_render_elements)
				if(typeof _opacity_render_elements[i]!="function") //Mootools
				{
					_setOpacity(_opacity_render_elements[i],null);
				}				
				return;
			}
			var _value = _linearTween(_step,0,100-0,this._steps);
			for(var i in _opacity_render_elements)
			if(typeof _opacity_render_elements[i]!="function") //Mootools
			{
				_setOpacity(_opacity_render_elements[i],_value);
			}
			setTimeout(this._id+".ORD("+(_step+_direction)+","+_direction+")",this._zoomDuration/this._steps);
		}
	},		
	registerEvent:function(_name,_handle)
	{
		this._eventhandles[_name] = _handle;
	},
	_handleEvent:function(_name,_arg)
	{
		return (_exist(this._eventhandles[_name]))?this._eventhandles[_name](this,_arg):true;
	},	
	_showLoading:function(_bool)
	{
		var _loading = _obj(this._id+".loading");
		if (_bool)
		{
			var _small_image = _obj(this._id);
			var _small_image_left = _advancePosLeft(_small_image);
			var _small_image_top = _advancePosTop(_small_image);
			var _small_image_width = _small_image.offsetWidth;
			var _small_image_height = _small_image.offsetHeight;
			if(isNaN(_small_image_height)||isNaN(_small_image_width))
			{
				return;
			}
			_addClass(_loading,"kivTransparent");
			_setDisplay(_loading,1);
			_setLeft(_loading,0);
			_setTop(_loading,0);
			var _loading_width = _loading.offsetWidth;
			var _loading_height = _loading.offsetHeight;
			var _loading_left = (_small_image_width - _loading_width)/2 + _small_image_left;
			var _loading_top = (_small_image_height - _loading_height)/2 + _small_image_top;
			_setLeft(_loading,_loading_left);
			_setTop(_loading,_loading_top);
			_removeClass(_loading,"kivTransparent");
		}
		else
		{
			_setDisplay(_loading,0);
		}
	},
	_handle_small_image_click:function(_e)
	{
		if (!this._handleEvent("OnBeforeImageClick",null)) return;
		if (this._status=="closed")
		{
			this.open();		
		}
		this._handleEvent("OnImageClick",null);
	},
	_handle_small_image_mouseover:function(_e)
	{
		this._handleEvent("OnImageMouseOver",null);
	},
	_handle_small_image_mouseout:function(_e)
	{
		this._handleEvent("OnImageMouseOut",null);
	},
	_handle_big_image_load_done:function()
	{
		if (this._isLoading)
		{
			this._showLoading(0);
		}
		this._zoomin_prepare();
		if (this._background>0) 
		{
			var _div_background = _obj(this._id+".background");
			_setOpacity(_div_background,this._background);			
			this._show_background(1);			
		}		
		this._status = "opening";
		this.DZM(0,1);
	},
	_handle_big_image_click:function()
	{
		if (!this._handleEvent("OnBeforeBigImageClick",null)) return;
		this.close();
		this._handleEvent("OnBigImageClick",null);
	},
	_handle_background_click:function()
	{
		if (!this._handleEvent("OnBeforeBackgroundClick",null)) return;
		this.close();
		this._handleEvent("OnBackgroundClick",null);
	},
	_handle_close_button_click:function(_e)
	{
		if (!this._handleEvent("OnBeforeCloseButtonClick",null)) return;
		this.close();
		this._handleEvent("OnCloseButtonClick",null);
	},
	_handle_zoompanel_mouseover:function(_e)
	{
		this._handleEvent("OnZoomPanelMouseOver",null);
	},
	_handle_zoompanel_mouseout:function(_e)
	{
		this._handleEvent("OnZoomPanelMouseOut",null);		
	},	
	_show_background:function(_bool)
	{
		if (this._background>0)
		{
			var _div_background = _obj(this._id+".background");
			if (_bool)
			{
				var _docsize = _getDocumentSize();
				var _winsize = _getWindowSize();
				var _width = (_docsize._width>_winsize._width)?_docsize._width:_winsize._width;
				var _height = (_docsize._height>_winsize._height)?_docsize._height:_winsize._height;						
				_setWidth(_div_background, _width);
				_setHeight(_div_background, _height);
				_setTop(_div_background, 0);
				_setLeft(_div_background, 0);
				_setDisplay(_div_background, 1);
			}
			else
			{	
				_setDisplay(_div_background,0);
			}
		}
	},
	WRS:function(_e)
	{
		if(this._background>0 && this._status=="opened")
		{
			var _div_background = _obj(this._id+".background");
			_setDisplay(_div_background, 0);
			this._show_background(1);//This will help to recalculate background
		}
	}
}
function _small_image_click(_e)
{
	var _kiv = eval("__="+this.id);
	_kiv._handle_small_image_click(_e);
}
function _small_image_mouseout(_e)
{
	var _kiv = eval("__="+this.id);
	_kiv._handle_small_image_mouseout();	
}
function _small_image_mouseover(_e)
{
	var _kiv = eval("__="+this.id);
	_kiv._handle_small_image_mouseover();	
}
function _big_image_click(_e)
{
	var _kiv = eval("__="+_replace(".bigimage","",this.id));
	_kiv._handle_big_image_click();
}
function _big_image_load(_e)
{
	var _kiv = eval("__="+_replace(".bigimage","",this.id));
	_kiv._handle_big_image_load_done();
}
function _background_click(_e)
{
	var _kiv = eval("__="+_replace(".background","",this.id));
	_kiv._handle_background_click();	
}
function _zoompanel_mouseout(_e)
{
	var _kiv = eval("__="+_replace(".zoompanel","",this.id));
	_kiv._handle_zoompanel_mouseout();	
}
function _zoompanel_mouseover(_e)
{
	var _kiv = eval("__="+_replace(".zoompanel","",this.id));
	_kiv._handle_zoompanel_mouseover();	
}
function _close_button_click(_e)
{
	var _parent = _goParentNode(this);
	while(_index(".zoompanel",_parent.id)<0)
	{
		_parent = _goParentNode(_parent);
	}
	var _kiv = eval("__="+_replace(".zoompanel","",_parent.id));
	_kiv._handle_close_button_click(_e);
}
if(typeof(__KIVInits)!='undefined' && _exist(__KIVInits))
{	
	for(var i=0;i<__KIVInits.length;i++)
	{
		__KIVInits[i]();
	}
}
