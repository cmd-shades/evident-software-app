/**
 * @author Administrator
 */
function _obj(_id)
{
	return document.getElementById(_id);
}
if (!_exist(_identity))
{
	var _identity = 0;	
}
function _getIdentity()
{
	_identity++;
	return _identity;
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
function _index(_search,_original)
{
	return _original.indexOf(_search);
}
function _stopPropagation(_e)
{
	if(_e.stopPropagation)
		_e.stopPropagation();
	else
		_e.cancelBubble = true;
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
/*
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
*/
function _json2string(_o)
{
	var _res="";
	var _isarray = (_o!=null && _o[0]!=null);
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
function _goParentNode(_theObj,_level)
{
	if(!_exist(_level)) _level=1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.parentNode;
    return _theObj;
}
function ListBoxItem(_id)
{
	this._id = _id;
}
ListBoxItem.prototype = 
{
	_init:function()
	{
		var _this = _obj(this._id);
		var _listbox = _get_listbox(_obj(this._id));
		var _viewstate = _listbox._loadViewState();
		if(_viewstate["AllowHover"])
		{
			_addEvent(_this,"mouseover",_item_mouseover,false);
			_addEvent(_this,"mouseout",_item_mouseout,false);			
		}
		_addEvent(_this,"click",_item_click,false);
		_addEvent(_this,"dblclick",_item_dbclick,false);
		if(_viewstate["UseCheckBoxes"])
		{
			var _check = (_getElements("input","klbCheck",_this))[0];
			_addEvent(_check,"click",_check_click,false);
		}		
	},
	get_text:function()
	{
		return decodeURIComponent((this.get_data())["Text"]);
	},
	set_text:function(_text)
	{
		var _this = _obj(this._id);
		var _input_data = _this.firstChild;
		var _data = this.get_data();
		var _text_span = _getElements("span","klbText",_this);
		_data["Text"] = encodeURIComponent(_text);
		_text_span.innerHTML = _text;
		_input_data.value = _json2string(_data);		
	},
	get_value:function()
	{
		return decodeURIComponent((this.get_data())["Value"]);		
	},
	set_value:function(_value)
	{
		this.set_data("Value",encodeURIComponent(_value));
	},
	get_index:function()
	{
		var _this = _obj(this._id);
		var _ul = _goParentNode(_this);
		var _lis = _getElements("li","klbItem",_ul);
		for(var i=0;i<_lis.length;i++)
		{
			if(_this==_lis[i])
			{
				return i;
			}
		}
		return null;
	},
	get_data:function()
	{
		var _this  = _obj(this._id);
		var _input_data = _this.firstChild;
		var _data = eval("__="+_input_data.value);
		for(var i in _data)
		{
			_data[i] = decodeURIComponent(_data[i]);
		}
		return _data;
	},
	set_data:function(_data)
	{
		var _this  = _obj(this._id);
		var _input_data = _this.firstChild;
		for(var i in _data)
		{
			_data[i] = encodeURIComponent(_data[i]);
		}
		_input_data.value = _json2string(_data);		
	},
	set_enabled:function(_bool)
	{
		var _this = _obj(this._id);
		if(_bool)
		{
			_removeClass(_this,"klbDisabledItem");
		}
		else
		{
			_addClass(_this,"klbDisabledItem");
		}
	},
	get_enabled:function()
	{
		var _this = _obj(this._id);
		return (_index("Disabled",_getClass(_this))<0);
	},	
	enable:function()
	{
		this.set_enabled(true);
	},
	disable:function()
	{
		this.set_enabled(false);
	},
	select:function()
	{	
		this.set_selected(true);
	},
	unselect:function()
	{
		this.set_selected(false);
	},
	check:function()
	{
		this.set_checked(true);
	},
	uncheck:function()
	{
		this.set_checked(false);		
	},
	set_active:function(_bool)
	{
		var _this = _obj(this._id);
		if(_bool)
		{
			_addClass(_this,"klbActive");
		}
		else
		{
			_removeClass(_this,"klbActive");
		}
	},
	set_selected:function(_bool,_no_update)
	{
		var _this = _obj(this._id);
		var _listbox = _get_listbox(_obj(this._id));
		if(_bool)
		{
			if(!_listbox._handleEvent("OnBeforeSelect",{"Item":this},this)) return;	
			_addClass(_this,"klbSelected");
			_listbox._handleEvent("OnSelect",{"Item":this},this);
		}
		else
		{
			if(!_listbox._handleEvent("OnBeforeUnSelect",{"Item":this},this)) return;	
			_removeClass(_this,"klbSelected");					
			_listbox._handleEvent("OnUnSelect",{"Item":this},this);
		}
		if(_no_update==null) _listbox._update();		
	},
	get_selected:function()
	{
		var _this = _obj(this._id);
		return (_index("klbSelected",_getClass(_this))>-1);
	},
	set_checked:function(_bool,_no_update)
	{
		var _this = _obj(this._id);
		var _check = (_getElements("input","klbCheck",_this))[0];
		var _listbox = _get_listbox(_obj(this._id));
		if(_check)
		{
			if(_bool)
			{
				if(!_listbox._handleEvent("OnBeforeCheck",{"Item":this},this)) return;	
				_check.checked = _bool;
				_listbox._handleEvent("OnCheck",{"Item":this},this);				
			}
			else
			{
				if(!_listbox._handleEvent("OnBeforeUnCheck",{"Item":this},this)) return;	
				_check.checked = _bool;
				_listbox._handleEvent("OnUnCheck",{"Item":this},this);				
			}
		}
		if(_no_update==null) _listbox._update();
	},
	get_checked:function()
	{
		var _this = _obj(this._id);
		var _check = (_getElements("input","klbCheck",_this))[0];
		if(_check)
		{
			return _check.checked;
		}
		return false;		
	},
	set_checkable:function(_bool)
	{
		var _this = _obj(this._id);
		var _check = (_getElements("input","klbCheck",_this))[0];
		if(_check)
		{
			_check.disabled = !_bool;
		}		
	},
	get_checkable:function()
	{
		var _this = _obj(this._id);
		var _check = (_getElements("input","klbCheck",_this))[0];
		if(_check)
		{
			return (!_check.disabled);
		}				
	},
	get_imageurl:function()
	{
		var _this = _obj(this._id);
		var _image = (_getElements("img","klbImage",_this))[0];	
		return _image.src;	
	},
	set_imageurl:function(_url)
	{
		var _this = _obj(this._id);
		var _image = (_getElements("img","klbImage",_this))[0];	
		_image.src = _url;			
	},
	set_tooltip:function(_text)
	{
		var _this = _obj(this._id);
		_this.title = _text;			
	},
	get_tooltip:function()
	{
		var _this = _obj(this._id);
		return _this.title;					
	},
	set_allowdrag:function(_bool)
	{
	},
	get_allowdrag:function()
	{
	},
	get_element:function()
	{
		return _obj(this._id);
	},
	_handle_item_mouseover:function(_e)
	{
		var _this = _obj(this._id);
		if(_index("Disabled",_getClass(_this))<0)
		{
			_addClass(_this,"klbHovered");	
		}
	},
	_handle_item_mouseout:function(_e)
	{
		var _this = _obj(this._id);
		if(_index("Disabled",_getClass(_this))<0)
		{
			_removeClass(_this,"klbHovered");		
		}
	},
	_handle_item_click:function(_e)
	{
		var _this = _obj(this._id);
		if(_index("Disabled",_getClass(_this))>0) return;
		var _listbox = _get_listbox(_this);
		var _viewstate = _listbox._loadViewState();
		if (!_viewstate["AllowSelect"]) return;
		var _ul = _goParentNode(_this);
		var _lis = _getElements("li","klbItem",_ul);
		if(_viewstate["AllowMultiSelect"])
		{
		}
		else
		{
			for(var i=0;i<_lis.length;i++)
			{
				if(_index("klbSelected",_getClass(_lis[i]))>0)
				{
					_removeClass(_lis[i],"klbSelected");
					_removeClass(_lis[i],"klbActive");					
				}
			}
		}
			this.set_selected(!this.get_selected());
			this.set_active(this.get_selected());
	},
	_handle_check_click:function(_e)
	{
		var _this = _obj(this._id)
    this.set_checked(this.get_checked());
		var _listbox = _get_listbox(_this);
		_listbox._update();
		return _stopPropagation(_e);	
	},
	_handle_item_dbclick:function(_e)
	{
		var _this = _obj(this._id)
		if(_index("Disabled",_getClass(_this))>0) return;
		var _listbox = _get_listbox(_this);
		var _viewstate = _listbox._loadViewState();
		if(_viewstate["AllowTransferOnDoubleClick"])
		{
			var _selected_items = _listbox.get_selected_items();
			var _fist_selected_index = (_selected_items.length>0)?_selected_items[0].get_index():-1;
			for(var i=0;i<_selected_items.length;i++)
			{
				_listbox.transfer_to_destination(_selected_items[i],"no update");
			}
			var _item = _listbox.get_item(_fist_selected_index);
			if(_item!=null) _item.select();
			_listbox._update();			
			if(_viewstate["AutoPostBackOnTransfer"]) _listbox._postback();
		}
		return _stopPropagation(_e);
	}	
}
function KoolListBox(_id)
{
	this._id = _id;
	this._init();
}
KoolListBox.prototype = 
{
	_init:function()
	{		
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		var _li_items = _getElements("li","klbItem",_this);
		for(var i=0;i<_li_items.length;i++)
		{
			_li_items[i].id=this._id+"_i"+ _getIdentity();
			if(_viewstate["AllowHover"])
			{
				_addEvent(_li_items[i],"mouseover",_item_mouseover,false);
				_addEvent(_li_items[i],"mouseout",_item_mouseout,false);				
			}
			_addEvent(_li_items[i],"click",_item_click,false);
			_addEvent(_li_items[i],"dblclick",_item_dbclick,false);
		}
		var _div_group = (_getElements("div","klbGroup",_this))[0];
		_div_group.scrollTop = _viewstate["ScrollTop"];
		_addEvent(_div_group,"scroll",_div_group_scroll,false);
		if(_viewstate["UseCheckBoxes"])
		{
			var _checks = _getElements("input","klbCheck",_this);
			for(var i=0;i<_checks.length;i++)
			{
				_addEvent(_checks[i],"click",_check_click,false);
			}			
		}
		var _a_function_buttons = _getElements("a","klbButton",_this);
		for(var i=0;i<_a_function_buttons.length;i++)
		{
			_addEvent(_a_function_buttons[i],"click",_function_button_click,false);
		}
		_push_to_onload_lists(this._id);
	},
	_loadViewState:function()
	{
		var _input_viewstate = _obj(this._id+"_viewstate");
		return  eval("__="+_input_viewstate.value);	
	},
	_saveViewState:function(_state)
	{
		var _input_viewstate = _obj(this._id+"_viewstate");
		if(_input_viewstate)
		{
			_input_viewstate.value = _json2string(_state);		
			return true;
		}
		else
		{
			return false;
		}
	},
	_enable_button:function(_type,_bool)
	{
		var _this = _obj(this._id);
		var _lookup_class = (_bool==true)?"klb"+_type+"Disabled":"klb"+_type;
		var _set_class = (_bool==true)?"klb"+_type:"klb"+_type+"Disabled"+ " klbDisabled";
		var _elements = _getElements("a",_lookup_class,_this);
		if(_elements.length>0)
		{
			var _button =_elements[0];
			_setClass(_button,"klbButton "+_set_class);
		}
	},
	_logEntry:function(_event,_data)
	{
		var _viewstate = this._loadViewState();
		var _log_entries = _viewstate["LogEntries"];
		if (!_log_entries) _log_entries=[];
		_log_entries.push({"Event":_event,"Data":_data});
		_viewstate["LogEntries"]=_log_entries;
		this._saveViewState(_viewstate);
	},
	_update:function()
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		var _lis = _getElements("li","klbItem",_this);
		_viewstate["SelectedIndices"]=[];
		for(var i=0;i<_lis.length;i++)
		{
			if(_index("klbSelected",_getClass(_lis[i]))>0)
			{
				_viewstate["SelectedIndices"].push(i);
			}
		}
		_viewstate["CheckedIndices"]=[];
		var _checks = _getElements("input","klbCheck",_this);
		for(var i=0;i<_checks.length;i++)
		{
			if(_checks[i].checked)
			{
				_viewstate["CheckedIndices"].push(i);
			}
		}
		this._saveViewState(_viewstate);
		var _exist_selected = (_viewstate["SelectedIndices"].length>0);
		this._enable_button("Delete",_exist_selected);
		this._enable_button("MoveUp",(_exist_selected&&(_viewstate["SelectedIndices"][0]>0)));
		this._enable_button("MoveDown",(_exist_selected&&(_viewstate["SelectedIndices"][_viewstate["SelectedIndices"].length-1]<_lis.length-1)));		
		this._enable_button("TransferOut",_exist_selected);	
		this._enable_button("TransferAllOut",(_lis.length>0));	
		if(_viewstate["NotifyingUpdateIds"]!=null)
		{
			for( i in _viewstate["NotifyingUpdateIds"])
			{
				var _peer  = _get_listbox(_obj(_viewstate["NotifyingUpdateIds"][i]));
				_peer._updated_from_destination();
			}
		}
	},
	_send_notification_to:function(_listbox_id)
	{
		var _viewstate = this._loadViewState();
		var _update_notification_ids = [];
		if(_viewstate["NotifyingUpdateIds"]!=null)
		{
			for( i in _viewstate["NotifyingUpdateIds"])
			{
				if(_viewstate["NotifyingUpdateIds"][i]!=_listbox_id)
				{
					_update_notification_ids.push(_viewstate["NotifyingUpdateIds"][i]);					
				}
			}
		} 
		_update_notification_ids.push(_listbox_id);
		_viewstate["NotifyingUpdateIds"] = _update_notification_ids;
		this._saveViewState(_viewstate);
	},
	_updated_from_destination:function()
	{
		var _viewstate = this._loadViewState();
		var _destination  = _get_listbox(_obj(_viewstate["TransferToId"]));
		this._enable_button("TransferAllIn",((_destination.get_items()).length>0));
		this._enable_button("TransferIn",((_destination.get_selected_items()).length>0));
	},
	get_item:function(_pos)
	{
		var _this = _obj(this._id);
		var _lis = _getElements("li","klbItem",_this);
		return (_lis[_pos]!=null)?(new ListBoxItem(_lis[_pos].id)):null;
	},
	get_items:function()
	{
		var _this = _obj(this._id);
		var _li_items = _getElements("li","klbItem",_this);
		var _items = [];
		for(var i=0;i<_li_items.length;i++)
		{
				_items.push(new ListBoxItem(_li_items[i].id));				
		}
		return _items;
	},
	get_selected_items:function()
	{
		var _this = _obj(this._id);
		var _lis_selected = _getElements("li","klbSelected",_this);
		var _selected_items = [];
		for(var i=0;i<_lis_selected.length;i++)
		{
				_selected_items.push(new ListBoxItem(_lis_selected[i].id));
		}
		return _selected_items;
	},
	delete_item:function(_pos,_no_update,_no_render)
	{
		if (typeof _pos=="object")
		{
			_pos=_pos.get_index();
		}
		var _this = _obj(this._id);
		var _li_items = _getElements("li","klbItem",_this);
		var _ul = (_getElements("ul","klbList",_this))[0];
		if(_li_items[_pos]!=null)
		{
			var _item = new ListBoxItem(_li_items[_pos].id);
			var _item_data = _item.get_data();
			if(!this._handleEvent("OnBeforeDelete",{"Data":_item_data},_item)) return;			
			if(!_no_render)
			{
				_ul.removeChild(_li_items[_pos]);				
			}
			this._logEntry("Delete",{"Position":_pos});
			this._handleEvent("OnDelete",{"Data":_item_data},_item);
		}
		if(_no_update==null) this._update();
	},	
	select_item:function(_pos,_no_update)
	{
		var _item = this.get_item(_pos);
		if(_item)
		{
			_item.set_selected(true,_no_update);
		}
	},
	unselect_item:function(_pos,_no_update)
	{
		var _item = this.get_item(_pos);
		if(_item)
		{
			_item.set_selected(false,_no_update);
		}
	},	
	move_item:function(_from,_to,_no_update)
	{
		var _this = _obj(this._id);
		var _lis = _getElements("li","klbItem",_this);
		var _ul = _goParentNode(_lis[0]);
		if(_to<0) _to=0;
		if(_to>_lis.length-1) _to = _lis.length-1;
		var _item = this.get_item(_from);
		if(!this._handleEvent("OnBeforeReorder",{"From":_from,"To":_to},_item)) return;			
		if(_from<_to)
		{
			if(_to<_lis.length-1)
			{
				_ul.insertBefore(_lis[_from],_lis[_to+1]);							
			}
			else
			{
				_ul.appendChild(_lis[_from]);
			}
		}
		else
		{
			_ul.insertBefore(_lis[_from],_lis[_to]);			
		}
		this._logEntry("Move",{"From":_from,"To":_to});
		if(_no_update==null) this._update();
		this._handleEvent("OnReorder",{"From":_from,"To":_to},_item);
	},
	transfer_to_destination:function(_item,_no_update)
	{
		var _viewstate = this._loadViewState();
		if(_viewstate["TransferToId"]!=null)
		{
			var _destination  = _get_listbox(_obj(_viewstate["TransferToId"]));
			if(typeof _item=="number") _item = this.get_item(_item);			
			this._transfer_out(_destination,_item);
		}
		if(_no_update==null) this._update();
	},
	transfer_all_to_destination:function(_no_update)
	{
		var _viewstate = this._loadViewState();
		if(_viewstate["TransferToId"]!=null)
		{
			var _destination  = _get_listbox(_obj(_viewstate["TransferToId"]));
			var _items = this.get_items();
			for(var i=0;i<_items.length;i++)
			{
				this._transfer_out(_destination,_items[i]);				
			}
		}
		if(_no_update==null) this._update();
	},
	transfer_all_from_destination:function(_no_update)
	{
		var _viewstate = this._loadViewState();
		if(_viewstate["TransferToId"]!=null)
		{
			var _destination  = _get_listbox(_obj(_viewstate["TransferToId"]));				
			var _items = _destination.get_items();
			for(var i=0;i<_items.length;i++)
			{
				_destination._transfer_out(this,_items[i]);				
			}
			_destination._update();
		}
		if(_no_update==null) this._update();
	},	
	transfer_from_destination:function(_item,_no_update)
	{
		var _viewstate = this._loadViewState();
		if(_viewstate["TransferToId"]!=null)
		{
			var _destination  = _get_listbox(_obj(_viewstate["TransferToId"]));
			if(typeof _item=="number") _item = _destination.get_item(_item);					
			_destination._transfer_out(this,_item);
			_destination._update();
		}
		if(_no_update==null) this._update();
	},
	_transfer_out:function(_to_listbox,_item)
	{
		var _viewstate = this._loadViewState();
		if(!this._handleEvent("OnBeforeTransfer",{"Destination":_to_listbox,"Item":_item})) return;			
		var _item_data = _item.get_data();
		if(_viewstate["UseCheckBoxes"])
		{
			_item_data["checked"] = _item.get_checked();
		}
		_to_listbox._transfer_in(_item_data);
		if(_viewstate["TransferMode"].toLowerCase()=="move")
		{
			this.delete_item(_item,"no_update");
		}		
		this._handleEvent("OnTransfer",{"Destination":_to_listbox,"Data":_item_data});			
	},
	_transfer_in:function(_item_data,_no_render)
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		if(!_no_render)
		{
			var _ul = (_getElements("ul","klbList",_this))[0];
			var _li = _newNode("li",_ul);
			_li.id = this._id+"_i"+ _getIdentity();
			_addClass(_li,"klbItem");
			var _input_data = document.createElement("input");
			_input_data.type="hidden";
			_addClass(_input_data,"klbItemData");
			_input_data.value= _json2string(_item_data);
			_li.appendChild(_input_data);			
			var _div_template = _obj(this._id+"_template");
			if(_div_template)
			{
				var _div_display = _newNode("div",_li);
				var _template = _div_template.innerHTML;
				for(var _key in _item_data)
				{
					if(typeof _item_data[_key]!="function") //Mootools
					{
						_template = _template.replace(eval("/{"+_key+"}/g"),_item_data[_key]);					
					}	
				}
				_div_display.innerHTML = _template;	
			}
			else
			{
				if(_viewstate["UseCheckBoxes"])
				{
					var _input_check = document.createElement("input");
					_input_check.type="checkbox";
					_addClass(_input_check,"klbCheck");
					if(_item_data["checked"])
					{
						_input_check.checked = true;
					}
					_li.appendChild(_input_check);
				}
				if(_item_data["ImageUrl"]!=null)
				{
					var _image = _newNode("img",_li);
					_image.src=_item_data["ImageUrl"];
					_addClass(_image,"klbImage");
				}
				var _span_text = _newNode("span",_li);
				_addClass(_span_text,"klbText");
				_span_text.innerHTML = _item_data["Text"];			
			}
			if(_viewstate["AllowHover"])
			{
				_addEvent(_li,"mouseover",_item_mouseover,false);
				_addEvent(_li,"mouseout",_item_mouseout,false);				
			}						
			_addEvent(_li,"click",_item_click,false);
		}
		this._logEntry("TransferIn",_item_data);
	},	
	_handle_function_button_click:function(_a,_e)
	{
		if(_index("klbDisabled",_getClass(_a))<0)
		{
			var _viewstate = this._loadViewState();
			var _selected_items = this.get_selected_items();
			var _fist_selected_index = (_selected_items.length>0)?_selected_items[0].get_index():-1;
			if(_index("Delete",_getClass(_a))>0)
			{
				for(var i=0;i<_selected_items.length;i++)
				{
					this.delete_item(_selected_items[i]);						
				}
				var _item = this.get_item(_fist_selected_index);
				if (_item != null) 
				{
					_item.set_selected(true,"no update");
				}
				else
				{
					var _items = this.get_items();
					if(_items.length>0)
					{
						_items[_items.length-1].set_selected(true,"no update");
					}
				}
				this._update();
				if(_viewstate["AutoPostBackOnDelete"]) this._postback();
			}
			else if(_index("MoveUp",_getClass(_a))>0)
			{
				for(var i=0;i<_selected_items.length;i++)
				{
					this.move_item(_selected_items[i].get_index(),_selected_items[i].get_index()-1);
				}
				if(_viewstate["AutoPostBackOnReorder"]) this._postback();
			}
			else if(_index("MoveDown",_getClass(_a))>0)
			{
				for(var i=0;i<_selected_items.length;i++)
				{
					this.move_item(_selected_items[i].get_index(),_selected_items[i].get_index()+1);
				}
				if(_viewstate["AutoPostBackOnReorder"]) this._postback();
			}
			else if(_index("TransferOut",_getClass(_a))>0)
			{
				for(var i=0;i<_selected_items.length;i++)
				{
					this.transfer_to_destination(_selected_items[i],"no update");
				}
				var _item = this.get_item(_fist_selected_index);
				if (_item != null) 
				{
					_item.set_selected(true,"no update");
				}
				else
				{
					var _items = this.get_items();
					if(_items.length>0)
					{
						_items[_items.length-1].set_selected(true,"no update");
					}
				}
				this._update();
				if(_viewstate["AutoPostBackOnTransfer"]) this._postback();				
			}
			else if(_index("TransferIn",_getClass(_a))>0)
			{
				if(_viewstate["TransferToId"]!=null)
				{
					var _destination  = _get_listbox(_obj(_viewstate["TransferToId"]));
					var _des_selected_items = _destination.get_selected_items();
					var _des_first_selected_index = (_des_selected_items.length>0)?_des_selected_items[0].get_index():-1;
						for(var i=0;i<_des_selected_items.length;i++)
						{
							this.transfer_from_destination(_des_selected_items[i],"no update");
						}
						var _item = _destination.get_item(_des_first_selected_index);
						if (_item != null) 
						{
							_item.set_selected(true,"no update");
						}
						else
						{
							var _items = _destination.get_items();
							if(_items.length>0)
							{
								_items[_items.length-1].set_selected(true,"no update");
							}
						}
						_destination._update();
						this._update();
						if(_viewstate["AutoPostBackOnTransfer"]) this._postback();				
				}
			}
			else if(_index("TransferAllOut",_getClass(_a))>0)
			{
				this.transfer_all_to_destination();
				if(_viewstate["AutoPostBackOnTransfer"]) this._postback();				
			}
			else if(_index("TransferAllIn",_getClass(_a))>0)
			{
				this.transfer_all_from_destination();
				if(_viewstate["AutoPostBackOnTransfer"]) this._postback();				
			}			
		}	
	},
	_handle_window_onload:function(_e)
	{
		var _viewstate = this._loadViewState();
		if(_viewstate["TransferToId"]!=null)
		{
			this._updated_from_destination();
			var _destination  = _get_listbox(_obj(_viewstate["TransferToId"]));
			_destination._send_notification_to(this._id);
		}
		this._update();		
	},
	_handle_div_group_scroll:function(_e)
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		var _div_group = (_getElements("div","klbGroup",_this))[0];
		_viewstate["ScrollTop"] = _div_group.scrollTop;
		this._saveViewState(_viewstate);
	},
	_handleEvent:function(_name,_arg,_sender)
	{
		var _viewstate = this._loadViewState();
		if(_exist(_viewstate["ClientEvents"])&&_exist(_viewstate["ClientEvents"][_name]))
		{
			var _func = eval(_viewstate["ClientEvents"][_name]);		
			return _func((_sender!=null)?_sender:this,_arg);
		}
		else
		{
			return true;
		}
	},	
	_postback:function()
	{
			var _viewstate = this._loadViewState();
			if(_viewstate["UpdatePanel"])
			{
				var _updatepanel = eval("__="+_viewstate["UpdatePanel"]);
				_updatepanel.registerEvent("OnUpdatePanel",_updatepanel_onupdate);
				_updatepanel.update();
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
	}
}
function _get_listbox(_div_listbox)
{
	while(_div_listbox.nodeName!="DIV" || _index("KLB",_getClass(_div_listbox))<0)
	{
		_div_listbox = _goParentNode(_div_listbox);
		if (_div_listbox.nodeName == "BODY") return null;
	}
	return eval(_div_listbox.id);
}
function _item_mouseover(_e)
{
	(new ListBoxItem(this.id))._handle_item_mouseover(this,_e);
}
function _item_mouseout(_e)
{
	(new ListBoxItem(this.id))._handle_item_mouseout(this,_e);
}
function _item_click(_e)
{
	(new ListBoxItem(this.id))._handle_item_click(this,_e);	
}
function _item_dbclick(_e)
{
	return (new ListBoxItem(this.id))._handle_item_dbclick(this,_e);		
}
function _check_click(_e)
{
	return (new ListBoxItem((_goParentNode(this)).id))._handle_check_click(_e);
}
function _div_group_scroll(_e)
{
	var _listbox = _get_listbox(this);
	return _listbox._handle_div_group_scroll(_e);
}
function _function_button_click(_e)
{
	var _listbox = _get_listbox(this);
	_listbox._handle_function_button_click(this,_e);
}
var _handle_onload_lists=[];
function _push_to_onload_lists(_id)
{
	var _added = false;
	for(var i=0;i<_handle_onload_lists.length;i++)
	{
		if(_handle_onload_lists[i]==_id)
		{
			_added=true;
		}
	}
	if(!_added)
	{
		_handle_onload_lists.push(_id);
	}
}
function _window_onload(_e)
{
	for(var i=0;i<_handle_onload_lists.length;i++)
	{
		var _listbox  = _get_listbox(_obj(_handle_onload_lists[i]));
		_listbox._handle_window_onload(_e)
	}
}
_addEvent(window,"load",_window_onload,false);
function _updatepanel_onupdate(_sender,_arg)
{
	_window_onload();
}
if(typeof(__KLBInits)!='undefined' && _exist(__KLBInits))
{	
	for(var i=0;i<__KLBInits.length;i++)
	{
		__KLBInits[i]();
	}
}
/*
 * 
OnBeforeReorder
OnReorder
OnBeforeTransfer
OnTransfer
OnBeforeDelete
OnDelete
OnBeforeCheck
OnCheck
OnBeforeUnCheck
OnUnCheck
OnBeforeSelect
OnSelect
OnBeforeUnSelect
OnUnSelect
 * 
 *
 */
