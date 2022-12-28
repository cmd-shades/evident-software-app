/**
 * @author Nghiem Anh Tuan
 */
function _exist(_theObj)
{
    return (_theObj!=null);
}
function _obj(_id)
{
	return document.getElementById(_id);
}
function _newNode(_sTag,_oParent)
{
    var _oNode = document.createElement(_sTag);
    _oParent.appendChild(_oNode);
    return _oNode;
}
function _goFirstChild(_theObj,_level)
{
	if(!_exist(_level)) _level=1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.firstChild;
    return _theObj;
}
function _goNextSibling(_theObj,_level)
{
	if(!_exist(_level)) _level=1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.nextSibling;
    return _theObj;
}
function _goParentNode(_theObj,_level)
{
	if(!_exist(_level)) _level=1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.parentNode;
    return _theObj;
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
function _setHeight(_theObj,_val)
{
    _theObj.style.height=_val+"px";
}
function _setWidth(_theObj,_val)
{
    _theObj.style.width=_val+"px";
}
function _getWidth(_theObj)
{
	return parseInt(_theObj.style.width);
}
function _getHeight(_theObj)
{
	return parseInt(_theObj.style.height);
}
function _setzIndex(_theObj,_val)
{
    _theObj.style.zIndex=_exist(_val)?_val:null;
}
function _getzIndex(_theObj)
{
	if(_theObj.style.zIndex!=null)
		return parseInt(_theObj.style.zIndex);
	else
		return 0;
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
function _setDisplay(_theObj,_val)
{
    _theObj.style.display=(_val)?"":"none";
}
function _getDisplay(_theObj)
{
    return (_theObj.style.display!="none");
}
function _getClass(_theObj)
{
    return _theObj.className;
}
function _setClass(_theObj,_val)
{
    _theObj.className = _val;
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
function _removeEvent(_ob, _evType, _fn, _useCapture) 
{
	if (_ob.removeEventListener) 
	{
		_ob.removeEventListener(_evType, _fn, _useCapture);
		return true;
	}
	else if (_ob.detachEvent) {
		if (_ob['ref'+_evType]) {
			for (var _ref in _ob['ref'+_evType]) {
				if (_ob['ref'+_evType][_ref]._fn === _fn) {
					_ob.detachEvent('on'+_evType, _ob['ref'+_evType][_ref]._tmp);
					_ob['ref'+_evType][_ref]._fn = null;
					_ob['ref'+_evType][_ref]._tmp = null;
					delete _ob['ref'+_evType][_ref];
					return true;
				}
			}
		}
		return false;
	}
	else {
		return false;
	}
}
function _stopPropagation(_e)
{
	if(_e.stopPropagation)
		_e.stopPropagation();
	else
		_e.cancelBubble = true;
}
function _preventDefaut(_e)
{
	if (_e.preventDefault)
		_e.preventDefault();
	else
		event.returnValue = false;
	return false;
}
function _purge(d) {
    var a = d.attributes, i, l, n;
    if (a) {
        l = a.length;
        for (i = 0; i < l; i += 1) {
			if (a[i]) n = a[i].name;
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
function _json2string(_o)
{
	var _res="";
	var _isarray = (_o instanceof Array);
	for (var _name in _o)
	{
		switch(typeof(_o[_name]))
		{
			case "string":
					_res+=(_isarray)?"\""+ _o[_name]+"\",":"\""+_name+"\":\""+ _o[_name]+"\",";
				break;
			case "number":
					_res+=(_isarray)?_o[_name]+",":"\""+_name+"\":"+_o[_name]+",";
				break;
			case "boolean":
					_res+=(_isarray)?(_o[_name]?"true":"false")+",":"\""+_name+"\":"+(_o[_name]?"true":"false")+",";
				break;			
			case "object":
					_res+=(_isarray)?_json2string(_o[_name])+",":"\""+_name+"\":"+_json2string(_o[_name])+",";				
				break;								
		}
	}
	if (_res.length>0)
		_res = _res.substring(0,_res.length-1);
	_res=(_isarray)?"["+_res+"]":"{"+_res+"}";
	if (_res=="{}") _res="null";
	return _res;
}
function _index(_search,_original)
{
	return _original.indexOf(_search);
}
function _mouseXY(_ev){
	if(_ev.pageX || _ev.pageY){
		return {_x:_ev.pageX, _y:_ev.pageY};
	}
	else if (_ev.clientX || _ev.clientY )
	{
		return {
			_x:_ev.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft),
			_y:_ev.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop)
		};		
	}
	else
	{
		return {_x:null,_y:null};
	}
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
	else if ((_index("msie 6",_agent)!=-1) && (_index("msie 7",_agent)==-1) && (_index("msie 8",_agent)==-1) && (_index("opera",_agent)==-1))
	{
		return "ie6";
	}
	else if ((_index("msie 7",_agent)!=-1) && (_index("opera",_agent)==-1))
	{
		return "ie7";
	}
	else if ((_index("msie 8",_agent)!=-1) && (_index("opera",_agent)==-1))
	{
		return "ie8";
	}
	else if ((_index("msie",_agent)!=-1) && (_index("opera",_agent)==-1))
	{
		return "ie";
	}
	else if (_index("chrome",_agent)!=-1)
	{
		return "chrome";
	}
	else
	{
		return "firefox";
	}	
}
function _get_animation_function(_name)
{
	switch(_name.toLowerCase())
	{
		case "linear":
			return function(t, b, c, d)
			{
				return c*t/d + b;					
			};
			break;
		case "easein":
			return function(t, b, c, d)
			{
				return c*(t/=d)*t + b;					
			};
			break;
		case "easeout":
			return function(t, b, c, d)
			{
				return -c *(t/=d)*(t-2) + b;					
			};
			break;
		case "easeboth":
			return function(t, b, c, d)
			{
				if ((t/=d/2) < 1) return c/2*t*t + b;
				return -c/2 * ((--t)*(t-2) - 1) + b;					
			};
			break;
		case "easeinstrong":
			return function(t, b, c, d)
			{
				return c*(t/=d)*t*t*t + b;					
			};
			break;
		case "easeoutstrong":
			return function(t, b, c, d)
			{
				return -c * ((t=t/d-1)*t*t*t - 1) + b;					
			};
			break;
		case "easebothstrong":
			return function(t, b, c, d)
			{
				if ((t/=d/2) < 1) {return c/2*t*t*t*t + b;}
    			return -c/2 * ((t-=2)*t*t*t - 2) + b;					
			};
			break;
		case "bouncein":
			return function(t, b, c, d)
			{
				return c - (_get_animation_function("bounceout"))(d-t, 0, c, d) + b;					
			};
			break;
		case "bounceout":
			return function(t, b, c, d)
			{
		    	if ((t/=d) < (1/2.75)) {
		    		return c*(7.5625*t*t) + b;
		    	} else if (t < (2/2.75)) {
		    		return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		    	} else if (t < (2.5/2.75)) {
		    		return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		    	}
		        return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;				
			};
			break;
		case "bounceboth":
			return function(t, b, c, d)
			{
		    	if (t < d/2) {
            		return (_get_animation_function("bouncein"))(t*2, 0, c, d) * .5 + b;
    		    }
		    	return (_get_animation_function("bounceout"))(t*2-d, 0, c, d) * .5 + c*.5 + b;				
			};
			break;
		case "elasticin":
			return function(t, b, c, d, a, p)
			{
		    	if (t == 0) {
		            return b;
		        }
		        if ( (t /= d) == 1 ) {
		            return b+c;
		        }
		        if (!p) {
		            p=d*.3;
		        }
		    	if (!a || a < Math.abs(c)) {
		            a = c; 
		            var s = p/4;
		        }
		    	else {
		            var s = p/(2*Math.PI) * Math.asin (c/a);
		        }
		    	return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;				
			};
			break;
		case "elasticout":
			return function(t, b, c, d, a, p)
			{
		    	if (t == 0) {
		            return b;
		        }
		        if ( (t /= d) == 1 ) {
		            return b+c;
		        }
		        if (!p) {
		            p=d*.3;
		        }
		    	if (!a || a < Math.abs(c)) {
		            a = c;
		            var s = p / 4;
		        }
		    	else {
		            var s = p/(2*Math.PI) * Math.asin (c/a);
		        }
		    	return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
			};
			break;
		case "elasticboth":
			return function(t, b, c, d, a, p)
			{
		    	if (t == 0) {
		            return b;
		        }
		        if ( (t /= d/2) == 2 ) {
		            return b+c;
		        }
		        if (!p) {
		            p = d*(.3*1.5);
		        }
		    	if ( !a || a < Math.abs(c) ) {
		            a = c; 
		            var s = p/4;
		        }
		    	else {
		            var s = p/(2*Math.PI) * Math.asin (c/a);
		        }
		    	if (t < 1) {
		            return -.5*(a*Math.pow(2,10*(t-=1)) * 
		                    Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		        }
		    	return a*Math.pow(2,-10*(t-=1)) * 
		                Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
			};
			break;
		case "backin":
			return function(t, b, c, d, s)
			{
		    	if (typeof s == 'undefined') {
		            s = 1.70158;
		        }
		    	return c*(t/=d)*t*((s+1)*t - s) + b;
			};
			break;
		case "backout":
			return function(t, b, c, d)
			{
		    	if (typeof s == 'undefined') {
		            s = 1.70158;
		        }
		    	return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
			};
			break;
		case "backboth":
			return function(t, b, c, d, s)
			{
		    	if (typeof s == 'undefined') {
		            s = 1.70158; 
		        }
		    	if ((t /= d/2 ) < 1) {
		            return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		        }
		    	return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
			};
			break;
		case "none":
		default:
			return function(t, b, c, d)
			{
				return c+b;					
			};
			break;				
	}
}
/*--------------------------------------------------------*/
function KoolMenuItem(_id)
{
	this._id = _id;
}
KoolMenuItem.prototype = 
{
	_loadSetting:function()
	{
		return eval("__="+_obj(this._id+"_setting").value);
	},
	_saveSetting:function(_setting)
	{
		var _input = _obj(this._id+"_setting");
		_input.value = _json2string(_setting);
	},
	_getMenu:function()
	{
		var _this = _obj(this._id);
		var _div_menu = _goParentNode(_this);
		while(_div_menu.nodeName!="DIV" || _index("KMU",_getClass(_div_menu))<0)
		{
			_div_menu = _goParentNode(_div_menu);
			if (_div_menu.nodeName == "BODY") return null;
		}
		return eval(_div_menu.id);		
	},
	enable:function(_bool)
	{
		var _link = _goFirstChild(_ovb(this._id));
		if(_bool)
		{
			_removeClass(_link,"kmuDisabled");
		}
		else
		{
			_addClass(_link,"kmuDisabled");
		}
	},
	select:function()
	{
		var _menu = this._getMenu();
		if(!_menu._handleEvent("OnBeforeItemSelect",{"ItemId":this._id})) return;
		_menu._save_select(this._id);
		_menu_setting = _menu._loadSetting();
		_menu._handleEvent("OnItemSelect",{"ItemId":this._id});
		if(_menu_setting["PostBackOnSelect"])
		{
			_menu._doPostBack();
		}
	},
	isEnabled:function()
	{
		var _link = _goFirstChild(_obj(this._id));
		return (_index("Disabled",_getClass(_link))<0);
	},
	_usedTemplate:function()
	{
		var _this = _obj(this._id);
		return (_index("Template",_getClass(_this))>0);		
	},
	_prepare_first_expanding:function()
	{
		var _this = _obj(this._id);
		var _group = _obj(this._id+"_group");		
		var _slide = _goParentNode(_group);
		if(_index("kmuPrem",_getClass(_slide))>0)
		{
			if(_index("Vertical",_getClass(_group))>0)
			{
				var _max_item_width = 0;
				for(var i=0;i<_group.childNodes.length;i++)
				{
					var _node = _group.childNodes[i];
					if (_node.nodeName=="LI")
					{
						if(_index("Separator",_getClass(_node))<0)
						{
							var _node_link = _goFirstChild(_node);
							if (_max_item_width<_node_link.offsetWidth)
							{
								_max_item_width = _node_link.offsetWidth;
							}
						}
					}
				}
				_max_item_width+=5;
				for(var i=0;i<_group.childNodes.length;i++)
				{
					var _node = _group.childNodes[i];
					if (_node.nodeName=="LI")
					{
						var _node_link = _goFirstChild(_node);
						_setWidth(_node_link, _max_item_width);
					}
				}				
			}
			var _width = _getWidth(_slide);
			var _height = _getHeight(_slide);
			if(isNaN(_width))
			{
				_width = _slide.offsetWidth;
				_setWidth(_slide,_width);
			}
			if(isNaN(_height))
			{
				_height = _slide.offsetHeight;
				_setHeight(_slide,_height);
			}
			if(_getBrowser()=="ie6")
			{
				var _zindex = _getzIndex(_slide)-1;
				var _dummy_div = document.createElement("div");
				_dummy_div.innerHTML = "<iframe src=\"javascript:'';\" tabindex='-1' style='position:absolute;width:"+_width+"px;height:"+_height+"px;display:none;border:0px;z-index:"+_zindex+";filter:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)'>Your browser does not support inline iframe.</frame>";
				var _iframe = _goFirstChild(_dummy_div);
				_this.insertBefore(_iframe,_slide);			
			}
			_removeClass(_slide,"kmuPrem");			
		}
	},
	_register_expand:function()
	{
		this._remove_collapse_expand_registering();
		var _menu = this._getMenu();
		var _menu_setting = _menu._loadSetting();
		var _this_setting = this._loadSetting();
		_this_setting["TimeoutID"] = setTimeout("(new KoolMenuItem('"+this._id+"')).expand();",_menu_setting["ExpandDelay"]);
		this._saveSetting(_this_setting);
	},	
	expand:function()
	{
		if(this.hasChild() && !this.isExpanded())
		{
			var _menu = this._getMenu();
			var _menu_setting = _menu._loadSetting();
			if(_menu_setting["ClickToOpen"])
			{
				var _ul_parent = _goParentNode(_obj(this._id));
				if(_index("RootGroup",_getClass(_ul_parent))>0)
				{
					_menu.collapse();
					_menu._setActive(1);
				}
			}
			if(!_menu._handleEvent("OnBeforeItemExpand",{"ItemId":this._id})) return;
			var _this =_obj(this._id);
			var _menu_setting = _menu._loadSetting();
			var _this_setting = this._loadSetting();
			var _expand_animation = _menu_setting["ExpandAnimation"];
			var _animation_type = _expand_animation["Type"];
			var _animation_duration = _expand_animation["Duration"];
			var _time_per_step = 20;
			var _animation_step = _animation_duration/_time_per_step;
			_this_setting["TimeoutID"] = null;
			this._saveSetting(_this_setting);
			var _slide = _goParentNode(_obj(this._id+"_group"));
			_setDisplay(_slide,1);
			_slide.style.overflow = "visible";//Need to be visible in order for IE to recognize correct width.
			this._prepare_first_expanding();
			_slide.style.overflow = "hidden";
			_setzIndex(_this,1);
			var _slide_width = _getWidth(_slide);
			var _slide_height = _getHeight(_slide);
			var _this_width = _this.offsetWidth;
			var _this_height = _this.offsetHeight;
			var _offsetX = _this_setting["OffsetX"];
			var _offsetY = _this_setting["OffsetY"];
			var _direction = _this_setting["ExpandDirection"].toLowerCase();
			if (_direction=="auto")
			{
				var _parent_ul = _goParentNode(_this);
				if(_index("kmuVertical",_getClass(_parent_ul))<0)
				{
					_direction = "down";
				}
				else
				{
					_direction = "right";
				}
			}
			switch(_direction)
			{
				case "up":				
					_setTop(_slide,_offsetY-_slide_height);
					_setLeft(_slide,_offsetX);				
					break;
				case "down":
					_setTop(_slide,_offsetY + _this_height);
					_setLeft(_slide,_offsetX);				
					break;
				case "left":
					_setTop(_slide,_offsetY);
					_setLeft(_slide,_offsetX - _slide_width);				
					break;
				case "right":
					_setTop(_slide,_offsetY);
					_setLeft(_slide,_offsetX + _this_width);				
					break;
			}
			if(_getBrowser()=="ie6")
			{
				var _iframe = _slide.previousSibling;
				_setTop(_iframe,_getTop(_slide));
				_setLeft(_iframe,_getLeft(_slide));
				_setDisplay(_iframe,1);
			}
			var _this_link = _goFirstChild(_this);
			_addClass(_this_link,"kmuExpanded");
			if (_exist(_this_setting["AnimTimeoutID"]))
			{
				clearTimeout(_this_setting["AnimTimeoutID"]);
			}			
			this._handle_animate({"func":"expand","direction":_direction,"type":_animation_type,"duration":_animation_duration,"step":_animation_step,"current":0});
		}
	},
	_expand_done:function()
	{
		var _this = _obj(this._id);
		var _this_link = _goFirstChild(_this);
		_addClass(_this_link,"kmuExpanded");
		var _group = _obj(this._id+"_group");
		var _slide = _goParentNode(_group);
		_slide.style.overflow = "visible";		
		_setDisplay(_slide,1);
		_setTop(_group,null);
		_setLeft(_group,null);
		if(_getBrowser()=="ie6")
		{
			var _iframe = _slide.previousSibling;
			_setDisplay(_iframe,1);
		}
		this._getMenu()._handleEvent("OnItemExpand",{"ItemId":this._id});
	},
	_register_collapse:function()
	{
		this._remove_collapse_expand_registering();
		var _menu = this._getMenu();
		var _menu_setting = _menu._loadSetting();
		var _this_setting = this._loadSetting();
		_this_setting["TimeoutID"] = setTimeout("(new KoolMenuItem('"+this._id+"')).collapse();",_menu_setting["CollapseDelay"]);
		this._saveSetting(_this_setting);
	},	
	collapse:function()
	{
		if(this.hasChild() && this.isExpanded())
		{
			var _menu = this._getMenu();
			if(!_menu._handleEvent("OnBeforeItemCollapse",{"ItemId":this._id})) return;
			var _this = _obj(this._id);
			var _this_link = _goFirstChild(_this);
			_removeClass(_this_link,"kmuExpanded");
			var _menu_setting = _menu._loadSetting();
			var _this_setting = this._loadSetting();
			var _collapse_animation = _menu_setting["CollapseAnimation"];
			var _animation_type = _collapse_animation["Type"];
			var _animation_duration = _collapse_animation["Duration"];
			var _time_per_step = 20;
			var _animation_step = _animation_duration/_time_per_step;
			_this_setting["TimeoutID"] = null;
			this._saveSetting(_this_setting);
			var _slide = _goParentNode(_obj(this._id+"_group"));
			_slide.style.overflow = "hidden";
			_setzIndex(_this,0);
			var _direction = _this_setting["ExpandDirection"].toLowerCase();
			if (_direction=="auto")
			{
				var _parent_ul = _goParentNode(_this);
				if(_index("kmuVertical",_getClass(_parent_ul))<0)
				{
					_direction = "down";
				}
				else
				{
					_direction = "right";
				}
			}
			if (_exist(_this_setting["AnimTimeoutID"]))
			{
				clearTimeout(_this_setting["AnimTimeoutID"]);
			}
			this._handle_animate({"func":"collapse","direction":_direction,"type":_animation_type,"duration":_animation_duration,"step":_animation_step,"current":_animation_step});
		}		
	},
	_collapse_done:function()
	{
		var _this = _obj(this._id);
		var _this_link = _goFirstChild(_this);
		_removeClass(_this_link,"kmuExpanded");
		var _this_group = _obj(this._id+"_group");
		var _slide = _goParentNode(_this_group);
		_setDisplay(_slide,0);
		_setTop(_this_group,null);
		_setLeft(_this_group,null);
		if(_getBrowser()=="ie6")
		{
			var _iframe = _slide.previousSibling;
			_setDisplay(_iframe,0);
		}
		this._getMenu()._handleEvent("OnItemCollapse",{"ItemId":this._id});				
	},
	isExpanded:function()
	{
		var _this_link = _goFirstChild(_obj(this._id));
		return (_index("kmuExpanded",_getClass(_this_link))>=0);
	},
	hasChild:function()
	{
		return _exist(_obj(this._id+"_group"));
	},
	getChildItems:function()
	{
		var _res = new Array();
		if(this.hasChild())
		{
			var _group = _obj(this._id+"_group");
			for(var i=0;i<_group.childNodes.length;i++)
			{
				var _node = _group.childNodes[i];
				if (_node.nodeName=="LI" && _index("Separator",_getClass(_node))<0)
				{
					_res.push(new KoolMenuItem(_node.id));
				}
			}
		}
		return _res;
	},
	_handle_animate:function(_param)
	{
		var _setting = this._loadSetting(); 
		var _animation_type = _param["type"];
		var _animation_duration = _param["duration"];
		var _animation_step = _param["step"];
		var _current = _param["current"];
		var _direction = _param["direction"];
		var _time_per_step = _animation_duration/_animation_step;
		var _group = _obj(this._id+"_group");
		var _slide = _goParentNode(_group);
		var _slide_width = _getWidth(_slide);
		var _slide_height = _getHeight(_slide);
		var _func = _get_animation_function(_param["type"]);
		var _from = 0;
		switch(_direction)
		{
			case "up":
				_from = _slide_height;
				break;
			case "down":
				_from = -_slide_height;
				break;
			case "left":
				_from = _slide_width;
				break;
			case "right":
				_from = -_slide_width;
				break;
		}
		_range = -_from;				
		switch(_param["func"])
		{
			case "expand":
				if (_current>=_animation_step || _param["type"]=="none")
				{
					this._expand_done();
				}
				else
				{
					var _pos = _func(_current,_from,_range,_animation_step);
					if(_direction=="down" || _direction=="up")
					{
						_setTop(_group, _pos);
					}
					else if (_direction=="left" || _direction=="right")
					{
						_setLeft(_group, _pos);
					}
					_param["current"] = _current + 1;
					_setting["AnimTimeoutID"] = setTimeout("kmu_animate('"+this._id+"',"+ _json2string(_param) +")",_time_per_step);
					this._saveSetting(_setting);
				}						
				break;
			case "collapse":
				if (_current<=0 || _param["type"]=="none")
				{
					this._collapse_done();
				}
				else
				{
					var _pos = _func(_current,_from,_range,_animation_step);
					if(_direction=="down" || _direction=="up")
					{
						_setTop(_group, _pos);
					}
					else if (_direction=="left" || _direction=="right")
					{
						_setLeft(_group, _pos);
					}
					_param["current"] = _current - 1;
					_setting["AnimTimeoutID"] = setTimeout("kmu_animate('"+this._id+"',"+ _json2string(_param) +")",_time_per_step);
					this._saveSetting(_setting);
				}
				break;				
		}
	},
	_remove_collapse_expand_registering:function()
	{
		var _this_setting = this._loadSetting();		
		var _timeout_id = _this_setting["TimeoutID"];
		if (_exist(_timeout_id))
		{
			clearTimeout(_timeout_id);
		}		
	},
	_handle_item_mouseover:function(_e)
	{
		var _menu = this._getMenu();
		var _menu_setting = _menu._loadSetting();
		if(!_menu._handleEvent("OnBeforeItemMouseOver",{"ItemId":this._id})) return;
		var _menu_setting = _menu._loadSetting();
		if(this.isEnabled() && this.hasChild() && _menu._isActive())
		{
			this._register_expand();	
		}
		_menu._handleEvent("OnItemMouseOver",{"ItemId":this._id});
	},
	_handle_item_mouseout:function(_e)
	{
		var _menu = this._getMenu();
		var _menu_setting = _menu._loadSetting();
		if(!_menu._handleEvent("OnBeforeItemMouseOut",{"ItemId":this._id})) return;
		if(this.isEnabled() && this.hasChild())
		{
			this._remove_collapse_expand_registering();
			if(_menu_setting["ClickToOpen"])
			{
				var _ul_parent = _goParentNode(_obj(this._id));
				if(_index("RootGroup",_getClass(_ul_parent))<0)
				{
					this._register_collapse();
				}	
			}
			else
			{
				this._register_collapse();	
			}	
		}
		_menu._handleEvent("OnItemMouseOut",{"ItemId":this._id});
	},
	_handle_item_click:function(_e)
	{
		var _menu = this._getMenu();
		var _menu_setting = _menu._loadSetting();
		if(!_menu._handleEvent("OnBeforeItemClick",{"ItemId":this._id})) return;
		if (this.isEnabled())
		{
			if (this.hasChild())
			{
				this.expand();
				if (_menu_setting["ClickToOpen"])
				{
					_menu._setActive(1);
				}			
			}
			else if(!this._usedTemplate())
			{
				this._remove_collapse_expand_registering();
				this.select();
				_menu.collapse();
				_stopPropagation(_e);
			}			
		}
		_menu._handleEvent("OnItemClick",{"ItemId":this._id});
	}
}
function KoolMenu(_id)
{
	this._id = _id;
	this.id = _id;
	this._eventhandles = new Array();	
	this._init();
	this.targetId = "";
}
KoolMenu.prototype = 
{
	_init:function()
	{
		var _this = _obj(this._id);
		var _input_select = _obj(this._id+"_select");
		_input_select.value="";
		var _setting = this._loadSetting();
		var _context_menu = _setting["ContextMenu"];
		if(_context_menu)
		{
			var _root = _obj(this._id+"_ctmnu");
			var _ul_root = _goParentNode(_root);
			var _link_root = _goFirstChild(_root);
			var _cssText = "width:0px;height:0px;padding:0px;margin:0px;border:0px;";
			_this.style.cssText = _cssText;
			_root.style.cssText = _cssText;
			_ul_root.style.cssText = _cssText;
			_link_root.style.cssText = _cssText;
			_this.style.position = "absolute";
			var _attachto = _setting["AttachTo"];
			_addEvent(window,"load",eval("__=function(){kmu_window_onload('"+this._id+"')}"),false);
			_closeonclick_menu_ids.push(this._id);
		}
		var _item_lis = _getElements("li","kmuItem",_this);
		for(var i=0;i<_item_lis.length;i++)
		{
			if (_index("Separator",_getClass(_item_lis[i]))<0 && _item_lis[i].id!=(this._id+"_ctmnu"))
			{
				_addEvent(_item_lis[i],"mouseover",_item_mouseover,false);
				_addEvent(_item_lis[i],"mouseout",_item_mouseout,false);
				_addEvent(_item_lis[i],"click",_item_click,false);				
			}
		}
		if(!_context_menu)
		{
			var _root_group = _goFirstChild(_obj(this._id));		
			if (_index("kmuVertical",_getClass(_root_group))>-1)
			{
				var _max_item_width = 0;
				for(var i=0;i<_root_group.childNodes.length;i++)
				{
					var _node = _root_group.childNodes[i];
					if (_node.nodeName=="LI")
					{
						if(_index("Separator",_getClass(_node))<0)
						{
							var _node_link = _goFirstChild(_node);
							if (_max_item_width<_node_link.offsetWidth)
							{
								_max_item_width = _node_link.offsetWidth;
							}
						}
					}
				}
				for(var i=0;i<_root_group.childNodes.length;i++)
				{
					var _node = _root_group.childNodes[i];
					if (_node.nodeName=="LI")
					{
							var _node_link = _goFirstChild(_node);
							_setWidth(_node_link, _max_item_width);
					}
				}			
			}
			if(_setting["ClickToOpen"])
			{
				_closeonclick_menu_ids.push(this._id);	
			}
		}
		_addEvent(_this,"mouseup",_kmu_event_cancel,false);
	},
	_setActive:function(_bool)
	{
		var _root_group = _goFirstChild(_obj(this._id));
		if (_bool)
		{
			_addClass(_root_group,"kmuActive");	
		}
		else
		{
			_removeClass(_root_group,"kmuActive");
		}
	},
	_isActive:function()
	{
		var _setting = this._loadSetting();
		if(_setting["ClickToOpen"])
		{
			var _root_group = _goFirstChild(_obj(this._id));
			return (_index("Active",_getClass(_root_group))>0)
		}
		else
		{
			return true;
		}
	},
	_save_select:function(_id)
	{
		var _input_select = _obj(this._id+"_select");
		_input_select.value = _id;
	},
	_doPostBack:function()
	{
		var _form = _obj(this._id);
		while(_form.nodeName!="FORM")
		{
			if(_form.nodeName=="BODY") return;//do nothing
			_form = _goParentNode(_form);
		}
		_form.submit();//If the form is found, make submission.
	},
	_loadSetting:function()
	{
		return eval("__="+_obj(this._id+"_setting").value);
	},
	_saveSetting:function(_setting)
	{
		var _input = _obj(this._id+"_setting");
		_input.value = _json2string(_setting);
	},	
	collapse:function()
	{
		var _this = _obj(this._id);
		var _open_links = _getElements("a","kmuExpanded",_this);
		for(var i=_open_links.length-1;i>=0;i--)
		{
			var _item = new KoolMenuItem(_goParentNode(_open_links[i]).id);
			_item.collapse();
		}
		this._setActive(0);
	},
	getItem:function(_id)
	{
		return new KoolMenuItem(_id);
	},
	getRootItems:function()
	{
		var _setting = this._loadSetting();
		var _res = new Array();
		var _group = _goFirstChild(_obj(this._id));
		if(_setting["ContextMenu"])
		{
			_group = _obj(this._id+"_ctmnu_group");
		}		
		for(var i=0;i<_group.childNodes.length;i++)
		{
			var _node = _group.childNodes[i];
			if (_node.nodeName=="LI" && _index("Separator",_getClass(_node))<0)
			{
				_res.push(new KoolMenuItem(_node.id));
			}
		}
		return _res;
	},
	registerEvent:function(_name,_handle)
	{
		this._eventhandles[_name]=_handle;
	},
	_handleEvent:function(_name,_arg)
	{
		return (_exist(this._eventhandles[_name]))?this._eventhandles[_name](this,_arg):true;
	},
	_handle_target_contextmenu:function(_e,_target)
	{
		var _this = _obj(this._id);
		var _mouse_position = _mouseXY(_e);
		if(_getBrowser()=="firefox")
		{
			_setLeft(_this,_mouse_position._x+1);
			_setTop(_this,_mouse_position._y+1);			
		}
		else
		{
			_setLeft(_this,_mouse_position._x);
			_setTop(_this,_mouse_position._y);				
		}
		var _root_item = new KoolMenuItem(this._id+"_ctmnu");		
		_root_item.expand();
		this.targetId = _target.id;
	},
	_attach_contextmenu_to_targets:function(_bool)
	{
		var _attachto = this._loadSetting()["AttachTo"];		
		for(var i=0;i<_attachto.length;i++)
		{
			var _target = _obj(_attachto[i]);
			if(_exist(_target))
			{
				if(_bool)
				{
					_addEvent(_target,"contextmenu",eval("__=function(e){return kmu_target_contextmenu(e,this,'"+this._id+"');}"),false);	
				}
				else
				{
					_removeEvent(_target,"contextmenu",eval("__=function(e){return kmu_target_contextmenu(e,this,'"+this._id+"');}"),false);
				}
			}	
		}		
	},	
	_handle_window_onload:function(_e)
	{
		this._attach_contextmenu_to_targets(1);
	},
	attachTo:function(_ids)
	{
		this._attach_contextmenu_to_targets(0);
		var _setting = this._loadSetting();
		_setting["AttachTo"] = _ids.split(',');
		this._saveSetting(_setting);
		this._attach_contextmenu_to_targets(1);
	},
	reAttach:function()
	{
		this._attach_contextmenu_to_targets(0);
		this._attach_contextmenu_to_targets(1);
	}
}
function _item_mouseout(_e)
{
	(new KoolMenuItem(this.id))._handle_item_mouseout(_e);
}
function _item_mouseover(_e)
{
	(new KoolMenuItem(this.id))._handle_item_mouseover(_e);
}
function _item_click(_e)
{
	(new KoolMenuItem(this.id))._handle_item_click(_e);	
}
function _kmu_event_cancel(_e)
{
	_stopPropagation(_e);
	return _preventDefaut(_e);
}
function kmu_window_onload(_id )
{
	var _menu = eval("__="+_id);
	_menu._handle_window_onload();	
}
function kmu_animate(_id,_param)
{
	(new KoolMenuItem(_id))._handle_animate(_param);
}
function kmu_target_contextmenu(_e,_target,_id)
{
	var _menu = eval("__="+_id);
	_menu._handle_target_contextmenu(_e,_target);
	return _preventDefaut(_e);
}
var _closeonclick_menu_ids = new Array();
function _window_mouseup(_e)
{
	for(var i=0;i<_closeonclick_menu_ids.length;i++)
	{
		var _menu = eval("__="+_closeonclick_menu_ids[i]);
		if(_exist(_menu))
		{
			_menu.collapse();
		}
	}
}
_addEvent(document,"mouseup",_window_mouseup,false);
if(typeof(__KMUInits)!='undefined' && _exist(__KMUInits))
{	
	for(var i=0;i<__KMUInits.length;i++)
	{
		__KMUInits[i]();
	}
}
/*
 * OnBeforeItemSelect
 * OnItemSelect
 * OnBeforeItemMouseOver
 * OnItemMouseOver
 * OnBeforeItemMouseOut
 * OnItemMouseOut
 * OnBeforeItemExpand
 * OnItemExpand
 * OnBeforeItemCollapse
 * OnItemCollapse
 * OnBeforeItemClick
 * OnItemClick
 */
