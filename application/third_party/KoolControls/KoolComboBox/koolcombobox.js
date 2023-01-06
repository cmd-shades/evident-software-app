/**
 * @author Nghiem Anh Tuan
 */
function _exist(_theObj)
{
    return (_theObj!=null)
}
if (!_exist(_identity))
{
	//To prevent the redeclare of _identity when another combobox is added.
	var _identity = 0;	
}
function _getIdentity()
{
	_identity++;
	return _identity;
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
function _setHeight(_theObj,_val)
{
    _theObj.style.height=_val+"px";
}

function _setWidth(_theObj,_val)
{
    _theObj.style.width=_val+"px";
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


function _expired()
{
	return false;
}


function _setDisplay(_theObj,_val)
{
    _theObj.style.display=(_val)?"block":"none";
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
function _json2string(_o)
{
	
	var _res="";
	for (var _name in _o)
	{
		switch(typeof(_o[_name]))
		{
			case "string":
				if(_exist(_o.length))
					_res+="'"+_o[_name]+"',";
				else
					_res+="'"+_name+"':'"+_o[_name]+"',";
				break;
			case "number":
				if(_exist(_o.length))
					_res+=_o[_name]+",";
				else
					_res+="'"+_name+"':"+_o[_name]+",";
				break;
			case "object":
				if(_exist(_o.length))
					_res+=_json2string(_o[_name])+",";
				else			
					_res+="'"+_name+"':"+_json2string(_o[_name])+",";					
				break;								
		}
	}
	if (_res.length>0)
		_res = _res.substring(0,_res.length-1);
	_res=(_exist(_o.length))?"["+_res+"]":"{"+_res+"}";
	if (_res=="{}") _res="null";
	return _res;
}

//returns absolute position of some element within document
function _getElementAbsolutePos(element) {
	var __isIE =  navigator.appVersion.match(/MSIE/);
	var __userAgent = navigator.userAgent;
	var __isFireFox = __userAgent.match(/firefox/i);
	var __isFireFoxOld = __isFireFox && 
	   (__userAgent.match(/firefox\/2./i) || __userAgent.match(/firefox\/1./i));
	var __isFireFoxNew = __isFireFox && !__isFireFoxOld;
	
    var res = new Object();
    res.x = 0; res.y = 0;
    if (element !== null) {
        res.x = element.offsetLeft;
        res.y = element.offsetTop;
        
        var offsetParent = element.offsetParent;
        var parentNode = element.parentNode;
        var borderWidth = null;

        while (offsetParent != null) {
            res.x += offsetParent.offsetLeft;
            res.y += offsetParent.offsetTop;
            
            var parentTagName = offsetParent.tagName.toLowerCase();    

            if ((__isIE && parentTagName != "table") || 
                (__isFireFoxNew && parentTagName == "td")) {            
                borderWidth = __getBorderWidth(offsetParent);
                res.x += borderWidth.left;
                res.y += borderWidth.top;
            }
            
            if (offsetParent != document.body && 
                offsetParent != document.documentElement) {
                res.x -= offsetParent.scrollLeft;
                res.y -= offsetParent.scrollTop;
            }

            //next lines are necessary to support FireFox problem with offsetParent
               if (!__isIE) {
                while (offsetParent != parentNode && parentNode !== null) {
                    res.x -= parentNode.scrollLeft;
                    res.y -= parentNode.scrollTop;
                    
                    if (__isFireFoxOld) {
                        borderWidth = __getBorderWidth(parentNode);
                        res.x += borderWidth.left;
                        res.y += borderWidth.top;
                    }
                    parentNode = parentNode.parentNode;
                }    
            }

            parentNode = offsetParent.parentNode;
            offsetParent = offsetParent.offsetParent;
        }
    }
    return res;
}



function _getLeft(_o)
{
	return _getElementAbsolutePos(_o).x;
	var _curleft = 0;
	if (_o.offsetParent) 
		while (1) 
		{
			_curleft += _o.offsetLeft;
			if (!_o.offsetParent) break;
			_o = _o.offsetParent;
		}
	else if (_o.x) 
		_curleft += _o.x;
	return _curleft;
}
function _getTop(_o)
{
	return _getElementAbsolutePos(_o).y;
	var _curtop = 0;
	if (_o.offsetParent) 
	while (1) 
	{
		_curtop += _o.offsetTop;
		if (!_o.offsetParent) break;
		_o = _o.offsetParent;
	}
	else if (_o.y) 
		_curtop += _o.y;
	return _curtop;
}


function _index(_search,_original)
{
	return _original.indexOf(_search);
}


function _preventDefaut(_e)
{
	if (_e.preventDefault)
		_e.preventDefault();
	else
		event.returnValue = false;
	return false;
}
/* No need 
function _GetCursorPosition(_ObjTextBox)
{
	if(_ObjTextBox.selectionStart)
	{
		return _ObjTextBox.selectionStart;
	}
	else if (_ObjTextBox.createTextRange)
	{
		var i = _ObjTextBox.value.length;
		var _Range = document.selection.createRange().duplicate();
		while (_Range.parentElement()== _ObjTextBox && _Range.move("character",1)==1) 
		    --i;
		return i;
	}
	return -1;
}
*/

//KoolComboBox------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------

function KoolComboboxItem(_id)
{
	this._id = _id;
	this.id = _id;
}
KoolComboboxItem.prototype = 
{
	_getCombobox:function()
	{
		return eval(this._id.substring(0,_index(".",this._id)));
	},
	getData:function()
	{
		var _data = eval("__="+_goFirstChild(_obj(this._id)).value);
		for (var i in _data) 
		{
			if(typeof _data[i]!="function") //Mootools
			{
				try
				{
					_data[i] = decodeURIComponent(_data[i]);	
				}
				catch(ex)
				{
					_data[i] = unescape(_data[i]);
				}				
			}
		}
		return _data;
	},
	enable:function(_bool)
	{
		/*
		 * Make the item enable if it is disabled.
		 */
		var _li = _obj(this._id);
		if(_expired()) return;
		(_bool)?_removeClass(_li,"kcbDisable"):_addClass(_li,"kcbDisable");
	},
	isEnabled:function()
	{
		return _index("kcbDisable",_getClass(_obj(this._id)))<0;
	},
	isSelected:function()
	{
		return _index("kcbSelected",_getClass(_obj(this._id)))>-1;
	},	
	setVisible:function(_visible)
	{
		if(_expired()) return;
		_setDisplay(_obj(this._id),_visible);
	},
	select:function()
	{
		var _combo = this._getCombobox();		
//		if (!this.isSelected())
//		{
			if(_expired()) return;
			if (!_combo._handleEvent("OnBeforeSelect",{"Item":this})) return;
			/*
			 * Make this item selected
			 */
			var _li = _obj(this._id);
			_combo._removeAllSelectedItems();
			_addClass(_li,"kcbSelected");
			//Now insert selected text and value to text and value input
			var _selectedText = _obj(_combo._id+"_selectedText");
			var _selectedValue = _obj(_combo._id+"_selectedValue");
			var _itemdata = this.getData();
			_selectedText.value = _itemdata["text"];
			_selectedValue.value = _itemdata["value"];
			
			_combo._handleEvent("OnSelect",{"Item":this});					
//		}
	},
	unselect:function()
	{
		/*
		 * make this item unselected.
		 * The text in combobox will be blank.
		 */
		var _combo = this._getCombobox();

//		if (this.isSelected())
//		{
			if (!_combo._handleEvent("OnBeforeUnselect",{"Item":this})) return;
			/*
			 * Make this item unselected
			 */
			var _li = _obj(this._id);
			_combo._removeAllSelectedItems();
			_removeClass(_li,"kcbSelected");
			//Now insert selected text and value to text and value input
			var _selectedText = _obj(_combo._id+"_selectedText");
			var _selectedValue = _obj(_combo._id+"_selectedValue");
			_selectedText.value = "";
			_selectedValue.value = "";
			_combo._handleEvent("OnUnselect",{"Item":this});					
//		}


	},
	_handle_click:function(_e)
	{
		if (this.isEnabled())
		{
			//Only select if the item is enabled
			this.select();
			var _combo = this._getCombobox();
			var _iv = _combo._inputValidate;
			_combo.close();
			_combo._inputValidate = _iv;
			//Above technique to avoid validate when user select by mouse.	
		}
		else
		{
			//Set focus back to the _selectedText
			var _selectedText = _obj(this._getCombobox()._id+"_selectedText");
			_selectedText.focus();
		}
	},
	_handle_mouseover:function(_e)
	{
		this._getCombobox()._removeSelectFocus();
		if (this.isEnabled())
		{
			var _li = _obj(this._id);
			_addClass(_li,"kcbSelectFocus");			
		}
	},
	_handle_mouseout:function(_e)
	{
		var _li = _obj(this._id);
		_removeClass(_li,"kcbSelectFocus");
	}
}

function KoolCombobox(_id,_mode,_boxWidth,_boxHeight,_minBoxHeight,_maxBoxHeight,_inputValidate,_openDirection,_align,_serviceFunction)
{
	this._id = _id;
	this.id = _id;	
	this._mode = _mode;
	this._boxWidth = (_boxWidth=="auto")?-1:parseInt(_boxWidth);
	this._boxHeight = (_boxHeight=="auto")?-1:parseInt(_boxHeight);
	this._minBoxHeight = (_minBoxHeight=="auto")?-1:parseInt(_minBoxHeight);	
	this._maxBoxHeight = (_maxBoxHeight=="auto")?-1:parseInt(_maxBoxHeight);
	this._inputValidate = _inputValidate;
	this._openDirection = _openDirection;
	this._align = _align;	
	this._serviceFunction = (_serviceFunction!="")?_serviceFunction:null;
	this._eventhandles = new Array();
	this._init();
	//Select the selectedIndex
	/*
	var _selectedText = _obj(_id+"_selectedText");
	var _selectedValue = _obj(_id+"_selectedValue");
	_selectedText.value = "";
	_selectedValue.value = "";
	if (_exist(_selectedIndex))
	{
		var _itemdata = (new KoolComboboxItem(_id+".i"+_selectedIndex)).getData();
		_selectedText.value = _itemdata["text"];
		_selectedValue.value = _itemdata["value"];		
	}
	*/
}
KoolCombobox.prototype = 
{
	_init:function()
	{
		var __cb = _obj(this._id);
		var _kcbCombobox = _goFirstChild(__cb,2);
		var _kcbBox = _goNextSibling(_kcbCombobox);
		_addEvent(_kcbCombobox,"mouseover",_kcbCommbobox_mouseover,false);
		_addEvent(_kcbCombobox,"mouseout",_kcbCommbobox_mouseout,false);
		_addEvent(_kcbCombobox,"mousedown",_kcbCommbobox_mousedown,false);
		_addEvent(_kcbCombobox,"mouseup",_kcbCommbobox_mouseup,false);
		_addEvent(_kcbCombobox,"click",_kcbCommbobox_click,false);

		/*
		 * Adding handle to close combobox
		 */
		_addEvent(document,"mousedown",eval("___=function(){if ("+this._id+".isOpening())"+this._id+".close()}"),false);
		_addEvent(_kcbBox,"mousedown",_kcbBox_mousedown,false);
		
		//Attach event to item
		this._turn_itemsEvent(1);
		
		/*
		 * Adding event to selectedText box
		 */
		var _selectedText = _obj(this._id+"_selectedText");
		//May be wrong here
		_addEvent(_selectedText,"keydown",_selectedText_keypress,false);
		_addEvent(_selectedText,"focus",_selectedText_focus,false);
		
		//Hack for the width of long text in IE: but problem with Safari,
		//We should make change only for IE
		var _agent=navigator.userAgent.toLowerCase();
		var _isIE=((_index("msie",_agent)!=-1) && (_index("opera",_agent)==-1));
		if (_isIE)
		{
			var _text = _selectedText.value;
			_selectedText.value = "";
			// If the combobox is inside a table, then the box is not visible
			// at the time the script run, so the offsetWidth will be 0.
			if (_selectedText.offsetWidth>0)
			{
				_setWidth(_selectedText,_selectedText.offsetWidth);	
			}
			_selectedText.value = _text;			
		}
		//--------End hack ---------------------
		
		//Init id for items
		var _divItemBox = _getElements("div","kcbItemBox",_obj(this._id))[0];
		var _items = _getElements("li","kcbItem",_divItemBox);
		var _selectedText = _obj(this._id+"_selectedText");
		var _selectedValue = _obj(this._id+"_selectedValue");
		_selectedText.value = "";
		_selectedValue.value = "";
		for(var i=0;i<_items.length;i++)
		{
			_items[i].id=this._id+".i"+ _getIdentity();
			if(_index("Selected",_getClass(_items[i]))>0)
			{
				_itemdata = (new KoolComboboxItem(_items[i].id)).getData();
				_selectedText.value = _itemdata["text"];
				_selectedValue.value = _itemdata["value"];
			}
		}	
		
	},
	_turn_itemsEvent:function(_on)
	{
		var _divItemBox = _getElements("div","kcbItemBox",_obj(this._id))[0];
		var _items = _getElements("li","kcbItem",_divItemBox);
		
		var _func = (_on)?_addEvent:_removeEvent;
		
		for(var i=0;i<_items.length;i++)
		{
			_func(_items[i],"click",_item_click,false);
			_func(_items[i],"mouseover",_item_mouseover,false);			
			_func(_items[i],"mouseout",_item_mouseout,false);			
		}
				
		/*
		for(var i=0;i<_items.length;i++)
		{
			if (_on)
			{
				_addEvent(_items[i],"click",_item_click,false);
				_addEvent(_items[i],"mouseover",_item_mouseover,false);			
				_addEvent(_items[i],"mouseout",_item_mouseout,false);			
			}
			else
			{
				_removeEvent(_items[i],"click",_item_click,false);
				_removeEvent(_items[i],"mouseover",_item_mouseover,false);
				_removeEvent(_items[i],"mouseout",_item_mouseout,false);			
			}
		}
		*/
	},
	_removeAllSelectedItems:function()
	{
		//Remove in item
		var _divItemBox = _getElements("div","kcbItemBox",_obj(this._id))[0];
		var _items = _getElements("li","kcbItem",_divItemBox);
		for(var i=0;i<_items.length;i++)
		{
			_removeClass(_items[i],"kcbSelected");
		}
		//Remove all in clientstate
		//...
				
	},
	getItemIds:function()
	{
		var _divItemBox = _getElements("div","kcbItemBox",_obj(this._id))[0];
		var _items = _getElements("li","kcbItem",_divItemBox);
		var _ids = new Array();
		if(_expired()) return _ids;
		for(var i=0;i<_items.length;i++)
		{
			_ids.push(_items[i].id);
		}
		return _ids;				
	},
	getItem:function(_itemid)
	{
		//Return KoolComboBoxItem
		return new KoolComboboxItem(_itemid);	
	},
	getText:function()
	{
		return _obj(this._id+"_selectedText").value;
	},
	getValue:function()
	{
		return _obj(this._id+"_selectedValue").value;
	},	
	open:function()
	{
		//if (!this.isOpening())
		//{
			/*
			 * Open the div panel of combobox
			 */
			if (!this._handleEvent("OnBeforeOpen",{})) return;
			
			var _div = _goFirstChild(_obj(this._id));
			var _combo = _goFirstChild(_div);
			var _box = _goNextSibling(_goFirstChild(_div));
			var _iframe = _goNextSibling(_box);

			//Set the width of box
			var _boxwidth = (this._boxWidth>0)?this._boxWidth:_combo.offsetWidth;
			_setWidth(_box,_boxwidth);

			
			var _itembox = _getElements("div","kcbItemBox",_div)[0];
			
			_itembox.style.height="auto";
					
			/*
			 * Open the box
			 */
			_addClass(_div,"kcbOpen");
			
			_div.style.position = "relative";
			
			//Set height for the itembox
			if (this._boxHeight>0)
			{
				_setHeight(_itembox,this._boxHeight);
			}
			else
			{
				if (_itembox.offsetHeight<this._minBoxHeight && this._minBoxHeight>0)
				{
					_setHeight(_itembox,this._minBoxHeight);
				}
				else if (_itembox.offsetHeight>this._maxBoxHeight && this._maxBoxHeight>0)
				{
					_setHeight(_itembox,this._maxBoxHeight);
				}
			}
			_itembox.scrollTop = 0;
			
			var _combo_top = _combo.offsetTop;
			var _combo_left = _combo.offsetLeft;
			
			//Set position of box,
			switch(this._openDirection)
			{
				case "up":
					_box.style.top = _combo_top - _box.offsetHeight +"px";
					break;
				case "auto":
				case "down":
				default:
					_box.style.top = _combo_top + _combo.offsetHeight +"px";
					break;					
			}
			_box.style.left = ((this._align=="right")?_combo_left+_combo.offsetWidth-_boxwidth:_combo_left) +"px";
			
			
			if(_exist(_iframe))
			{
				_setWidth(_iframe,_box.offsetWidth);
				_setHeight(_iframe,_box.offsetHeight);
				_iframe.style.top = _box.style.top;
				_iframe.style.left = _box.style.left;				
			}			
			
			_selectedText = _obj(this._id+"_selectedText");
			_selectedText.focus();
			_selectedText.select();			
			this._handleEvent("OnOpen",{});								
		//}
		
	},
	isOpening:function()
	{
		var _div = _goFirstChild(_obj(this._id));
		return _index("Open",_getClass(_div))>0;
		
	},
	
	close:function()
	{
		
		
		//if(this.isOpening())
		//{
			if (!this._handleEvent("OnBeforeClose",{})) return;
			/*
			 * Close the div panel of combobox
			 */
			var _div = _goFirstChild(_obj(this._id));
			_removeClass(_div,"kcbOpen");
			_div.style.position = "static";
			
			this._removeSelectFocus();
			
				var _selectedText = _obj(this._id+"_selectedText");
				var _text = _selectedText.value;
				if (this._inputValidate)
				{
					for(var i=0;i<=_text.length;i++)
					{
						var _keyword = _text.substr(0,_text.length-i);
						var _itemids = this._filter(_keyword,"text",1,false);
						if (_itemids.length>0)
						{
							break;
						}
					}
					if (_itemids.length>0)
					{
						//Only do selection if the text is fully found
						var _oitem = new KoolComboboxItem(_itemids[0]);
						if (i>0) 
						{
							_oitem.select();
						}
						else
						{
							//i==0;
							if (_oitem.getData()["text"]!=_text)
							{
								_oitem.select();
							}
						}
						
					}
					else
					{
						//Set selection to nothing because there is no option to choose.
						_selectedText.value = "";
					}
					
				}
			
			this._handleEvent("OnClose",{});
		//}
	},
	removeItem:function(_id)
	{
		/*
		 * Remove a item with id
		 */
		var _li = _obj(_id);
		if(_expired()) return;
		if (_exist(_li) && _index("Item",_getClass(_li))>0 && _index(this._id,_id)==0)
		{
			var _ul = _goParentNode(_li);
			_purge(_li);
			_ul.removeChild(_li);
		}
	},
	addItem:function(_text,_value,_extradata)
	{
		/*
		 * _id is auto generated
		 * Add a new item to the list.
		 */
		var _data = new Object();
		_data["text"] = _text;
		_data["value"] = _value;
		for( var i in _extradata)
		{
			if(typeof _extradata[i]!="function") //Mootools
			{
				_data[i] = _extradata[i];	
			}			
		}
			
		if(_expired()) return;
		//Create LI node
		var _itembox = _getElements("div","kcbItemBox",_obj(this._id))[0];
		var _ul = _goFirstChild(_itembox);		
		var _li = _newNode("li",_ul);
		_li.id = this._id+".i"+ _getIdentity();
		_setClass(_li,"kcbLI kcbItem");
		var _template = _obj(this._id+"_itemtemplate").innerHTML;
		var itemrender = unescape(_template);
		for(var _key in _data)
		{
			if(typeof _data[_key]!="function") //Mootools
			{
				itemrender = itemrender.replace(eval("/{"+_key+"}/g"),_data[_key]);
				_data[_key] = encodeURIComponent(_data[_key]);// Prepare to save with json2string				
			}
		}
		_li.innerHTML = "<input type='hidden' value=\""+_json2string(_data)+"\"/><a class='kcbA' href='javascript:void 0'><div class='kcbIn'>"+itemrender+"</div></a>";				
		//Add event		
		_addEvent(_li,"click",_item_click,false);
		_addEvent(_li,"mouseover",_item_mouseover,false);			
		_addEvent(_li,"mouseout",_item_mouseout,false);			
		//Get last id
		return (new KoolComboboxItem(_li.id));
	},
	sort:function(_type,_sortby)
	{
		/*
		 * Sort the option by _type
		 * _type = "desc","asc"
		 * _sortby = the name of the variable
		 */
	},
	registerEvent:function(_name,_handle)
	{
		/*
		 * Register event
		 */
		if(_expired()) return;
		this._eventhandles[_name]=_handle;
	},
	_handleEvent:function(_name,_arg)
	{
		if(_expired()) return true;
		return (_exist(this._eventhandles[_name]))?this._eventhandles[_name](this,_arg):true;
	},	
	_handle_mousedown:function(_e)
	{
		//Open and keep it open.
		//this.open();
		//Help to keep open through close from document mousedown
		//this._keepOpen = true;
	},
	_handle_click:function(_e)
	{
		//Open and keep it open.
		this.open();
		//Help to keep open through close from document mousedown
		//this._keepOpen = true;
		
	},
	_moveSelectFocus:function(_next)
	{
		var _selectFocusPosition = -1;
		var _items = _getElements("li","kcbItem",_obj(this._id));
		for(var i=0;i<_items.length;i++)
		{
			if (_index("kcbSelectFocus",_getClass(_items[i]))>-1)
			{
				_selectFocusPosition = i;
				break;
			}
		}

		if (_selectFocusPosition<0 && _next<0)
		{
			_selectFocusPosition = _items.length;
		}

		var _count=0,maxCount = Math.abs(_next);
		var _direction = _next/maxCount;
		var _nextPosition = _selectFocusPosition+_direction;
		
		//Check the Disable Node
		while (_nextPosition>-1 && _nextPosition<_items.length && _count<maxCount)
		{
			if (_index("Disable",_getClass(_items[_nextPosition]))<0 && _getDisplay(_items[_nextPosition]))
			{
				_count++;
			}
			_nextPosition+=_direction;
		}
		if (_count==maxCount)
		{
			
			if (_selectFocusPosition>-1 && _selectFocusPosition<_items.length)
			{
				_removeClass(_items[_selectFocusPosition],"kcbSelectFocus");	
			}
			_selectFocusPosition = _nextPosition - _direction;
			_addClass(_items[_selectFocusPosition],"kcbSelectFocus");
			var _item = _items[_selectFocusPosition];
			(new KoolComboboxItem(_item.id)).select();
			
			//Scroll down the itembox div if necessary
			var _itembox = _goParentNode(_item,2);
			if (_item.offsetTop+_item.offsetHeight>_itembox.scrollTop+_itembox.offsetHeight)
			{
				_itembox.scrollTop = _item.offsetTop;
			}
			else if(_item.offsetTop<_itembox.scrollTop && _itembox.scrollTop>0)
			{
				_itembox.scrollTop = _item.offsetTop+_item.offsetHeight - _itembox.offsetHeight;
			}

			var _selectedText = _obj(this._id+"_selectedText");
			_selectedText.select();			
		}
		/*
		if (_selectFocusPosition+_next<0 || _selectFocusPosition+_next>=_items.length) return;
				
		if (_selectFocusPosition>-1 && _selectFocusPosition<_items.length)
		{
			_removeClass(_items[_selectFocusPosition],"kcbSelectFocus");	
		}
		
		_selectFocusPosition+=_next;
		_addClass(_items[_selectFocusPosition],"kcbSelectFocus");
		(new KoolComboboxItem(_items[_selectFocusPosition].id)).select();
		var _selectedText = _obj(this._id+"_selectedText");
		_selectedText.select();
		*/
		
	},
	_removeSelectFocus:function()
	{
		var _items = _getElements("li","kcbItem",_obj(this._id));
		for(var i=0;i<_items.length;i++)
			if(_index("kcbSelectFocus",_getClass(_items[i])))
				_removeClass(_items[i],"kcbSelectFocus");		
	},
	_handle_keyup:function()
	{
		if (this.isOpening()) 
			this._moveSelectFocus(-1);
		else	
			this.open();
	},
	_handle_keydown:function()
	{
		if (this.isOpening()) 
			this._moveSelectFocus(1);
		else
			this.open();
	},
	_handle_keyenter:function()
	{
		var _selectFocusPosition = -1;
		var _items = _getElements("li","kcbItem",_obj(this._id));
		for(var i=0;i<_items.length;i++)
		{
			if (_index("kcbSelectFocus",_getClass(_items[i]))>-1)
			{
				_selectFocusPosition = i;
				break;
			}
		}
		//Select the select focus if posible
		if (_selectFocusPosition>-1 && _selectFocusPosition<_items.length)
		{
			_removeClass(_items[i],"kcbSelectFocus");
			(new KoolComboboxItem(_items[i].id)).select();
		}
		//Close the select
		this.close();		
	},
	_handle_keyesc:function()
	{
		this.close();
	},
	_filter:function(_keyword,_datakey,_type,_casesensitive)
	{
		/*
		 * _keyword: keyword to filter
		 * _datakey: the key to get data
		 * _type: type of filter (0: none;1: startwith; 2:contain)
		 */
		var _itemdata = new Array();
		var _itemids = new Array();
		var _items = _getElements("li","kcbItem",_obj(this._id));
		for(var i=0;i<_items.length;i++)
		{
			var _oitem = new KoolComboboxItem(_items[i].id);
			_itemids.push(_oitem._id);			
			_itemdata.push((_casesensitive)?_oitem.getData()[_datakey]:_oitem.getData()[_datakey].toLowerCase());			
		}
		if (!_casesensitive) _keyword = _keyword.toLowerCase();
		var _result = new Array();
		switch(_type)
		{
			case 0:
				break;
			case 1:
				for(var i=0;i<_itemids.length;i++)
					if (_index(_keyword,_itemdata[i])==0)
						_result.push(_itemids[i]);
				break;
			case 2:
				for(var i=0;i<_itemids.length;i++)
					if (_index(_keyword,_itemdata[i])>-1)
						_result.push(_itemids[i]);
				break;
		}
		return _result;
	},
	HT:function() //_handle_typing
	{
		var _selectedText = _obj(this._id+"_selectedText");
		var _text = _selectedText.value;

		if(!_exist(this._serviceFunction))
		{
			if (this._inputValidate)
			{
				for(var i=0;i<=_text.length;i++)
				{
					var _keyword = _text.substr(0,_text.length-i);
					var _itemids = this._filter(_keyword,"text",1,false);
					if (_itemids.length>0)
					{
						break;
					}
				}
				if (_itemids.length>0)
				{
					this._removeAllSelectedItems();
					(new KoolComboboxItem(_itemids[0])).select();
					_selectedText.selectionStart = _keyword.length;
					_selectedText.selectionEnd = _selectedText.value.length;	
				}				
			}
			
		}
		else
		{
			if (!this._handleEvent("OnBeforeSendUpdateRequest",{"Text":_text})) return;
			//There is service function.
			koolajax.callback(eval(this._serviceFunction)(_text),eval("__=function (_r){"+this._id+".SFR(_r)}"));
			//Remove all items event
			var _itembox = _getElements("div","kcbItemBox",_obj(this._id))[0];
			var _ul = _goFirstChild(_itembox);
			_purge(_ul);
			//Remove all current items
			_ul.innerHTML = "";
			//Add Loading message
			_ul.innerHTML = "<li id='"+this._id+".loading' class='kcbLI'><div class='kcbLoading'>Loading...</div></li>";
			this._handleEvent("OnSendUpdateRequest",{"Text":_text});
		}
	},
	SFR:function(_res)//Service function return.
	{
		if (!this._handleEvent("OnBeforeUpdateItemList",{"Data":_res})) return;


		var _itembox = _getElements("div","kcbItemBox",_obj(this._id))[0];

		var _ul = _goFirstChild(_itembox);		
		//Remove loading message
		_ul.innerHTML = "";
		//Base on result, adding items to selection list
		var _template = _obj(this._id+"_itemtemplate").innerHTML;
		var _ulhtml="";
		for(i in _res)
		{
			if(typeof _res[i]!="function") //Mootools
			{
				//var _itemdata = new Array({"text":"","value":""});
				var _itemdata = new Object();
				//Pre-set value
				_itemdata["text"] = "";
				_itemdata["value"]="";
				var _itemrender = unescape(_template);
				for(_key in _res[i])
				{
					if(typeof _res[i][_key]!="function") //Mootools
					{
						_itemdata[_key] = _res[i][_key];
						//Using eval is perpect to create custom regular expression.
						_itemrender = _itemrender.replace(eval("/{"+_key+"}/g"),_itemdata[_key]);
						_itemdata[_key] = encodeURIComponent(_itemdata[_key]);
						
					}
				}
				_ulhtml+="<li id='"+this._id+".i"+_getIdentity()+"' class='kcbLI kcbItem'><input type='hidden' value=\""+_json2string(_itemdata)+"\"/><a class='kcbA' href='javascript:void 0'><div class='kcbIn'>"+_itemrender+"</div></a></li>";			
			}
		}
		_ul.innerHTML = _ulhtml;
		this._turn_itemsEvent(1);		
		this._handleEvent("OnUpdateItemList",{});
		/*
		var _selectedText = _obj(this._id+"_selectedText");
		var _text = _selectedText.value;
		//Select correctly
		if (this._inputValidate) 
		{
			for(var i=0;i<=_text.length;i++)
			{
				var _keyword = _text.substr(0,_text.length-i);
				var _itemids = this._filter(_keyword,"text",1,false);
				if (_itemids.length>0)
				{
					break;
				}
			}
			if (_itemids.length>0)
			{
				(new KoolComboboxItem(_itemids[0])).select();
				_selectedText.selectionStart = _keyword.length;
				_selectedText.selectionEnd = _selectedText.value.length;							
			}						
		
		}		
		*/
		//Change and set selection
	}
}

//Event handles -----------------------------------------------------------------------
//-------------------------------------------------------------------------------------

function _kcbCommbobox_mouseover()
{
	_addClass(this,"kcbOver");
}
function _kcbCommbobox_mouseout()
{
	_removeClass(this,"kcbOver");
}


function _kcbCommbobox_mousedown(_e)
{
	var __cb = _goParentNode(this,2);
	var _div = _goFirstChild(__cb);
	_addClass(_div,"kcbDown");
	var _combobox = eval(__cb.id);
	_combobox._handle_mousedown(_e);
	return false;
}

function _kcbCommbobox_click(_e)
{
	var __cb = _goParentNode(this,2);
	var _combobox = eval(__cb.id);
	_combobox._handle_click(_e);	
}


function _kcbCommbobox_mouseup()
{
	var _div = _goParentNode(this);
	_removeClass(_div,"kcbDown");	
}

function _kcbBox_mousedown(_e)
{
	//OnMouseDown to the Box: Cancel propagation to document in order to prevent close fire
	if(_e.stopPropagation)
		_e.stopPropagation();
	else
		_e.cancelBubble = true;
}
function _item_click(_e)
{
	(new KoolComboboxItem(this.id))._handle_click(_e);
}

function _item_mouseover(_e)
{
	(new KoolComboboxItem(this.id))._handle_mouseover(_e);
}
function _item_mouseout(_e)
{
	(new KoolComboboxItem(this.id))._handle_mouseout(_e);
}

function _selectedText_keypress(_e)
{
	var _combo = eval("__="+this.id.replace("_selectedText",""));
	var _key = _e.keyCode;
	if (!_combo._handleEvent("OnBeforeKeyPress", {"keyCode": _key})) 
	{
		return _preventDefaut(_e);
	}
	switch(_key)
	{
		case 40:
				_combo._handle_keydown();
				return _preventDefaut(_e);
			break;
		case 38:
				_combo._handle_keyup();
				return _preventDefaut(_e);
			break;
		case 13:
				_combo._handle_keyenter();
				return _preventDefaut(_e);
			break;						
		case 27:
				_combo._handle_keyesc();
				return _preventDefaut(_e);
		case 39: // Right
		case 37: // Left
		case 16: //Shift
		case 17: //Ctrl
		case 18: //Alt
		case 8:  //BackSpace
			break;
		case 9:  //Tabs
			_combo.close();
			_combo._removeSelectFocus();
			break;
		default:
			setTimeout(_combo._id+".HT()",1);
			//return _preventDefaut(_e);
			break;
	}
	_combo._handleEvent("OnKeyPress",{"KeyCode":_key});
}

function _selectedText_focus(_e)
{
	this.select();
}
if(typeof(__KCBInits)!='undefined' && _exist(__KCBInits))
{	
	for(var i=0;i<__KCBInits.length;i++)
	{
		__KCBInits[i]();
	}
}