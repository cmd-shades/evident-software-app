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
function _index(_search,_original)
{
	return _original.indexOf(_search);
}
function _newNode(_sTag,_oParent)
{
    var _oNode = document.createElement(_sTag);
    _oParent.appendChild(_oNode);
    return _oNode;
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
function _goFirstChild(_theObj,_level)
{
	if(!_exist(_level)) _level=1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.firstChild;
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
function _getWidth(_theObj)
{
	return parseInt(_theObj.style.width);
}
function _getHeight(_theObj)
{
	return parseInt(_theObj.style.height);
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
function _getFirstElement(_tag,_class,_parent)
{
	var _list = _getElements(_tag,_class,_parent);
	if(_list.length>0)
	{
		return _list[0];
	}
	return false;
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
  if (! _theObj)
    return;
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
  if (! _ob)
    return;
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
function _cancel_textselection()
{
	return false;
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
var _dragging_id=null;
var _dropping_done = null;
function _document_mousemove(_e)
{
	if(_dragging_id!=null)
	{
		(new _DragObject(_dragging_id))._handle_dragging(_e);
	}
}
function _document_mousemove2(_e)
{
	if(_dragging_id!=null)
	{
		(new _DragObject(_dragging_id))._handle_dragging(_e);
	}
}
function _document_mouseup(_e)
{
	if(_dragging_id!=null)
	{
		(new _DragObject(_dragging_id))._handle_ending(_e);
	}	
}
function _document_mouseup2(_e)
{
	if(_dragging_id!=null)
	{
		(new _DragObject(_dragging_id))._handle_ending(_e);
	}	
}
function _DragObject(_id)
{
	this._id=_id;
	this._init();
}
_DragObject.prototype = 
{
	_init:function()
	{
	},
	_bind:function()
	{
		var _this = _obj(this._id);
		_addClass(_this,"dragobject");
		_addEvent(_this,"mousedown",_dragobject_mousedown,false);
		_addEvent(_this,"touchstart",_dragobject_mousedown2,false);
		_this.onselectstart = _cancel_textselection;	
		_this.ondragstart = _cancel_textselection;	
		_this.onmousedown = _cancel_textselection;			
	},
	_disable:function()
	{
		var _this = _obj(this._id);
		_addClass(_this,"dragDisable");
	},
	_enable:function()
	{
		var _this = _obj(this._id);
		_removeClass(_this,"dragDisable");		
	},
	_handle_mousedown:function(_e)
	{
		var _this = _obj(this._id);
		_dragging_id=this._id;
		_addEvent(document,"mousemove",_document_mousemove,false);
		_addEvent(document,"touchmove",_document_mousemove2,false);
		_addEvent(document,"mouseup",_document_mouseup,false);
		_addEvent(document,"touchend",_document_mouseup2,false);
		var _pivot = _getPivot(_this);
		_pivot._dragndrop_start(_dragging_id);
		_dropping_done = false;
	},
	_handle_dragging:function(_e)
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);
		var _dummy = _obj("_dragdummy")
		if(_dummy==null)
		{
			_dummy = _this.cloneNode(true);
			_dummy.id = "_dragdummy";
			_setClass(_dummy,_pivot._getStyle()+"DragDummy");
			document.body.appendChild(_dummy);
			_dummy.style.position = "absolute";
		}
		var _pos = _mouseXY(_e);
		_dummy.style.left = (_pos._x+5)+"px";
		_dummy.style.top = (_pos._y+5)+"px";
	},
	_handle_ending:function(_e)
	{
		var _dummy = _obj("_dragdummy");
		if(_dummy) document.body.removeChild(_dummy);	
		_dragging_id=null;
		_removeEvent(document,"mousemove",_document_mousemove,false);
		_removeEvent(document,"touchmove",_document_mousemove2,false);
		_removeEvent(document,"mouseup",_document_mouseup,false);
		_removeEvent(document,"touchend",_document_mouseup2,false);
		_dropping_done=true;
	}
}
function _dragobject_mousedown(_e)
{
	(new _DragObject(this.id))._handle_mousedown(_e);
}
function _dragobject_mousedown2(_e)
{
	(new _DragObject(this.id))._handle_mousedown(_e);
}
function _DropObject(_id)
{
	this._id=_id;
	this._init();
}
_DropObject.prototype = 
{
	_init:function()
	{
	},
	_bind:function()
	{
		var _this = _obj(this._id);
		_addClass(_this,"dropobject");
		_addEvent(_this,"mouseover",_dropobject_mouseover,false);
		_addEvent(_this,"mouseout",_dropobject_mouseout,false);		
		_addEvent(_this,"mouseup",_dropobject_mouseup,false);		
		_addEvent(_this,"touchend",_dropobject_mouseup2,false);		
	},
	_disable:function()
	{
		var _this = _obj(this._id);
		_addClass(_this,"dragDisable");
	},
	_enable:function()
	{
		var _this = _obj(this._id);
		_removeClass(_this,"dragDisable");		
	},
	_handle_mouseover:function(_e)
	{
		if(_dragging_id!=null && _dragging_id!=this._id)
		{
			var _this = _obj(this._id);
			var _pivot = _getPivot(_this);
			_pivot._handle_dropobject_mouseover(this._id);			
		}
		return _stopPropagation(_e);
	},
	_handle_mouseout:function(_e)
	{
		if (_dragging_id != null && _dragging_id!=this._id) 
		{
			var _this = _obj(this._id);
			var _pivot = _getPivot(_this);
			_pivot._handle_dropobject_mouseout(this._id);
		}
		return _stopPropagation(_e);
	},	
	_handle_mouseup:function(_e)
	{
		if(_dropping_done==false)
		{
			var _this = _obj(this._id);
			var _pivot = _getPivot(_this);
			_pivot._dragndrop_end(_dragging_id,this._id);
			_dropping_done=true;
		}
	},
  _handle_mouseup2:function(_e)
	{
			var _this = _obj(this._id);
			var _pivot = _getPivot(_this);
			_pivot._dragndrop_end(_dragging_id,this._id);
	}
}
function _dropobject_mouseover(_e)
{
	return (new _DropObject(this.id))._handle_mouseover(_e);
}
function _dropobject_mouseout(_e)
{
	return (new _DropObject(this.id))._handle_mouseout(_e);
}
function _dropobject_mouseup(_e)
{
	return (new _DropObject(this.id))._handle_mouseup(_e);
}
function _dropobject_mouseup2(_e)
{
  var endTarget = document.elementFromPoint(
      _e.changedTouches[0].pageX,
      _e.changedTouches[0].pageY
  );
  var el = endTarget;
  while (el && el.className.indexOf('dropobject') === - 1)
    el = el.parentElement;
  if (el && el.className.indexOf('dropobject') > -1)
    return (new _DropObject(el.id))._handle_mouseup2(_e);
}
function PivotField(_id)
{
	this._id = _id;
	this.id = _id;
}
PivotField.prototype = 
{
	sort:function(_direction)
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);
		if(!_pivot._handleEvent("OnBeforeFieldSort",{"Sort":_direction},this)) return;
		_pivot._addCommand(this._id,"Sort",{"Sort":_direction});
		_pivot._registerPostLoadEvent("OnFieldSort",{"Sort":_direction},this);	
	},
	sortGroup:function(_direction)
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);
		if(!_pivot._handleEvent("OnBeforeGroupSort",{"Sort":_direction},this)) return;
		_pivot._addCommand(this._id,"SortGroup",{"Sort":_direction});
		_pivot._registerPostLoadEvent("OnGroupSort",{"Sort":_direction},this);	
	},
	sortGrandGroup:function(_direction)
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);
		if(!_pivot._handleEvent("OnBeforeGroupSort",{"Sort":_direction},this)) return;
		_pivot._addCommand(this._id,"SortGroup",{"Sort":_direction});
		_pivot._registerPostLoadEvent("OnGroupSort",{"Sort":_direction},this);	
	},
	changeSortData:function(_field_name,_value)
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);
		if(!_pivot._handleEvent("OnBeforeChangeSortData",{"FieldName":_field_name,"Check":_value},this)) return;
		_pivot._addCommand(_pivot._id,"ChangeSortData",{"FieldName":_field_name,"Check":_value});
		_pivot._registerPostLoadEvent("OnChangeSortData",{"FieldName":_field_name,"Check":_value},this);	
	},	
	filter_by_expression:function(_expression,_value1,_value2)
	{
		if(!_exist(_value2)) _value2=null;
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);
		if(!_pivot._handleEvent("OnBeforeFieldFilter",{"FilterBy":"Values","Expression": _expression,"value1": _value1,"value2": _value2},this)) return;
		_pivot._addCommand(this._id,"CloseFilterPanel",{
			"Command": "ok",
			"FilterBy": "Values",
			"Expression": _expression,
			"value1": _value1,
			"value2": _value2
		});
		_pivot._registerPostLoadEvent("OnFieldFilter",{"FilterBy":"Values","Expression": _expression,"value1": _value1,"value2": _value2},this);	
	},
	filter_by_selection:function(_includeall,_exceptions)
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);		
		if(!_pivot._handleEvent("OnBeforeFieldFilter",{"FilterBy":"Options","IncludeAll": _includeall,"ExceptionList": _exceptions},this)) return;
		_pivot._addCommand(this._id,"CloseFilterPanel",{
			"Command": "ok",
			"FilterBy": "Options",
			"IncludeAll": _includeall,
			"ExceptionList": _exceptions
		});	
		_pivot._registerPostLoadEvent("OnFieldFilter",{"FilterBy":"Options","IncludeAll": _includeall,"ExceptionList": _exceptions},this);	
	},
	open_filter_panel:function()
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);		
		var _table = _goFirstChild(_obj(_pivot._id));
		if(!_pivot._handleEvent("OnBeforeFilterPanelOpen",{},this)) return;			
		_pivot._addCommand(this._id,"OpenFilterPanel",{"Width":_table.offsetWidth,"Height":_table.offsetHeight});
		_pivot._registerPostLoadEvent("OnFilterPanelOpen",{},this);			
	},
	expand:function()
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);		
		if(!_pivot._handleEvent("OnBeforeFieldExpand",{},this)) return;
		_pivot._addCommand(this._id,"Expand",{});	
		_pivot._registerPostLoadEvent("OnFieldExpand",{},this);	
	},
	collapse:function()
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);		
		if(!_pivot._handleEvent("OnBeforeFieldCollapse",{},this)) return;
		pivot._addCommand(this._id,"Collapse",{});
		_pivot._registerPostLoadEvent("OnFieldCollapse",{},this);	
	}
}
function PivotGroup(_id)
{
	this._id = _id;
	this.id = _id;
}
PivotGroup.prototype = 
{
	expand:function()
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);
		if(!_pivot._handleEvent("OnBeforeGroupExpand",{},this)) return;
		_pivot._addCommand(this._id,"Expand",{});
		_pivot._addCommand(_pivot._id,"Expand",{});
		_pivot._registerPostLoadEvent("OnGroupExpand",{},this);	
	},
	collapse:function()
	{
		var _this = _obj(this._id);
		var _pivot = _getPivot(_this);
		if(!_pivot._handleEvent("OnBeforeGroupCollapse",{},this)) return;
		_pivot._addCommand(this._id,"Collapse",{});
		_pivot._addCommand(_pivot._id,"Collapse",{});
		_pivot._registerPostLoadEvent("OnGroupCollapse",{},this);
	}
}
 function KoolPivotTable(_id,_ajaxEnabled,_ajaxHandlePage)
{
	this._id = _id;
	this.id = _id;
	this._ajaxEnabled = _ajaxEnabled;
	this._ajaxHandlePage = _ajaxHandlePage;
	this._init();
}
var PV_Column = 0;
var PV_Row = 1;
var PV_Filter = 2;
var PV_Data = 3;
KoolPivotTable.prototype = 
{
	_init:function()
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		if( _getClass(_goFirstChild(_this)).indexOf("kptFilterPanel") > -1)
		{
			var _filterpanel = _goFirstChild(_this);
			var _functionpanel = _goFirstChild(_filterpanel);
			var _scrollingpanel = _goNextSibling(_functionpanel);
			_setHeight(_scrollingpanel,_getHeight(_filterpanel)-_functionpanel.offsetHeight);
			var _item_id = _filterpanel.id;
			var _ok_button = _obj(_item_id+"_ok");
			var _cancel_button = _obj(_item_id+"_cancel");
			_addEvent(_ok_button,"click",_filter_ok_onclick,false);
			_addEvent(_cancel_button,"click",_filter_cancel_onclick,false);						
			var _select = _obj(_item_id+"_select");
			var _value1 = _obj(_item_id+"_value1");
			var _value2 = _obj(_item_id+"_value2");
			var _value2_span = _goParentNode(_value2);
			switch(_select.options[_select.selectedIndex].value)
			{
				case "none":
					_setDisplay(_value1,0);
					_setDisplay(_value2_span,0);					
					break;
				case "between":
				case "not_between":				
					_setDisplay(_value1,1);
					_setDisplay(_value2_span,1);					
					break;
				default:
					_setDisplay(_value1,1);
					_setDisplay(_value2_span,0);	
					break;														
			}
			_addEvent(_select,"change",_filterpanel_select_onchange,false);
			_addEvent(_obj(_item_id+"_include"),"change",_filterpanel_include_exclude_onchange,false);
			_addEvent(_obj(_item_id+"_exclude"),"change",_filterpanel_include_exclude_onchange,false);
			var _checks = _getElements("input","kptCheck",_this);
			for(var i=0;i<_checks.length;i++)
			{
				_addEvent(_checks[i],"change",_filterpanel_listitem_onchange,false);
			}
			_addEvent(_value1,"focus",_filterpanel_value_onfocus,false);		
			_addEvent(_value2,"focus",_filterpanel_value_onfocus,false);
			_addEvent(_obj(_item_id+"_selectall"),"change",_filterpanel_selectall_onchange,false);
			var _all = true;
			for(i=1;i<_checks.length;i++)
			{
				if(!_checks[i].checked) _all = false;
			}
			_obj(_item_id+"_selectall").checked = _all;
			if(_obj(_item_id+"_hidden").value=="ie")
			{
				_addClass(_obj(_item_id+"_filterwithoptions"),"kptHighlight");
			}
			else
			{
				_addClass(_obj(_item_id+"_filterwithvalues"),"kptHighlight");				
			}
		}
		else
		{
			if(_viewstate[this._id]["AllowReorder"])
			{
				var _dropbox = new _DropObject(this._id+"_filterzone");
				if(_dropbox) _dropbox._bind();
				var _dropbox = new _DropObject(this._id+"_datazone");
				if(_dropbox) _dropbox._bind();
				var _dropbox = new _DropObject(this._id+"_columnzone");
				if(_dropbox) _dropbox._bind();
				var _dropbox = new _DropObject(this._id+"_rowzone");
				if(_dropbox) _dropbox._bind();
				var _items = _getElements("span","kptFieldItem",_this);
				for(i=0;i<_items.length;i++)
				{
					if(_viewstate[_items[i].id]["AllowReorder"])
					{
						(new _DragObject(_items[i].id))._bind();
					}
					(new _DropObject(_items[i].id))._bind();			
				}
			}
			var _filterbuttons = _getElements("span","kptFilterButton",_this);
			for(i=0;i<_filterbuttons.length;i++)
			{
				_addEvent(_filterbuttons[i],"click",_filterbutton_onclick,false);
				_addEvent(_filterbuttons[i],"mousedown",_stopPropagation,false);				
			}
			var _sortbuttons = _getElements("span","kptSortButton",_this);
			for(i=0;i<_sortbuttons.length;i++)
			{
				_addEvent(_sortbuttons[i],"mousedown",_stopPropagation,false);				
			}
			if(_pivot_list[this._id])
			{
				this.redraw();				
			}
			else
			{
				_addEvent(window,"load",eval("__=function(){pivot_redraw(\""+this._id+"\");}"),false);				
			}			
		}
		if(_pivot_list[this._id])
		{
			this._handleEvent("OnLoad",{},this);
		}
		else
		{
			this._handleEvent("OnInit",{},this);
			this._handleEvent("OnLoad",{},this);
		}
		if (_pivot_list[this._id])
		{
			_post_load_events = _pivot_list[this._id]["PostLoadEvent"];
			for(_name in _post_load_events)
			{
				if(typeof _post_load_events[_name]!="function") //Mootools
				{
					try{this._handleEvent(_name,_post_load_events[_name]);}catch(ex){}					
				}
			}
		}
		_pivot_list[this._id] = {"PostLoadEvent":{}};
	},
	go_page:function(_page_index)
	{
		this._addCommand(this._id+"_pg","GoPage",{"PageIndex":_page_index});
	},
	change_page_size:function(_pagesize)
	{
		this._addCommand(this._id+"_pg","ChangePageSize",{"PageSize":_pagesize});
	},
	_getStyle:function()
	{
		var _this = _obj(this._id);
		return (_getClass(_this)).replace("KPT","");
	},
	_addCommand:function(_id,_command,_args)
	{
		var _pivot_cmd = _obj(this._id+"_cmd");
		var _cmds = new Object();
		if (_pivot_cmd.value!="")
		{
			_cmds = eval("__="+ _pivot_cmd.value);	
		}
		_cmds[_id] = {
			"Command": _command,
			"Args": _args
		};
		_pivot_cmd.value = _json2string(_cmds);
	},
	_loadViewState:function()
	{
		var _input_viewstate = _obj(this._id+"_viewstate");
    return JSON.parse(_input_viewstate.value);
	},
	_saveViewState:function(_viewstate)
	{
		var _input_viewstate = _obj(this._id+"_viewstate");
                _input_viewstate.value = JSON.stringify(_viewstate);
	},
	_dragndrop_start:function(_drag_id)
	{
	},
	_to_field_type:function(_name)
	{
		if (_name.toLowerCase()=="column") return PV_Column;
		if (_name.toLowerCase()=="row") return PV_Row;
		if (_name.toLowerCase()=="filter") return PV_Filter;
		if (_name.toLowerCase()=="data") return PV_Data;
	},
	_dragndrop_end:function(_drag_id,_drop_id)
	{
		if(_drag_id==_drop_id) return;
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		var _matrix = [];
		var _from =null;
		var _to = null;
		var _from_position=null;
		var _to_position=null;
    for(i=0;i<_viewstate[this._id]["PVField_Ids"][PV_Filter].length;i++)
		{
			_matrix[_viewstate[this._id]["PVField_Ids"][PV_Filter][i]] = "Filter";
		}
    for(i=0;i<_viewstate[this._id]["PVField_Ids"][PV_Data].length;i++)
		{
			_matrix[_viewstate[this._id]["PVField_Ids"][PV_Data][i]] = "Data";
		}
		for(i=0;i<_viewstate[this._id]["PVField_Ids"][PV_Row].length;i++)
		{
			_matrix[_viewstate[this._id]["PVField_Ids"][PV_Row][i]] = "Row";
		}
		for(i=0;i<_viewstate[this._id]["PVField_Ids"][PV_Column].length;i++)
		{
			_matrix[_viewstate[this._id]["PVField_Ids"][PV_Column][i]] = "Column";
		}
		_from = _matrix[_drag_id];
		for(i=0;i< _viewstate[this._id]["PVField_Ids"][this._to_field_type(_from)].length;i++)
			if(_drag_id==_viewstate[this._id]["PVField_Ids"][this._to_field_type(_from)][i])
			{
				_from_position = i;
			}
		if(_index("_filterzone",_drop_id)>0)
		{
			_to = "filter";
			_to_position = _viewstate[this._id]["PVField_Ids"][PV_Filter].length;	
		}
		else if (_index("_datazone",_drop_id)>0)
		{
			_to = "data";
			_to_position = _viewstate[this._id]["PVField_Ids"][PV_Data].length;
		}
		else if (_index("_columnzone",_drop_id)>0)
		{
			_to = "column";
			_to_position = _viewstate[this._id]["PVField_Ids"][PV_Column].length;
		}
		else if (_index("_rowzone",_drop_id)>0)
		{
			_to = "row";
			_to_position = _viewstate[this._id]["PVField_Ids"][PV_Row].length;
		}
		else
		{
			var _to = _matrix[_drop_id];
			for(i=0;i< _viewstate[this._id]["PVField_Ids"][this._to_field_type(_to)].length;i++)
				if(_drop_id==_viewstate[this._id]["PVField_Ids"][this._to_field_type(_to)][i])
				{
					_to_position = i;
				}
		}
		this.move_field(_from,_from_position,_to,_to_position);
	},
	_handle_dropobject_mouseover:function(_drop_id)
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		var _tail_id = null;
		if(_index("zone",_drop_id)>0)
		{
			var _name = _drop_id.replace("zone","").replace(this._id+"_","");
			_name = _name.substring(0,1).toUpperCase()+ _name.substring(1);
                        if(_viewstate[this._id]["PVField_Ids"][this._to_field_type(_name)].length>1)
			{
				_tail_id = _viewstate[this._id]["PVField_Ids"][this._to_field_type(_name)][_viewstate[this._id]["PVField_Ids"][this._to_field_type(_name)].length-1];
			}
		}
		else
		{
		}
		var _top_indicator = _obj(this._id+"_topindicator");
		var _bottom_indicator = _obj(this._id+"_bottomindicator");
		if(_top_indicator==null || _bottom_indicator==null)
		{
			_top_indicator = _newNode("div",_this);
			_top_indicator.id=this._id+"_topindicator";
			_top_indicator.style.position="absolute";
			_setClass(_top_indicator,"kptTopIndicator");
			_bottom_indicator = _newNode("div",_this);
			_bottom_indicator.id=this._id+"_bottomindicator";
			_bottom_indicator.style.position="absolute";
			_setClass(_bottom_indicator,"kptBottomIndicator");			
		}
		_setDisplay(_top_indicator,1);
		_setDisplay(_bottom_indicator,1);
		_indicator_height = _top_indicator.offsetHeight;
		_indicator_width = _top_indicator.offsetWidth;		
		var _item = _obj(((_tail_id)?_tail_id:_drop_id));
		var _parent = _item;
		var _item_top=0,_item_left=0;
		var _item_width = _item.offsetWidth;
		while(_parent.id!=this._id)
		{
			_item_top+=_parent.offsetTop;
			_item_left+=_parent.offsetLeft;
			_parent = _parent.offsetParent;	
		}
		var _item_height = _item.offsetHeight;
		if(_tail_id)
		{
			_top_indicator.style.top = (_item_top - _indicator_height)+"px";
			_top_indicator.style.left = (_item_left + _item_width + 3 - _indicator_width/2)+"px";
			_bottom_indicator.style.top = (_item_top + _item_height)+"px";
			_bottom_indicator.style.left = (_item_left + _item_width + 3 - _indicator_width/2)+"px";			
		}
		else
		{			
			_top_indicator.style.top = (_item_top - _indicator_height)+"px";
			_top_indicator.style.left = (_item_left - _indicator_width/2 - 3)+"px";
			_bottom_indicator.style.top = (_item_top + _item_height)+"px";
			_bottom_indicator.style.left = (_item_left - _indicator_width/2 - 3)+"px";						
		}
	},
	_handle_dropobject_mouseout:function(_drop_id)
	{
		var _top_indicator = _obj(this._id+"_topindicator");
		var _bottom_indicator = _obj(this._id+"_bottomindicator");
		if(_top_indicator)
		{
			_setDisplay(_top_indicator,0);
			_setDisplay(_bottom_indicator,0);
		}		
	},
	get_field:function(_fieldname)
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		_span_fields = _getElements("span","kptFieldItem",_this);
		for(i=0;i<_span_fields.length;i++)
		{
			if(_viewstate[_span_fields[i].id]["FieldName"]==_fieldname)
			{
				return new PivotField(_span_fields[i].id);
			}
		}
		return false;
	},
	move_field:function(_from,_from_position,_to,_to_position)
	{
		if(!this._handleEvent("OnBeforeFieldMove",{"From":_from,"FromPosition":_from_position,"To":_to,"ToPosition":_to_position},this)) return;
		this._addCommand(this._id,"MoveField",{"From":_from,"FromPosition":_from_position,"To":_to,"ToPosition":_to_position});
		this._registerPostLoadEvent("OnFieldMove",{"From":_from,"FromPosition":_from_position,"To":_to,"ToPosition":_to_position},this);
		this.commit();
	},
	commit:function()
	{
		if(!this._handleEvent("OnBeforeCommit",{},this)) return;
		if (this._ajaxEnabled)
		{
			var _updatepanel = eval(this._id+"_updatepanel");
			_updatepanel.update((this._ajaxHandlePage!="")?this._ajaxHandlePage:null);
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
		var _status = _getFirstElement("div","kptStatus",_obj(this._id));
		if(_status) _addClass(_status,"kptLoading");
		this._handleEvent("OnCommit",{},this);
	},
	attach_data:function(_name,_value)
	{
		if (this._ajaxEnabled)
		{
			var _updatepanel = eval(this._id+"_updatepanel");
			_updatepanel.attachData(_name,_value);
		}				
	},
	refresh:function()
	{
		this._addCommand(this._id,"Refresh",{});
	},
	redraw:function()
	{
		var _this =_obj(this._id);
		var _content_div = _getFirstElement("div","kptContentDiv",_this);
		var _content_table = _goFirstChild(_content_div);
		var _columnheader_div = _getFirstElement("div","kptColumnHeaderDiv",_this);
		var _columnheader_table = _goFirstChild(_columnheader_div);
		var _rowheader_div = _getFirstElement("div","kptRowHeaderDiv",_this);
		var _rowheader_table = _goFirstChild(_rowheader_div);
		var _tmp = _getElements("div","kptColumnHeaderDiv",_this);
		if(_tmp.length>0)
		{
			var _columnheader_div = _tmp[0];
			_setHeight(_goFirstChild(_columnheader_div),_goParentNode(_columnheader_div).offsetHeight);
		}
		var _viewstate = this._loadViewState();
		if(_viewstate[this._id]["HorizontalScrolling"])
		{
			var _content_zone = _goParentNode(_content_div);
			var _rowheader_zone = _getFirstElement("td","kptRowHeaderZone",_this);
			var _horizontal_scroll_div = _getFirstElement("div","kptHorizontalScrollDiv",_this);
			var _horizontal_scroll_inner_div = _goFirstChild(_horizontal_scroll_div);
			var _columnheader_cols = _getElements("col","",_columnheader_div);
			var _content_cols = _getElements("col","",_content_div);
			var _columnheader_tds = _getElements("td","",_columnheader_div.firstChild.lastChild.lastChild);
			var _content_tds = _getElements("td","",_content_div.firstChild.lastChild.lastChild);
			for(i=0;i<_content_cols.length;i++)
			{
				var _max = (_content_tds[i].offsetWidth>_columnheader_tds[i].offsetWidth)?_content_tds[i].offsetWidth:_columnheader_tds[i].offsetWidth;				
				_setWidth(_content_cols[i],_max);
				_setWidth(_columnheader_cols[i],_max);
			}
			var _common_width = (_content_table.offsetWidth>_content_zone.offsetWidth)?_content_table.offsetWidth:_content_zone.offsetWidth;
			_content_table.style.tableLayout = "fixed";
			_columnheader_table.style.tableLayout = "fixed";			
			_setWidth(_content_table,_common_width);
			_setWidth(_columnheader_table,_common_width);
			var _total_width = _getWidth(_this);
			var _width = _total_width-_rowheader_zone.offsetWidth - ((_viewstate[this._id]["VerticalScrolling"])?18:0);
			_setWidth(_content_div,_width);
			_setWidth(_columnheader_div,_width);			
			_setWidth(_horizontal_scroll_div,_horizontal_scroll_div.offsetWidth);
			_setWidth(_horizontal_scroll_inner_div,(_content_table.offsetWidth/_width)*_horizontal_scroll_div.offsetWidth);
			_addEvent(_horizontal_scroll_div,"scroll",_horizontal_scroll_div_onscroll,false);		
			_horizontal_scroll_div.scrollLeft = _viewstate[this._id]["ScrollLeft"];
		}
		_content_table.style.tableLayout = "fixed";
		_columnheader_table.style.tableLayout = "fixed";			
		var _tmp = _getElements("div","kptRowHeaderDiv",_this);
		if(_tmp.length>0)
		{
			var _rowheader_div = _tmp[0];
			var _rowheader_trs = _getElements("tr","",_rowheader_div);
			var _content_trs = _getElements("tr","",_content_div);
			for(var i=0;i<_rowheader_trs.length;i++)
			{
				_height = _rowheader_trs[i].lastChild.offsetHeight;
				if(_height<_content_trs[i].offsetHeight)
				{
					_height = _content_trs[i].offsetHeight;
				}
				_setHeight(_rowheader_trs[i],_height);
				_setHeight(_content_trs[i],_height);
			}
		}
		if(_viewstate[this._id]["VerticalScrolling"])
		{
			var _total_height = _getHeight(_this);
			var _table = _goFirstChild(_this);		
			var _vertical_scroll_div = _getFirstElement("div","kptVerticalScrollDiv",_this);
			var _vertical_scroll_inner_div = _goFirstChild(_vertical_scroll_div);
			var _vertical_scroll_zone = _goParentNode(_vertical_scroll_div);
			var _height = _content_div.offsetHeight-(_table.offsetHeight - _this.offsetHeight);
			_setHeight(_content_div,_height);
			_setHeight(_rowheader_div,_height);
			_setHeight(_vertical_scroll_div,_vertical_scroll_zone.offsetHeight);
			_setHeight(_vertical_scroll_inner_div,(_content_table.offsetHeight/_height)*_vertical_scroll_zone.offsetHeight);
			_addEvent(_vertical_scroll_div,"scroll",_vertical_scroll_div_onscroll,false);		
			_addEvent(_content_div,"mousewheel",_content_div_onmousewheel,false);
			_addEvent(_content_div,"DOMMouseScroll",_content_div_onmousewheel,false);			
			_addEvent(_rowheader_div,"mousewheel",_content_div_onmousewheel,false);
			_addEvent(_rowheader_div,"DOMMouseScroll",_content_div_onmousewheel,false);
			_vertical_scroll_div.scrollTop = _viewstate[this._id]["ScrollTop"];		
		}
	},
	_handle_horizontal_scroll_div_onscroll:function(_horizontal_scroll_div)
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		var _content_div = _getFirstElement("div","kptContentDiv",_this);
		var _content_table = _goFirstChild(_content_div);
		var _columnheader_div = _getFirstElement("div","kptColumnHeaderDiv",_this);
		var _columnheader_table = _goFirstChild(_columnheader_div);
		var _scroll_left = (_horizontal_scroll_div.scrollLeft/_horizontal_scroll_div.scrollWidth)*_content_table.offsetWidth;
		_content_table.style.left = (-_scroll_left)+"px";
		_content_table.style.left = (-_scroll_left)+"px";
		_columnheader_table.style.left = (-_scroll_left)+"px";
		_columnheader_table.style.left = (-_scroll_left)+"px";
		_viewstate[this._id]["ScrollLeft"] = parseInt(_horizontal_scroll_div.scrollLeft);
		this._saveViewState(_viewstate);
	},
	_handle_vertical_scroll_div_onscroll:function(_vertical_scroll_div)
	{
		var _this = _obj(this._id);
		var _viewstate = this._loadViewState();
		var _content_div = _getFirstElement("div","kptContentDiv",_this);
		var _content_table = _goFirstChild(_content_div);
		var _rowheader_div = _getFirstElement("div","kptRowHeaderDiv",_this);
		var _rowheader_table = _goFirstChild(_rowheader_div);
		var _scroll_top = (_vertical_scroll_div.scrollTop/_vertical_scroll_div.scrollHeight)*_content_table.offsetHeight;
		_content_table.style.top = (-_scroll_top)+"px";
		_content_table.style.top = (-_scroll_top)+"px";
		_rowheader_table.style.top = (-_scroll_top)+"px";
		_rowheader_table.style.top = (-_scroll_top)+"px";
		_viewstate[this._id]["ScrollTop"] = parseInt(_vertical_scroll_div.scrollTop);
		this._saveViewState(_viewstate);		
	},
	_handle_content_div_onmousewheel:function(_e)
	{
		var _scroll_value=0;
		if(_e.wheelDelta)
		{
			_scroll_value = _e.wheelDelta/120;
		}
		else if(_e.detail)
		{
			_scroll_value = _e.detail/-3;
		}
		var _this = _obj(this._id);
		var _vertical_scroll_div = _getFirstElement("div","kptVerticalScrollDiv",_this);
		var _old_scrolltop = _vertical_scroll_div.scrollTop;
		_vertical_scroll_div.scrollTop = _vertical_scroll_div.scrollTop - _scroll_value*38;
	},
	_handle_filterbutton_onclick:function(_this,_e)
	{
		var _item_id = _goParentNode(_this).id;
		(new PivotField(_item_id)).open_filter_panel();
		this.commit();
	},
	_handle_filter_ok_onclick:function(_e)
	{
		var _this = _obj(this._id);
		var _panel_id = _goFirstChild(_this).id;
		var _hidden = _obj(_panel_id+"_hidden");
		if(_hidden.value=="vl")
		{
			var _select = _obj(_panel_id+"_select");
			var _expression = _select.options[_select.selectedIndex].value;
			var _value1 = encodeURIComponent(_obj(_panel_id+"_value1").value);
			var _value2 = encodeURIComponent(_obj(_panel_id+"_value2").value);
			if(!this._handleEvent("OnBeforeFieldFilter",{"FilterBy":"Values","Expression": _expression,"value1": _value1,"value2": _value2},this)) return;			
			this._addCommand(_panel_id,"CloseFilterPanel",{"Command":"ok","FilterBy":"Values","Expression":_expression,"value1":_value1,"value2":_value2});
			this._registerPostLoadEvent("OnFieldFilter",{"FilterBy":"Values","Expression": _expression,"value1": _value1,"value2": _value2},this);	
		}
		else
		{
			var _includeall = true;
			var _exceptions = [];
			var _items = _getElements("input","kptCheck",_this);
			var _selectall = _items[0];
			var _includes = _obj(_panel_id+"_include").checked;
			_items.splice(0,1);			
			if(_selectall.checked)
			{
				if(_includes)
				{
					_includeall = true;
					_expceptions = [];
				}
				else
				{
					_includeall = false;
					_exceptions = [];
				}
			}
			else
			{
				var _checked_counts = 0;
				var _checkeds = [];
				var _uncheckeds = [];
				var _xlist;
				for (var i = 0; i < _items.length; i++)
				{
					if (_items[i].checked) 
					{
						_checkeds.push(_items[i]);
					}
					else
					{
						_uncheckeds.push(_items[i]);
					}					
				}
				if (_includes) 
				{
					if(_checkeds.length>_uncheckeds.length)
					{
						_includeall = true;
						_xlist = _uncheckeds;
					}
					else
					{
						_includeall = false;
						_xlist = _checkeds;						
					}
				}
				else 
				{
					if(_checkeds.length>_uncheckeds.length)
					{
						_includeall = false;
						_xlist = _uncheckeds;						
					}
					else
					{
						_includeall = true;
						_xlist = _checkeds;						
					}				
				}
				for(var i=0;i<_xlist.length;i++)
				{
					var _label = _goNextSibling(_xlist[i]);
					_exceptions.push(encodeURIComponent(_label.innerHTML));
				}
			}
			if(!this._handleEvent("OnBeforeFieldFilter",{"FilterBy":"Options","IncludeAll": _includeall,"ExceptionList": _exceptions},this)) return;
			this._addCommand(_panel_id,"CloseFilterPanel",{
				"Command": "ok",
				"FilterBy": "Options",
				"IncludeAll": _includeall,
				"ExceptionList": _exceptions
			});	
			this._registerPostLoadEvent("OnFieldFilter",{"FilterBy":"Options","IncludeAll": _includeall,"ExceptionList": _exceptions},this);	
		}
		this.commit();		
	},
	_handle_filter_cancel_onclick:function(_e)
	{
		var _this = _obj(this._id);
		this._addCommand(_goFirstChild(_this).id,"CloseFilterPanel",{"Command":"cancel"});
		this.commit();
	},
	_pivot_sort_toggle:function(_sort_button)
	{
		var _item_id = _goParentNode(_sort_button).id;
		(new PivotField(_item_id)).sort((_index("SortAsc",_getClass(_sort_button))>0)?"desc":"asc");
		this.commit();
	},
	_pivot_group_sort_toggle:function(_sort_button)
	{
		var _item_id = _goParentNode(_sort_button).id;
		(new PivotField(_item_id)).sortGroup((_index("SortAsc",_getClass(_sort_button))>0)?"desc":"asc");
		this.commit();
	},	
	_pivot_grand_group_sort_toggle:function(_sort_button)
	{
		var _item_id = _goParentNode(_sort_button).id;
		(new PivotField(_item_id)).sortGrandGroup((_index("SortAsc",_getClass(_sort_button))>0)?"desc":"asc");
		this.commit();
	},
	_pivot_change_sort_data:function(_radio_button)
	{
		var _item_id = _goParentNode(_radio_button).id;
		(new PivotField(_item_id)).changeSortData(_radio_button.value, _radio_button.checked?"checked":"unchecked");
		this.commit();
	},		
	_handleEvent:function(_name,_arg,_sender)
	{
		var _viewstate = this._loadViewState();
		if(_exist(_viewstate[this._id]["ClientEvents"])&&_exist(_viewstate[this._id]["ClientEvents"][_name]))
		{
			var _func = eval(_viewstate[this._id]["ClientEvents"][_name]);		
			return _func((_sender!=null)?_sender:this,_arg);
		}
		else
		{
			return true;
		}
	},
	_registerPostLoadEvent:function(_name,_arg)
	{
		_pivot_list[this._id]["PostLoadEvent"][_name] = _arg;
	}			
}
function _filterbutton_onclick(_e)
{
	(_getPivot(this))._handle_filterbutton_onclick(this,_e);
}
function _filter_ok_onclick(_e)
{
	(_getPivot(this))._handle_filter_ok_onclick(_e);	
}
function _filter_cancel_onclick(_e)
{
	(_getPivot(this))._handle_filter_cancel_onclick(_e);	
}
function _getPivot(_this)
{
	var _div_pivot = _goParentNode(_this);
	while(_div_pivot.nodeName!="DIV" || _index("KPT",_getClass(_div_pivot))<0)
	{
		_div_pivot = _goParentNode(_div_pivot);
		if (_div_pivot.nodeName == "BODY") return null;
	}
	return eval(_div_pivot.id);
}
function get_pivot(_this)
{
	return _getPivot(_this);
}
function pivot_gopage(_this,_page)
{
	var _pivot = _getPivot(_this);
	_pivot.go_page(_page);
	_pivot.commit();
}
function pivot_group_toggle(_this)
{
	var _td = _goParentNode(_this,1);
	var _span_expands = _getElements("span","kptExpand",_td);
	if(_span_expands.length>0)
	{
		(new PivotGroup(_td.id)).collapse();	
	}
	else
	{
		(new PivotGroup(_td.id)).expand();
	}
	_getPivot(_this).commit();	
}
function _filterpanel_select_onchange(_e)
{
	var _item_id = this.id.replace("_select","");
	var _value1 = _obj(_item_id+"_value1");
	var _value2_span = _goParentNode(_obj(_item_id+"_value2"));
	switch(this.options[this.selectedIndex].value)
	{
		case "none":
			_setDisplay(_value1,0);
			_setDisplay(_value2_span,0);					
			break;
		case "between":
		case "not_between":				
			_setDisplay(_value1,1);
			_setDisplay(_value2_span,1);
			_value1.focus();					
			break;
		default:
			_setDisplay(_value1,1);
			_setDisplay(_value2_span,0);	
			_value1.focus();					
			break;														
	}
	_obj(_item_id+"_hidden").value="vl";
	_removeClass(_obj(_item_id+"_filterwithoptions"),"kptHighlight");
	_addClass(_obj(_item_id+"_filterwithvalues"),"kptHighlight");
}
function _filterpanel_value_onfocus()
{
	var _item_id = this.id.replace("_value1","").replace("_value2","");
	_obj(_item_id+"_hidden").value="vl";
	_removeClass(_obj(_item_id+"_filterwithoptions"),"kptHighlight");
	_addClass(_obj(_item_id+"_filterwithvalues"),"kptHighlight");
}
function _filterpanel_include_exclude_onchange(_e)
{
	var _item_id = this.id.replace("_include","").replace("_exclude","");
	_obj(_item_id+"_hidden").value = "ie";
	_addClass(_obj(_item_id+"_filterwithoptions"),"kptHighlight");
	_removeClass(_obj(_item_id+"_filterwithvalues"),"kptHighlight");
}
function _filterpanel_listitem_onchange(_e)
{
	var _pivot = _getPivot(this);
	var _item_id =  _goFirstChild(_obj(_pivot._id)).id;
	_obj(_item_id+"_hidden").value = "ie";
	_addClass(_obj(_item_id+"_filterwithoptions"),"kptHighlight");
	_removeClass(_obj(_item_id+"_filterwithvalues"),"kptHighlight");
	if(_index("_selectall",this.id)<0)
	{
		var _this = _obj(_item_id);
		var _items = _getElements("input","kptCheck",_this);
		var _all = true;
		for(i=1;i<_items.length;i++)
		{
			if(!_items[i].checked) _all = false;
		}
		_obj(_item_id+"_selectall").checked = _all;
	}
}
function _filterpanel_selectall_onchange(_e)
{
	var _id =  this.id.replace("_selectall","");
	var _this = _obj(_id);
	var _items = _getElements("input","kptCheck",_this);
	for(i=0;i<_items.length;i++)
	{
		if(_items[i]!=this)
		{
			_items[i].checked = this.checked;
		}
	}
}
function _horizontal_scroll_div_onscroll(_e)
{
	(_getPivot(this))._handle_horizontal_scroll_div_onscroll(this);
}
function _vertical_scroll_div_onscroll(_e)
{
	(_getPivot(this))._handle_vertical_scroll_div_onscroll(this);
}
function _content_div_onmousewheel(_e)
{
	(_getPivot(this))._handle_content_div_onmousewheel(_e);
	return _preventDefaut(_e);
}
function pivot_redraw(_id)
{
	(eval(_id)).redraw();
}
function pivot_sort_toggle(_this)
{
	(_getPivot(_this))._pivot_sort_toggle(_this);
}
function pivot_group_sort_toggle(_this)
{
	(_getPivot(_this))._pivot_group_sort_toggle(_this);
}
function pivot_grand_group_sort_toggle(_this)
{
	(_getPivot(_this))._pivot_grand_group_sort_toggle(_this);
}
function pivot_change_sort_data(_this)
{
	(_getPivot(_this))._pivot_change_sort_data(_this);
}
function pivot_pagesize_select_onchange(_this)
{
	var _page_size = _this.options[_this.selectedIndex].value;
	var _pivot = _getPivot(_this);
	_pivot.change_page_size(_page_size);
	_pivot.commit();	
}
var _pivot_list = new Array();
if(typeof(__KPTInits)!='undefined' && _exist(__KPTInits))
{	
	for(var i=0;i<__KPTInits.length;i++)
	{
		__KPTInits[i]();
	}
}
