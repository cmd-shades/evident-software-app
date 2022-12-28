/**
 * @author Nghiem Anh Tuan
 */
var _identity = 0;
function _getIdentity()
{
	_identity++;
	return _identity;
}
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
	for (var _name in _o)
	{
		switch(typeof(_o[_name]))
		{
			case "string":
					_res+="\""+_name+"\":\""+ _o[_name]+"\",";
				break;
			case "number":
					_res+="\""+_name+"\":"+_o[_name]+",";
				break;
			case "boolean":
					_res+="\""+_name+"\":"+(_o[_name]?"true":"false")+",";
				break;			
			case "object":
					_res+="\""+_name+"\":"+_json2string(_o[_name])+",";					
				break;								
		}
	}
	if (_res.length>0)
		_res = _res.substring(0,_res.length-1);
	_res="{"+_res+"}";
	if (_res=="{}") _res="null";
	return _res;
}
function _index(_search,_original)
{
	return _original.indexOf(_search);
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
function _utf8_decode (str_data) {
    var tmp_arr = [],
        i = 0,
        ac = 0,
        c1 = 0,
        c2 = 0,
        c3 = 0;
    str_data += '';
    while (i < str_data.length) {
        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if (c1 > 191 && c1 < 224) {
            c2 = str_data.charCodeAt(i + 1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i + 1);
            c3 = str_data.charCodeAt(i + 2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    }
    return tmp_arr.join('');
}
function _utf8_encode (argString) {
    if (argString === null || typeof argString === "undefined") {
        return "";
    }
    var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
    var utftext = "",
        start, end, stringl = 0;
    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;
        if (c1 < 128) {
            end++;
        } else if (c1 > 127 && c1 < 2048) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.slice(start, end);
            }
            utftext += enc;
            start = end = n + 1;
        }
    }
    if (end > start) {
        utftext += string.slice(start, stringl);
    }
    return utftext;
}
function _base64_decode (data) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        dec = "",
        tmp_arr = [];
    if (!data) {
        return data;
    }
    data += '';
    do { // unpack four hexets into three octets using index points in b64
        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));
        bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;
        o1 = bits >> 16 & 0xff;
        o2 = bits >> 8 & 0xff;
        o3 = bits & 0xff;
        if (h3 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1);
        } else if (h4 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1, o2);
        } else {
            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
        }
    } while (i < data.length);
    dec = tmp_arr.join('');
    dec = this._utf8_decode(dec);
    return dec;
}
function _base64_encode (data) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        enc = "",
        tmp_arr = [];
    if (!data) {
        return data;
    }
    data = this._utf8_encode(data + '');
    do { // pack three octets into four hexets
        o1 = data.charCodeAt(i++);
        o2 = data.charCodeAt(i++);
        o3 = data.charCodeAt(i++);
        bits = o1 << 16 | o2 << 8 | o3;
        h1 = bits >> 18 & 0x3f;
        h2 = bits >> 12 & 0x3f;
        h3 = bits >> 6 & 0x3f;
        h4 = bits & 0x3f;
        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    } while (i < data.length);
    enc = tmp_arr.join('');
    var r = data.length % 3;
    return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
}
function _json2string(_o)
{
	var _res="";
	for (var _name in _o)
	{
		switch(typeof(_o[_name]))
		{
			case "string":
					_res+="\""+_name+"\":\""+_o[_name]+"\",";
				break;
			case "number":
					_res+="\""+_name+"\":"+_o[_name]+",";
				break;
			case "boolean":
					_res+="\""+_name+"\":"+(_o[_name]?"true":"false")+",";
				break;			
			case "object":
					_res+="\""+_name+"\":"+_json2string(_o[_name])+",";					
				break;								
		}
	}
	if (_res.length>0)
		_res = _res.substring(0,_res.length-1);
	_res="{"+_res+"}";
	if (_res=="{}") _res="null";
	return _res;
}
function _loadViewState(_id)
{
	var _viewstate = _obj(_id+"_viewstate");
	if(_viewstate)
	{
    return JSON.parse(_base64_decode(_viewstate.value));		    
	}
	else
	{
		return null;
	}
}
function _saveViewState(_id,_viewstate)
{
	var _input_viewstate = _obj(_id+"_viewstate");
	if(_input_viewstate)
	{
    _input_viewstate.value = _base64_encode(JSON.stringify(_viewstate));		    
		return true;
	}
	else
	{
		return false;
	}
}
function _getCaretPosition(_this)
{
	var CaretPos = 0;
	if (document.selection) {
		var Sel = document.selection.createRange();
		var SelLength = document.selection.createRange().text.length;
		Sel.moveStart ('character', -_this.value.length);
		CaretPos = Sel.text.length - SelLength;
	}
	else if (_this.selectionStart || _this.selectionStart == '0')
		CaretPos = _this.selectionStart;
	return (CaretPos);
}
function _setCaretPosition(_this,_pos)
{
	if (document.selection) { 	
       var oSel = document.selection.createRange ();
       oSel.moveStart ('character', _pos);
       oSel.moveEnd ('character', _pos-_this.value.length);
       oSel.select ();
     }
     else if (_this.selectionStart || _this.selectionStart == '0') {
       _this.selectionStart = _pos;
       _this.selectionEnd = _pos;
     }	
}
function _getSelectionStart(_this)
{
	if (document.selection) { 	
        var bookmark = document.selection.createRange().getBookmark();
        var _selection = _this.createTextRange();
        _selection.moveToBookmark(bookmark);
        var _before = _this.createTextRange();
        _before.collapse(true);
        _before.setEndPoint("EndToStart", _selection);
        var _beforeLength = _before.text.length;
        var _selLength = _selection.text.length;
		return _beforeLength;
     }
     else if (_this.selectionStart || _this.selectionStart == '0') {
       return _this.selectionStart;
     }		
}
function _getSelectionEnd(_this)
{
	if (document.selection) { 	
        var bookmark = document.selection.createRange().getBookmark();
        var _selection = _this.createTextRange();
        _selection.moveToBookmark(bookmark);
        var _before = _this.createTextRange();
        _before.collapse(true);
        _before.setEndPoint("EndToStart", _selection);
        var _beforeLength = _before.text.length;
        var _selLength = _selection.text.length;
		return _beforeLength+_selLength;
     }
     else if (_this.selectionStart || _this.selectionStart == '0') {
       return _this.selectionEnd;
     }			
}
function _setSelection(_this,_start,_end)
{
	if (document.selection) { 	
       var oSel = document.selection.createRange ();
 		oSel.moveStart ('character', -_this.value.length);
       oSel.moveStart ('character', _start);
       oSel.moveEnd ('character', _end-_start);
       oSel.select();
     }
     else if (_this.selectionStart || _this.selectionStart == '0') {
       _this.selectionStart = _start;
       _this.selectionEnd = _end;
     }
}
function urldecode(url) {
  return decodeURIComponent(url.replace(/\+/g, ' '));
}
/*--------------------------------------------------------*/
function KoolRequiredFieldValidator(_id)
{
	this._id = _id;
}
KoolRequiredFieldValidator.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);		
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		_addClass(_this,"kfrValidator");
		_setDisplay(_this,0);
		_this.innerHTML = _viewstate["ErrorMessage"];		
		if(_viewstate["CssClass"])
		{
		 	_addClass(_this,_viewstate["CssClass"]);
		}			
		if(_viewstate["ToolTip"])
		{
		 	_this.title = _viewstate["ToolTip"];
		}
		var _target = _obj(_viewstate["TargetId"]);
		if(_target)
		{
			_addEvent(_target,"blur",_validator_target_onblur);
		}
	},
	show_message:function(_bool)
	{
		var _this = _obj(this._id);
		_setDisplay(_this,_bool);
	},	
	validate:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _target = _obj(_viewstate["TargetId"]);
		var _value;
		if(_target!=null)
		{
			var _manager = eval("__="+_viewstate["Form"]);
			var _target_element = _manager.get_control(_target.id);
			if(_target_element)
			{
				_value = _target_element.get_value().toString();
			}
			else
			{
				_value = _target.value;				
			}
			if (!this._validate_function(_value)) 
			{
				this.show_message(1);
				return false;
			}
		}
		this.show_message(0);
		return true;		
	},
	_validate_function:function(_value)
	{
		return (_value!="");
	}
}
function KoolRangeValidator(_id)
{
	this._id = _id;
}
KoolRangeValidator.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;		
		var _viewstate = _loadViewState(this._id);
		_addClass(_this,"kfrValidator");
		_setDisplay(_this,0);
		_this.innerHTML = _viewstate["ErrorMessage"];		
		if(_viewstate["CssClass"])
		{
		 	_addClass(_this,_viewstate["CssClass"]);
		}			
		if(_viewstate["ToolTip"])
		{
		 	_this.title = _viewstate["ToolTip"];
		}
		var _target = _obj(_viewstate["TargetId"]);
		if(_target)
		{
			_addEvent(_target,"blur",_validator_target_onblur);
		}
	},
	show_message:function(_bool)
	{
		var _this = _obj(this._id);
		_setDisplay(_this,_bool);
	},	
	validate:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _target = _obj(_viewstate["TargetId"]);
		var _value;
		if(_target!=null)
		{
			var _manager = eval("__="+_viewstate["Form"]);
			var _target_element = _manager.get_control(_target.id);
			if(_target_element)
			{
				_value = _target_element.get_value();
			}
			else
			{
				_value = _target.value;				
			}
			if (!this._validate_function(_value)) 
			{
				this.show_message(1);
				return false;
			}
		}
		this.show_message(0);
		return true;		
	},
	_validate_function:function(_value)
	{
		var _viewstate = _loadViewState(this._id);		
		_float_value = parseFloat(_value);
		return (_viewstate["MinValue"] <= _float_value && _float_value <= _viewstate["MaxValue"]);
	}
}
function KoolRegularExpressionValidator(_id)
{
	this._id = _id;
}
KoolRegularExpressionValidator.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		_addClass(_this,"kfrValidator");
		_setDisplay(_this,0);
		_this.innerHTML = _viewstate["ErrorMessage"];		
		if(_viewstate["CssClass"])
		{
		 	_addClass(_this,_viewstate["CssClass"]);
		}			
		if(_viewstate["ToolTip"])
		{
		 	_this.title = _viewstate["ToolTip"];
		}
		var _target = _obj(_viewstate["TargetId"]);
		if(_target)
		{
			_addEvent(_target,"blur",_validator_target_onblur);
		}
	},
	show_message:function(_bool)
	{
		var _this = _obj(this._id);
		_setDisplay(_this,_bool);
	},
	validate:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _target = _obj(_viewstate["TargetId"]);
		var _value;
		if(_target!=null)
		{
			var _manager = eval("__="+_viewstate["Form"]);
			var _target_element = _manager.get_control(_target.id);
			if(_target_element)
			{
				_value = _target_element.get_value();
			}
			else
			{
				_value = _target.value;				
			}
			if (!this._validate_function(_value)) 
			{
				this.show_message(1);
				return false;
			}
		}
		this.show_message(0);
		return true;		
	},
	_validate_function:function(_value)
	{
		var _viewstate = _loadViewState(this._id);
		var _rex = eval(_viewstate["Expression"]);
		return _rex.test(_value);
	}
}
function KoolCustomValidator(_id)
{
	this._id = _id;
}
KoolCustomValidator.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		_addClass(_this,"kfrValidator");
		_setDisplay(_this,0);
		_this.innerHTML = _viewstate["ErrorMessage"];		
		if(_viewstate["CssClass"])
		{
		 	_addClass(_this,_viewstate["CssClass"]);
		}			
		if(_viewstate["ToolTip"])
		{
		 	_this.title = _viewstate["ToolTip"];
		}
		var _target = _obj(_viewstate["TargetId"]);
		if(_target)
		{
			_addEvent(_target,"blur",_validator_target_onblur);
		}
	},
	show_message:function(_bool)
	{
		var _this = _obj(this._id);
		_setDisplay(_this,_bool);
	},	
	validate:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _validate_function = eval("__="+_viewstate["ClientValidationFunction"]);
		if(_validate_function)
		{
			if (!_validate_function()) 
			{
				this.show_message(1);
				return false;
			}		
			else
			{
				this.show_message(0);
				return true;				
			}			
		}
		this.show_message(1);
		return false;			
	}
}
function KoolPasswordValidator(_id)
{
	this._id = _id;
}
KoolPasswordValidator.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		_addClass(_this,"kfrValidator");
		_setDisplay(_this,0);
		_this.innerHTML = _viewstate["ErrorMessage"];		
		if(_viewstate["CssClass"])
		{
		 	_addClass(_this,_viewstate["CssClass"]);
		}			
		if(_viewstate["ToolTip"])
		{
		 	_this.title = _viewstate["ToolTip"];
		}
		var _target = _obj(_viewstate["TargetId"]);
		if(_target)
		{
			_addEvent(_target,"blur",_validator_target_onblur);
		}
	},
	show_message:function(_bool)
	{
		var _this = _obj(this._id);
		_setDisplay(_this,_bool);
	},	
	validate:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _password = new KoolPasswordTextBox(_viewstate["TargetId"]);
		if(_password)
		{
			if (!_password.validate()) 
			{
				this.show_message(1);
				return false;
			}		
			else
			{
				this.show_message(0);
				return true;				
			}			
		}
		this.show_message(1);
		return false;			
	}
}
function _fix_rounded_for_ie(_parent,_span,_this)
{
			var _this_margin = _getStyle(_this,"margin");
			var _this_height = _this.offsetHeight;
			_this.style.margin = "0px";
			var _table = _newNode("table",_parent);
			_setClass(_table,"kfrRoundedWrapper");
			_table.style.margin = _this_margin;
			_table.cellPadding = "0";
			_table.cellSpacing = "0";
			var _tr = _newNode("tr",_table)
			var _td = _newNode("td",_tr);
			var _div = _newNode("div",_td);
			_div.innerText = " ";
			_setClass(_div,"kfrRoundedOuter");
			_setHeight(_div,_this_height-4);
			var _td = _newNode("td",_tr);
			var _div = _newNode("div",_td);
			_div.innerText = " ";
			_setClass(_div,"kfrRoundedInner");
			_setHeight(_div,_this_height-4);
			var _td = _newNode("td",_tr);
			var _div = _newNode("div",_td);
			_div.innerText = " ";
			_setClass(_div,"kfrRoundedInner");
			_setHeight(_div,_this_height-2);
			var _td = _newNode("td",_tr);
			_td.style.fontSize="1px";
			_parent.insertBefore(_table,_span);
			_td.appendChild(_span);			
			_this.style.borderLeft="0";
			_this.style.borderRight="0";
			_this.style.paddingLeft="1px";
			_this.style.paddingRight="0px";						
			var _td = _newNode("td",_tr);
			var _div = _newNode("div",_td);
			_div.innerText = " ";
			_setClass(_div,"kfrRoundedInner");
			_setHeight(_div,_this_height-2);
			var _td = _newNode("td",_tr);
			var _div = _newNode("div",_td);
			_div.innerText = " ";
			_setClass(_div,"kfrRoundedInner");
			_setHeight(_div,_this_height-4);
			var _td = _newNode("td",_tr);
			var _div = _newNode("div",_td);
			_div.innerText = " ";
			_setClass(_div,"kfrRoundedOuter");
			_setHeight(_div,_this_height-4);	
}
function KoolTextBox(_id)
{
	this._id = _id;
}
KoolTextBox.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;
		var _parent = _goParentNode(_this);
		var _span = _newNode("span",_parent);
		_addClass(_span,"kfrTextBoxWrapper");
		_setClass(_this,"kfrTextBox kfrRoundCorner");
		_parent.insertBefore(_span,_this);
		_span.appendChild(_this);
		if (_index("ie",_getBrowser())>-1)
		{
			_fix_rounded_for_ie(_parent,_span,_this);
		}
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			_this.autocomplete = _viewstate["AutoComplete"]?"on":"off";
			if(_viewstate["StatePersistent"])
			{
				_this.value = decodeURIComponent(decodeURI(_viewstate["Value"]));
			}
			_viewstate["OldValue"] = _viewstate["Value"];
			if(_this.value=="")
			{
				var _span = _goParentNode(_this);
				_addClass(_span,"kfrEmpty");
				_this.value = _viewstate["EmptyMessage"];
			}
			if(_viewstate["ToolTip"])
			{
				_this.title = _viewstate["ToolTip"];				
			}
			if(_viewstate["CssClass"])
			{
			 	_addClass(_this,_viewstate["CssClass"]);
			}			
			_saveViewState(this._id,_viewstate);
			_addEvent(_this,"keypress",_this_onkeypress);
			_addEvent(_this,"keyup",_this_onkeyup);			
		}
			_addEvent(_this,"focus",_this_onfocus);
			_addEvent(_this,"blur",_this_onblur);
	},
	get_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{	
			return decodeURI(_viewstate["Value"]);
      return decodeURI(_viewstate["Value"]);	
		}
		else
		{
			var _this = _obj(this._id);
			return _this.value;			
		}
	},
	set_value:function(_value)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		_this.value = _value;
		if(_viewstate)
		{	
			_viewstate["Value"] =  encodeURI(_value);
			_viewstate["OldValue"] = encodeURI(_value);
			_saveViewState(this._id,_viewstate);
		}
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	},
	get_element:function()
	{
		return _obj(this._id);
	},
	_handle_onfocus:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _span = _goParentNode(_this);
			if(_index("kfrEmpty",_getClass(_span))>-1)
			{
				_removeClass(_span,"kfrEmpty");
				_this.value = "";
			}			
		}
		if (_index("ie",_getBrowser())>-1)
		{
			_addClass(_goParentNode(_this,4),"kfrFocus");
		}
	},
	_handle_onblur:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			if(_this.value=="")
			{
				var _span = _goParentNode(_this);
				_addClass(_span,"kfrEmpty");
				_this.value = _viewstate["EmptyMessage"];
			}
			var _newvalue = this.get_value();
			var _oldvalue = decodeURI(_viewstate["OldValue"]);
			if(_oldvalue!=_newvalue)
			{
				_viewstate["OldValue"] = encodeURI(_newvalue);
				_saveViewState(this._id,_viewstate);
				this._handle_clientevent("OnChange",{"OldValue":_oldvalue,"NewValue":_newvalue});
			}			
		}
		if (_index("ie",_getBrowser())>-1)
		{
			_removeClass(_goParentNode(_this,4),"kfrFocus");	
		}
	},
	_handle_onkeypress:function(_e)
	{
		var _viewstate = _loadViewState(this._id);
		var _this = _obj(this._id);
		var _maxlength = _viewstate["MaxLength"];
		if(_maxlength!=null && _this.value.length>=_maxlength)
		{
			if(_getBrowser()=="firefox"||_getBrowser()=="opera")
			{
				var _code=_e.keyCode;
				if(_code==8||(_code>36&&_code<41))
				{
					return;
				}				
			}
			return _preventDefaut(_e);				
		}
	},
	_handle_onkeyup:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		_viewstate["Value"] = encodeURI(_this.value);
		_saveViewState(this._id,_viewstate);
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	}
}
function KoolMaskedTextBox(_id)
{
	this._id = _id;
}
KoolMaskedTextBox.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _span = _newNode("span",_parent);
		_addClass(_span,"kfrTextBoxWrapper");
		_setClass(_this,"kfrTextBox kfrRoundCorner");
		_parent.insertBefore(_span,_this);
		_span.appendChild(_this);
		if (_index("ie",_getBrowser())>-1)
		{
			_fix_rounded_for_ie(_parent,_span,_this);
		}
		_this.autocomplete = _viewstate["AutoComplete"]?"on":"off";
		var _showing_mask = this._get_showing_mask(_viewstate["Mask"],_viewstate["PromptChar"]);
		var _hidden_mask = this._get_hidden_mask(_viewstate["Mask"]);
		var _user_input = new Array(_showing_mask.length);
		var i=0;
		while(i<_user_input.length)
		{
			if(_hidden_mask[i][0]=="r")
			{
				_user_input[i]="0";
			}
			else
			{
				_user_input[i] = null;
			}
			i++;
		}
		if(_viewstate["StatePersistent"] && _viewstate["UserInput"]!=null)
		{
			_user_input = _viewstate["UserInput"];
		}
		_viewstate["UserInput"] = _user_input;
		_viewstate["ShowingMask"] = encodeURI(_showing_mask);
		_viewstate["HiddenMask"] = _hidden_mask;
		_saveViewState(this._id,_viewstate);
		if(_viewstate["Value"]!=null)
		{
			this.set_value(_viewstate["Value"]);
		}
		else
		{
			this._verify_range_input();
			this._mix_mask_and_input();
		}
		_viewstate = _loadViewState(this._id);
		_viewstate["OldValue"] = _viewstate["ValueWithPromptAndLiterals"];
		_saveViewState(this._id,_viewstate);
		_addEvent(_this,"focus",_this_onfocus);
		_addEvent(_this,"blur",_this_onblur);
		_addEvent(_this,"keypress",_this_onkeypress);
		_addEvent(_this,"keydown",_this_onkeydown);
	},
	_get_showing_mask:function(_mask,_prompt_char)
	{
		if(_prompt_char==null||_prompt_char=="") _prompt_char="_";
		var _showing_mask="";
		var i=0;
		while(i<_mask.length)
		{
			switch(_mask.charAt(i))
			{
				case "\\":
					if(i+1<_mask.length)
					{
						i++;
						_showing_mask = _showing_mask.concat(_mask.charAt(i));	
					}
					break;
				case "<":
					var _start = i;
					i++;
					while(_mask.charAt(i)!=">")
					{
						if (_mask.charAt(i) == "\\") 
						{
							i++;
						}
						i++;
					}
					var _end = i;
					var _sub = _mask.substring(_start+1,_end);
					if(_index("..",_sub)>0)
					{
						var _options = _sub.split("..");
						for(var j=0;j<_options.length;j++)
						{
							_options[j] = parseInt(_options[j],10);
						}
						var _length = _options[1].toString().length;
						for(var j=0;j<_length;j++)
						{
							_showing_mask =_showing_mask.concat(_prompt_char);
						}
					}
					else
					{
						var _options = _sub.split("|");
						_showing_mask = _showing_mask.concat(_prompt_char);
					}
					break;					
				case "#":
				case "a":
				case "A":
				case "~":				
					_showing_mask = _showing_mask.concat(_prompt_char);
					break;
				default:
					_showing_mask = _showing_mask.concat(_mask.charAt(i));
					break;				
			}
			i++;
		}
		return _showing_mask;
	},	
	_get_hidden_mask:function(_mask)
	{
		var _hidden_mask=new Array();
		var i=0;
		while(i<_mask.length)
		{
			switch(_mask.charAt(i))
			{
				case "\\":
					if(i+1<_mask.length)
					{
						i++;
						_hidden_mask.push([null]);			
					}
					break;
				case "<":
					var _start = i;
					i++;
					while(_mask.charAt(i)!=">")
					{
						if (_mask.charAt(i) == "\\") 
						{
							i++;
						}
						i++;
					}
					var _end = i;
					var _sub = _mask.substring(_start+1,_end);
					if(_index("..",_sub)>0)
					{
						var _options = _sub.split("..");
						for(var j=0;j<_options.length;j++)
						{
							_options[j] = parseInt(_options[j],10);
						}
						var _length = _options[1].toString().length;
						_options[2]=_length;
						for(var j=0;j<_length;j++)
						{
							var _clone_options = _options.slice(0);
							_clone_options[3]=j;				
							_hidden_mask.push(["r",_clone_options]);
						}
					}
					else
					{
						var _options = _sub.split("|");
						_hidden_mask.push(["o",_options]);
					}
					break;					
				case "#":
				case "a":
				case "A":
				case "~":				
					_hidden_mask.push([_mask.charAt(i)]);
					break;
				default:
					_hidden_mask.push([null]);
					break;				
			}
			i++;
		}
		return _hidden_mask;
	},	
	set_value:function(_value)
	{
		var _viewstate = _loadViewState(this._id);
		var _user_input = _viewstate["UserInput"];
		var _hidden_mask = _viewstate["HiddenMask"];
		var j=0;
		for(i in _hidden_mask)
		{
			if(typeof _hidden_mask[i]!="function") //Mootools
			{
				_user_input[i] = null;
				if (_hidden_mask[i][0]) 
				{
					if (j < _value.length) 
					{
						_user_input[i] = encodeURI(_value.charAt(j));
					}
					j++;
				}
			}
		}
		_viewstate["UserInput"] = _user_input;
		_viewstate["Value"] = this._get_value(_user_input);
		_viewstate["ValueWithPrompt"] = this._get_value_with_prompt(_user_input);
		_viewstate["ValueWithLiterals"] = this._get_value_with_literals(_user_input);
		_viewstate["ValueWithPromptAndLiterals"] = this._get_value_with_prompt_and_literals(_user_input);		
		_saveViewState(this._id,_viewstate);
		this._mix_mask_and_input();
	},
	get_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		return decodeURI(_viewstate["Value"]);
	},
	get_value_with_prompt:function()
	{
		var _viewstate = _loadViewState(this._id);
		return decodeURI(_viewstate["ValueWithPrompt"]);
	},	
	get_value_with_literals:function()
	{
		var _viewstate = _loadViewState(this._id);
		return decodeURI(_viewstate["ValueWithLiterals"]);		
	},
	get_value_with_prompt_and_literals:function()
	{
		var _viewstate = _loadViewState(this._id);
		return decodeURI(_viewstate["ValueWithPromptAndLiterals"]);		
	},
	_get_value:function(_user_input)
	{
		var _viewstate = _loadViewState(this._id);
		var _value="";
		for(i in _user_input)
		{
			if(typeof _user_input[i]!="function") //Mootools
			{
				_value = _value.concat((_user_input[i])? decodeURI(_user_input[i]):"");					
			}
		}
		return _value;
	},
	_get_value_with_prompt:function(_user_input)
	{
		var _viewstate = _loadViewState(this._id);
		var _hidden_mask = _viewstate["HiddenMask"];
		var _value="";
		for(i in _user_input)
		{
			if(typeof _user_input[i]!="function") //Mootools
			if(_hidden_mask[i][0])
			{
				_value = _value.concat((_user_input[i])?decodeURI(_user_input[i]): _viewstate["PromptChar"]);				
			}
		}
		return _value;
	},	
	_get_value_with_literals:function(_user_input)
	{
		var _viewstate = _loadViewState(this._id);
		var _hidden_mask = _viewstate["HiddenMask"];
		var _showing_mask = decodeURI(_viewstate["ShowingMask"]);
		var _value="";
		for(i in _user_input)
		{
			if(typeof _user_input[i]!="function") //Mootools
			{
				if(_hidden_mask[i][0])
				{
					_value = _value.concat((_user_input[i])?decodeURI(_user_input[i]):"");				
				}
				else
				{
					_value = _value.concat(_showing_mask[i]);				
				}				
			}
		}
		return _value;
	},
	_get_value_with_prompt_and_literals:function(_user_input)
	{
		var _viewstate = _loadViewState(this._id);
		var _hidden_mask = _viewstate["HiddenMask"];
		var _showing_mask = decodeURI(_viewstate["ShowingMask"]);
		var _value="";
		for(i in _user_input)
		{
			if(typeof _user_input[i]!="function") //Mootools
			{
				if(_hidden_mask[i][0])
				{
					_value = _value.concat((_user_input[i])?decodeURI(_user_input[i]):_viewstate["PromptChar"]);				
				}
				else
				{
					_value = _value.concat(_showing_mask[i]);				
				}
			}
		}
		return _value;		
	},	
	_mix_mask_and_input:function()
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _user_input = _viewstate["UserInput"];
		var _output = decodeURI(_viewstate["ShowingMask"]);
		for(var i=0;i<_output.length;i++)
		{
			if(_user_input[i]!=null)
			{
				_output = _output.substr(0,i) + decodeURI(_user_input[i]) + _output.substr(i+1);
			}
		}
		_this.value = _output;
	},
	_verify_range_input:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _hidden_mask = _viewstate["HiddenMask"];
		var _user_input = _viewstate["UserInput"];
		var _showing_mask = decodeURI(_viewstate["ShowingMask"]);
		var _current_position=0;
		while(_current_position<_showing_mask.length)
		{
			if(_hidden_mask[_current_position][0]=="r")
			{
				var _options = _hidden_mask[_current_position][1];
				var _min = _options[0];
				var _max = _options[1];
				var _length = _options[2];
				var _str_num = "";
				for(var i=0;i<_length;i++)
				{
					_str_num = _str_num.concat(_user_input[_current_position+i]);
				}
				var _num = parseInt(_str_num,10);
				if(_num<_min)
				{
					_num = _min;
				}
				else if(_num>_max)
				{
					_num=_max;
				}
				_str_num = _num.toString();
				for(var i=0;i<_length;i++)
				{
					_user_input[_current_position+i] = (i<_length-_str_num.length)?"0":_str_num.charAt(i-_length+_str_num.length);
				}						
				_current_position+=_length-1;
			}
			_current_position++;	
		}
		_viewstate["UserInput"] = _user_input;
		_viewstate["Value"] = encodeURI(this._get_value(_user_input));
		_viewstate["ValueWithLiterals"] = encodeURI(this._get_value_with_literals(_user_input));
		_viewstate["ValueWithPrompt"] = encodeURI(this._get_value_with_prompt(_user_input));
		_viewstate["ValueWithPromptAndLiterals"] = encodeURI(this._get_value_with_prompt_and_literals(_user_input));			
		_saveViewState(this._id,_viewstate);
	},
	_handle_onkeypress:function(_e)
	{
		var _code = (_e.charCode!=null)?_e.charCode:_e.keyCode;
		var _type = null;
		switch(true)
		{
			case (47<_code && _code<58): //0-9
				_type = "#";
				break;
			case (64<_code && _code<91): //A-Z
				_type = "A";
				break;
			case (96<_code && _code<123): //a-z
				_type = "a";
				break;		
			case (31<_code && _code<48): //special char
			case (57<_code && _code<65): //special char
			case (90<_code && _code<97): //special char
			case (122<_code && _code<127): //special char
				_type = "~";
				break;
			default:
				break;			
		}
		if(_type)
		{
			var _this = _obj(this._id);
			var _viewstate = _loadViewState(this._id);
			var _hidden_mask = _viewstate["HiddenMask"];
			var _user_input = _viewstate["UserInput"];
			var _showing_mask = decodeURI(_viewstate["ShowingMask"]);
			var _current_position = _getCaretPosition(_this);
			if(_current_position<_showing_mask.length)
			{
				var _selectionStart = _getSelectionStart(_this);
				var _selectionEnd = _getSelectionEnd(_this);
				for(var i=((_selectionStart<_selectionEnd)?_selectionStart:_selectionEnd);i<((_selectionStart<_selectionEnd)?_selectionEnd:_selectionStart);i++)
				{
					_user_input[i]=(_hidden_mask[i][0]=="r")?"0":null;
				}
				while( _hidden_mask[_current_position][0]==null && _current_position<_showing_mask.length-1)
				{
					_current_position++;
				}
				switch(_hidden_mask[_current_position][0])
				{
					case _type:
						_user_input[_current_position] = encodeURI(String.fromCharCode(_code));
						_viewstate["UserInput"] = _user_input;
						_saveViewState(this._id,_viewstate);
						this._verify_range_input();
						this._mix_mask_and_input();
						_setCaretPosition(_this,_current_position+1);
						break;
					case "o":
						for(i in _hidden_mask[_current_position][1])
						{
							if(typeof _hidden_mask[_current_position][1][i]!="function") //Mootools
							if(_hidden_mask[_current_position][1][i]==String.fromCharCode(_code))
							{
								_user_input[_current_position] = String.fromCharCode(_code);
								_viewstate["UserInput"] = _user_input;
								_saveViewState(this._id,_viewstate);
								this._verify_range_input();
								this._mix_mask_and_input();
								_setCaretPosition(_this,_current_position+1);				
							}
						}					
						break;
					case "r":
						if(_type=="#")
						{
							_user_input[_current_position] = String.fromCharCode(_code);
							_viewstate["UserInput"] = _user_input;
							_saveViewState(this._id,_viewstate);
							this._verify_range_input();
							this._mix_mask_and_input();
							_setCaretPosition(_this,_current_position+1);					
						}
						break;						
				}				
			}
			return _preventDefaut(_e);	
		}		
	},
	_handle_onkeydown:function(_e)
	{
		var _this = _obj(this._id);
		var _current_position = _getCaretPosition(_this);
		var _selectionStart = _getSelectionStart(_this);
		var _selectionEnd = _getSelectionEnd(_this);
		var _code = _e.keyCode;
		switch(_code)
		{
			case 8:
					var _viewstate = _loadViewState(this._id);
					var _user_input = _viewstate["UserInput"];
					var _hidden_mask = _viewstate["HiddenMask"];
					if(_selectionStart!=_selectionEnd)
					{
						for(var i=((_selectionStart<_selectionEnd)?_selectionStart:_selectionEnd);i<((_selectionStart<_selectionEnd)?_selectionEnd:_selectionStart);i++)
						{							
							_user_input[i] = (_hidden_mask[i][0]=="r")?"0":null;
						}						
					}
					else
					{
						if(_current_position>0) _current_position--;
						while( _hidden_mask[_current_position][0]==null && _current_position>0)
						{
							_current_position--;
						}						
					}
					_user_input[_current_position] = (_hidden_mask[_current_position][0]=="r")?"0":null;
					_saveViewState(this._id,_viewstate);
					this._verify_range_input();
					this._mix_mask_and_input();
					_setCaretPosition(_this,_current_position);
					return _preventDefaut(_e);			
				break;
			case 46:
					var _viewstate = _loadViewState(this._id);
					var _user_input = _viewstate["UserInput"];
					var _showing_mask = _viewstate["ShowingMask"];
					var _hidden_mask = _viewstate["HiddenMask"];
					if(_selectionStart!=_selectionEnd)
					{
						for(var i=((_selectionStart<_selectionEnd)?_selectionStart:_selectionEnd);i<((_selectionStart<_selectionEnd)?_selectionEnd:_selectionStart);i++)
						{
							_user_input[i] = (_hidden_mask[i][0]=="r")?"0":null;
						}
						_current_position = _selectionEnd-1;						
					}
					else
					{
						while( _hidden_mask[_current_position][0]==null && _current_position<_showing_mask.length)
						{
							_current_position++;
						}				
					}
					_user_input[_current_position] = (_hidden_mask[_current_position][0]=="r")?"0":null;
					_saveViewState(this._id,_viewstate);
					if(_current_position<_showing_mask.length)
					{
						_current_position++;
					}
					this._verify_range_input();
					this._mix_mask_and_input();
					_setCaretPosition(_this,_current_position);
					return _preventDefaut(_e);
				break;
		}
	},
	_handle_onfocus:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		switch(_viewstate["SelectionOnFocus"].toLowerCase())
		{
			case "carettobeginning":
				_setCaretPosition(_this,0);
				break;
			case "carettoend":
				_setCaretPosition(_this,_this.value.length);
				break;
			case "selectall":
				_setSelection(_this,0,_this.value.length);
				break;
			default:
				break;			
		}
	},
	_handle_onblur:function(_e)
	{
		var _viewstate = _loadViewState(this._id);
		var _oldvalue = decodeURI(_viewstate["OldValue"]);
		var _newvalue = decodeURI(_viewstate["ValueWithPromptAndLiterals"]);
		if(_oldvalue!=_newvalue)
		{
			this._handle_clientevent("OnChange",{"OldValue":_oldvalue,"NewValue":_newvalue});				
			_viewstate["OldValue"] = encodeURI(_newvalue);
			_saveViewState(this._id,_viewstate);
		}		
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}			
}
function KoolNumericTextBox(_id)
{
	this._id = _id;
}
KoolNumericTextBox.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _span = _newNode("span",_parent);
		_addClass(_span,"kfrTextBoxWrapper");
		_setClass(_this,"kfrTextBox kfrRoundCorner");
		_parent.insertBefore(_span,_this);
		_span.appendChild(_this);		
		var _sub = _newNode("input",_span);
		_sub.type = "text";
		_sub.name = _this.name;
		_this.name = "";
		_sub.id = this._id+"_sub";
		_setClass(_sub,"kfrHide");
		if(_viewstate["ShowSpinButton"]==true)
		{
			var _table = _newNode("table",_parent);
			_parent.insertBefore(_table,_span);
			_setClass(_table,"kfrTable");
			_table.cellPadding = "0";
			_table.cellSpacing = "0";			
			var _tr = _newNode("tr",_table);
			if(_viewstate["SpinButtonPosition"].toLowerCase()=="left")
			{
				var _td = _newNode("td",_tr);
				var _a = _newNode("a",_td);
				_a.id = this._id+"_spinup";
				_setClass(_a,"kfrSpinUp");
				var _span_spin = _newNode("span",_a);
				_span_spin.innerText = "SpinUp";
				var _a = _newNode("a",_td);
				_a.id = this._id+"_spindown";
				_setClass(_a,"kfrSpinDown");
				var _span_spin = _newNode("span",_a);
				_span_spin.innerText = "SpinDown";
			}
			var _td = _newNode("td",_tr);
			_td.appendChild(_span);			
			if(_viewstate["SpinButtonPosition"].toLowerCase()!="left")
			{
				var _td = _newNode("td",_tr);
				var _a = _newNode("a",_td);
				_a.id = this._id+"_spinup";
				_setClass(_a,"kfrSpinUp");
				var _span_spin = _newNode("span",_a);
				_span_spin.innerHTML = "SpinUp";
				var _a = _newNode("a",_td);
				_a.id = this._id+"_spindown";
				_setClass(_a,"kfrSpinDown");
				var _span_spin = _newNode("span",_a);
				_span_spin.innerHTML = "SpinDown";
			}
		}
		if (_index("ie",_getBrowser())>-1)
		{
			_fix_rounded_for_ie(_goParentNode(_span),_span,_this);
		}
		if(_viewstate["ToolTip"])
		{
			_this.title = _viewstate["ToolTip"];				
		}
		if(_viewstate["CssClass"])
		{
		 	_addClass(_this,_viewstate["CssClass"]);
		}			
		_this.autocomplete = _viewstate["AutoComplete"]?"on":"off";
		if(_viewstate["StatePersistent"])
		{
			_this.value = _viewstate["Value"];
			_sub.value = _this.value;
		}
		if(_this.value=="")
		{
			var _span = _goParentNode(_this);
			_addClass(_span,"kfrEmpty");
			_this.value = _viewstate["EmptyMessage"];
		}
		else
		{
			_viewstate["Value"] = _this.value;	
			_saveViewState(this._id,_viewstate);
			this._show_display_value();			
		}
		_viewstate["OldValue"] = _viewstate["Value"];
		_saveViewState(this._id,_viewstate);
		_addEvent(_this,"focus",_this_onfocus);
		_addEvent(_this,"blur",_this_onblur);
		_addEvent(_this,"keypress",_this_onkeypress);
		_addEvent(_this,"keydown",_this_onkeydown);
		_addEvent(_this,"keyup",_this_onkeyup);
		if(_viewstate["IncrementSettings"]["InterceptMouseWheel"])
		{
			_addEvent(_this,"mousewheel",_this_onmousewheel);
			_addEvent(_this,"DOMMouseScroll",_this_onmousewheel);			
		}
		if (_viewstate["ShowSpinButton"] == true) 
		{
			_addEvent(_obj(this._id+"_spinup"),"click",_spinup_onclick);
			_addEvent(_obj(this._id+"_spindown"),"click",_spindown_onclick);		
		}
	},
	get_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		return _viewstate["Value"];
	},
	set_value:function(_value)
	{
		var _viewstate = _loadViewState(this._id);
		var _sub = _obj(this._id+"_sub");
		_viewstate["Value"] = _value;
		_sub.value = _value;
		_saveViewState(this._id,_viewstate);
		this._show_display_value();
	},
	_show_display_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _this = _obj(this._id);
		_value = _viewstate["Value"];
		var _decimal_digits = _viewstate["NumberFormat"]["DecimalDigits"];
		var _decimal_separator = _viewstate["NumberFormat"]["DecimalSeparator"];
		var _group_separator = _viewstate["NumberFormat"]["GroupSeparator"];
		var _group_size = _viewstate["NumberFormat"]["GroupSize"];
		var _pattern = (_value<0)?_viewstate["NumberFormat"]["NegativePattern"]:_viewstate["NumberFormat"]["PositivePattern"];
		if(_group_separator==null) _group_separator = " ";
		if(_pattern==null) _pattern = "n";
		if(_decimal_digits==null) _decimal_digits = 0;
		if(_decimal_digits!=null)
		{
			_value *= Math.pow(10,_decimal_digits);
			if(_viewstate["NumberFormat"]["AllowRounding"]==true)
			{
				_value = Math.round(_value);
			}
			else
			{
				_value = (_value!=0)?(_value/Math.abs(_value))*Math.floor(Math.abs(_value)):0;
			}
			_value/=Math.pow(10,_decimal_digits);
		}
		var _value_text = _value.toString();
		var _stop_position = _index(".",_value_text);
		var _num_phrase="";
		var _decimal_phrase="";
		if(_stop_position<0)
		{
			_decimal_phrase="";
			_num_phrase=_value_text;
		}
		else
		{
			_num_phrase = _value_text.slice(0,_stop_position);
			_decimal_phrase = _value_text.slice(_stop_position+1);			
		}
		for(i=_decimal_phrase.length;i<_decimal_digits;i++)
		{
			_decimal_phrase+="0";				
		}		
		if (_group_size!=null)
		{
			var _cpos=_num_phrase.length;
			while(_cpos>_group_size)
			{
				_cpos-=_group_size;
				_num_phrase = _num_phrase.slice(0,_cpos)+_group_separator+_num_phrase.slice(_cpos);
			}
		}
		_value_text = _num_phrase+ ((_decimal_digits>0)?".":"") + _decimal_phrase;
		if(_decimal_separator!=null)
		{
			_value_text = _value_text.replace(".",_decimal_separator);							
		}
		_value_text = (_value<0)?_value_text.replace("-",""):_value_text;
		_this.value = _pattern.replace("n",_value_text);
		var _parent = _goParentNode(_this);
		if(_index("kfrEmpty",_getClass(_parent))>-1)
		{
			_removeClass(_parent,"kfrEmpty");
		}		
	},
	_handle_onfocus:function(_e)
	{
		var _this = _obj(this._id);
		var _parent = _goParentNode(_this);
		var _viewstate = _loadViewState(this._id);
		_viewstate["Focus"] = true;
		_saveViewState(this._id,_viewstate);
		if(_index("kfrEmpty",_getClass(_parent))>-1)
		{
			_removeClass(_parent,"kfrEmpty");
			_this.value = "";
		}
		else
		{
			var _viewstate = _loadViewState(this._id);
			_this.value = _viewstate["Value"];
			_this.selectionStart = 0;
			_this.selectionEnd = _this.value.length;
		}
		if (_index("ie",_getBrowser())>-1)
		{
			_addClass(_goParentNode(_this,4),"kfrFocus");
		}		
	},
	_handle_onblur:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		_viewstate["Focus"] = false;
		if(_this.value=="")
		{
			var _span = _goParentNode(_this);
			_addClass(_span,"kfrEmpty");
			_this.value = _viewstate["EmptyMessage"];
		}
		else
		{
			this._show_display_value(_viewstate["Value"]);
		}
		if(_viewstate["OldValue"]!=_viewstate["Value"])
		{
			this._handle_clientevent("OnChange",{"OldValue":_viewstate["OldValue"],"NewValue":_viewstate["Value"]});
			_viewstate["OldValue"] = _viewstate["Value"];
		}
		_saveViewState(this._id,_viewstate);
		if (_index("ie",_getBrowser())>-1)
		{
			_removeClass(_goParentNode(_this,4),"kfrFocus");
		}
	},
	_handle_onkeypress:function(_e)
	{
		var _viewstate = _loadViewState(this._id);
		var _this = _obj(this._id);
		if(_getBrowser()=="firefox"||_getBrowser()=="opera")
		{
			var _code=_e.keyCode;
			if(_code==8||_code==9||_code==13||(_code>36&&_code<41))
			{
				return;
			}				
		}
		var _maxlength = _viewstate["MaxLength"];
		if(_maxlength!=null && _this.value.length>=_maxlength)
		{
			return _preventDefaut(_e);			
		}
		var _code = (_getBrowser()=="firefox")?_e.charCode:_e.keyCode;
		var _comming_value = _this.value;
		_comming_value = _comming_value.slice(0,_this.selectionStart)+ String.fromCharCode(_code)+_comming_value.slice(_this.selectionEnd);			
		var _decimal_separator = (_viewstate["NumberFormat"]["DecimalSeparator"]!=null)?_viewstate["NumberFormat"]["DecimalSeparator"]:".";
		var _rex = eval("/^[-+]?\\d*\\" +_decimal_separator+ "?\\d*$/");
		if(!_rex.test(_comming_value))
		{
			return _preventDefaut(_e);
		}
	},
	_increment:function(_direction)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _value =  (_this.value=="")?0: parseFloat(_this.value);
		_value = _value+_direction*_viewstate["IncrementSettings"]["Step"];
		if(_viewstate["MaxValue"]!=null && _value>_viewstate["MaxValue"]) return;
		if(_viewstate["MinValue"]!=null && _value<_viewstate["MinValue"]) return;
		_viewstate["Value"] = _value;
		_this.value = _value;	
		_saveViewState(this._id,_viewstate);			
	},
	_handle_onkeydown:function(_e)
	{
		switch(_e.keyCode)
		{
			case 38:
				var _viewstate = _loadViewState(this._id);
				if(_viewstate["IncrementSettings"]["InterceptArrowKeys"])
				{
					this._increment(1);
					return _preventDefaut(_e);					
				}
				break;
			case 40:
				var _viewstate = _loadViewState(this._id);
				if(_viewstate["IncrementSettings"]["InterceptArrowKeys"])
				{
					this._increment(-1);
					return _preventDefaut(_e);					
				}
				break;				
		}
	},
	_handle_onkeyup:function(_e)
	{
		var _this = _obj(this._id);
		var _sub = _obj(this._id+"_sub");
		var _viewstate = _loadViewState(this._id);
			var _decimal_separator = (_viewstate["NumberFormat"]["DecimalSeparator"]!=null)?_viewstate["NumberFormat"]["DecimalSeparator"]:".";
			if(_this.value!="")
			{
				var _value = _this.value.replace(_decimal_separator,".");			
				_viewstate["Value"] = parseFloat(_value);
			}
			else
			{
				_viewstate["Value"] = "";
			}
			_sub.value = _viewstate["Value"];
			_saveViewState(this._id,_viewstate);	
	},
	_handle_spinup_onclick:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		_this.value = _viewstate["Value"];
		this._increment(1);
		this._show_display_value();
	},
	_handle_spindown_onclick:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		_this.value = _viewstate["Value"];
		this._increment(-1);
		this._show_display_value();
	},	
	_handle_onmousewheel:function(_e)	
	{
		var _viewstate = _loadViewState(this._id);
		if(!_viewstate["Focus"]) return;		
		var _scroll_value=0;
		if(_e.wheelDelta)
		{
			_scroll_value = _e.wheelDelta/120;
		}
		else if(_e.detail)
		{
			_scroll_value = _e.detail/-3;
		}
		this._increment(_scroll_value);
		return _preventDefaut(_e);					
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}			
}
/*
function KoolNumericTextBox(_id)
{
	this._id = _id;
}
KoolNumericTextBox.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _span = _newNode("span",_parent);
		_addClass(_span,"kfrTextBoxWrapper");
		_setClass(_this,"kfrTextBox kfrRoundCorner");
		_parent.insertBefore(_span,_this);
		_span.appendChild(_this);		
		_input_value = _newNode("input",_span);
		_input_value.type="text";
		_input_value.id=this._id;
		_setClass(_input_value,"kfrHide");
		_this.id = this._id+"_sub";
		_input_value.name = _this.name;
		_this.name="";
		_input_value.style.cssText = _this.style.cssText;
		if(_viewstate["ShowSpinButton"]==true)
		{
			var _table = _newNode("table",_parent);
			_parent.insertBefore(_table,_span);
			_setClass(_table,"kfrTable");
			_table.cellPadding = "0";
			_table.cellSpacing = "0";			
			var _tr = _newNode("tr",_table);
			if(_viewstate["SpinButtonPosition"].toLowerCase()=="left")
			{
				var _td = _newNode("td",_tr);
				var _a = _newNode("a",_td);
				_a.id = this._id+"_spinup";
				_setClass(_a,"kfrSpinUp");
				var _span_spin = _newNode("span",_a);
				_span_spin.innerText = "SpinUp";
				var _a = _newNode("a",_td);
				_a.id = this._id+"_spindown";
				_setClass(_a,"kfrSpinDown");
				var _span_spin = _newNode("span",_a);
				_span_spin.innerText = "SpinDown";
			}
			var _td = _newNode("td",_tr);
			_td.appendChild(_span);			
			if(_viewstate["SpinButtonPosition"].toLowerCase()!="left")
			{
				var _td = _newNode("td",_tr);
				var _a = _newNode("a",_td);
				_a.id = this._id+"_spinup";
				_setClass(_a,"kfrSpinUp");
				var _span_spin = _newNode("span",_a);
				_span_spin.innerHTML = "SpinUp";
				var _a = _newNode("a",_td);
				_a.id = this._id+"_spindown";
				_setClass(_a,"kfrSpinDown");
				var _span_spin = _newNode("span",_a);
				_span_spin.innerHTML = "SpinDown";
			}
		}
		var _this = _obj(this._id);
		var _sub = _obj(this._id+"_sub");
		if (_index("ie",_getBrowser())>-1)
		{
			_fix_rounded_for_ie(_goParentNode(_span),_span,_sub);
		}
		if(_viewstate["ToolTip"])
		{
			_sub.title = _viewstate["ToolTip"];				
		}
		if(_viewstate["CssClass"])
		{
		 	_addClass(_sub,_viewstate["CssClass"]);
		}			
		_sub.autocomplete = _viewstate["AutoComplete"]?"on":"off";
		if(_viewstate["StatePersistent"])
		{
			_sub.value = _viewstate["Value"];
			_this.value = _viewstate["Value"];
		}
		if(_sub.value=="")
		{
			var _span = _goParentNode(_this);
			_addClass(_span,"kfrEmpty");
			_sub.value = _viewstate["EmptyMessage"];
		}
		else
		{
			_viewstate["Value"] = _sub.value;
			_this.value = _viewstate["Value"];			
			_saveViewState(this._id,_viewstate);
			this._show_display_value();			
		}
		_viewstate["OldValue"] = _viewstate["Value"];
		_saveViewState(this._id,_viewstate);
		_addEvent(_sub,"focus",_sub_onfocus);
		_addEvent(_sub,"blur",_sub_onblur);
		_addEvent(_sub,"keypress",_sub_onkeypress);
		_addEvent(_sub,"keydown",_sub_onkeydown);
		_addEvent(_sub,"keyup",_sub_onkeyup);
		if(_viewstate["IncrementSettings"]["InterceptMouseWheel"])
		{
			_addEvent(_sub,"mousewheel",_sub_onmousewheel);
			_addEvent(_sub,"DOMMouseScroll",_sub_onmousewheel);			
		}
		if (_viewstate["ShowSpinButton"] == true) 
		{
			_addEvent(_obj(this._id+"_spinup"),"click",_spinup_onclick);
			_addEvent(_obj(this._id+"_spindown"),"click",_spindown_onclick);		
		}
	},
	get_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		return _viewstate["Value"];
	},
	set_value:function(_value)
	{
		var _viewstate = _loadViewState(this._id);
		_viewstate["Value"] = _value;
		_saveViewState(this._id,_viewstate);
		this._show_display_value();
	},
	_show_display_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _sub = _obj(this._id+"_sub");
		_value = _viewstate["Value"];
		var _decimal_digits = _viewstate["NumberFormat"]["DecimalDigits"];
		var _decimal_separator = _viewstate["NumberFormat"]["DecimalSeparator"];
		var _group_separator = _viewstate["NumberFormat"]["GroupSeparator"];
		var _group_size = _viewstate["NumberFormat"]["GroupSize"];
		var _pattern = (_value<0)?_viewstate["NumberFormat"]["NegativePattern"]:_viewstate["NumberFormat"]["PositivePattern"];
		if(_group_separator==null) _group_separator = " ";
		if(_pattern==null) _pattern = "n";
		if(_decimal_digits==null) _decimal_digits = 0;
		if(_decimal_digits!=null)
		{
			_value *= Math.pow(10,_decimal_digits);
			if(_viewstate["NumberFormat"]["AllowRounding"]==true)
			{
				_value = Math.round(_value);
			}
			else
			{
				_value = (_value!=0)?(_value/Math.abs(_value))*Math.floor(Math.abs(_value)):0;
			}
			_value/=Math.pow(10,_decimal_digits);
		}
		var _value_text = _value.toString();
		var _stop_position = _index(".",_value_text);
		var _num_phrase="";
		var _decimal_phrase="";
		if(_stop_position<0)
		{
			_decimal_phrase="";
			_num_phrase=_value_text;
		}
		else
		{
			_num_phrase = _value_text.slice(0,_stop_position);
			_decimal_phrase = _value_text.slice(_stop_position+1);			
		}
		for(i=_decimal_phrase.length;i<_decimal_digits;i++)
		{
			_decimal_phrase+="0";				
		}		
		if (_group_size!=null)
		{
			var _cpos=_num_phrase.length;
			while(_cpos>_group_size)
			{
				_cpos-=_group_size;
				_num_phrase = _num_phrase.slice(0,_cpos)+_group_separator+_num_phrase.slice(_cpos);
			}
		}
		_value_text = _num_phrase+ ((_decimal_digits>0)?".":"") + _decimal_phrase;
		if(_decimal_separator!=null)
		{
			_value_text = _value_text.replace(".",_decimal_separator);							
		}
		_value_text = (_value<0)?_value_text.replace("-",""):_value_text;
		_sub.value = _pattern.replace("n",_value_text);
		var _parent = _goParentNode(_sub);
		if(_index("kfrEmpty",_getClass(_parent))>-1)
		{
			_removeClass(_parent,"kfrEmpty");
		}		
	},
	_handle_onfocus:function(_e)
	{
		var _sub = _obj(this._id+"_sub");
		var _parent = _goParentNode(_sub);
		var _viewstate = _loadViewState(this._id);
		_viewstate["Focus"] = true;
		_saveViewState(this._id,_viewstate);
		if(_index("kfrEmpty",_getClass(_parent))>-1)
		{
			_removeClass(_parent,"kfrEmpty");
			_sub.value = "";
		}
		else
		{
			var _viewstate = _loadViewState(this._id);
			_sub.value = _viewstate["Value"];
			_sub.selectionStart = 0;
			_sub.selectionEnd = _sub.value.length;
		}
		if (_index("ie",_getBrowser())>-1)
		{
			_addClass(_goParentNode(_sub,4),"kfrFocus");
		}		
	},
	_handle_onblur:function(_e)
	{
		var _sub = _obj(this._id+"_sub");
		var _viewstate = _loadViewState(this._id);
		_viewstate["Focus"] = false;
		if(_sub.value=="")
		{
			var _span = _goParentNode(_sub);
			_addClass(_span,"kfrEmpty");
			_sub.value = _viewstate["EmptyMessage"];
		}
		else
		{
			this._show_display_value(_viewstate["Value"]);
		}
		if(_viewstate["OldValue"]!=_viewstate["Value"])
		{
			this._handle_clientevent("OnChange",{"OldValue":_viewstate["OldValue"],"NewValue":_viewstate["Value"]});
			_viewstate["OldValue"] = _viewstate["Value"];
		}
		_saveViewState(this._id,_viewstate);
		if (_index("ie",_getBrowser())>-1)
		{
			_removeClass(_goParentNode(_sub,4),"kfrFocus");
		}
	},
	_handle_onkeypress:function(_e)
	{
		var _viewstate = _loadViewState(this._id);
		var _sub = _obj(this._id+"_sub");
		if(_getBrowser()=="firefox"||_getBrowser()=="opera")
		{
			var _code=_e.keyCode;
			if(_code==8||_code==9||_code==13||(_code>36&&_code<41))
			{
				return;
			}				
		}
		var _maxlength = _viewstate["MaxLength"];
		if(_maxlength!=null && _sub.value.length>=_maxlength)
		{
			return _preventDefaut(_e);			
		}
		var _code = (_getBrowser()=="firefox")?_e.charCode:_e.keyCode;
		var _comming_value = _sub.value;
		_comming_value = _comming_value.slice(0,_sub.selectionStart)+ String.fromCharCode(_code)+_comming_value.slice(_sub.selectionEnd);			
		var _decimal_separator = (_viewstate["NumberFormat"]["DecimalSeparator"]!=null)?_viewstate["NumberFormat"]["DecimalSeparator"]:".";
		var _rex = eval("/^[-+]?\\d*\\" +_decimal_separator+ "?\\d*$/");
		if(!_rex.test(_comming_value))
		{
			return _preventDefaut(_e);
		}
	},
	_increment:function(_direction)
	{
		var _this = _obj(this._id);
		var _sub = _obj(this._id+"_sub");
		var _viewstate = _loadViewState(this._id);
		var _value =  (_sub.value=="")?0: parseFloat(_sub.value);
		_value = _value+_direction*_viewstate["IncrementSettings"]["Step"];
		_viewstate["Value"] = _value;
		_this.value = _value;	
		_sub.value = _value;
		_saveViewState(this._id,_viewstate);
	},
	_handle_onkeydown:function(_e)
	{
		switch(_e.keyCode)
		{
			case 38:
				var _viewstate = _loadViewState(this._id);
				if(_viewstate["IncrementSettings"]["InterceptArrowKeys"])
				{
					this._increment(1);
					return _preventDefaut(_e);					
				}
				break;
			case 40:
				var _viewstate = _loadViewState(this._id);
				if(_viewstate["IncrementSettings"]["InterceptArrowKeys"])
				{
					this._increment(-1);
					return _preventDefaut(_e);					
				}
				break;				
		}
	},
	_handle_onkeyup:function(_e)
	{
		var _this = _obj(this._id);
		var _sub = _obj(this._id+"_sub");
		var _viewstate = _loadViewState(this._id);
			var _decimal_separator = (_viewstate["NumberFormat"]["DecimalSeparator"]!=null)?_viewstate["NumberFormat"]["DecimalSeparator"]:".";
			if(_sub.value!="")
			{
				var _value = _sub.value.replace(_decimal_separator,".");			
				_viewstate["Value"] = parseFloat(_value);				
			}
			else
			{
				_viewstate["Value"] = "";
			}
			_this.value = _viewstate["Value"];
			_saveViewState(this._id,_viewstate);	
	},
	_handle_spinup_onclick:function(_e)
	{
		var _sub = _obj(this._id+"_sub");
		var _viewstate = _loadViewState(this._id);
		_sub.value = _viewstate["Value"];
		this._increment(1);
		this._show_display_value();
	},
	_handle_spindown_onclick:function(_e)
	{
		var _sub = _obj(this._id+"_sub");
		var _viewstate = _loadViewState(this._id);
		_sub.value = _viewstate["Value"];
		this._increment(-1);
		this._show_display_value();
	},	
	_handle_onmousewheel:function(_e)	
	{
		var _viewstate = _loadViewState(this._id);
		if(!_viewstate["Focus"]) return;		
		var _scroll_value=0;
		if(_e.wheelDelta)
		{
			_scroll_value = _e.wheelDelta/120;
		}
		else if(_e.detail)
		{
			_scroll_value = _e.detail/-3;
		}
		this._increment(_scroll_value);
		return _preventDefaut(_e);					
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}			
}
*/
function KoolPasswordTextBox(_id)
{
	this._id = _id;
}
KoolPasswordTextBox.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _span = _newNode("span",_parent);
		_addClass(_span,"kfrTextBoxWrapper");
		_setClass(_this,"kfrTextBox kfrRoundCorner");
		_parent.insertBefore(_span,_this);
		_span.appendChild(_this);
		_this.autocomplete = _viewstate["AutoComplete"]?"on":"off";
		if(_viewstate["ShowIndicator"]==true)
		{
			var _indicator = _obj(_viewstate["IndicatorElementId"]);
			if(_indicator==null)
			{
				_indicator = _newNode("span",_parent);				
				_indicator.id = this._id+"_indicator";
				_viewstate["IndicatorElementId"] = _indicator.id;
				_saveViewState(this._id,_viewstate);
				_parent.insertBefore(_indicator,_span);
				_parent.insertBefore(_span,_indicator);				
			}
			_setClass(_indicator,"kfrIndicator kfrIndicator_Score_0");
			_indicator.style.width = _viewstate["IndicatorWidth"];
		}
		if(_viewstate["ToolTip"])
		{
			_this.title = _viewstate["ToolTip"];				
		}
		if(_viewstate["CssClass"])
		{
		 	_addClass(_this,_viewstate["CssClass"]);
		}			
		if (_index("ie",_getBrowser())>-1)
		{
			_fix_rounded_for_ie(_parent,_span,_this);
		}
		else
		{
			if(_this.value=="")
			{
				var _span = _goParentNode(_this);
				_addClass(_span,"kfrEmpty");
				_this.type = "text";
				_this.value = _viewstate["EmptyMessage"];
			}				
		}	
		_addEvent(_this,"focus",_this_onfocus);
		_addEvent(_this,"blur",_this_onblur);
		_addEvent(_this,"keyup",_this_onkeyup);
	},
	get_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		return decodeURI(_viewstate["Value"]);
	},
	set_strength_score:function(_score)
	{
		var _viewstate = _loadViewState(this._id);
		_viewstate["StrengthScore"]=_score;
		_saveViewState(this._id,_viewstate);
		if(_viewstate["ShowIndicator"])
		{
			var _indicator = _obj(_viewstate["IndicatorElementId"]);
			_setClass(_indicator,"kfrIndicator");
			if(_viewstate["TextStrengthDescriptionStyles"]!=null)
			{
				var _list = _viewstate["TextStrengthDescriptionStyles"].split(";");
				_addClass(_indicator,_list[(_score>0)?(_score-1):0]);
			}
			else
			{
				_addClass(_indicator,"kfrIndicator_Score_"+_score.toString());				
			}
			var _strength_text_list = _viewstate["TextStrengthDescriptions"].split(";");
			if(_score>=_strength_text_list.length)
			{
				this.set_indicator_text(_strength_text_list[_strength_text_list.length-1]);			
			}
			else
			{
				this.set_indicator_text(_strength_text_list[(_score>0)?(_score-1):0]);	
			}
		}
	},
	get_strength_score:function()
	{
		var _viewstate = _loadViewState(this._id);
		return (_viewstate["StrengthScore"])?_viewstate["StrengthScore"]:0;		
	},	
	set_indicator_text:function(_text)
	{
		var _viewstate = _loadViewState(this._id);
		if (_viewstate["ShowIndicator"]) 
		{
			var _indicator = _obj(_viewstate["IndicatorElementId"]);
			_indicator.innerHTML = _text;
		}
	},	
	_handle_onfocus:function(_e)
	{
		var _this = _obj(this._id);
		var _span = _goParentNode(_this);
		if(_index("kfrEmpty",_getClass(_span))>-1  && _index("ie",_getBrowser())<0)
		{
			_removeClass(_span,"kfrEmpty");
			_this.type="password";
			_this.value = "";
		}
		if (_index("ie",_getBrowser())>-1)
		{
			_addClass(_goParentNode(_this,4),"kfrFocus");
		}	},
	_handle_onblur:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		_viewstate["Value"] = encodeURI(_this.value);
		_saveViewState(this._id,_viewstate);		
		if (_index("ie",_getBrowser())>-1)
		{
			_removeClass(_goParentNode(_this,4),"kfrFocus");
		}
		else
		{
			if(_this.value=="")
			{
				var _span = _goParentNode(_this);
				_addClass(_span,"kfrEmpty");
				_this.type = "text";
				_this.value = _viewstate["EmptyMessage"];
			}			
		}
	},
	_calculate_strength:function(_text)
	{
		if(_text=="") return 0;
		var _viewstate = _loadViewState(this._id);
		var _weighs = _viewstate["CalculationWeightings"].split(";");
		var _total=0;
		for(i=0;i<_weighs.length;i++)
		{
			_weighs[i] = parseFloat(_weighs[i]);
			_total+=_weighs[i];
		}
		for(i=0;i<_weighs.length;i++)
		{
			_weighs[i] = _weighs[i]/_total;
		}
		var _prefer_length = (_viewstate["PreferredPasswordLength"])?_viewstate["PreferredPasswordLength"]:8;
		var _min_numeric = (_viewstate["MinimumNumericCharacters"])?_viewstate["MinimumNumericCharacters"]:2; 
		var _min_uppercase = (_viewstate["MinimumUpperCaseCharacters"])?_viewstate["MinimumUpperCaseCharacters"]:2; 
		var _min_symbol = (_viewstate["MinimumSymbolCharacters"])?_viewstate["MinimumSymbolCharacters"]:2; 
		var _num_numeric=0,_num_uppercase=0,_num_symbol=0;
		for(i=0;i<_text.length;i++)
		{
			var _code = _text.charCodeAt(i);
			if((32<_code&&_code<48)||(57<_code&&_code<65)||(90<_code&&_code<97)||(122<_code&&_code<127)) _num_symbol++;
			if(47<_code&&_code<58) _num_numeric++;
			if(64<_code&&_code<91) _num_uppercase++;
		}
		var _text_length = _text.length;
		if(_text_length>_prefer_length) _text_length = _prefer_length;
		if(_num_numeric>_min_numeric) _num_numeric = _min_numeric;
		if(_num_uppercase>_min_uppercase) _num_uppercase = _min_uppercase;
		if(_num_symbol>_min_symbol) _num_symbol = _min_symbol;
		var _strength = ((_text_length/_prefer_length)*5*_weighs[0]
						+(_num_numeric/_min_numeric)*5*_weighs[1]
						+(_num_uppercase/_min_uppercase)*5*_weighs[2]
						+(_num_symbol/_min_symbol)*5*_weighs[3]);
		return Math.round(_strength);	
	},
	_handle_onkeyup:function(_e)
	{
		var _viewstate = _loadViewState(this._id);
		var _this = _obj(this._id);
		_viewstate["Value"]= encodeURI(_this.value);
		_saveViewState(this._id,_viewstate);
		var _code =_e.keyCode;
		var _score = this._calculate_strength(_this.value);
		if(!this.validate()) _score=0;
		if(this._handle_clientevent("OnBeforeSetStrengthScore",{"Score":_score}))
		{
			this.set_strength_score(_score);
			this._handle_clientevent("OnSetStrengthScore",{"Score":_score});			
		}
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		var _text = this.get_value();
		var _viewstate = _loadViewState(this._id);
		var _prefer_length = (_viewstate["PreferredPasswordLength"])?_viewstate["PreferredPasswordLength"]:8;
		var _min_numeric = (_viewstate["MinimumNumericCharacters"])?_viewstate["MinimumNumericCharacters"]:0; 
		var _min_uppercase = (_viewstate["MinimumUpperCaseCharacters"])?_viewstate["MinimumUpperCaseCharacters"]:0; 
		var _min_symbol = (_viewstate["MinimumSymbolCharacters"])?_viewstate["MinimumSymbolCharacters"]:0; 
		var _num_numeric=0,_num_uppercase=0,_num_symbol=0;
		for(i=0;i<_text.length;i++)
		{
			var _code = _text.charCodeAt(i);
			if((32<_code&&_code<48)||(57<_code&&_code<65)||(90<_code&&_code<97)||(122<_code&&_code<127)) _num_symbol++;
			if(47<_code&&_code<58) _num_numeric++;
			if(64<_code&&_code<91) _num_uppercase++;
		}
		var _text_length = _text.length;
		if(_text_length<_prefer_length || _num_numeric<_min_numeric || _num_uppercase<_min_uppercase || _num_symbol<_min_symbol)
		{
			return false;
		}		
		return this._handle_clientevent("OnValidate",{});
	}			
}
function KoolDateTextBox(_id)
{
	this._id = _id;
}
KoolDateTextBox.prototype = {
	_setup:function()
	{
	},
	get_value:function()
	{
	},
	set_value:function(_value)
	{
	},	
	_handle_onfocus:function(_e)
	{
	},
	_handle_onblur:function(_e)
	{
	},
	_handle_onkeypress:function(_e)
	{
	}
}
function KoolRadioButton(_id)
{
	this._id = _id;
}
KoolRadioButton.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(!_this)
		{
			return;
		}
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _span = _newNode("span",_parent);
		_setClass(_span,"kfrRadio");
		_parent.insertBefore(_span,_this);
		_span.appendChild(_this);
		_addClass(_this,"kfrHide");
		var _label = _obj(this._id+"_label");
		if(_label==null)
		{
			var _formid = _getFormId(_this);
			var _form = _obj(_formid);
			var _labels = _getElements("label","",_form);
			for(var i=0;i<_labels.length;i++)
			{
				if (_labels[i].getAttribute("for")==this._id)
				{
					_label = _labels[i];
				}
			}
			if(_label==null)
			{
				_label = _newNode("label",_parent);
				_label.setAttribute("for",this._id);
				_parent.insertBefore(_label,_span);
				_parent.insertBefore(_span,_label);					
			}
			_label.id = this._id+"_label";
		}
		_setClass(_label,"kfrLabel");
		var _sub = _newNode("label",_span);
		_sub.id = this._id+"_sub";
		_sub.setAttribute("for",this._id);
		_setClass(_sub,"kfrSub");
		if(_viewstate)
		{
			_label.innerHTML = _viewstate["Text"];
			if(_viewstate["CssClass"])
			{
				_addClass(_label,_viewstate["CssClass"]);
			}
			this.select(_viewstate["Selected"]);			
			if(_viewstate["ToolTip"])
			{
				_sub.title = _viewstate["ToolTip"];	
				_label.title = _viewstate["ToolTip"];				
			}
			if(_viewstate["CssClass"])
			{
			 	_addClass(_label,_viewstate["CssClass"]);
			}			
			if(_viewstate["Enabled"]!=null)
			{
				this.enable(_viewstate["Enabled"]);
			}
		}
		else
		{
			this.select(_this.checked);
		}
		_addEvent(_label,"mouseover",_label_mouseover);
		_addEvent(_label,"mouseout",_label_mouseout);
		_addEvent(_sub,"mouseover",_sub_mouseover);
		_addEvent(_sub,"mouseout",_sub_mouseout);
		_addEvent(_this,"change",_radio_change);
		_addEvent(_this,"focus",_this_onfocus);
	},
	select:function(_bool)
	{
		var _this = _obj(this._id);
		var _span = _goParentNode(_this);
		if (_bool) 
		{
			_addClass(_span, "kfrRadioSelected");
		}
		else
		{
			_removeClass(_span, "kfrRadioSelected");
		}
		_this.checked = _bool;
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			_viewstate["Selected"] = _bool;	
			if(_viewstate["OldValue"]!=null)
			{
				if(_viewstate["OldValue"]!=_bool)
				{
					this._handle_clientevent("OnChange",{"NewValue":_bool});
					_viewstate["OldValue"] = _bool;
				}
			}
			else
			{
				_viewstate["OldValue"] = _bool;				
			}
			_saveViewState(this._id,_viewstate);			
		}
		_radio_change(null,_this);
	},
	is_selected:function()
	{
		var _this = _obj(this._id);
		return _this.checked;	
	},
	_self_sync:function()
	{
		var _this = _obj(this._id);
		var _span = _goParentNode(_this);
		if(_this.checked)
		{
			_addClass(_span, "kfrRadioSelected");			
		}
		else
		{
			_removeClass(_span, "kfrRadioSelected");
		}
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			_viewstate["Selected"] = _this.checked;
			if(_viewstate["OldValue"]!=null)
			{
				if(_viewstate["OldValue"]!=_this.checked)
				{
					this._handle_clientevent("OnChange",{"NewValue":_this.checked});
					_viewstate["OldValue"] = _this.checked;
				}
			}
			else
			{
				_viewstate["OldValue"] = _this.checked;				
			}
			_saveViewState(this._id,_viewstate);			
		}		
	},
	enable:function(_bool)
	{
		var _this = _obj(this._id);
		var _label = _obj(this._id+"_label");
		var _sub = _obj(this._id+"_sub");
		if (_bool) 
		{
			_removeClass(_sub, "kfrDisabled");
			_removeClass(_label, "kfrDisabled");
		}
		else
		{
			_addClass(_sub, "kfrDisabled");
			_addClass(_label, "kfrDisabled");
		}
		_this.disabled = !_bool;
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			_viewstate["Enabled"] = _bool;
			_saveViewState(this._id,_viewstate);			
		}
	},
	_handle_mouseover:function(_e)
	{
		var _sub = _obj(this._id+"_sub");
		_addClass(_sub,"kfrOver");
	},
	_handle_mouseout:function(_e)
	{
		var _sub = _obj(this._id+"_sub");
		_removeClass(_sub,"kfrOver");
	},
	/*	
	_handle_mouseclick:function(_e)
	{
		var _this = _obj(this._id);
		if(_this.disabled==false)
		{
			this.select(1);
			_radio_change(_e,_this);
		}
	},
	*/
	_handle_change:function(_e)
	{
		var _this = _obj(this._id);
		this._self_sync();
	},
	_handle_onfocus:function(_e)
	{
		var _this = _obj(this._id);
		_this.blur();
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}				
}
function KoolCheckBox(_id)
{
	this._id = _id;
}
KoolCheckBox.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(!_this) return;
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _span = _newNode("span",_parent);
		_setClass(_span,"kfrCheckBox");
		_parent.insertBefore(_span,_this);
		_span.appendChild(_this);
		_addClass(_this,"kfrHide");	
		var _label = _obj(this._id+"_label");
		if(_label==null)
		{
			var _formid = _getFormId(_this);
			var _form = _obj(_formid);
			var _labels = _getElements("label","",_form);
			for(var i=0;i<_labels.length;i++)
			{
				if (_labels[i].getAttribute("for")==this._id)
				{
					_label = _labels[i];
				}
			}
			if(_label==null)
			{
				_label = _newNode("label",_parent);
				_label.setAttribute("for",this._id);
				_parent.insertBefore(_label,_span);
				_parent.insertBefore(_span,_label);					
			}
			_label.id = this._id+"_label";
		}
		_setClass(_label,"kfrLabel");
		var _sub = _newNode("label",_span);
		_sub.id = this._id+"_sub";
		_sub.setAttribute("for",this._id);
		_setClass(_sub,"kfrSub");		
		if(_viewstate)
		{
			_label.innerHTML = _viewstate["Text"];
			if(_viewstate["CssClass"])
			{
				_addClass(_label,_viewstate["CssClass"]);
			}
			this.select(_viewstate["Selected"]);			
			if(_viewstate["ToolTip"])
			{
				_sub.title = _viewstate["ToolTip"];	
				_label.title = _viewstate["ToolTip"];				
			}
			if(_viewstate["CssClass"])
			{
			 	_addClass(_label,_viewstate["CssClass"]);
			}
			if(_viewstate["Enabled"]!=null)
			{
				this.enable(_viewstate["Enabled"]);
			}									
		}
		else
		{
			this.select(_this.checked);
		}
		_addEvent(_label,"mouseover",_label_mouseover);
		_addEvent(_label,"mouseout",_label_mouseout);
		_addEvent(_sub,"mouseover",_sub_mouseover);
		_addEvent(_sub,"mouseout",_sub_mouseout);
		_addEvent(_this,"change",_this_change);
		_addEvent(_this,"focus",_this_onfocus);
	},
	select:function(_bool)
	{
		var _this = _obj(this._id);
		var _span = _goParentNode(_this);
		var _viewstate = _loadViewState(this._id);
		if (_bool) 
		{
			_addClass(_span, "kfrCheckBoxSelected");
		}
		else
		{
			_removeClass(_span, "kfrCheckBoxSelected");
		}
		_this.checked = _bool;
		if(_viewstate)
		{
			_viewstate["Selected"] = _bool;
			if(_viewstate["OldValue"]!=null)
			{
				if(_viewstate["OldValue"]!=_bool)
				{
					this._handle_clientevent("OnChange",{"NewValue":_bool});
					_viewstate["OldValue"] = _bool;
				}
			}
			else
			{
				_viewstate["OldValue"] = _bool;				
			}
			_saveViewState(this._id,_viewstate);			
		}
	},
	is_selected:function()
	{
		var _this = _obj(this._id);
		return _this.checked;
	},
	enable:function(_bool)
	{
		var _this = _obj(this._id);
		var _label = _obj(this._id+"_label");
		var _sub = _obj(this._id+"_sub");
		var _viewstate = _loadViewState(this._id);
		if (_bool) 
		{
			_removeClass(_sub, "kfrDisabled");
			_removeClass(_label, "kfrDisabled");
		}
		else
		{
			_addClass(_sub, "kfrDisabled");
			_addClass(_label, "kfrDisabled");
		}
		_this.disabled = !_bool;
		if(_viewstate)
		{
			_viewstate["Enabled"] = _bool;
			_saveViewState(this._id,_viewstate);		
		}		
	},
	_handle_mouseover:function(_e)
	{
		var _sub = _obj(this._id+"_sub");
		_addClass(_sub,"kfrOver");
	},
	_handle_mouseout:function(_e)
	{
		var _sub = _obj(this._id+"_sub");
		_removeClass(_sub,"kfrOver");
	},
	/*	
	_handle_mouseclick:function(_e)
	{
		var _this = _obj(this._id);
		if(_this.disabled==false)
		{
			this.select(!this.is_selected());			
		}		
	},
	*/
	_handle_change:function(_e)
	{
		var _this = _obj(this._id);
		this.select(_this.checked);		
	},
	_handle_onfocus:function(_e)
	{
		var _this = _obj(this._id);
		_this.blur();
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}				
}
function KoolDropDownList(_id)
{
	this._id = _id;
}
KoolDropDownList.prototype = {
	_setup:function()
	{
		var _this = _obj(this._id);
		if(!_this) return;
		if(_index("kfr",_getClass(_this))>-1) return;
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _span = _newNode("span",_parent);
		_span.setAttribute("unselectable","on");
		_setClass(_span,"kfrDropDownList");
		_parent.insertBefore(_span,_this);
		var _span_out = _newNode("span",_span);
		_setClass(_span_out,"kfrOut");
		var _span_sub = _newNode("span",_span_out);
		_span_sub.id=this._id+"_sub";
		_setClass(_span_sub,"kfrSub");
		var _span_arrow = _newNode("span",_span_out);
		_setClass(_span_arrow,"kfrArrow");
		_span_out.appendChild(_this);
		_addClass(_this,"kfrRealDropDown");
		if(_viewstate)
		{
			if(_viewstate["SelectedIndex"]!=null)
			{
				_this.selectedIndex = _viewstate["SelectedIndex"];
			}
			if(_viewstate["ToolTip"])
			{
				_span.title = _viewstate["ToolTip"];			
			}
			if(_viewstate["Enabled"]!=null)
			{
				this.enable(_viewstate["Enabled"]);
			}			
		}
		_span_sub.innerHTML = this.get_selected_text();
		_addEvent(_this,"change",_this_change);
	},
	get_selected_text:function()
	{
		var _this = _obj(this._id);
		return _this.options[_this.selectedIndex].text;
	},
	get_selected_value:function()
	{
		var _this = _obj(this._id);
		return _this.options[_this.selectedIndex].value;
	},
	get_selected_index:function()
	{
		var _this = _obj(this._id);
		return _this.selectedIndex;
	},
	set_selected_index:function(_index)
	{
		var _this = _obj(this._id);
		_this.selectedIndex = _index;
		this._handle_change();		
	},
	set_selected_value:function(_value)
	{
		var _this = _obj(this._id);
		for(var i=0;i<_this.options.length;i++)
		{
			if(_this.options[i].value==_value)
			{
				_this.selectedIndex = i;
			}
		}
		this._handle_change();
	},
	set_selected_text:function(_text)
	{
		var _this = _obj(this._id);
		for(var i=0;i<_this.options.length;i++)
		{
			if(_this.options[i].text==_text)
			{
				_this.selectedIndex = i;
			}
		}
		this._handle_change();
	},	
	enable:function(_bool)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _span_out = _goParentNode(_this);
		_this.disabled = !_bool;
		if(_this.disabled)
		{
			_addClass(_span_out,"kfrDisabled");
		}
		else
		{
			_remove(_span_out,"kfrDisabled");	
		}
		if(_viewstate)
		{
			_viewstate["Enabled"] = _bool;
		}		
	},
	_handle_change:function()
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _span_sub = _obj(this._id+"_sub");
		_span_sub.innerHTML = this.get_selected_text();
		if(_viewstate)
		{
			_viewstate["SelectedIndex"] = _this.selectedIndex;
			_viewstate["SelectedValue"] = this.get_selected_value();
			_viewstate["SelectedText"] = this.get_selected_text();
			_saveViewState(this._id,_viewstate);
		}
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}					
}
function KoolLinkButton(_id)
{
	this._id = _id;
}
KoolLinkButton.prototype = 
{
	_setup:function()
	{
		var _this = _obj(this._id);
		var _text = _this.innerHTML;
		_this.innerHTML = "";
		var _viewstate = _loadViewState(this._id);
		_setClass(_this,"kfrButtonWrapper kfrButtonLink");
		var _span_out = _newNode("span",_this);
		var _this_text = _newNode("span",_span_out);
		_setClass(_this_text,"kfrButton");
		_setClass(_span_out,"kfrOut");
		_this_text.innerHTML = _text;
		_addEvent(_this,"mousedown",_link_button_mousedown);
		_addEvent(_this,"mouseup",_link_button_mouseup);
		_addEvent(_this,"mouseout",_link_button_mouseout);
		_addEvent(_this,"click",_link_button_click);
		if(_viewstate)
		{
			if(_viewstate["ToolTip"])
			{
				_a.title = _viewstate["ToolTip"];
			}
			if(_viewstate["CssClass"])
			{
			 	_addClass(_this,_viewstate["CssClass"]);
			}
			var _case_icon = 0;
			if(_viewstate["LeftImage"])
			{
				var _span_icon = _newNode("span",_span_out);
				_setClass(_span_icon,"kfrLeftImage");
				_span_icon.style.backgroundImage="url("+_viewstate["LeftImage"]+")";
				_span_out.insertBefore(_span_icon,_this_text);
				_case_icon = 1;
			}
			if(_viewstate["RightImage"])
			{
				var _span_icon = _newNode("span",_span_out);
				_span_icon.style.backgroundImage="url("+_viewstate["RightImage"]+")";
				_setClass(_span_icon,"kfrRightImage");
				_case_icon = (_case_icon==0)?2:3;
			}
			switch(_case_icon)
			{
				case 1:
					_addClass(_span_out,"kfrSpaceLeft");
					break;
				case 2:
					_addClass(_span_out,"kfrSpaceRight");
					break;
				case 3:
					_addClass(_span_out,"kfrSpaceBoth");
					break;				
			}
		}
	},
	enable:function(_bool)
	{
		var _this = _obj(this._id);
		if(_bool)
		{
			_removeClass(_this,"kfrButtonDisabled");
		}
		else
		{
			_addClass(_this,"kfrButtonDisabled");			
		}	
	},
	set_text:function(_text)
	{
		var _this_text = _obj(this._id+"_text");
		_this_text.innerHTML = _text;
	},
	get_text:function(_text)
	{
		var _this_text = _obj(this._id+"_text");
		return _this_text.innerHTML;
	},
	set_link:function(_link)
	{
		var _this = _obj(this._id);
		_this.href = _link;
	},
	get_link:function(_text)
	{
		var _this = _obj(this._id);
		return _this.href;
	},
	set_target:function(_link)
	{
		var _this = _obj(this._id);
		_this.href = _link;
	},
	get_target:function(_text)
	{
		var _this = _obj(this._id);
		return _this.href;
	},
	_handle_mouseup:function(_e)
	{
		var _this = _obj(this._id);
		_removeClass(_this,"kfrButtonDown");
	},
	_handle_mousedown:function(_e)
	{
		var _this = _obj(this._id);
		_addClass(_this,"kfrButtonDown");
	},
	_handle_mouseout:function(_e)
	{
		var _this = _obj(this._id);
		_removeClass(_this,"kfrButtonDown");
	},
	_handle_click:function(_e)
	{
		this._handle_clientevent("OnClick",{});
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}		
}
function _link_button_click(_e)
{
	return (new KoolLinkButton(this.id))._handle_click(_e);
}
function _link_button_mousedown(_e)
{
	return (new KoolLinkButton(this.id))._handle_mousedown(_e);	
}
function  _link_button_mouseup(_e)
{
	return (new KoolLinkButton(this.id))._handle_mouseup(_e);		
}
function _link_button_mouseout(_e)
{
	return (new KoolLinkButton(this.id))._handle_mouseout(_e);		
}
function KoolToggleButton(_id)
{
	this._id = _id;
}
KoolToggleButton.prototype = 
{
	_setup:function()
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _a = _newNode("a",_parent);
		_parent.insertBefore(_a,_this);
		var _span_out = _newNode("span",_a);
		_span_out.appendChild(_this);
		_setClass(_this,"kfrButton");
		_setClass(_span_out,"kfrOut");
		_setClass(_a,"kfrButtonWrapper");
		_addEvent(_this,"mousedown",_button_mousedown);
		_addEvent(_this,"mouseup",_button_mouseup);
		_addEvent(_this,"mouseout",_button_mouseout);
		_addEvent(_this,"click",_toggle_button_click);
		if(_viewstate)
		{
			this._render();	
		}
	},
	_render:function()
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _span_out = _goParentNode(_this);
		var _a = _goParentNode(_span_out);
			var _selected_index = _viewstate["SelectedIndex"];
			var _state = _viewstate["ToggleStates"][_selected_index];
			var _case_icon = 0;
			_this.value=_state["Text"];
			_setClass(_this,"kfrButton");
			if(_state["ToolTip"])
			{
				_a.title = _state["ToolTip"];
			}
			if(_state["ButtonCss"])
			{
				_addClass(_this,_state["ButtonCss"]);
			}
			if(_state["LeftImage"])
			{
				var _span_icons = _getElements("span","kfrLeftImage",_span_out);
				var _span_icon = _span_icons[0];
				if (_span_icon) 
				{
					_span_icon.style.backgroundImage="url("+_state["LeftImage"]+")";
					_setClass(_span_icon,"kfrLeftImage");
				}	
				else
				{
					_span_icon = _newNode("span",_span_out);
					_setClass(_span_icon,"kfrLeftImage");
					_span_icon.style.backgroundImage="url("+_state["LeftImage"]+")";
					_span_out.insertBefore(_span_icon,_this);
					_addEvent(_span_icon,"click",_button_icon_click);
					_addEvent(_span_icon,"mousedown",_button_mousedown);
					_addEvent(_span_icon,"mouseup",_button_mouseup);
					_addEvent(_span_icon,"mouseout",_button_mouseout);					
				}
				if(_state["LeftImageCss"])
				{
					_addClass(_span_icon,_state["LeftImageCss"]);
				}
				_case_icon = 1;
			}
			else
			{
				var _span_icons = _getElements("span","kfrLeftImage",_span_out);
				if(_span_icons[0])
				{
					_purge(_span_icons[0]);
					_span_out.removeChild(_span_icons[0]);
				}
			}
			if(_state["RightImage"])
			{
				var _span_icons = _getElements("span","kfrRightImage",_span_out);
				var _span_icon = _span_icons[0];
				if (_span_icon) 
				{
					_span_icon.style.backgroundImage="url("+_state["RightImage"]+")";
					_setClass(_span_icon,"kfrRightImage");
				}	
				else
				{
					_span_icon = _newNode("span",_span_out);
					_span_icon.style.backgroundImage="url("+_state["RightImage"]+")";
					_setClass(_span_icon,"kfrRightImage");
					_addEvent(_span_icon,"click",_button_icon_click);
					_addEvent(_span_icon,"mousedown",_button_mousedown);
					_addEvent(_span_icon,"mouseup",_button_mouseup);
					_addEvent(_span_icon,"mouseout",_button_mouseout);
				}
				if(_state["RightImageCss"])
				{
					_addClass(_span_icon,_state["RightImageCss"]);
				}				
				_case_icon = (_case_icon==0)?2:3;
			}
			else
			{
				var _span_icons = _getElements("span","kfrRightImage",_span_out);
				if(_span_icons[0])
				{
					_purge(_span_icons[0]);
					_span_out.removeChild(_span_icons[0]);
				}				
			}
			_setClass(_span_out,"kfrOut");
			switch(_case_icon)
			{
				case 1:
					_addClass(_span_out,"kfrSpaceLeft");
					break;
				case 2:
					_addClass(_span_out,"kfrSpaceRight");
					break;
				case 3:
					_addClass(_span_out,"kfrSpaceBoth");
					break;				
			}
	},
	enable:function(_bool)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this);
		_this.disabled = !_bool;
		if(_this.disabled)
		{
			_addClass(_a,"kfrButtonDisabled");
		}
		else
		{
			_addClass(_a,"kfrButtonDisabled");			
		}	
	},
	set_selected_index:function(_index)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		_viewstate["SelectedIndex"] = _index;
		_saveViewState(this._id,_viewstate);
		this._render();
	},
	get_selected_index:function()
	{
		var _viewstate = _loadViewState(this._id);
		return _viewstate["SelectedIndex"];
	},
	get_selected_text:function()
	{
		var _viewstate = _loadViewState(this._id);
		return _viewstate["ToggleStates"][_viewstate["SelectedIndex"]]["Text"];		
	},
	get_selected_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		return _viewstate["ToggleStates"][_viewstate["SelectedIndex"]]["Value"];				
	},	
	_handle_mouseup:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_removeClass(_a,"kfrButtonDown");
	},
	_handle_mousedown:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_addClass(_a,"kfrButtonDown");
	},
	_handle_mouseout:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_removeClass(_a,"kfrButtonDown");
	},
	_handle_click:function(_e)
	{
		var _viewstate = _loadViewState(this._id);
		var _selected_index = _viewstate["SelectedIndex"];
		var _total_states = _viewstate["TotalStates"];
		if(_selected_index<_total_states-1)
		{
			_selected_index++;
		}
		else
		{
			_selected_index = 0;
		}
		_viewstate["SelectedIndex"] = _selected_index;
		_viewstate["SelectedText"] = _viewstate["ToggleStates"][_selected_index]["Text"];
		_viewstate["SelectedValue"] = _viewstate["ToggleStates"][_selected_index]["Value"];
		_saveViewState(this._id,_viewstate);
		this._render();
		this._handle_clientevent("OnClick",{});
	},
	_handle_icon_click:function(_e)
	{
		var _this = _obj(this._id);
		_this.click();
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}		
}
function _toggle_button_click(_e)
{
	var _element = eval("__=new KoolToggleButton(\""+this.id+"\")");
	return _element._handle_click(_e);	
}
function KoolSplitButton(_id)
{
	this._id = _id;
}
KoolSplitButton.prototype = 
{
	_setup:function()
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _a = _newNode("a",_parent);
		_a.href = "javascript:void 0";
		_parent.insertBefore(_a,_this);
		var _span_out = _newNode("span",_a);
		_span_out.appendChild(_this);
		_setClass(_this,"kfrButton");
		_setClass(_span_out,"kfrOut");
		_setClass(_a,"kfrButtonWrapper kfrSplitButton");
		var _span_split_arrow = _newNode("span",_a);
		_span_split_arrow.id=this._id+"_arrow";
		_setClass(_span_split_arrow,"kfrSplitRight");
		_addEvent(_this,"mousedown",_button_split_mousedown);
		_addEvent(_this,"mouseup",_button_split_mouseup);
		_addEvent(_this,"mouseout",_button_split_mouseout);
		_addEvent(_this,"click",_button_split_click);
		_addEvent(_span_split_arrow,"mousedown",_button_split_arrow_mousedown);
		_addEvent(_span_split_arrow,"mouseup",_button_split_arrow_mouseup);
		_addEvent(_span_split_arrow,"mouseout",_button_split_arrow_mouseout);
		_addEvent(_span_split_arrow,"click",_button_split_arrow_click);
		if(_viewstate)
		{
			if(_viewstate["ToolTip"])
			{
				_a.title = _viewstate["ToolTip"];
			}
			if(_viewstate["CssClass"])
			{
			 	_addClass(_this,_viewstate["CssClass"]);
			}
			this._render();	
			var _div_container = _newNode("div",_a);
			_div_container.id = this._id+"_container";
			_setClass(_div_container,"kfrSplitButtonContainer");
			_setDisplay(_div_container,0);
			var _div_container_inner = _newNode("div",_div_container);
			_setClass(_div_container_inner,"kfrContainerInner");
			var _toggle_states = _viewstate["ToggleStates"];
			for(i in _toggle_states)
			{
				var _div_out = _newNode("div",_div_container_inner);
				_setClass(_div_out,"kfrOutter");
				_div_out.id=this._id+"_option"+i;
				var _div_in = _newNode("div",_div_out);
				_setClass(_div_in,"kfrInner");
				_addEvent(_div_out,"click",_button_split_option_click,false);
				if(_toggle_states[i]["LeftImage"])
				{
					var _span_icon = _newNode("span",_div_in);
					_setClass(_span_icon,"kfrLeftImage");
					_span_icon.style.backgroundImage="url("+_toggle_states[i]["LeftImage"]+")";
				}
				var _span_text = _newNode("span",_div_in);
				_setClass(_span_text,"kfrText");
				_span_text.innerHTML = _toggle_states[i]["Text"];
				if(_toggle_states[i]["RightImage"])
				{
					var _span_icon = _newNode("span",_div_in);
					_setClass(_span_icon,"kfrRightImage");
					_span_icon.style.backgroundImage="url("+_toggle_states[i]["RightImage"]+")";
				}
			}
			_ksb_closeonclick_ids.push(this._id);			
		}
	},
	_render:function()
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _span_out = _goParentNode(_this);
		var _a = _goParentNode(_span_out);
			var _selected_index = _viewstate["SelectedIndex"];
			var _state = _viewstate["ToggleStates"][_selected_index];
			var _case_icon = 0;
			_this.value=_state["Text"];
			_setClass(_this,"kfrButton");
			if(_state["ToolTip"])
			{
				_a.title = _viewstate["ToolTip"];
			}
			if(_state["ButtonCss"])
			{
				_addClass(_this,_state["ButtonCss"]);
			}
			if(_state["LeftImage"])
			{
				var _span_icons = _getElements("span","kfrLeftImage",_span_out);
				var _span_icon = _span_icons[0];
				if (_span_icon) 
				{
					_span_icon.style.backgroundImage="url("+_state["LeftImage"]+")";
					_setClass(_span_icon,"kfrLeftImage");
				}	
				else
				{
					_span_icon = _newNode("span",_span_out);
					_setClass(_span_icon,"kfrLeftImage");
					_span_icon.style.backgroundImage="url("+_state["LeftImage"]+")";
					_span_out.insertBefore(_span_icon,_this);
					_addEvent(_span_icon,"click",_button_icon_click);
					_addEvent(_span_icon,"mousedown",_button_mousedown);
					_addEvent(_span_icon,"mouseup",_button_mouseup);
					_addEvent(_span_icon,"mouseout",_button_mouseout);					
				}
				if(_state["LeftImageCss"])
				{
					_addClass(_span_icon,_state["LeftImageCss"]);
				}
				_case_icon = 1;
			}
			else
			{
				var _span_icons = _getElements("span","kfrLeftImage",_span_out);
				if(_span_icons[0])
				{
					_purge(_span_icons[0]);
					_span_out.removeChild(_span_icons[0]);
				}
			}
			if(_state["RightImage"])
			{
				var _span_icons = _getElements("span","kfrRightImage",_span_out);
				var _span_icon = _span_icons[0];
				if (_span_icon) 
				{
					_span_icon.style.backgroundImage="url("+_state["RightImage"]+")";
					_setClass(_span_icon,"kfrRightImage");
				}	
				else
				{
					_span_icon = _newNode("span",_span_out);
					_span_icon.style.backgroundImage="url("+_state["RightImage"]+")";
					_setClass(_span_icon,"kfrRightImage");
					_addEvent(_span_icon,"click",_button_icon_click);
					_addEvent(_span_icon,"mousedown",_button_mousedown);
					_addEvent(_span_icon,"mouseup",_button_mouseup);
					_addEvent(_span_icon,"mouseout",_button_mouseout);
				}
				if(_state["RightImageCss"])
				{
					_addClass(_span_icon,_state["RightImageCss"]);
				}				
				_case_icon = (_case_icon==0)?2:3;
			}
			else
			{
				var _span_icons = _getElements("span","kfrRightImage",_span_out);
				if(_span_icons[0])
				{
					_purge(_span_icons[0]);
					_span_out.removeChild(_span_icons[0]);
				}				
			}
			_setClass(_span_out,"kfrOut");
			switch(_case_icon)
			{
				case 1:
					_addClass(_span_out,"kfrSpaceLeft");
					break;
				case 2:
					_addClass(_span_out,"kfrSpaceRight");
					break;
				case 3:
					_addClass(_span_out,"kfrSpaceBoth");
					break;				
			}
	},
	enable:function(_bool)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this);
		_this.disabled = !_bool;
		if(_this.disabled)
		{
			_addClass(_a,"kfrButtonDisabled");
		}
		else
		{
			_addClass(_a,"kfrButtonDisabled");			
		}	
	},
	set_selected_index:function(_index)
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		_viewstate["SelectedIndex"] = _index;
		_saveViewState(this._id,_viewstate);
		this._render();
	},
	get_selected_index:function()
	{
		var _viewstate = _loadViewState(this._id);
		return _viewstate["SelectedIndex"];
	},
	get_selected_text:function()
	{
		var _viewstate = _loadViewState(this._id);
		return _viewstate["ToggleStates"][_viewstate["SelectedIndex"]]["Text"];		
	},
	get_selected_value:function()
	{
		var _viewstate = _loadViewState(this._id);
		return _viewstate["ToggleStates"][_viewstate["SelectedIndex"]]["Value"];				
	},	
	_handle_option_click:function(_e,_selected_index)
	{
		var _this = _obj(this._id);
		this.set_selected_index(_selected_index);
		_this.click();
	},
	_handle_mouseup:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_removeClass(_a,"kfrButtonDown");
	},
	_handle_mousedown:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_addClass(_a,"kfrButtonDown");
	},
	_handle_mouseout:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_removeClass(_a,"kfrButtonDown");
	},
	_handle_click:function(_e)
	{
		this._handle_clientevent("OnClick",{});
	},
	_handle_icon_click:function(_e)
	{
		var _this = _obj(this._id);
		_this.click();
	},
	_handle_split_arrow_mouseup:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_removeClass(_a,"kfrSplitArrowDown");		
	},
	_handle_split_arrow_mousedown:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_addClass(_a,"kfrSplitArrowDown");
	},
	_handle_split_arrow_mouseout:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_removeClass(_a,"kfrSplitArrowDown");
	},
	_handle_split_arrow_click:function(_e)
	{
		var _div_container = _obj(this._id+"_container");
		_getDisplay(_div_container)?this.collapse():this.expand();
	},
	expand:function()
	{
		var _div_container = _obj(this._id+"_container");;
		var _a = _goParentNode(_div_container);
		_div_container.style.minWidth = _a.offsetWidth+"px";
		var _viewstate = _loadViewState(this._id);
		for(var i=0;i<_viewstate["TotalStates"];i++)
		{
			_setDisplay(_obj(this._id+"_option"+i),1);
		}
		_setDisplay(_obj(this._id+"_option"+_viewstate["SelectedIndex"]),0);
		var _div_container = _obj(this._id+"_container");
		_setDisplay(_div_container,1);
		_a.focus();
	},
	_expand_done:function()
	{
	},
	collapse:function()
	{
		var _div_container = _obj(this._id+"_container");
		_setDisplay(_div_container,0);
	},
	_collapse_done:function()
	{
	},
	_handle_animate:function(_param)
	{
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}		
}
function _button_split_arrow_mousedown(_e)
{
	var _id = this.id.replace("_arrow","");
	var _element = eval("__=new KoolSplitButton(\""+_id+"\")");
	return _element._handle_split_arrow_mousedown(_e);	
}
function _button_split_arrow_mouseup(_e)
{
	var _id = this.id.replace("_arrow","");
	var _element = eval("__=new KoolSplitButton(\""+_id+"\")");
	return _element._handle_split_arrow_mouseup(_e);		
}
function _button_split_arrow_mouseout(_e)
{
	var _id = this.id.replace("_arrow","");
	var _element = eval("__=new KoolSplitButton(\""+_id+"\")");
	return _element._handle_split_arrow_mouseout(_e);		
}
function _button_split_arrow_click(_e)
{
	var _id = this.id.replace("_arrow","");
	var _element = eval("__=new KoolSplitButton(\""+_id+"\")");
	return _element._handle_split_arrow_click(_e);		
}
function _button_split_option_click(_e)
{
	var _id = this.id.substring(0,_index("_option",this.id));
	var _selected_index = parseInt(this.id.replace(_id+"_option",""));
	(new KoolSplitButton(_id))._handle_option_click(_e,_selected_index);
}
function _button_split_mousedown(_e)
{
	var _input = this;
	if(_index("Image",_getClass(this))>0)
	{
		_input = (_index("Left",_getClass(this))>0)?this.nextSibling:this.previousSibling;
	}
	var _element = eval("__=new KoolSplitButton(\""+_input.id+"\")");
	return _element._handle_mousedown(_e);	
}
function _button_split_mouseup(_e)
{
	var _input = this;
	if(_index("Image",_getClass(this))>0)
	{
		_input = (_index("Left",_getClass(this))>0)?this.nextSibling:this.previousSibling;
	}
	var _element = eval("__=new KoolSplitButton(\""+_input.id+"\")");
	return _element._handle_mouseup(_e);		
}
function _button_split_mouseout(_e)
{
	var _input = this;
	if(_index("Image",_getClass(this))>0)
	{
		_input = (_index("Left",_getClass(this))>0)?this.nextSibling:this.previousSibling;
	}
	var _element = eval("__=new KoolSplitButton(\""+_input.id+"\")");
	return _element._handle_mouseout(_e);		
}
function _button_split_click(_e)
{
	var _element = eval("__=new KoolSplitButton(\""+this.id+"\")");
	return _element._handle_click(_e);		
}
function _button_split_icon_click(_e)
{
	var _input = (_index("Left",_getClass(this))>0)?this.nextSibling:this.previousSibling;
	var _element = eval("__=new KoolSplitButton(\""+_input.id+"\")");
	return _element._handle_icon_click(_e);	
}
var _ksb_closeonclick_ids = new Array();
function _window_mouseup(_e)
{
	for(var i=0;i<_ksb_closeonclick_ids.length;i++)
	{
		var _ksb = new KoolSplitButton(_ksb_closeonclick_ids[i]);
		if(_exist(_ksb))
		{
			_ksb.collapse();
		}
	}
}
_addEvent(document,"mouseup",_window_mouseup,false);
function KoolButton(_id)
{
	this._id = _id;
}
KoolButton.prototype = 
{
	_setup:function()
	{
		var _this = _obj(this._id);
		var _viewstate = _loadViewState(this._id);
		var _parent = _goParentNode(_this);
		var _a = _newNode("a",_parent);
		_parent.insertBefore(_a,_this);
		var _span_out = _newNode("span",_a);
		_span_out.appendChild(_this);
		_setClass(_this,"kfrButton");
		_setClass(_span_out,"kfrOut");
		_setClass(_a,"kfrButtonWrapper");
		_addEvent(_this,"mousedown",_button_mousedown);
		_addEvent(_this,"mouseup",_button_mouseup);
		_addEvent(_this,"mouseout",_button_mouseout);
		_addEvent(_this,"click",_button_click);
		if(_viewstate)
		{
			if(_viewstate["ToolTip"])
			{
				_a.title = _viewstate["ToolTip"];
			}
			if(_viewstate["CssClass"])
			{
			 	_addClass(_this,_viewstate["CssClass"]);
			}
			var _case_icon = 0;
			if(_viewstate["LeftImage"])
			{
				var _span_icon = _newNode("span",_span_out);
				_setClass(_span_icon,"kfrLeftImage");
				_span_icon.style.backgroundImage="url("+_viewstate["LeftImage"]+")";
				_span_out.insertBefore(_span_icon,_this);
				_case_icon = 1;
				_addEvent(_span_icon,"click",_button_icon_click);
				_addEvent(_span_icon,"mousedown",_button_mousedown);
				_addEvent(_span_icon,"mouseup",_button_mouseup);
				_addEvent(_span_icon,"mouseout",_button_mouseout);
			}
			if(_viewstate["RightImage"])
			{
				var _span_icon = _newNode("span",_span_out);
				_span_icon.style.backgroundImage="url("+_viewstate["RightImage"]+")";
				_setClass(_span_icon,"kfrRightImage");
				_addEvent(_span_icon,"click",_button_icon_click);
				_addEvent(_span_icon,"mousedown",_button_mousedown);
				_addEvent(_span_icon,"mouseup",_button_mouseup);
				_addEvent(_span_icon,"mouseout",_button_mouseout);
				_case_icon = (_case_icon==0)?2:3;
			}
			switch(_case_icon)
			{
				case 1:
					_addClass(_span_out,"kfrSpaceLeft");
					break;
				case 2:
					_addClass(_span_out,"kfrSpaceRight");
					break;
				case 3:
					_addClass(_span_out,"kfrSpaceBoth");
					break;				
			}
		}
	},
	enable:function(_bool)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this);
		_this.disabled = !_bool;
		if(_this.disabled)
		{
			_addClass(_a,"kfrButtonDisabled");
		}
		else
		{
			_addClass(_a,"kfrButtonDisabled");			
		}	
	},
	set_text:function(_text)
	{
		var _this = _obj(this._id);
		_this.value = _text;
	},
	get_text:function(_text)
	{
		var _this = _obj(this._id);
		return _this.value;
	},	
	_handle_mouseup:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_removeClass(_a,"kfrButtonDown");
	},
	_handle_mousedown:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_addClass(_a,"kfrButtonDown");
	},
	_handle_mouseout:function(_e)
	{
		var _this = _obj(this._id);
		var _a = _goParentNode(_this,2);
		_removeClass(_a,"kfrButtonDown");
	},
	_handle_click:function(_e)
	{
		this._handle_clientevent("OnClick",{});
	},	
	_handle_icon_click:function(_e)
	{
		var _this = _obj(this._id);
		_this.click();
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	validate:function()
	{
		return this._handle_clientevent("OnValidate",{});
	}		
}
/*Button event*/
function _button_mousedown(_e)
{
	var _input = this;
	if(_index("Image",_getClass(this))>0)
	{
		_input = (_index("Left",_getClass(this))>0)?this.nextSibling:this.previousSibling;
	}
	var _element = eval("__=new KoolButton(\""+_input.id+"\")");
	return _element._handle_mousedown(_e);	
}
function _button_mouseup(_e)
{
	var _input = this;
	if(_index("Image",_getClass(this))>0)
	{
		_input = (_index("Left",_getClass(this))>0)?this.nextSibling:this.previousSibling;
	}
	var _element = eval("__=new KoolButton(\""+_input.id+"\")");
	return _element._handle_mouseup(_e);		
}
function _button_mouseout(_e)
{
	var _input = this;
	if(_index("Image",_getClass(this))>0)
	{
		_input = (_index("Left",_getClass(this))>0)?this.nextSibling:this.previousSibling;
	}
	var _element = eval("__=new KoolButton(\""+_input.id+"\")");
	return _element._handle_mouseout(_e);		
}
function _button_icon_click(_e)
{
	var _input = (_index("Left",_getClass(this))>0)?this.nextSibling:this.previousSibling;
	var _element = eval("__=new KoolButton(\""+_input.id+"\")");
	return _element._handle_icon_click(_e);	
}
function _button_click(_e)
{
	return (new KoolButton(this.id))._handle_click(_e);	
}
function KoolForm(_id)
{
	this._id = _id;
	this.id = _id;
	this._formid =  _id.replace("_manager","");
	this._eventhandles = new Array();
	this._init();
}
KoolForm.prototype = 
{
	_init:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _control_classes = _viewstate["ControlClasses"];
		var _form = _obj(this._formid);
		_addClass(_form,_viewstate["Style"]+"KFR");
		_addEvent(_form,"submit",_form_onsubmit);
		var _license_divs = _getElements("div","_y"+"e"+"11o"+"w",document);
		if(_license_divs[0])
		{
			_form.insertBefore(_license_divs[0],_form.firstChild);
			_setClass(_license_divs[0],"");	
		}
		this.refresh();
	},
	refresh:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _control_classes = _viewstate["ControlClasses"];		
		for(_controlid in _control_classes)
		{
			if(typeof _control_classes[_controlid]!="function") //Mootools
			if(_obj(_controlid))
			{
				this.get_control(_controlid)._setup();					
			}
		}
		if(_viewstate["DecorationEnabled"])
		{
			this._form_decor();			
		}
	},
	validate:function()
	{
		var _viewstate = _loadViewState(this._id);
		var _control_classes = _viewstate["ControlClasses"];		
		var _validated = true;
		var _not_valid_targets = {};		
		if (_viewstate["Validate"]) {
			for (_controlid in _control_classes) {
				if(typeof _control_classes[_controlid]!="function") //Mootools
				{
					var _control_viewstate = _loadViewState(_controlid);
					if (_control_viewstate!=null && _obj(_controlid)!=null) 
					{
						if (_control_viewstate["Validate"]) 
						{
							var _isvalid = true;
							var _control = eval("__= new " + _control_classes[_controlid] + "(\"" + _controlid + "\")");					
							if (_control_viewstate["TargetId"])
							{
								if(_not_valid_targets[_control_viewstate["TargetId"]]==null)
								{
									_isvalid= _control.validate()
									if(!_isvalid)
									{
										_not_valid_targets[_control_viewstate["TargetId"]]=1;
									}
								}
								else
								{
									_control.show_message(0);
								}							
							}
							else
							{
								_isvalid= _control.validate();
							}
							_validated &= _isvalid;
						}
					}					
				}
			}
			_validated &= this._handle_clientevent("OnValidate",{});
		}
		return _validated;			
	},
	_form_decor:function()
	{
		var _decoration_zones = _getElements("*","decoration",document);
		for(var j=0;j<_decoration_zones.length;j++)
		{
			this._decorate_element(_decoration_zones[j]);
			/*Input*/
			var _inputs = _getElements("input","",_decoration_zones[j]);
			for(var i=0;i<_inputs.length;i++)
			{
				this._decorate_element(_inputs[i]);
			}
			/*FieldSet*/
			var _textareas = _getElements("textarea","",_decoration_zones[j]);
			for(var i=0;i<_textareas.length;i++)
			{
				this._decorate_element(_textareas[i]);
			}
			/*Select*/
			var _selects = _getElements("select","",_decoration_zones[j]);
			for(var i=0;i<_selects.length;i++)
			{
				this._decorate_element(_selects[i]);
			}
			/*Label*/
			var _labels = _getElements("label","",_decoration_zones[j]);
			for(var i=0;i<_labels.length;i++)
			{
				this._decorate_element(_labels[i]);
			}
			/*FieldSet*/
			var _fieldsets = _getElements("fieldset","",_decoration_zones[j]);
			for(var i=0;i<_fieldsets.length;i++)
			{
				this._decorate_element(_fieldsets[i]);
			}
			/*Headings*/
			var _hs = _getElements("h1","",_decoration_zones[j]);
			for(var i=0;i<_hs.length;i++)
			{
				this._decorate_element(_hs[i]);
			}
			var _hs = _getElements("h2","",_decoration_zones[j]);
			for(var i=0;i<_hs.length;i++)
			{
				this._decorate_element(_hs[i]);
			}
			var _hs = _getElements("h3","",_decoration_zones[j]);
			for(var i=0;i<_hs.length;i++)
			{
				this._decorate_element(_hs[i]);
			}
			var _hs = _getElements("h4","",_decoration_zones[j]);
			for(var i=0;i<_hs.length;i++)
			{
				this._decorate_element(_hs[i]);
			}
			var _hs = _getElements("h5","",_decoration_zones[j]);
			for(var i=0;i<_hs.length;i++)
			{
				this._decorate_element(_hs[i]);
			}		
		}		
	},
	_decorate_element:function(_element)
	{
		if(_index("kfr",_getClass(_element))>-1) return;
		if(_index("nodecor",_getClass(_element))>-1) return;
		var _viewstate = _loadViewState(this._id);
		switch( _element.nodeName.toLowerCase())
		{
			case "input":
					switch(_element.type)
					{
						case "text":
							if(_element.id=="") _element.id = "textbox_"+_getIdentity();
						case "password":
							if(_element.id=="") _element.id = "password_"+_getIdentity();
							if(_viewstate["Decoration"]["TextBox"])
							{
								(new KoolTextBox(_element.id))._setup(); 								
							}
							break;
						case "radio":
							if(_element.id=="") _element.id = "radiobutton_"+_getIdentity();
							if(_viewstate["Decoration"]["RadioButton"])
							{
								(new KoolRadioButton(_element.id))._setup(); 
							}
							break;		
						case "checkbox":
							if(_element.id=="") _element.id = "checkbox_"+_getIdentity();
							if(_viewstate["Decoration"]["CheckBox"])
							{
								(new KoolCheckBox(_element.id))._setup(); 
							}
							break;		
						case "button":
						case "reset":
						case "submit":
							if(_element.id=="") _element.id = "button_"+_getIdentity();
							if(_viewstate["Decoration"]["Button"])
							{
								(new KoolButton(_element.id))._setup(); 
							}
							break;
					}
				break;
			case "textarea":
					if(_element.id=="") _element.id = "textarea_"+_getIdentity();
					(new KoolTextBox(_element.id))._setup(); 
				break;
			case "select":
					if(_element.id=="") _element.id = "select_"+_getIdentity();
					if(_viewstate["Decoration"]["DropDownList"])
					{
						(new KoolDropDownList(_element.id))._setup(); 
					}
				break;
			case "label":
					if(_viewstate["Decoration"]["Label"])
					{
						_addClass(_element,"kfrLabel");
					}
				break;
			case "fieldset":
					if(_viewstate["Decoration"]["FieldSet"])
					{
						_setClass(_element,"kfrFieldSet kfrRoundCorner");					
					}
					if(_index("ie",_getBrowser())>-1)
					{
						var _parent = _goParentNode(_element);
						var _element_margin = _getStyle(_element,"margin");
						var _element_height = _element.offsetHeight;
						_element.style.margin = "0px";
						var _table = _newNode("table",_parent);
						_setClass(_table,"kfrRoundedWrapper_FieldSet");
						_table.style.margin = _element_margin;
						_table.cellPadding = "0";
						_table.cellSpacing = "0";
						var _tr = _newNode("tr",_table)
						var _td = _newNode("td",_tr);
						var _div = _newNode("div",_td);
						_div.innerText = " ";
						_setClass(_div,"kfrRoundedOuter");
						_setHeight(_div,_element_height-12);
						_div.style.marginTop = "8px";
						var _td = _newNode("td",_tr);
						var _div = _newNode("div",_td);
						_div.innerText = " ";
						_setClass(_div,"kfrRoundedInner");
						_setHeight(_div,_element_height-12);
						_div.style.marginTop = "8px";
						var _td = _newNode("td",_tr);
						var _div = _newNode("div",_td);
						_div.innerText = " ";
						_setClass(_div,"kfrRoundedInner");
						_setHeight(_div,_element_height-10);
						_div.style.marginTop = "8px";
						var _td = _newNode("td",_tr);
						_parent.insertBefore(_table,_element);
						_td.appendChild(_element);			
						_element.style.borderLeft="0";
						_element.style.borderRight="0";				
						var _td = _newNode("td",_tr);
						var _div = _newNode("div",_td);
						_div.innerText = " ";
						_setClass(_div,"kfrRoundedInner");
						_setHeight(_div,_element_height-10);
						_div.style.marginTop = "8px";
						var _td = _newNode("td",_tr);
						var _div = _newNode("div",_td);
						_div.innerText = " ";
						_setClass(_div,"kfrRoundedInner");
						_setHeight(_div,_element_height-12);
						_div.style.marginTop = "8px";
						var _td = _newNode("td",_tr);
						var _div = _newNode("div",_td);
						_div.innerText = " ";
						_setClass(_div,"kfrRoundedOuter");
						_setHeight(_div,_element_height-12);
						_div.style.marginTop = "8px";					
					}
				break;
			case "h1":
			case "h2":
			case "h3":
			case "h4":
			case "h5":
					if(_viewstate["Decoration"]["Headings"])
					{
						_addClass(_element,"kfr"+_element.nodeName);					
					}
				break;
		}				
	},
	get_control:function(_controlid)
	{
		var _viewstate =_loadViewState(this._id);
		if(_viewstate["ControlClasses"][_controlid])
		{
			return eval("__=new "+_viewstate["ControlClasses"][_controlid]+"(\""+_controlid+"\")");			
		}
		else
		{
			return null;
		}
	},
	_handle_onsubmit:function(_e)
	{
		if(!this.validate() || !this._handle_clientevent("OnSubmit",{}))
		{
			return _preventDefaut(_e);			
		}
	},
	_handle_clientevent:function(_event,_args)
	{
		var _viewstate = _loadViewState(this._id);
		if(_viewstate)
		{
			var _clientevents = _viewstate["ClientEvents"];
			if(_clientevents && _clientevents[_event])
			{
				var _handle_function = eval("__="+_clientevents[_event]);
				var _result = _handle_function(this,_args);
				if(_result!=null && _result==false) return false;
			}
		}
		return true;
	},
	_handle_validator_target_onblur:function(_e,_target_id)
	{
		var _viewstate = _loadViewState(this._id);
		var _control_classes = _viewstate["ControlClasses"];				
		if (_viewstate["Validate"]) {
			var _isvalid = true;
			for (_controlid in _control_classes) 
			if(typeof _control_classes[_controlid]!="function") //Mootools
			{
				var _control_viewstate = _loadViewState(_controlid);
				if (_obj(_controlid)!=null && _control_viewstate!=null && (_index("Validator",_control_classes[_controlid])>0)) 
				{
					if (_control_viewstate["TargetId"]==_target_id) 
					{
						var _control = eval("__= new " + _control_classes[_controlid] + "(\"" + _controlid + "\")");
						_control.show_message(0);
						if(_isvalid)
						{
							_isvalid=_control.validate();							
						}
					}
				}
			}
			if(!_isvalid) return;
		}		
	}	
}
function _getFormId(_this)
{
	var _form = _this;
	while(_form.nodeName!="FORM")
	{
		if(_form.nodeName=="BODY") return;//do nothing
		_form = _goParentNode(_form);
	}
	return _form.id;
}
function _getConventionalClass(_elementid)
{
		var _element = _obj(_elementid);
		switch( _element.nodeName.toLowerCase())
		{
			case "input":
					switch(_element.type)
					{
						case "text":
						case "password":
							return "KoolTextBox";
							break;
						case "radio":
							return "KoolRadioButton";
							break;		
						case "checkbox":
							return "KoolCheckBox";
							break;		
						case "button":
						case "submit":
							return "KoolButton";
							break;		
					}
				break;
			case "textarea":
					return "KoolTextBox";
				break;
			case "select":
					return "KoolDropDownList";
				break;
		}	
}
/*This event*/
function _this_onfocus(_e)
{
	var _viewstate = _loadViewState(this.id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(this.id);
	var _element = eval("__=new "+_object_class+"(\""+this.id+"\")");
	return _element._handle_onfocus(_e);
}
function _this_onblur(_e)
{
	var _viewstate = _loadViewState(this.id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(this.id);
	var _element = eval("__=new "+_object_class+"(\""+this.id+"\")");
	return _element._handle_onblur(_e);
}
function _this_onkeypress(_e)
{
	var _viewstate = _loadViewState(this.id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(this.id);
	var _element = eval("__=new "+_object_class+"(\""+this.id+"\")");
	return _element._handle_onkeypress(_e);
}
function _this_onkeydown(_e)
{
	var _viewstate = _loadViewState(this.id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(this.id);
	var _element = eval("__=new "+_object_class+"(\""+this.id+"\")");
	return _element._handle_onkeydown(_e);
}
function _this_onkeyup(_e)
{
	var _viewstate = _loadViewState(this.id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(this.id);
	var _element = eval("__=new "+_object_class+"(\""+this.id+"\")");
	return _element._handle_onkeyup(_e);
}
function _this_onmousewheel(_e)
{
	var _element_id = this.id;
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_onmousewheel(_e);	
}
/*Spin event*/
function _spinup_onclick(_e)
{
	var _element_id = this.id.replace("_spinup","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_spinup_onclick(_e);	
}
function _spindown_onclick(_e)
{
	var _element_id = this.id.replace("_spindown","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_spindown_onclick(_e);	
}
/*Label event*/
function _label_mouseover(_e)
{
	var _element_id = this.id.replace("_label","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_mouseover(_e);
}
function _label_mouseout(_e)
{
	var _element_id = this.id.replace("_label","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_mouseout(_e);
}
function _label_mouseclick(_e)
{
	var _element_id = this.id.replace("_label","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_mouseclick(_e);
}
/*Sub event*/
/*
function _sub_onfocus(_e)
{
	var _element_id = this.id.replace("_sub","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_onfocus(_e);
}
function _sub_onblur(_e)
{
	var _element_id = this.id.replace("_sub","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_onblur(_e);
}
function _sub_onkeypress(_e)
{
	var _element_id = this.id.replace("_sub","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_onkeypress(_e);
}
function _sub_onkeydown(_e)
{
	var _element_id = this.id.replace("_sub","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_onkeydown(_e);
}
function _sub_onkeyup(_e)
{
	var _element_id = this.id.replace("_sub","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_onkeyup(_e);
}
function _sub_mouseclick(_e)
{
	var _element_id = this.id.replace("_sub","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_mouseclick(_e);
}
*/
function _sub_mouseover(_e)
{
	var _element_id = this.id.replace("_sub","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_mouseover(_e);
}
function _sub_mouseout(_e)
{
	var _element_id = this.id.replace("_sub","");
	var _viewstate = _loadViewState(_element_id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(_element_id);
	var _element = eval("__=new "+_object_class+"(\""+_element_id+"\")");
	return _element._handle_mouseout(_e);
}
function _validator_target_onblur(_e)
{
	var _formid = _getFormId(this);
	var _form_manager = eval("__="+_formid+"_manager");
	_form_manager._handle_validator_target_onblur(_e,this.id);	
}
function _this_change(_e)
{
	var _viewstate = _loadViewState(this.id);
	var _object_class = (_viewstate)?_viewstate["ObjectClass"]:_getConventionalClass(this.id);
	var _element = eval("__=new "+_object_class+"(\""+this.id+"\")");
	return _element._handle_change(_e);
}
function _radio_change(_e,_this)
{
	var _obj_elements = document.getElementsByName((_this)?_this.name:this.name);
	for(var i=0;i<_obj_elements.length;i++)
	{
		if(_index("kfr",_getClass(_obj_elements[i]))>-1 && _obj_elements[i]!=_this)
		{
			var _element = eval("__=new KoolRadioButton(\""+_obj_elements[i].id+"\")");		
			_element._handle_change(_e);
		}
	}
}
function _form_onsubmit(_e)
{
	var _form_manager = eval("__="+this.id+"_manager");
	return _form_manager._handle_onsubmit(_e);
}
if(typeof(__KFRInits)!='undefined' && _exist(__KFRInits))
{	
	for(var i=0;i<__KFRInits.length;i++)
	{
		__KFRInits[i]();
	}
}
