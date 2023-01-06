
var _identity = 0;
function _exist(_theObj)
{
	if (typeof(_theObj) == "undefined") 
	{
		return false;
	}
    return (_theObj!=null);
}

function _getIdentity()
{
	_identity++;
	return _identity;
}
function _exist(_theObj)
{
    return (_theObj!=null)
}
var KoolAjaxDebug=null;
function _debug(_s)
{
	if(_exist(KoolAjaxDebug))
		KoolAjaxDebug(_s);
}
function _obj(_oid)
{
    return document.getElementById(_oid);
}
function _newNode(_sTag,_oParent)
{
    _oNode = document.createElement(_sTag);
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

function _newTextNode(_txtContent,_objParent)
{
    var _node=document.createTextNode(_txtContent);
    _objParent.appendChild(_node);
    return _node;
}
function _removeAllChildren(_theObj)
{
    var _num = _theObj.childNodes.length;
    for(var i=0;i<_num;i++)
        _theObj.removeChild(_theObj.firstChild);
}

function _emptyFunction(){}


function _trip_hash(_url)
{
	if(_url.indexOf("#")>0)
	{
		return _url.substring(0,_url.indexOf("#"));	
	}
	else
	{
		return _url;
	}
}


function __parseBorderWidth(width) {
    var res = 0;
    if (typeof(width) == "string" && width != null 
                && width != "" ) {
        var p = width.indexOf("px");
        if (p >= 0) {
            res = parseInt(width.substring(0, p));
        }
        else {
             //do not know how to calculate other 
             //values (such as 0.5em or 0.1cm) correctly now
             //so just set the width to 1 pixel
            res = 1; 
        }
    }
    return res;
}

//returns border width for some element
function __getBorderWidth(element) {
    var res = new Object();
    res.left = 0; res.top = 0; res.right = 0; res.bottom = 0;
    if (window.getComputedStyle) {
        //for Firefox
        var elStyle = window.getComputedStyle(element, null);
        res.left = parseInt(elStyle.borderLeftWidth.slice(0, -2));  
        res.top = parseInt(elStyle.borderTopWidth.slice(0, -2));  
        res.right = parseInt(elStyle.borderRightWidth.slice(0, -2));  
        res.bottom = parseInt(elStyle.borderBottomWidth.slice(0, -2));  
    }
    else {
        //for other browsers
        res.left = __parseBorderWidth(element.style.borderLeftWidth);
        res.top = __parseBorderWidth(element.style.borderTopWidth);
        res.right = __parseBorderWidth(element.style.borderRightWidth);
        res.bottom = __parseBorderWidth(element.style.borderBottomWidth);
    }
   
    return res;
}
function _index(_search,_original)
{
	return _original.indexOf(_search);
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
};
function _purge(d) {
    var a = d.attributes, i, l, n;
    if (a) {
        l = a.length;
        for (i = 0; i < l; i += 1) {
			if (a[i]) n = a[i].name;//Fix for IE8 the a[i] is null
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

function _unescapes(_o)
{
	for (var _name in _o)
	{
		switch(typeof(_o[_name]))
		{
			case "string":
				try
				{
					_o[_name] = decodeURIComponent(_o[_name]);
					//Sometime there is encoding issue with decodeURIComponent.
					//It throws error of "malformed uri"
					//So we try to use the unescape instead.	
				}
				catch(_ex)
				{
					_o[_name] = unescape(_o[_name]);
				}
				
				
				break;
			case "object":
				_o[_name] = _unescapes(_o[_name]);
				break;			
		}
	}
	return _o;
}

function _preventDefaut(_e)
{
	if (_e.preventDefault)
		_e.preventDefault();
	else
		event.returnValue = false;
	return false;
}

//--------------------------------------------------
function KoolUpdatePanel(_oid,_loading)
{
	this._oid = _oid;
	this._loading = _loading;
	this.events = new Array();
	eval(_oid+"handleTrigger = function(){"+_oid+".update();}");
	this.triggers = new Array();
	this._updating = 0;
	this._eventhandles = new Array();
	this._params = new Array();
	this._bindDefaultTriggers();
}
KoolUpdatePanel.prototype = 
{
	update:function(_url)
	{
		if (!this._updating)
		{
			var _request = new KoolAjaxRequest({
				url:_url,
				onDone:_OnPanelUpdateDone,
				onError:_OnPanelUpdateError
			});
			
			var _panel = _obj(this._oid);
			_request.addArg("__updatepanel",this._oid);
			_getParamInsideElement(_panel,_request);
			//Load extra params
			for(var i=0;i<this._params.length;i++)
			{
				_request.addArg(this._params[i]._sName,this._params[i]._vValue);
			}
			//Reset params buffer
			this._params = new Array();
			//Call OnBeforeSendingRequest
			if (_exist(this._eventhandles["OnBeforeSendingRequest"]))
			{
				var _arg = new Object();
				_arg.UpdateRequest = _request;
				if (!this._eventhandles["OnBeforeSendingRequest"](this,_arg)) return;
			}			
			koolajax.sendRequest(_request);
			if (this._loading)
			{
				this._showLoading(1);
			}
			
			//Call OnSendingRequest
			if (_exist(this._eventhandles["OnSendingRequest"]))
			{
				this._eventhandles["OnSendingRequest"](this,null);
			}				
		}
	},
	setContent:function(_content)
	{
		var _oPanel = _obj(this._oid);
		//_purge(_oPanel);
		_oPanel.innerHTML = _content;
	},
	addTrigger:function(_elementid,_event)
	{
		var _element = _obj(_elementid);
		if(_exist(_element))
		{
			this.triggers.push({"id":_elementid,"ev":_event});
			_addEvent(_element,("_"+_event.toLowerCase()).replace("_on",""),eval(this._oid+"handleTrigger"),0);
		
		}
	},
	_showLoading:function(_b)
	{
		var _loadingPanel = _obj(this._oid+"_loading");
		var _oPanel = _obj(this._oid);
		if (_exist(_loadingPanel))
		{
			try
			{
				/*
				_loadingPanel.style.top = _getTop(_oPanel)+"px";
				_loadingPanel.style.left = _getLeft(_oPanel)+"px";
				_loadingPanel.style.width = _oPanel.offsetWidth+"px";
				_loadingPanel.style.height = _oPanel.offsetHeight+"px";			
				_loadingPanel.style.display = (_b)?"block":"none";
				*/
				//var _oPanel_pos = _getElementAbsolutePos(_oPanel);
				//_loadingPanel.style.top = _oPanel_pos.y+"px";
				//_loadingPanel.style.left = _oPanel_pos.x+"px";
				_loadingPanel.style.top = "0px";
				_loadingPanel.style.left = "0px";				
				_loadingPanel.style.width = (isNaN(_oPanel.offsetWidth)?0:_oPanel.offsetWidth)+"px";
				_loadingPanel.style.height = (isNaN(_oPanel.offsetHeight)?0:_oPanel.offsetHeight)+"px";			
				_loadingPanel.style.display = (_b)?"block":"none";
				if(_getBrowser()=="ie6")
				{
					var _iframe = _obj(this._oid+"_iframe");
					if(!_exist(_iframe))
					{
						var _dummy_div = document.createElement("div");
						_dummy_div.innerHTML = "<iframe src=\"javascript:'';\" tabindex='-1' style='position:absolute;display:none;border:0px;top:0px;left:0px;filter:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)'>Your browser does not support inline iframe.</frame>";
						_iframe = _goFirstChild(_dummy_div);
						_oPanel.insertBefore(_iframe,_loadingPanel);
						_iframe.id = this._oid+"_iframe";
					}
					
					_iframe.style.width = _loadingPanel.style.width;
					_iframe.style.height = _loadingPanel.style.height;
					_iframe.style.display = (_b)?"block":"none";
				}
			}catch(_e){}
		}
	},
	_bindDefaultTriggers:function()
	{
		var _oPanel = _obj(this._oid);
		var _inputs = _oPanel.getElementsByTagName("input");
		for(var i=0;i<_inputs.length;i++)
		{
			if(_inputs[i].type == "submit")
			{
				_addEvent(_inputs[i],"click",_submit_button_onclick,false);
			}
		}
	},
	_rebindTriggers:function()
	{
		for(var i=0;i<this.triggers.length;i++)
		{
			var _element = _obj(this.triggers[i]["id"]);
			if(_exist(_element))
			{
				_addEvent(_element,("_"+this.triggers[i]["ev"].toLowerCase()).replace("_on",""),eval(this._oid+"handleTrigger"),0);
			}
		}
	},
	attachData:function(_sName,_vValue)
	{
		var _tmp = new Object();
        _tmp._sName = _sName;
        _tmp._vValue = _vValue;		
		this._params.push(_tmp);
	},
	registerEvent:function(_eventName,_handleFunc)
	{
		this._eventhandles[_eventName] = _handleFunc;
	}	
}
function _submit_button_onclick(_e)
{
	var _div_updatepanel = this.parentNode;
	while((_div_updatepanel.className.indexOf("_kup")!=0))
	{
		_div_updatepanel = _div_updatepanel.parentNode;		
	}
	var _updatepanel = eval("__="+_div_updatepanel.id);
	//Send the data of submit button
	if(this.name!="")
	{
		_updatepanel.attachData(this.name,this.value);
	}
	_updatepanel.update();
	return _preventDefaut(_e);
}


function _getParamInsideElement(_theObj,_request)
{
	if (_theObj.name != "") 
	{
		switch (_theObj.nodeName.toLowerCase()) 
		{
			case "input":
				
				switch (_theObj.type.toLowerCase()) 
				{
					case "radio":
					case "checkbox":
						if (!_theObj.checked) break;					
					case "":
					case "text":
					case "hidden":
					case "file":
					case "password":
						_request.addArg(_theObj.name, _theObj.value);
						break;
				}
				
				break;
			case "select":
			case "textarea":
				_request.addArg(_theObj.name, _theObj.value);
				break;
		}
	}	
	for(var i=0;i<_theObj.childNodes.length;i++)
	{
		_getParamInsideElement(_theObj.childNodes[i],_request);
	}
}
function _OnPanelUpdateDone(_raw)
{
	var _start = _raw.indexOf("<updatepanel>")+13;	
	var _end = _raw.indexOf("</updatepanel>");
	var _ct = "";
	if (_start<13 || _end<0)
	{
		_ct = _raw;
	}
	else
	{
		var _ct = _raw.substring(_start,_end);	
	}
	
	
	var _panelId;
	for(var i=0;i<this.request._data.data.length;i++)
		if (this.request._data.data[i]._name=="__updatepanel")
			_panelId=this.request._data.data[i]._value;
	var _panel = eval(_panelId);
	
	//Call OnBeforeUpdatePanel
	if (_exist(_panel._eventhandles["OnBeforeUpdatePanel"])) 
	{
		var _arg = new Object();
		_arg.Content = _ct;
		if (!_panel._eventhandles["OnBeforeUpdatePanel"](_panel, _arg)) 
			return;
	}
	
	var _oPanel = _obj(_panelId);
	var _content  = _goFirstChild(_oPanel);
	//_purge(_content); // THe _purge make memory leak worse.
	_content.innerHTML = _ct;

	//var _oXmlDoc = koolajax.parseXml("<div></div>").firstChild;
	//link: work only on FF,IE,Opera (Safari+Chrome:notwork)
	/*
	var _linkTags = _oPanel.getElementsByTagName("link");
	for (var i = 0; i < _linkTags.length; i++) 
	{
		_link = _linkTags[i].cloneNode();
		//_oPanel.appendChild(_link);
		
		//for (var j = 0; j < _linkTags[i].attributes.length; j++) 
		//	_link.setAttribute(_linkTags[i].attributes[j].name, _linkTags[i].attributes[j].value);
	}
	*/
	
	//script: Combine all scripts to run
	
	/* Old script run
	var _scriptTags = _content.getElementsByTagName("script");
	var _all_script="";
	for (var i = 0; i < _scriptTags.length; i++) 
	{
		_all_script+=_scriptTags[i].text;
	}
	if (_all_script!="")
	{
		setTimeout(_all_script,5);	
	}
	*/
	var _scriptTags = _content.getElementsByTagName("script");
	var _inline_scripts = "";
	var _a = _scriptTags.length;
	for (var i = 0; i < _a; i++) 
	{
		if (_scriptTags[i].src!="")
		{
			var _new_script = _newNode("script",_content);
			_new_script.type="text/javascript";
			_new_script.src = _scriptTags[i].src;
		}
		else
		{
			_inline_scripts += _scriptTags[i].text;
		}
	}
	
	if(_inline_scripts!="")
	{
		var _new_script = _newNode("script",_content);
		_new_script.text = _inline_scripts;
	}
	
	_panel._rebindTriggers();
	_panel._bindDefaultTriggers();	
	if (_panel._loading)
	{
		_panel._showLoading(0);
	}
	//Call OnUpdatePanel
	if (_exist(_panel._eventhandles["OnUpdatePanel"])) {
		_panel._eventhandles["OnUpdatePanel"](_panel, null);
	}
	
}
function _OnPanelUpdateError(_error)
{
	var _panelId;
	for(var i=0;i<this.request._data.data.length;i++)
		if (this.request._data.data[i]._name=="__updatepanel")
			_panelId=this.request._data.data[i]._value;
	var _panel = eval(_panelId);	
	//Call OnBeforeUpdatePanel
	if (_panel._loading)
	{
		_panel._showLoading(0);
	}
	if (_exist(_panel._eventhandles["OnError"]))
	{
		var _arg = new Object();
		_arg.Error = _error;
		_panel._eventhandles["OnError"](_panel, _arg);
	}
}

var koolajax = {
	charset:null,
	_eventhandles: new Array(),
    _arrRequests: new Array(),
    sendRequest:function(_request)
    {
		if(_request._data.sync)
		{
			//synchronous call
			return _request._send();
		}
		else
		{		
			this._arrRequests.push(_request);
			_request._send();
		}
        
    },
	//ORSC = OnReadyStateChange
    ORSC:function(_oid)
    {
        //Deliver the ReadyStateChange
		var _pos =  this._getRequestPosition(_oid);
		var _request = this._arrRequests[_pos];
		if (_exist(_request))        
		{
			_request._onReadyState();
			if (_request._xhr.readyState == 4)
			{
				//if state 4 has reached, delete the request
				this._arrRequests.splice(_pos, 1);
				delete _request;//Free the memory.
			}
		}
    },
	_getRequestPosition:function(_oid)
	{
		var _pos = null;
		for(var i=0;i<this._arrRequests.length;i++)
			if (this._arrRequests[i]._oid==_oid)
			{
				_pos = i;
				break;
			}
		return _pos;
	},
	//RTO = RequestTimeOut
	RTO:function(_oid)
	{
		var _request = this._arrRequests[ this._getRequestPosition(_oid)];				
		if (_exist(_request)) 
		{
			_request._handleTimeout();			
		}		
	},
    callback:function(_request,_onDone,_url)
    {
        _request._data.url = _url;
        if(_exist(_onDone))
        {
			//Asynchonous
            _request._funcUserResult = _onDone;
            _request._data.onDone = _OnCallbackDone;
			_request._data.onError = _OnCallbackError;
			try
			{
				//Try the POST by default
				this.sendRequest(_request);	
			}
			catch(_e)
			{
				//Try the Get. This will fix the issue with IE6 but lie some security issue.
				//_request._data.method="get";
				//this.sendRequest(_request);
				//alert(_e);
				//_debug("IE6 issue:Could not do the POST request.");
			}
			
        }
        else
        {
			//Synchronous
			_request._data.sync = 1;
			var _oRes;
			try
			{
				//Try the POST by default
				var _ct = this.sendRequest(_request);
				var _start = _ct.indexOf("<callback>")+10;
				var _end = _ct.indexOf("</callback>");
				var _res = _ct.substring(_start,_end);				
				_oRes =  eval("__kr="+_res);
				_oRes = _unescapes(_oRes);
			}
			catch(_e)
			{
				//Try the Get
				//_request._data.method="get";
				//_oRes =  eval("__kr="+this.sendRequest(_request));
				//_debug("IE6 issue:Could not do the POST request.");
			}            
			
			if (_exist(_oRes))
			{
				if (_oRes["r"]!=null)
				{
					//No problem
					return _oRes["r"];
				}
				else
				{
					//On error throw exception and return null;
					throw(_oRes["e"]);
					return;
				}				
			}
        }
    },
    funcRequest:function(_funcName,_args)
    {
		var _request = new KoolAjaxRequest({});
		_request.addArg("__func",_funcName);
        for(var i=0;i<_args.length;i++)
            _request.addArg("__args[]",_args[i]);
        return _request;
    },
    updatePanel:function(_panelId,_url)
    {
		var _oUpdatePanel = eval(_panelId);
		if (_exist(_oUpdatePanel))
		{
			_oUpdatePanel.update(_url);			
		}
    },
	parseXml:function(_xmlData)
	{
		
	    if (!window.DOMParser) {
	        var _progIDs = [ 'Msxml2.DOMDocument.3.0', 'Msxml2.DOMDocument' ];
	        for (var i = 0, l = _progIDs.length; i < l; i++) {
	            try {
	                var _xmlDOM = new ActiveXObject(_progIDs[i]);
	                _xmlDOM.async = false;
	                _xmlDOM.loadXML(_xmlData);
	                _xmlDOM.setProperty('SelectionLanguage', 'XPath');
	                return _xmlDOM;
	            }
	            catch (ex) {
	            }
	        }
	    }
	    else {
	        try {
	            var _domParser = new window.DOMParser();
	            return _domParser.parseFromString(_xmlData, 'text/xml');
	        }
	        catch (ex) {
	        }
	    }

	},
	load:function(_sPath,_onDone)
	{
		var _request = new KoolAjaxRequest({
			method:"get",
			url:_sPath,
			onDone:_onDone,
			sync:(!_exist(_onDone))
		});
		return this.sendRequest(_request);			
	},
	loadCss:function(_sPath,_onDone)
	{
		//Working well in IE,FF,Opera ... except for Safari
		var _request = new KoolAjaxRequest({
			method:"get",
			url:_sPath,
			onDone:_OnLoadCSSDone,
			sync:false
		});
		_request._loadCssDone = _onDone;
		this.sendRequest(_request);
	},
	loadScript:function(_sPath,_onDone)
	{
		//Working well in IE,FF,Opera,Safari
		var _request = new KoolAjaxRequest({
			method:"get",
			url:_sPath,
			onDone:_OnLoadScriptDone,
			sync:false
		});
		_request._loadScriptDone = _onDone;
		this.sendRequest(_request);			
	}
}

function _OnLoadCSSDone(_ct)
{
	var _style = _newNode("style",document.body);
	_style.setAttribute("type","text/css");
	if(_style.styleSheet)
	{
		_style.styleSheet.cssText = _ct;	
	}
	else
	{
		_newTextNode(_ct,_style);
	}
	
	if (_exist(this.request._loadCssDone))
		this.request._loadCssDone(this.url);
}
function _OnLoadScriptDone(_ct)
{
	var _script = _newNode("script",document.body);
	_script.setAttribute("type","text/javascript");
	_script.text = _ct;
	if (_exist(this.request._loadScriptDone))
		this.request._loadScriptDone(this.url);	
}

function _OnCallbackDone(_ct)
{
	var _start = _ct.indexOf("<callback>")+10;
	var _end = _ct.indexOf("</callback>");
	var _res = _ct.substring(_start,_end);
	var _oRes = eval("__kr="+_res);
	
	_oRes = _unescapes(_oRes);
	
	this.request._funcUserResult(_oRes["r"],_oRes["e"]);
}
function _OnCallbackError(_error)
{
	this.request._funcUserResult(null,_error);
}

function KoolAjaxRequest(_data)
{
	this._xhr = null;
	//init everything here.
	if (!_exist(_data.sync)) _data.sync=0;
	if (!_exist(_data.method)) _data.method="post";
	if (!_exist(_data.charset)) _data.charset=koolajax.charset;
	if (!_exist(_data.data)) _data.data= new Array();
	
	_data.request = this;//Refer to parent.
	this._data = _data;
	this._oid = _getIdentity();
}
KoolAjaxRequest.prototype = 
{
	_send:function()
	{
		//Init _xhr here
	    var _xhr = null;
	    //var _msxmlhttp = new Array('Msxml2.XMLHTTP.6.0','Msxml2.XMLHTTP.5.0','Msxml2.XMLHTTP.4.0','Msxml2.XMLHTTP.3.0', 'Msxml2.XMLHTTP','Microsoft.XMLHTTP');
		var _msxmlhttp = [ 'Msxml2.XMLHTTP.3.0', 'Msxml2.XMLHTTP' ];
		for (var i = 0; i < _msxmlhttp.length && _xhr==null; i++) 
	    {
	        try
	        {				
				if (typeof ActiveXObject != "undefined")
				{
					_xhr = new ActiveXObject(_msxmlhttp[i]);
				}
	        }
	        catch(e)
	        {
	            _xhr = null;
	        }
	    }
	 			
	    if(!_xhr && typeof XMLHttpRequest != "undefined")
		{
			_xhr = new XMLHttpRequest();
			_xhr.overrideMimeType('text/plain');
		}
	        
	    this._xhr = _xhr;
		
		if (!_exist(_xhr))
		{
			_debug("Could not able to create XHTMLRequest");
			return false;
		}
		//Sending request
        if (!_exist(this._data.url)) this._data.url = _trip_hash(window.location.href);
		//if (!_exist(this._data.url)) this._data.url = "http://localhost/KoolPHPSuite/KoolAjax/index.php";
		
		var _sentdata="__koolajax=1";
        for(var _dt in this._data.data)
			if(typeof this._data.data[_dt]!="function")
            	_sentdata+="&"+this._data.data[_dt]._name+"="+this._data.data[_dt]._value;
        
		if (this._data.method.toLowerCase()!="post")
            this._data.url+=((this._data.url.indexOf("?")<0)?"?":"&")+_sentdata;//Get
        _xhr.open(this._data.method,this._data.url,!this._data.sync);

        if (!this._data.sync) 
			_xhr.onreadystatechange = eval("__orsc=function(){koolajax.ORSC("+this._oid+")}");
        
		
		if (_exist(this._data.timeout))
		{
			this._timeoutid = setTimeout("koolajax.RTO("+this._oid+")",this._data.timeout);
		}
		
		this._abort = false;
		
        if (this._data.method.toLowerCase()!="post")
        {
            _xhr.send(null); //Get
        }
        else
        {
            _xhr.setRequestHeader("Method", "POST " + this._data.url + " HTTP/1.1");
   	        _xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"+((this._data.charset!=null)?";charset="+this._data.charset:""));
			_xhr.send(_sentdata);	            
        }
		
        _debug(this._data.method);
        _debug(_sentdata);
        _debug("Data send...");
        
        if (this._data.sync)
        {   
            return _xhr.responseText;
        }		
		
	},
	_handleTimeout:function()
	{
		//Handle timeout
		if(_exist(this._data.onTimeOut))
		{
			var _continue = this._data.onTimeOut();
			if(_continue)
			{
				//Set timeout
				this._timeoutid = setTimeout("koolajax.RTO("+this._oid+")",this._data.timeout);
			}
			else
			{
				//Abort
				this.abort();
			}
		}
		else
		{
			this.abort();
		}
	},
	abort:function()
	{
		this._abort = true;
		this._xhr.abort();
		if(_exist(this._data.onAbort))
		{
			this._data.onAbort();
		}		
	},
	addArg:function(_name,_value)
	{
		//Adding more arg to data
		var _tmp = new Object();
		_tmp._name= _name;
		_tmp._value =  encodeURIComponent(_value);
		//_tmp._value =  _value;
		this._data.data.push(_tmp);
	},
	_onReadyState:function()
	{
		_debug(this._xhr.readyState);
		switch(this._xhr.readyState)
		{
			case 1:
				if (_exist(this._data.onOpen)) this._data.onOpen();
				break;
			case 2:
				if (_exist(this._data.onSent)) this._data.onSent();
				break;
			case 3:
				if (_exist(this._data.onReceive)) this._data.onReceive();
				break;				
			case 4:
				//Clear timeoutid if exists
				_debug(this._xhr.responseText);
				if(_exist(this._timeoutid)) clearTimeout(this._timeoutid);
				if(!this._abort)
				{
					if (this._xhr.status==200)
					{
						var _rText = this._xhr.responseText;
						var _script = null;
						var _startScript = _rText.indexOf("[!@s>");
						// There is script
						if (_startScript>0)
						{
							_script = _rText.substring(_startScript+5,_rText.length);
							_rText = _rText.substr(0,_startScript);
						}
						//Status: OK
						if (_exist(this._data.onDone))
							this._data.onDone(_rText);
						//Run script
						if (_exist(_script)) 
						{
							setTimeout(_script,20);//Make sure everything is ready.
						}
							
					}
					else
					{
						//alert(this._xhr.status);
						//Error code : http://msdn.microsoft.com/en-us/library/ms767625(VS.85).aspx
						if (_exist(this._data.onError))
							this._data.onError(this._xhr.status);
					}
					
				}
				this._xhr.onreadystatechange = _emptyFunction;
				break;
		}
	}
}