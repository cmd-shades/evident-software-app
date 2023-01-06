/**
 * @author Nghiem Anh Tuan
 * Check the timeselect does not parse date
 */
function _exist(_theObj)
{
	if (typeof(_theObj) == "undefined") 
	{
		return false;
	}
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
function _replace(_search,_rep,_str)
{
	return _str.replace(eval("/"+_search+"/g"),_rep);
}
function _trim(_str, _chars) {
	_str += '';
	return _ltrim(_rtrim(_str, _chars), _chars);
}
function _ltrim(_str, _chars) {
	_str += '';
	_chars = _chars || "\\s";
	return _str.replace(new RegExp("^[" + _chars + "]+", "g"), "");
}
function _rtrim(_str, _chars) {
	_str += '';
	_chars = _chars || "\\s";
	return _str.replace(new RegExp("[" + _chars + "]+$", "g"), "");
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
function _leading_zero_parseInt(_str)
{
	while(_str.charAt(0)=="0" && _str.length>1)
	{
		_str = _str.substring(1);
	}
	return parseInt(_str);
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
function _clone_date(_date)
{
	return new Date(_date.getTime());
}
function _addDays(_date,_num_day)
{
	var _new_date = _clone_date(_date);
	_new_date.setDate(_new_date.getDate()+_num_day);
	return _new_date;
}
Date.prototype.getWeek = function() {
var onejan = new Date(this.getFullYear(),0,1);
return Math.ceil((((this - onejan) / 86400000) + onejan.getDay()+1)/7);
}
Date.prototype.format = function(format) {
	var returnStr = '';
	var replace = Date.replaceChars;
	for (var i = 0; i < format.length; i++) {
		var curChar = format.charAt(i);
		if (replace[curChar]) {
			returnStr += replace[curChar].call(this);
		} else {
			returnStr += curChar;
		}
	}
	return returnStr;
};
Date.replaceChars = {
	shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	longMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	shortDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	longDays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
	d: function() { return (this.getUTCDate() < 10 ? '0' : '') + this.getUTCDate(); },
	D: function() { return Date.replaceChars.shortDays[this.getUTCDay()]; },
	j: function() { return this.getUTCDate(); },
	l: function() { return Date.replaceChars.longDays[this.getUTCDay()]; },
	N: function() { return this.getUTCDay() + 1; },
	S: function() { return (this.getUTCDate() % 10 == 1 && this.getUTCDate() != 11 ? 'st' : (this.getUTCDate() % 10 == 2 && this.getUTCDate() != 12 ? 'nd' : (this.getUTCDate() % 10 == 3 && this.getUTCDate() != 13 ? 'rd' : 'th'))); },
	w: function() { return this.getUTCDay(); },
	z: function() { return "Not Yet Supported"; },
	W: function() { return "Not Yet Supported"; },
	F: function() { return Date.replaceChars.longMonths[this.getUTCMonth()]; },
	m: function() { return (this.getUTCMonth() < 9 ? '0' : '') + (this.getUTCMonth() + 1); },
	M: function() { return Date.replaceChars.shortMonths[this.getUTCMonth()]; },
	n: function() { return this.getUTCMonth() + 1; },
	t: function() { return "Not Yet Supported"; },
	L: function() { return "Not Yet Supported"; },
	o: function() { return "Not Supported"; },
	Y: function() { return this.getUTCFullYear(); },
	y: function() { return ('' + this.getUTCFullYear()).substr(2); },
	a: function() { return this.getUTCHours() < 12 ? 'am' : 'pm'; },
	A: function() { return this.getUTCHours() < 12 ? 'AM' : 'PM'; },
	B: function() { return "Not Yet Supported"; },
	g: function() { return this.getUTCHours() % 12 || 12; },
	G: function() { return this.getUTCHours(); },
	h: function() { return ((this.getUTCHours() % 12 || 12) < 10 ? '0' : '') + (this.getUTCHours() % 12 || 12); },
	H: function() { return (this.getUTCHours() < 10 ? '0' : '') + this.getUTCHours(); },
	i: function() { return (this.getUTCMinutes() < 10 ? '0' : '') + this.getUTCMinutes(); },
	s: function() { return (this.getUTCSeconds() < 10 ? '0' : '') + this.getUTCSeconds(); },
	e: function() { return "Not Yet Supported"; },
	I: function() { return "Not Supported"; },
	O: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + '00'; },
	T: function() { var m = this.getMonth(); this.setMonth(0); var result = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, '$1'); this.setMonth(m); return result;},
	Z: function() { return -this.getTimezoneOffset() * 60; },
	c: function() { return "Not Yet Supported"; },
	r: function() { return this.toString(); },
	U: function() { return this.getTime() / 1000; }
};
/*--------------------------------------------------------*/
function KoolCalendar(_id)
{
	this._id = _id;
	this._eventhandles = new Array();
	this._init();
}
KoolCalendar.prototype = 
{
	_init:function()
	{
		var _this = _obj(this._id);
		var _settings = this._loadSettings();
		var _next = _getElements("span","kcdNext",_this)[0];
		if(_next)
		{
			_addEvent(_goFirstChild(_next),"click",_next_onclick,false);
		}
		var _prev = _getElements("span","kcdPrev",_this)[0];
		if(_prev)
		{
			_addEvent(_goFirstChild(_prev),"click",_prev_onclick,false);
		}
		var _fastnext = _getElements("span","kcdFastNext",_this)[0];
		if(_fastnext)
		{
			_addEvent(_goFirstChild(_fastnext),"click",_fastnext_onclick,false);
		}
		var _fastprev = _getElements("span","kcdFastPrev",_this)[0];
		if(_fastprev)
		{
			_addEvent(_goFirstChild(_fastprev),"click",_fastprev_onclick,false);
		}
		var _qms = _getElements("span","kcdQMSNav",_this)[0];
		if(_qms)
		{
			_addEvent(_qms,"click",_qms_onclick,false);			
		}
		var _qms_div = _obj(this._id+"_qms");
		if(_exist(_qms_div))
		{
			var _months = _getElements("td","kcdMonth",_qms_div);
			for(var i=0;i<_months.length;i++)
			{
				_addEvent(_goFirstChild(_months[i]),"click",_qms_month_onclick,false);
			}
			var _years = _getElements("td","kcdYear",_qms_div);
			for(var i=0;i<_years.length;i++)
			{
				_addEvent(_goFirstChild(_years[i]),"click",_qms_year_onclick,false);
			}
			_addEvent(_goFirstChild(_obj(this._id+"_qms_Next")),"click",_qms_year_navigate_onclick,false);
			_addEvent(_goFirstChild(_obj(this._id+"_qms_Prev")),"click",_qms_year_navigate_onclick,false);
			_addEvent(_obj(this._id+"_qms_Today"),"click",_qms_button_onclick,false);
			_addEvent(_obj(this._id+"_qms_OK"),"click",_qms_button_onclick,false);
			_addEvent(_obj(this._id+"_qms_Cancel"),"click",_qms_button_onclick,false);
			_addEvent(_qms_div,"mouseup",_cancel_click_propagation,false);
			_close_on_outside_click_ids.push(this._id);
		}
		var _client_events = _settings["ClientEvents"];
		for(var _name in _client_events)
		{
			if(typeof _client_events[_name]!="function") //Mootools
			{
				if (eval("typeof "+_client_events[_name]+" =='function'"))
				{
					this._eventhandles[_name] = eval(_client_events[_name]);
				}				
			}
		}
		if (!_exist(_calendar_list[this._id]))
		{
			try{this._handleEvent("OnInit",{});}catch(ex){}				
		}
		try{this._handleEvent("OnLoad",{});}catch(ex){}					
		if (_exist(_calendar_list[this._id]))
		{
			_post_load_events = _calendar_list[this._id]["PostLoadEvent"];
			for(_name in _post_load_events)
			{
				if(typeof _post_load_events[_name]!="function") //Mootools
				{
					try{this._handleEvent(_name,_post_load_events[_name]);}catch(ex){}					
				}
			}
		}
		_calendar_list[this._id] = {"PostLoadEvent":{}};		
		this._attach_events_to_months_details();
	},
	_attach_events_to_months_details:function()
	{
		var _this = _obj(this._id);
		var _settings = this._loadSettings();
		if(_settings["EnableSelect"])
		{
			var _days = _getElements("td","kcdDay",_this);
			for(var i=0;i<_days.length;i++)
			{
				_addEvent(_days[i],"mouseover",_day_onmouseover,false);
				_addEvent(_days[i],"mouseout",_day_onmouseout,false);
				_addEvent(_days[i],"click",_day_onclick,false);			
			}
			if(_settings["EnableMultiSelect"])
			{
				var _col_headers = _getElements("th","kcdColHeader",_this);
				for(var i=0;i<_col_headers.length;i++)
				{
					if(_index("ViewSelector",_getClass(_col_headers[i]))>0)
					{
						_addEvent(_col_headers[i],"mouseover",_viewselector_onmouseover,false);
						_addEvent(_col_headers[i],"mouseout",_viewselector_onmouseout,false);
						_addEvent(_col_headers[i],"click",_viewselector_onclick,false);								
					}
					else
					{
						if(_settings["UseColumnHeadersAsSelectors"])
						{
							_addEvent(_col_headers[i],"mouseover",_colheader_onmouseover,false);
							_addEvent(_col_headers[i],"mouseout",_colheader_onmouseout,false);
							_addEvent(_col_headers[i],"click",_colheader_onclick,false);							
						}
					}
				}
				if(_settings["UseRowHeadersAsSelectors"])
				{
					var _row_headers = _getElements("th","kcdRowHeader",_this);
					for(var i=0;i<_row_headers.length;i++)
					{
							_addEvent(_row_headers[i],"mouseover",_rowheader_onmouseover,false);
							_addEvent(_row_headers[i],"mouseout",_rowheader_onmouseout,false);
							_addEvent(_row_headers[i],"click",_rowheader_onclick,false);				
					}								
				}
			}
		}
	},
	get_selected_dates:function()
	{
		var _viewstate = this._loadViewState();
		var _SelectedDates = _viewstate["SelectedDates"];
		if(!_exist(_SelectedDates))
		{
			_SelectedDates = new Array();
		}
		var _res = new Array();		
		for(var _date_string in _SelectedDates)
		{
			if(typeof _SelectedDates[_date_string]!="function") //Mootools
			{
				_res.push(new Date(_date_string+" UTC"));				
			}
		}
		return _res;
	},
	commit:function()
	{
		if(!this._handleEvent("OnBeforeCommit",{})){return;};
		var _settings = this._loadSettings();
		if(_settings["AjaxEnabled"])
		{
			var _updatepanel = eval(this._id+"_updatepanel");
			_updatepanel.update((_settings["AjaxHandlePage"]!="")?_settings["AjaxHandlePage"]:null);
			this._registerPostLoadEvent("OnCommit",{});
		}
		else
		{
			var _form = _obj(this._id);
			while(_form.nodeName!="FORM")
			{
				if(_form.nodeName=="BODY") return;//do nothing
				_form = _goParentNode(_form);
			}
			_form.submit();
		}
	},
	select:function(_date)
	{
		var _this =_obj(this._id);
		var _viewstate = this._loadViewState();
		var _settings = this._loadSettings();
		var _SelectedDates = _viewstate["SelectedDates"];
		var _date_abbr = _date.format("n/j/Y");
		var _selected_date = new Date(_date_abbr+" UTC");
		if(!this._handleEvent("OnBeforeSelect",{"Date":_selected_date})){return;};
		if(!_exist(_SelectedDates))
		{
			_SelectedDates = new Array();
		}		
		_SelectedDates[_date_abbr] = 1;
		_viewstate["SelectedDates"] = _SelectedDates;
		this._saveViewState(_viewstate);
		var _days =_getElements("td","kcdDay",_this);
		for(var i=0;i<_days.length;i++)
		{
			if(_days[i].abbr==_date_abbr)
			{
				_addClass(_days[i],"kcdSelected");
			}
		}
		if(_settings["ClientMode"])
		{
			this._handleEvent("OnSelect",{"Date":_selected_date});	
		}
		else
		{
			this._registerPostLoadEvent("OnSelect",{"Date":_selected_date});
		}
	},
	deselect:function(_date)
	{
		var _this =_obj(this._id);
		var _viewstate = this._loadViewState();
		var _settings = this._loadSettings();
		var _SelectedDates = _viewstate["SelectedDates"];
		var _date_abbr = _date.format("n/j/Y");
		if(!this._handleEvent("OnBeforeDeselect",{"Date":_date})){return;};
		if(!_exist(_SelectedDates))
		{
			_SelectedDates = new Array();
		}
		if(_SelectedDates[_date_abbr])
		{
			delete _SelectedDates[_date_abbr];
		}		
		_viewstate["SelectedDates"] = _SelectedDates;
		this._saveViewState(_viewstate);
		var _days =_getElements("td","kcdDay",_this);
		for(var i=0;i<_days.length;i++)
		{
			if(_days[i].abbr==_date_abbr)
			{
				_removeClass(_days[i],"kcdSelected");
			}
		}
		if(_settings["ClientMode"])
		{
			this._handleEvent("OnDeselect",{"Date":_date});	
		}
		else
		{
			this._registerPostLoadEvent("OnDeselect",{"Date":_date});
		}				
	},
	deselect_all:function()
	{
		var _this =_obj(this._id);
		var _viewstate = this._loadViewState();
		var _SelectedDates = _viewstate["SelectedDates"];
		if(_exist(_SelectedDates))
		{
			for(var _date_string in _SelectedDates)
			{
				if(typeof _SelectedDates[_date_string]!="function") //Mootools
				{
					this.deselect(new Date(_date_string+" UTC"));					
				}
			}
		}		
	},
	navigate:function(_new_focused_date,_no_effect)
	{
		if(!this._handleEvent("OnBeforeNavigate",{"Date":_new_focused_date})){return;};
		var _this = _obj(this._id);
		if(_index("Navigating",_getClass(_goFirstChild(_this)))>0)
		{
			this._navigate_done();			
		}
		var _settings = this._loadSettings();
		var _viewstate = this._loadViewState();
		var _old_focused_date = new Date(_viewstate["FocusedDate"]+" UTC");
		if(_settings["ClientMode"])
		{
			var _month_range = _settings["MultiViewRows"]*_settings["MultiViewColumns"];
			var _html = "";
			if(_month_range>1)
			{
				_html = this._renderMultiView(_new_focused_date);
			}
			else
			{
				_html = this._renderMonthDetail(_new_focused_date);
			}
			var _direction = "left";
			if(_new_focused_date<_old_focused_date)
			{
				_direction = "right";
			}
			var _type = _settings["NavigateAnimation"]["Type"].toLowerCase();
			var _duration = _settings["NavigateAnimation"]["Duration"];
			var _steps = _duration/20;
			var _table_slide = _getElements("table","kcdTableSlide",_this)[0];
			if(_table_slide)
			{
				var _div_container = _goParentNode(_table_slide);	
				_setWidth(_div_container,_div_container.offsetWidth);
				_div_container.style.overflow = "hidden";
				var _tr = _goFirstChild(_table_slide,2);
				var _existed_table = _goFirstChild(_tr,2);
				var _width = _existed_table.offsetWidth;
				var _td = null;
				if(_direction=="left")
				{
				 	_td= _newNode("td",_tr);
				}
				else
				{
					_td = document.createElement("td");
					_tr.insertBefore(_td,_goParentNode(_existed_table));
				}
				_td.innerHTML = _html;
				var _new_table = _goFirstChild(_td);
				_setWidth(_existed_table,_width);
				_setWidth(_new_table,_width);
				var _existed_td = _goParentNode(_existed_table);
				_existed_td.id = this._id+"_oldtd";
				if(_direction=="right")
				{
					_div_container.scrollLeft = _existed_td.offsetWidth;			
				}
				_addClass(_goFirstChild(_this),"kcdNavigating");
				if(_exist(_no_effect))
				{
					this._navigate_done();//Navigate directly to destination without sliding effect	
				}
				else
				{
					this._handle_animate({"direction":_direction,"type":_type,"duration":_duration,"steps":_steps,"current_step":0});	
				}
			}
			var _span_nav_text = _getElements("span","kcdNavText",_this)[0];			
			if(_month_range>1)
			{
				var _from_month = _new_focused_date;
				var _to_month = _clone_date(_from_month);
				_to_month.setUTCMonth(_to_month.getUTCMonth()+_month_range-1);
				_span_nav_text.innerHTML = _settings["MonthsFull"][_from_month.format("F")]+" "+_from_month.format("Y")+_settings["DateRangeSeparator"]+_settings["MonthsFull"][_to_month.format("F")]+" "+_to_month.format("Y");			
			}
			else
			{
				_span_nav_text.innerHTML = _settings["MonthsFull"][_new_focused_date.format("F")]+" "+_new_focused_date.format("Y");
			}			
		}
		_viewstate["FocusedDate"] = _new_focused_date.format("n/j/Y");
		this._saveViewState(_viewstate);
		if(!_settings["ClientMode"])
		{
			this._registerPostLoadEvent("OnNavigate",{"Date":_new_focused_date});
		}
	},
	_navigate_done:function()
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		var _focused_date = new Date(_viewstate["FocusedDate"]+" UTC");
		var _old_td = _obj(this._id+"_oldtd");		
		var _table_slide = _goParentNode(_old_td,3);
		var _div_container = _goParentNode(_table_slide);
		_purge(_old_td);
		_goParentNode(_old_td).removeChild(_old_td);
		_div_container.scrollLeft = 0;
		_div_container.style.overflow = "";
		_div_container.style.width = "";
		this._attach_events_to_months_details();
		_purge(_div_container);
		_removeClass(_goFirstChild(_this),"kcdNavigating");
		this._handleEvent("OnNavigate",{"Date":_focused_date});		
	},
	next:function()
	{
		var _settings = this._loadSettings();
		var _viewstate = this._loadViewState();
		var _focused_date =new Date(_viewstate["FocusedDate"]+" UTC");
		var _step = _settings["MultiViewRows"]*_settings["MultiViewColumns"];
		_focused_date.setUTCMonth(_focused_date.getUTCMonth()+_step);
		this.navigate(_focused_date);
	},
	prev:function()
	{
		var _settings = this._loadSettings();
		var _viewstate = this._loadViewState();
		var _focused_date =new Date(_viewstate["FocusedDate"]+" UTC");
		var _step = _settings["MultiViewRows"]*_settings["MultiViewColumns"];
		_focused_date.setUTCMonth(_focused_date.getUTCMonth()-_step);
		this.navigate(_focused_date);		
	},
	fast_next:function()
	{
		var _settings = this._loadSettings();
		var _viewstate = this._loadViewState();
		var _focused_date =new Date(_viewstate["FocusedDate"]+" UTC");
		var _step = _settings["FastNavigationStep"];
		_focused_date.setUTCMonth(_focused_date.getUTCMonth()+_step);
		this.navigate(_focused_date);		
	},
	fast_prev:function()
	{
		var _settings = this._loadSettings();
		var _viewstate = this._loadViewState();
		var _focused_date =new Date(_viewstate["FocusedDate"]+" UTC");
		var _step = _settings["FastNavigationStep"];
		_focused_date.setUTCMonth(_focused_date.getUTCMonth()-_step);
		this.navigate(_focused_date);		
	},
	_renderMultiView:function(_date)
	{
		var _settings = this._loadSettings();
		var _MultiViewRows = _settings["MultiViewRows"];
		var _MultiViewColumns = _settings["MultiViewColumns"];
		var _num_month = _MultiViewRows*_MultiViewColumns;
		var _from_month = new Date(_date.format("n/1/Y")+" UTC");
		var _to_month = _clone_date(_from_month);
		_to_month.setUTCMonth(_to_month.getUTCMonth()+_num_month-1);
		var _tpl_table = "<table cellspacing='0' border='0' style='width:100%;'>{body}</table>";		
		var _tpl_body = "<tbody>{trs}</tbody>";
		var _tpl_body_tr = "<tr>{tds}</tr>";
		var _tpl_body_td = "<td class='kcdMonthContainer {rowpos} {colpos}'>{monthview}</td>";
		var _body_trs = "";
		for(var r=0;r<_MultiViewRows;r++)
		{
			var _body_tds = "";
			for(var c=0;c<_MultiViewColumns;c++)
			{
				var _month = _clone_date(_from_month);
				_month.setUTCMonth(_month.getUTCMonth()+r*_MultiViewColumns+c);
				var _body_td = _replace("{monthview}",this._renderMonthView(_month),_tpl_body_td);
				_body_td = _replace("{rowpos}",(r==0)?"kcdFirstRow {rowpos}":"{rowpos}",_body_td);
				_body_td = _replace("{rowpos}",(r==_MultiViewRows-1)?"kcdLastRow {rowpos}":"{rowpos}",_body_td);
				_body_td = _replace("{rowpos}","",_body_td);
				_body_td = _replace("{colpos}",(c==0)?"kcdFirstCol {colpos}":"{colpos}",_body_td);
				_body_td = _replace("{colpos}",(c==_MultiViewColumns-1)?"kcdLastCol {colpos}":"{colpos}",_body_td);
				_body_td = _replace("{colpos}","",_body_td);
				_body_tds+=_body_td;
			}
			var _body_tr = _replace("{tds}",_body_tds,_tpl_body_tr);
			_body_trs+=_body_tr;	
		}
		_body = _replace("{trs}",_body_trs,_tpl_body);
		_table = _replace("{body}",_body,_tpl_table);
		return _table;
	},
	_renderMonthView:function(_date)
	{
		var _settings = this._loadSettings();
		var _tpl_table = "<table cellspacing='0' cellpadding='0' border='0' class='kcdMonthView' style='{width}{height}'>{head}{body}{foot}</table>";
		var _tpl_head = "<thead>{trs}</thead>";
		var _tpl_top_nav = "<tr><th class='kcdTopHeader'>{text}</th></tr>";
		var _tpl_body = "<tbody><tr><td class='kcdMain' style='overflow:hidden'>{detail}</td></tr></tbody>";
		var _tpl_foot = "<tfoot>{trs}</tfoot>";
		var _tpl_foot_tr = "<tr>{tds}</tr>";
		var _tpl_foot_td = "<td>{ct}</td>";
		var _top_nav = _replace("{text}",_settings["MonthsFull"][_date.format("F")],_tpl_top_nav);
		var _head_trs= "";
		_head_trs+=_top_nav;
		_head = _replace("{trs}",_head_trs,_tpl_head);
		var _body = _replace("{detail}",this._renderMonthDetail(_date),_tpl_body);
		_foot = "";
		_table = _tpl_table;
		_table = _replace("{width}",(_settings["Width"])?"width:"+_settings["Width"]+";":"",_table);
		_table = _replace("{height}",(_settings["Height"])?"height:"+_settings["Height"]+";":"",_table);
		_table = _replace("{head}",_head,_table);
		_table = _replace("{body}",_body,_table);
		_table = _replace("{foot}",_foot,_table);
		return _table;
	},
	_renderMonthDetail:function(_date)
	{
		var _settings = this._loadSettings();
		var _viewstate = this._loadViewState();
		var _SelectedDates = _viewstate["SelectedDates"];
		if(!_SelectedDates)
		{
			_SelectedDates = new Array();
		}
		var _week_key = new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		var _is_vertical_orientation = (_settings["Orientation"].toLowerCase()=="vertical");
		var _col_num = 7;
		var _row_num = 6;
		switch(_settings["MonthLayout"])
		{
			case "21x2":	
				_col_num = 21;
				_row_num = 2;				
				break;
			case "14x3":	
				_col_num = 14;
				_row_num = 3;				
				break;
			case "7x6":
				_col_num = 7;
				_row_num = 6;				
			default:
				break;
		}
		var _arr_dayname_format = _settings["DayName"];
		if(_is_vertical_orientation)
		{
			var _temp = _col_num;
			_col_num = _row_num;
			_row_num = _temp;			
		}
		var _first_day_of_month = new Date(_date.format("n/1/Y")+" UTC");
		_first_day_of_month.setTime(_first_day_of_month.getTime()+12*60*60*1000);
		var _diff = _first_day_of_month.getUTCDay() - _settings["FirstDayOfWeek"];
		if(_diff<0) _diff+=7;
		var _first_day_monthview = _addDays(_first_day_of_month,-_diff);
		var _tpl_table = "<table cellspacing='0' border='0' class='kcdMainTable'>{head}{body}</table>";
		var _tpl_head = "<thead><tr>{th_selector}{ths}</tr></thead>";
		var _tpl_head_th = "<th class='kcdColHeader' title='{title}'>{text}</th>";
		var _tpl_head_th_selector = "<th class='kcdColHeader kcdViewSelector'>{text}</th>";
		var _tpl_body = "<tbody>{trs}</tbody>";
		var _tpl_body_tr = "<tr>{th}{tds}</tr>";
		var _tpl_body_th = "<th class='kcdRowHeader' title='{title}'>{text}</th>";
		var _tpl_body_td = "<td abbr='{abbr}' class='{class}' {title}><a>{text}</a></td>";
		var _head_ths = "";
		for(var c=0;c<_col_num;c++)
		{
			var _head_th = "";
			if(_is_vertical_orientation)
			{
				var _day = _addDays(_first_day_monthview,c*_row_num);
				var _week_year = _day.getWeek();
				if (_week_year>52)
				{
					_week_year = 1;
				}
				_head_th = _replace("{text}",_week_year,_tpl_head_th);
				_head_th = _replace("{title}",_week_year,_head_th);
			}
			else
			{
				var _wday = (_settings["FirstDayOfWeek"]+c)%7;
				_head_th = _replace("{title}",_settings["DayNameFull"][_week_key[_wday]],_tpl_head_th);
				_head_th = _replace("{text}",_settings["DayName"][_week_key[_wday]],_head_th);				
			}
			_head_ths+=_head_th;
		}
		var _head_th_selector = "";
		if(_settings["ShowRowHeader"])
		{
			if(_settings["ShowViewSelector"])
			{
				_head_th_selector = _replace("{text}",_settings["ViewSelectorText"],_tpl_head_th_selector);
			}
			else
			{
				_head_th_selector = _replace("{text}","",_tpl_head_th);
				_head_th_selector = _replace("{title}","",_head_th_selector);
			}			
		}
		var _head = _replace("{ths}",_head_ths,_tpl_head);
		_head = _replace("{th_selector}",_head_th_selector,_head);
		var _body_trs = "";
		for(var r=0;r<_row_num;r++)
		{
			var _body_tds = "";
			for(var c=0;c<_col_num;c++)
			{
				var _day = _addDays(_first_day_monthview,r*_col_num+c);
				if(_is_vertical_orientation)
				{
					_day = _addDays(_first_day_monthview,c*_row_num+r);
				}
				var _is_show = (_day.getUTCMonth()!=_date.getUTCMonth())?(_settings["ShowOtherMonthsDays"]?true:false):true;
				var _body_td = _replace("{abbr}",_is_show?_day.format("n/j/Y"):"",_tpl_body_td);
				_body_td = _replace("{text}",_is_show?_day.getUTCDate():"",_body_td);
				_body_td = _replace("{class}",_is_show?"kcdDay {class}":"",_body_td);				
				_body_td = _replace("{class}",(_day.getUTCMonth()!=_date.getUTCMonth())?"kcdOtherMonth {class}":"{class}",_body_td);
				_body_td = _replace("{class}",(_day.getUTCDay()==0 || _day.getUTCDay()==6)?"kcdWeekend {class}":"{class}",_body_td);
				_body_td = _replace("{class}",(_settings["ShowToday"] && _day.format("n/j/Y")==_settings["Today"])?"kcdToday {class}":"{class}",_body_td);
				if(_exist(_settings["RangeMinDate"]))
				{
					var _min_date = new Date(_settings["RangeMinDate"]+" UTC");
					if(_day<_min_date)
					{						
						_body_td = _replace("{class}","kcdDisabled {class}",_body_td);
					}					
				}
				if(_exist(_settings["RangeMaxDate"]))
				{
					var _max_date = new Date(_settings["RangeMaxDate"]+" UTC");
					if(_day>_max_date)
					{						
						_body_td = _replace("{class}","kcdDisabled {class}",_body_td);
					}					
				}
				_body_td = _replace("{class}",(_SelectedDates[_day.format("n/j/Y")])?"kcdSelected {class}":"{class}",_body_td);
				_body_td = _replace("{class}","",_body_td);
				_body_td = _replace("{title}",_settings["ShowDayCellToolTips"]?"title='"+_settings["DayNameFull"][_day.format("l")]+", "+_settings["MonthsFull"][_day.format("F")]+_day.format(" d, Y")+"'":"",_body_td);
				_body_tds+=_body_td;
			}
			var _body_th = "";
			if(_settings["ShowRowHeader"])
			{
				if(_is_vertical_orientation)
				{
					var _wday = (_settings["FirstDayOfWeek"]+r)%7;
					_body_th = _replace("{title}",_settings["DayNameFull"][_week_key[_wday]],_tpl_body_th);
					_body_th = _replace("{text}",_settings["DayName"][_week_key[_wday]],_body_th);				
				}
				else
				{
					var _day = _addDays(_first_day_monthview,r*_col_num);
					var _week_year = _day.getWeek();
					if (_week_year>52)
					{
						_week_year = 1;
					}
					_body_th = _replace("{text}",_week_year,_tpl_body_th);
					_body_th = _replace("{title}",_week_year,_body_th);
				}
			}
			var _body_tr = _replace("{tds}",_body_tds,_tpl_body_tr);
			_body_tr = _replace("{th}",_body_th,_body_tr);
			_body_trs+=_body_tr;			
		}
		var _body = _replace("{trs}",_body_trs,_tpl_body);
		var _table = _replace("{head}",_settings["ShowColumnHeader"]?_head:"",_tpl_table);
		_table = _replace("{body}",_body,_table);
		return _table;
	},
	_show_quick_month_selector:function(_bool)
	{
		var _this = _obj(this._id);
		var _nav_text = _getElements("span","kcdQMSNav",_this)[0];
		if(_nav_text)
		{
			var _qms_div = _obj(this._id+"_qms");
			var _viewstate = this._loadViewState();
			if (!_bool)
			{
				_setDisplay(_qms_div,_bool);
				if(_exist(_viewstate["QMSDate"]))
				{
					delete _viewstate["QMSDate"];
				}
				this._saveViewState(_viewstate);				
				return;
			}
			var _div_cal = _nav_text;
			var _top = 0;
			var _left = 0;
			while(_div_cal.nodeName!="DIV" || _index("KCD",_getClass(_div_cal))<0)
			{
				_top+= isNaN(_div_cal.offsetTop)?0:_div_cal.offsetTop;
				_left+= isNaN(_div_cal.offsetLeft)?0:_div_cal.offsetLeft;
				_div_cal = _goParentNode(_div_cal);
				if (_div_cal.nodeName == "BODY")
				{
					_top = 0;
					_left = 0;
				}
			}
			_setTop(_qms_div,_top+_nav_text.offsetHeight);
			_setLeft(_qms_div,_left);
			_viewstate = this._loadViewState();
			_focused_date = new Date(_viewstate["FocusedDate"]+" UTC");
			var _months = _getElements("td","kcdMonth",_qms_div);
			for(var i=0;i<_months.length;i++)
			{
				_removeClass(_months[i],"kcdSelected");
			}			
			_addClass(_obj(this._id+"_qms_"+_focused_date.format("F")),"kcdSelected");
			var _years =  _getElements("td","kcdYear",_qms_div);
			for (var i = 0; i < _years.length; i++) 
			{
				_removeClass(_years[i],"kcdSelected");			
			}
			var _year = _obj(this._id+"_qms_"+_focused_date.getUTCFullYear());
			if(_exist(_year))
			{
				_addClass(_year,"kcdSelected");
			}
			else
			{
				var _first_year = parseInt(_replace(this._id+"_qms_","",_years[0].id));				
				var _additional = (_focused_date.getUTCFullYear()-4) - _first_year;
				for(var i=0;i<_years.length;i++)
				{
					var _year_num = _replace(this._id+"_qms_","",_years[i].id);
					_years[i].id = _replace(_year_num,parseInt(_year_num)+_additional,_years[i].id);
					_goFirstChild(_years[i]).innerHTML = parseInt(_year_num)+_additional;
				}					
				_year = _obj(this._id+"_qms_"+_focused_date.getUTCFullYear());
				_addClass(_year,"kcdSelected");
			}
			_viewstate["QMSDate"] = _viewstate["FocusedDate"];
			this._saveViewState(_viewstate);
			_setDisplay(_qms_div,_bool);
		}	
	},
	_handle_qms_year_onclick:function(_a,_e)
	{
		var _td = _goParentNode(_a);
		var _qms_div = _obj(this._id+"_qms");
		var _years = _getElements("td","kcdYear",_qms_div);
		for(var i=0;i<_years.length;i++)
		{
			_removeClass(_years[i],"kcdSelected");
		}
		_addClass(_td,"kcdSelected");
		var _year_num = parseInt(_replace(this._id+"_qms_","",_td.id));
		var _viewstate=this._loadViewState();
		var _qms_date = new Date(_viewstate["QMSDate"]+" UTC");
		_qms_date.setUTCFullYear(_year_num);
		_viewstate["QMSDate"] = _qms_date.format("n/j/Y");
		this._saveViewState(_viewstate);		
	},
	_handle_qms_month_onclick:function(_a,_e)
	{
		var _td = _goParentNode(_a);
		var _qms_div = _obj(this._id+"_qms");
		var _months = _getElements("td","kcdMonth",_qms_div);
		var _month_num = 0;
		for(var i=0;i<_months.length;i++)
		{
			_removeClass(_months[i],"kcdSelected");
			if(_td.id==_months[i].id)
			{
				_month_num = i;
			}
		}
		_addClass(_td,"kcdSelected");
		var _viewstate=this._loadViewState();
		var _qms_date = new Date(_viewstate["QMSDate"]+" UTC");
		_qms_date.setUTCMonth(_month_num);
		_viewstate["QMSDate"] = _qms_date.format("n/j/Y");
		this._saveViewState(_viewstate);		
	},
	_handle_qms_button_onclick:function(_button,_e)
	{
		var _button_type = _replace(this._id+"_qms_","",_button.id);
		var _settings = this._loadSettings();
		var _viewstate = this._loadViewState();
		switch(_button_type)
		{
			case "Today":
				var _today = new Date(_settings["Today"]+" UTC");
				var _focused_date = new Date(_viewstate["FocusedDate"]+" UTC");
				if(!_settings["ClientMode"])
				{
					this.navigate(new Date(_today.format("n/1/Y")+" UTC"));
					this.commit();
				}
				else if(_today.format("Y_n")!=_focused_date.format("Y_n"))
				{
					this.navigate(new Date(_today.format("n/1/Y")+" UTC"));					
				}
				break;
			case "OK":
				var _qms_date = new Date(_viewstate["QMSDate"]+" UTC");
				var _focused_date = new Date(_viewstate["FocusedDate"]+" UTC");
				if(!_settings["ClientMode"])
				{
					this.navigate(_qms_date);
					this.commit();
				}
				else if(_qms_date.format("Y_n")!=_focused_date.format("Y_n"))
				{
					this.navigate(_qms_date);
				}
				break;
			case "Cancel":
				break;								
		}
		this._show_quick_month_selector(0);
	},	
	_handle_qms_year_navigate_onclick:function(_a,_e)
	{
		var _qms_div = _obj(this._id+"_qms");
		var _years = _getElements("td","kcdYear",_qms_div);
		var _td = _goParentNode(_a);
		var _dir = _replace(this._id+"_qms_","",_td.id);
		var _additional = 10;		
		if(_dir=="Prev")
		{
			_additional = -10;
		}
		for(var i=0;i<_years.length;i++)
		{
			var _year_num = _replace(this._id+"_qms_","",_years[i].id);
			_years[i].id = _replace(_year_num,parseInt(_year_num)+_additional,_years[i].id);
			_goFirstChild(_years[i]).innerHTML = parseInt(_year_num)+_additional;
			_removeClass(_years[i],"kcdSelected");
		}					
		var _viewstate = this._loadViewState();
		var _qms_date = new Date(_viewstate["QMSDate"]+" UTC");
		var _year = _obj(this._id+"_qms_"+_qms_date.getUTCFullYear());
		if(_exist(_year))
		{
			_addClass(_year,"kcdSelected");
		} 
	},
	_handle_animate:function(_param)
	{
		var _direction = _param["direction"];
		var _type = _param["type"];
		var _steps = _param["steps"];
		var _current_step = _param["current_step"];
		var _old_td = _obj(this._id+"_oldtd");
		var _div_container = _goParentNode(_old_td,4);
		var _range = _old_td.offsetWidth;
		if(_current_step>_steps || _type=="none")
		{
			this._navigate_done();
		}
		else
		{
			if(typeof _calendar_timeout_id !="undefined")
			{
				clearTimeout(_calendar_timeout_id);
			}		
			var _animate_func = _get_animation_function(_type);
			if(_direction=="left")
			{
				_div_container.scrollLeft = _animate_func(_current_step,0,_range,_steps);
			}
			else
			{
				_div_container.scrollLeft = _animate_func(_current_step,_range,-_range,_steps);
			}
			_param["current_step"] = _current_step+1;
			_calendar_timeout_id = setTimeout("kcd_animate('"+this._id+"',"+ _json2string(_param) +")",20);
		}
	},
	_handle_next_onclick:function(_e)
	{
		var _settings = this._loadSettings();
		this.next();
		if(!_settings["ClientMode"])
		{
			this.commit();	
		}
	},
	_handle_prev_onclick:function(_e)
	{
		var _settings = this._loadSettings();
		this.prev();
		if(!_settings["ClientMode"])
		{
			this.commit();	
		}
	},
	_handle_fastnext_onclick:function(_e)
	{
		var _settings = this._loadSettings();
		this.fast_next();
		if(!_settings["ClientMode"])
		{
			this.commit();	
		}
	},
	_handle_fastprev_onclick:function(_e)
	{
		var _settings = this._loadSettings();
		this.fast_prev();
		if(!_settings["ClientMode"])
		{
			this.commit();	
		}
	},
	_handle_day_onclick:function(_td_day,_e)
	{
		if(_index("kcdDisabled",_getClass(_td_day))<0)
		{
			var _settings = this._loadSettings();
			var _date_string = _td_day.abbr;
			if(_index("kcdSelected",_getClass(_td_day))<0)
			{
				if(!_settings["EnableMultiSelect"])
				{
					this.deselect_all();
				}
				this.select(new Date(_date_string+" UTC"));			
			}
			else
			{
				this.deselect(new Date(_date_string+" UTC"));
			}
			if(!_settings["ClientMode"])
			{
				this.commit();	
			}			
		}
	},
	_handle_day_onmouseover:function(_td_day,_e)
	{
		if (_index("kcdDisabled", _getClass(_td_day)) < 0) 
		{
			_addClass(_td_day,"kcdOver");
			this._handleEvent("OnDayMouseOver",{});
		}
	},
	_handle_day_onmouseout:function(_td_day,_e)
	{
		if (_index("kcdDisabled", _getClass(_td_day)) < 0)
		{
			_removeClass(_td_day,"kcdOver");			
			this._handleEvent("OnDayMouseOut",{});
		} 
	},
	_handle_viewselector_onclick:function(_th,_e)
	{
		var _settings = this._loadSettings();
		var _table = _goParentNode(_th,3);
		var _days = _getElements("td","kcdDay",_table);
		for(var i=0;i<_days.length;i++)
		{
			if (_index("kcdDisabled", _getClass(_days[i])) < 0)
			{
				var _date_string = _days[i].abbr;
				this.select(new Date(_date_string+" UTC"));				
			}
		}
		if(!_settings["ClientMode"])
		{
			this.commit();	
		}
	},
	_handle_viewselector_onmouseover:function(_th,_e)
	{
		var _table = _goParentNode(_th,3);
		var _days = _getElements("td","kcdDay",_table);
		for(var i=0;i<_days.length;i++)
		{
			this._handle_day_onmouseover(_days[i],_e);	
		}
	},
	_handle_viewselector_onmouseout:function(_th,_e)
	{
		var _table = _goParentNode(_th,3);
		var _days = _getElements("td","kcdDay",_table);
		for(var i=0;i<_days.length;i++)
		{
			this._handle_day_onmouseout(_days[i],_e);	
		}
	},
	_handle_colheader_onclick:function(_th,_e)
	{
		var _settings = this._loadSettings();
		var _tr = _goParentNode(_th);
		var _pos = null;
		for(var i=0;i<_tr.childNodes.length;i++)
		{
			if (_th==_tr.childNodes[i])
			{
				_pos = i;
				break;
			}
		}
		if(_pos)
		{
			var _table = _goParentNode(_th,3);
			var _tbody = _table.lastChild;
			for(var i=0;i<_tbody.childNodes.length;i++)
			{
				_tr = _tbody.childNodes[i];
				var _td = _tr.childNodes[_pos];
				if(_index("kcdDay",_getClass(_td))>-1 && _index("kcdDisabled", _getClass(_td)) < 0)
				{
					var _date_string = _td.abbr;
					this.select(new Date(_date_string+" UTC"));						
				}
			}	
		}
		if(!_settings["ClientMode"])
		{
			this.commit();	
		}		
	},
	_handle_colheader_onmouseover:function(_th,_e)
	{
		var _tr = _goParentNode(_th);
		var _pos = null;
		for(var i=0;i<_tr.childNodes.length;i++)
		{
			if (_th==_tr.childNodes[i])
			{
				_pos = i;
				break;
			}
		}
		if(_pos)
		{
			var _table = _goParentNode(_th,3);
			var _tbody = _table.lastChild;
			for(var i=0;i<_tbody.childNodes.length;i++)
			{
				_tr = _tbody.childNodes[i];
				var _td = _tr.childNodes[_pos];
				if(_index("kcdDay",_getClass(_td))>-1)
				{
					this._handle_day_onmouseover(_td,_e);	
				}
			}	
		}
	},
	_handle_colheader_onmouseout:function(_th,_e)
	{
		var _tr = _goParentNode(_th);
		var _pos = null;
		for(var i=0;i<_tr.childNodes.length;i++)
		{
			if (_th==_tr.childNodes[i])
			{
				_pos = i;
				break;
			}
		}
		if(_pos)
		{
			var _table = _goParentNode(_th,3);
			var _tbody = _table.lastChild;
			for(var i=0;i<_tbody.childNodes.length;i++)
			{
				_tr = _tbody.childNodes[i];
				var _td = _tr.childNodes[_pos];
				if(_index("kcdDay",_getClass(_td))>-1)
				{
					this._handle_day_onmouseout(_td,_e);	
				}
			}	
		}
	},	
	_handle_rowheader_onclick:function(_th,_e)
	{
		var _settings = this._loadSettings();
		var _tr = _goParentNode(_th);
		var _days = _getElements("td","kcdDay",_tr);
		for(var i=0;i<_days.length;i++)
		{
				if(_index("kcdDay",_getClass(_days[i]))>-1 && _index("kcdDisabled", _getClass(_days[i])) < 0)
				{
					var _date_string = _days[i].abbr;
					this.select(new Date(_date_string+" UTC"));
				}
		}
		if(!_settings["ClientMode"])
		{
			this.commit();	
		}
	},
	_handle_rowheader_onmouseover:function(_th,_e)
	{
		var _tr = _goParentNode(_th);
		var _days = _getElements("td","kcdDay",_tr);
		for(var i=0;i<_days.length;i++)
		{
			if(_index("kcdDay",_getClass(_days[i]))>-1)
			{
				this._handle_day_onmouseover(_days[i],_e);	
			}
		}
	},
	_handle_rowheader_onmouseout:function(_th,_e)
	{
		var _tr = _goParentNode(_th);
		var _days = _getElements("td","kcdDay",_tr);
		for(var i=0;i<_days.length;i++)
		{
			if(_index("kcdDay",_getClass(_days[i]))>-1)
			{
				this._handle_day_onmouseout(_days[i],_e);	
			}
		}
	},
	_handle_qms_onclick:function(_e)
	{
		this._show_quick_month_selector(1);
	},
	_handle_outside_click:function(_e)
	{
		if(_obj(this._id)==null)return; // This necessary since if the calendar is loaded by ajax.
		this._show_quick_month_selector(0);
	},	
	_loadViewState:function()
	{
		var _input = _obj(this._id+"_viewstate");
		return eval("__="+_input.value);
	},
	_saveViewState:function(_viewstate)
	{
		var _input = _obj(this._id+"_viewstate");
		_input.value= _json2string(_viewstate);		
	},
	_loadSettings:function()
	{
		var _input = _obj(this._id+"_settings");
		return eval("__="+_input.value);
	},
	registerEvent:function(_name,_handle)
	{
		this._eventhandles[_name]=_handle;
	},	
	_handleEvent:function(_name,_arg)
	{
		return (_exist(this._eventhandles[_name]))?this._eventhandles[_name](this,_arg):true;
	},
	_registerPostLoadEvent:function(_name,_arg)
	{
		_calendar_list[this._id]["PostLoadEvent"][_name] = _arg;
	}	
}
/*
 * OnInit
 * OnLoad
 * OnBeforeSelect
 * OnSelect
 * OnBeforeDeselect
 * OnSelect
 * OnBeforeDeselect
 * OnDeselect
 * OnBeforeNavigate
 * OnNavigate
 * OnBeforeCommit
 * OnCommit
 * OnDayMouseOut
 * OnDayMouseOver
 */
var _calendar_list = new Array();
function KoolTimeView(_id)
{
	this._id = _id;
	this._eventhandles = new Array();
	this._init();	
}
KoolTimeView.prototype = 
{
	_init:function()
	{
		var _this = _obj(this._id);
		var _settings = this._loadSettings();
		var _times = _getElements("td","ktmTime",_this);
		for(var i=0;i<_times.length;i++)
		{
			_addEvent(_times[i],"mouseover",_ktm_time_onmouseover,false);
			_addEvent(_times[i],"mouseout",_ktm_time_onmouseout,false);
			_addEvent(_times[i],"click",_ktm_time_onclick,false);
		}
		var _client_events = _settings["ClientEvents"];
		for(var _name in _client_events)
		{
			if(typeof _client_events[_name]!="function") //Mootools
			{
				if (eval("typeof "+_client_events[_name]+" =='function'"))
				{
					this._eventhandles[_name] = eval(_client_events[_name]);
				}				
			}
		}
	},
	_loadSettings:function()
	{
		var _input = _obj(this._id+"_settings");
		return eval("__="+_input.value);
	},
	_handle_ktm_time_onmouseover:function(_td,_e)
	{
		_addClass(_td,"ktmOver");
		var _time = new Date("1/1/1970 "+_td.abbr+" UTC");
		this._handleEvent("OnMouseOver",{"Time":_time}); //Remember all time are UTC.
	},
	_handle_ktm_time_onmouseout:function(_td,_e)
	{
		_removeClass(_td,"ktmOver");
		var _time = new Date("1/1/1970 "+_td.abbr+" UTC");
		this._handleEvent("OnMouseOut",{"Time":_time}); //Remember all time are UTC.
	},
	_handle_ktm_time_onclick:function(_td,_e)
	{
		var _time = new Date("1/1/1970 "+_td.abbr+" UTC");
		if(!this._handleEvent("OnBeforeSelect",{"Time":_time})) return;
		this._handleEvent("OnSelect",{"Time":_time}); //Remember all time are UTC.
	},	
	registerEvent:function(_name,_handle)
	{
		this._eventhandles[_name]=_handle;
	},
	_handleEvent:function(_name,_arg)
	{
		return (_exist(this._eventhandles[_name]))?this._eventhandles[_name](this,_arg):true;
	}
}
/*
 * OnMouseOver
 * OnMouseOut
 * OnBeforeSelect
 * OnSelect
 */
function KoolDateTimePicker(_id,_enable_datepicker,_enable_timepicker)
{
	this._id = _id;
	this._eventhandles = new Array();
	this._enable_datepicker = _enable_datepicker;
	this._enable_timepicker = _enable_timepicker;
	this._selected_date = new Date((new Date()).format("n/j/Y")+" UTC");
	this._selected_time = new Date("1/1/1970 00:00:00 UTC");
	this._no_select_handle = false;
	this._init();	
}
KoolDateTimePicker.prototype = 
{
	_init:function()
	{
		var _settings = this._loadSettings();
		if(this._enable_datepicker)
		{
			var _dateopener = _obj(this._id+"_dateopener");
			_addEvent(_dateopener,"click",_dateopener_onclick,false);			
		}
		if(this._enable_timepicker)
		{
			var _timeopener = _obj(this._id+"_timeopener");
			_addEvent(_timeopener,"click",_timeopener_onclick,false);			
		}
		_addEvent(_obj(this._id+"_bound"),"mouseup",_cancel_click_propagation,false);
		_close_on_outside_click_ids.push(this._id);
		var _client_events = _settings["ClientEvents"];
		for(var _name in _client_events)
		{
			if(typeof _client_events[_name]!="function") //Mootools
			{
				if (eval("typeof "+_client_events[_name]+" =='function'"))
				{
					this._eventhandles[_name] = eval(_client_events[_name]);
				}				
			}
		}
		if(this._enable_datepicker)
		{
			var _calendar = eval(this._id+"_calendar");
			_calendar.registerEvent("OnSelect",_datetimepicker_on_calendar_select);			
		}
		if(this._enable_timepicker)
		{
			var _timeview = eval(this._id+"_timeview");
			_timeview.registerEvent("OnSelect",_datetimepicker_on_timeview_select);			
		}
		if(_getBrowser()=="ie6")
		{
			var _dummy_div = document.createElement("div");
			_dummy_div.innerHTML = "<iframe src=\"javascript:'';\" tabindex='-1' style='position:absolute;display:none;border:0px;z-index:500;filter:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)'>Your browser does not support inline iframe.</frame>";
			var _iframe = _goFirstChild(_dummy_div);
			var _bound = _obj(this._id+"_bound")
			var _picker = _goNextSibling(_goFirstChild(_bound));
			_bound.insertBefore(_iframe,_picker);			
		}
	},
	_is_datepicker_visible:function()
	{
		var _datepicker = _obj(this._id+"_datepicker");		
		if(_exist(_datepicker))
		{
			 return _getDisplay(_datepicker);
		}
		return false;
	},
	_is_timepicker_visible:function()
	{
		var _timepicker = _obj(this._id+"_timepicker");		
		if(_exist(_timepicker))
		{
			 return _getDisplay(_timepicker);
		}
		return false;		
	},
	get_value:function()
	{
		return (_obj(this._id)).value;
	},
	_parse_date:function(_str_date)
	{
		_str_date = _trim(_str_date," ");
		if (_str_date=="") return "Invalid Date";
		_str_date+=" ";
		var _settings = this._loadSettings();
		var _str_format = "";
		if(this._enable_datepicker && this._enable_timepicker)
		{
			_str_format = _settings["DateFormat"]+" "+_settings["TimeFormat"];
		}
		else if(this._enable_datepicker)
		{
			_str_format = _settings["DateFormat"];
		}
		else if(this._enable_timepicker)
		{
			_str_format = _settings["TimeFormat"];
		}
		_str_format = _trim(_str_format," ")+" ";
		var _keywords = new Array(	"d","D","j","l","N","S","w","z",
									"W",
									"F","m","M","n","t",
									"L","o","Y","y",
									"a","A","B","g","G","h","H","i","s","u",
									"e","I","O","P","T","Z",
									"c","r","U"						
		);
		var _str_keywords = "";
		for(var i=0;i<_keywords.length;i++)
		{
			_str_keywords += "["+_keywords[i]+"]";
		}
		var _separators = new Array();
		for(var i=0;i<_str_format.length;i++)
		{
			if( _index("["+_str_format.charAt(i)+"]",_str_keywords)<0)
			{
				_separators.push(_str_format.charAt(i));
			}
		}
		var _current_format = _str_format;
		var _current_date = _str_date;
		var _year=0;
		var _month=0;
		var _date=0;
		var _hour=0;
		var _minute=0;
		var _second=0;
		var _ampm = null;
		var _months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		var _short_months = new Array();
		for(var i=0;i<_months.length;i++)
		{
			_short_months[_months[i]]=i+1;
		}
		var _months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var _long_months = new Array();
		for(var i=0;i<_months.length;i++)
		{
			_long_months[_months[i]]=i+1;
		}
		for(var i=0;i<_separators.length;i++)
		{
			var _index_format = _index(_separators[i],_current_format);
			var _index_date = _index(_separators[i],_current_date);
			var _format = _current_format.substring(0,_index_format);
			var _value = (_index_date<0)?_current_date.substring(0):_current_date.substring(0,_index_date);
			switch(_format)
			{
				case "d":
					_date = _leading_zero_parseInt(_value);
					break;
				case "j":
				case "jS":
					_date = parseInt(_value);
					break;
				case "m":
					_month = _leading_zero_parseInt(_value);
					break;
				case "n":
					_month = parseInt(_value);
					break;
				case "F":
					_month = _long_months[_value];
					break;
				case "M":
					_month = _short_months[_value];
					break;
				case "o":
				case "Y":
					_year = parseInt(_value);
					break;
				case "y":
					_year = _leading_zero_parseInt(_value);
					break;
				case "a":
				case "A":
					_ampm = _value.toLowerCase();
					break;
				case "g":
				case "G":
					_hour = parseInt(_value);
					break;
				case "h":
				case "H":
					_hour = _leading_zero_parseInt(_value);
					break;
				case "i":
					_minute = _leading_zero_parseInt(_value);
					break;
				case "s":
					_second = _leading_zero_parseInt(_value);
					break;
			}
			if(_index_date<_current_date.length-1)
			{
				_current_format = _current_format.substring(_index_format+1);
				_current_date = _current_date.substring(_index_date+1);					
			}
			else
			{
				break;
			}
		}
		if(_ampm==null) _ampm = "am";
		if (_hour==12 && _ampm == "am")
		{
			_hour = 0;
		}
		else if ( 0 <_hour && _hour < 12 && _ampm == "pm") 
		{
			_hour += 12;
		}
		return (new Date(_month+"/"+_date+"/"+_year+" "+_hour+":"+_minute+":"+_second+" UTC"));
	},
	show_datepicker:function(_bool)
	{
		var _datepicker = _obj(this._id+"_datepicker");		
		if(_exist(_datepicker))
		{
			if(_bool)
			{
				if(!this._is_datepicker_visible())
				{
					if(!this._handleEvent("OnBeforeDatePickerOpen",{})) return;
					var _bound = _obj(this._id+"_bound");
					if(!isNaN(_bound.offsetHeight))
					{
						var _settings = this._loadSettings();
						_setTop(_datepicker,_bound.offsetHeight + _settings["OffsetTop"] -1);
						_setLeft(_datepicker,_settings["OffsetLeft"]);	
					}
					var _calendar = eval("__="+this._id+"_calendar");
					_calendar.deselect_all();
					var _calendar_viewstate = _calendar._loadViewState();
					var _calendar_focused_date = new Date(_calendar_viewstate["FocusedDate"]+" UTC");
					var _input = _obj(this._id);
					var _showing_datetime = this._parse_date(_input.value);
					if(!isNaN(_showing_datetime) && _showing_datetime!="Invalid Date" )
					{
						this._selected_date = new Date(_showing_datetime.format("n/j/Y")+" UTC");
						this._no_select_handle = true;
						_calendar.select(this._selected_date);
						this._no_select_handle = false;
						if(_calendar_focused_date.format("Y_n")!=_showing_datetime.format("Y_n"))
						{
							_setDisplay(_datepicker,1);
							_calendar.navigate(this._selected_date,1);
						}
					}
					_setDisplay(_datepicker,1);
					if(_getBrowser()=="ie6")
					{
						var _iframe = _goNextSibling(_goFirstChild(_bound));
						_setTop(_iframe,_getTop(_datepicker));
						_setLeft(_iframe,_getLeft(_datepicker));
						_setWidth(_iframe,_datepicker.offsetWidth);
						_setHeight(_iframe,_datepicker.offsetHeight);
						_setDisplay(_iframe,1);
					}
					_addClass(_bound,"kcdOpening");	
					this._handleEvent("OnDatePickerOpen",{});
				}
			}
			else
			{
				 if(this._is_datepicker_visible())
				 {
					if(!this._handleEvent("OnBeforeDatePickerClose",{})) return;
				 	var _bound = _obj(this._id+"_bound");
				 	_setDisplay(_datepicker,0);
					if(_getBrowser()=="ie6")
					{
						var _iframe = _goNextSibling(_goFirstChild(_bound));
						_setDisplay(_iframe,0);
					}
					_removeClass(_bound,"kcdOpening");
					this._handleEvent("OnDatePickerClose",{});
				 }
			}
		}		
	},
	show_timepicker:function(_bool)
	{
		var _timepicker = _obj(this._id+"_timepicker");		
		if(_exist(_timepicker))
		{
			if(_bool)
			{
				if(!this._is_timepicker_visible())
				{
					if(!this._handleEvent("OnBeforeTimePickerOpen",{})) return;
					var _bound = _obj(this._id+"_bound");
					if(!isNaN(_bound.offsetHeight))
					{
						var _settings = this._loadSettings();
						_setTop(_timepicker,_bound.offsetHeight + _settings["OffsetTop"] -1);
						_setLeft(_timepicker,_settings["OffsetLeft"]);	
					}
					_setDisplay(_timepicker,1);
					if(_getBrowser()=="ie6")
					{
						var _iframe = _goNextSibling(_goFirstChild(_bound));
						_setTop(_iframe,_getTop(_timepicker));
						_setLeft(_iframe,_getLeft(_timepicker));
						_setWidth(_iframe,_timepicker.offsetWidth);
						_setHeight(_iframe,_timepicker.offsetHeight);
						_setDisplay(_iframe,1);
					}					
					_addClass(_bound,"kcdOpening");
					this._handleEvent("OnTimePickerOpen",{});
				}
			}
			else
			{
				 if(this._is_timepicker_visible())
				 {
					if(!this._handleEvent("OnBeforeTimePickerClose",{})) return;
				 	var _bound = _obj(this._id+"_bound");
				 	_setDisplay(_timepicker,0);
					if(_getBrowser()=="ie6")
					{
						var _iframe = _goNextSibling(_goFirstChild(_bound));
						_setDisplay(_iframe,0);
					}					
					_removeClass(_bound,"kcdOpening");
					this._handleEvent("OnTimePickerClose",{});
				 }
			}
		}		
	},		
	_loadSettings:function()
	{
		var _input = _obj(this._id+"_settings");
		return eval("__="+_input.value);
	},		
	_handle_on_calendar_select:function(_date)
	{
		if(this._no_select_handle) return;
		var _settings = this._loadSettings();
		var _input = _obj(this._id);
		var _date_string = _date.format(_settings["DateFormat"]);		
		var _showing_datetime = this._parse_date(_input.value);
		if(!isNaN(_showing_datetime) && _showing_datetime!="Invalid Date" )
		{
			this._selected_time = new Date("1/1/1970 "+_showing_datetime.format("H:i")+" UTC");
		}
		var _time_string = this._selected_time.format(_settings["TimeFormat"]);
		if (this._enable_datepicker && this._enable_timepicker)
		{
			_input.value = _date_string+" "+_time_string;	
		}
		else if(this._enable_datepicker)
		{
			_input.value = _date_string;
		}
		else if(this._enable_timepicker)
		{
			_input.value = _time_string;
		}
		this._selected_date = _date;		
		this.show_datepicker(0);
		this._handleEvent("OnDateSelect",{});
		this._handleEvent("OnSelect",{});
	},
	_handle_on_timeview_select:function(_time)
	{
		if(this._no_select_handle) return;
		var _settings = this._loadSettings();
		var _input = _obj(this._id);
		var _time_string = _time.format(_settings["TimeFormat"]);
		var _showing_datetime = this._parse_date(_input.value);
		if(!isNaN(_showing_datetime) && _showing_datetime!="Invalid Date" )
		{
			this._selected_date = new Date(_showing_datetime.format("n/j/Y")+" UTC");
		}
		var _date_string = this._selected_date.format(_settings["DateFormat"]);
		if (this._enable_datepicker && this._enable_timepicker)
		{
			_input.value = _date_string+" "+_time_string;	
		}
		else if(this._enable_datepicker)
		{
			_input.value = _date_string;
		}
		else if(this._enable_timepicker)
		{
			_input.value = _time_string;
		}
		this._selected_time = _time;		
		this.show_timepicker(0);
		this._handleEvent("OnTimeSelect",{});
		this._handleEvent("OnSelect",{});
	},	
	_handle_dateopener_onclick:function(_e)
	{
		this.show_timepicker(0);	
		this.show_datepicker(!this._is_datepicker_visible());
	},
	_handle_timeopener_onclick:function(_e)
	{
		this.show_datepicker(0);		
		this.show_timepicker(!this._is_timepicker_visible());
	},
	_handle_outside_click:function(_e)
	{
		if(_obj(this._id)==null)return; // This necessary since if the calendar is loaded by ajax.
		this.show_datepicker(0);
		this.show_timepicker(0);
	},
	registerEvent:function(_name,_handle)
	{
		this._eventhandles[_name]=_handle;
	},
	_handleEvent:function(_name,_arg)
	{
		return (_exist(this._eventhandles[_name]))?this._eventhandles[_name](this,_arg):true;
	}
}
/*
 * OnBeforeDatePickerOpen
 * OnDatePickerOpen
 * OnBeforeTimePickerOpen
 * OnTimePickerOpen
 * OnBeforeDatePickerClose
 * OnDatePickerClose
 * OnBeforeTimePickerClose
 * OnTimePickerClose
 * OnDateSelect
 * OnTimeSelect
 * OnSelect
 * 
 * 
 */
function _datetimepicker_on_calendar_select(_sender,_args)
{
	var _datetimepicker = eval("__="+ _replace("_calendar","",_sender._id));
	_datetimepicker._handle_on_calendar_select(_args["Date"]);	
}
function _datetimepicker_on_timeview_select(_sender,_args)
{
	var _datetimepicker = eval("__="+ _replace("_timeview","",_sender._id));
	_datetimepicker._handle_on_timeview_select(_args["Time"]);
}
function _cancel_click_propagation(_e)
{
	_stopPropagation(_e);
	return _preventDefaut(_e);
}
var _close_on_outside_click_ids = new Array();
function _window_mouseup(_e)
{
	for(var i=0;i<_close_on_outside_click_ids.length;i++)
	{
		var _object = eval("__="+_close_on_outside_click_ids[i]);
		if(_exist(_object))
		{
			_object._handle_outside_click();
		}
	}
}
_addEvent(document,"mouseup",_window_mouseup,false);
function _get_calendar(_this)
{
	var _div_cal = _goParentNode(_this);
	while(_div_cal.nodeName!="DIV" || _index("KCD",_getClass(_div_cal))<0)
	{
		_div_cal = _goParentNode(_div_cal);
		if (_div_cal.nodeName == "BODY") return null;
	}
	return eval(_div_cal.id);	
}
function _next_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_next_onclick(_e);
}
function _prev_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_prev_onclick(_e);
}
function _fastnext_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_fastnext_onclick(_e);
}
function _fastprev_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_fastprev_onclick(_e);
}
function _day_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_day_onclick(this,_e);
}
function _day_onmouseover(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_day_onmouseover(this,_e);
}
function _day_onmouseout(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_day_onmouseout(this,_e);
}
function _viewselector_onmouseover(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_viewselector_onmouseover(this,_e);
}
function _viewselector_onmouseout(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_viewselector_onmouseout(this,_e);
}
function _viewselector_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_viewselector_onclick(this,_e);
}
function _colheader_onmouseover(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_colheader_onmouseover(this,_e);
}
function _colheader_onmouseout(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_colheader_onmouseout(this,_e);
}
function _colheader_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_colheader_onclick(this,_e);
}
function _rowheader_onmouseover(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_rowheader_onmouseover(this,_e);
}
function _rowheader_onmouseout(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_rowheader_onmouseout(this,_e);
}
function _rowheader_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_rowheader_onclick(this,_e);
}
function _qms_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_qms_onclick(this,_e);	
}
function _qms_month_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_qms_month_onclick(this,_e);	
}
function _qms_year_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_qms_year_onclick(this,_e);	
}
function _qms_button_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_qms_button_onclick(this,_e);	
}
function _qms_year_navigate_onclick(_e)
{
	var _cal = _get_calendar(this);
	_cal._handle_qms_year_navigate_onclick(this,_e);	
}
function _ktm_time_onmouseover(_e)
{
	var _time_view = _get_calendar(this);
	_time_view._handle_ktm_time_onmouseover(this,_e);	
}
function _ktm_time_onmouseout(_e)
{
	var _time_view = _get_calendar(this);
	_time_view._handle_ktm_time_onmouseout(this,_e);	
}
function _ktm_time_onclick(_e)
{
	var _time_view = _get_calendar(this);
	_time_view._handle_ktm_time_onclick(this,_e);	
}
function _dateopener_onclick(_e)
{
	var _picker = eval("__="+_replace("_dateopener","",this.id));
	_picker._handle_dateopener_onclick(_e);	
}
function _timeopener_onclick(_e)
{
	var _picker = eval("__="+_replace("_timeopener","",this.id));
	_picker._handle_timeopener_onclick(_e);	
}
function kcd_animate(_id,_param)
{
	var _cal = eval("__="+_id);
	_cal._handle_animate(_param);
}
if(typeof(__KCDInits)!='undefined' && _exist(__KCDInits))
{	
	for(var i=0;i<__KCDInits.length;i++)
	{
		__KCDInits[i]();
	}
}
