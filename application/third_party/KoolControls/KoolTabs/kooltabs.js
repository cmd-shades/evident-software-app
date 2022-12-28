//This file for component js
//Created with testing purpose, the code then will be obfuscate and put inside component.php
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



function _json2string(_o)
{
	var _res="{";
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
			case "object":
				_res+="\""+_name+"\":"+_json2string(_o[_name])+",";
				break;								
		}
	}
	if (_res.length>1)
		_res = _res.substring(0,_res.length-1);
	_res+="}";
	if (_res=="{}") _res="null";
	return _res;
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
//KoolTabs----------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------

function KoolTabsItem(_id)
{
	this.id = _id;
	this._id = _id;
}
KoolTabsItem.prototype =
{
	_getKTabs:function()
	{
		//Get the KoolTabs object.
		var _ulid = _goParentNode(_obj(this._id)).id;
		return eval(_ulid.substring(0,_index(".",_ulid)));
	},
	isEnabled:function()
	{
		var _li = _obj(this._id);
		return _index("ktsDisable",_getClass(_li))<0;				
	},
	enable:function(_bool)
	{
		var _li = _obj(this._id);
		(_bool)?_removeClass(_li,"ktsDisable"):_addClass(_li,"ktsDisable");		
	},
	isSelected:function()
	{
		var _a = _goFirstChild(_obj(this._id));
		return _index("Selected",_getClass(_a))>0;
	},
	select:function()
	{
		var _ktab = this._getKTabs();
		
		if (!_ktab._handleEvent("OnBeforeSelect",{"TabId":this._id})) return;
		
		
		this._removeSameLevelSelection();
		var _li = _obj(this._id);
		var _prev = _goPreviousSibling(_li);
		var _next = _goNextSibling(_li);
		
		if (_exist(_prev) && _index("ktsLI",_getClass(_prev))>-1)
		{
			_addClass(_goFirstChild(_prev),"ktsBefore");
		}
		if (_exist(_next) && _index("ktsLI",_getClass(_next))>-1)
		{
			_addClass(_goFirstChild(_next),"ktsAfter");
		}
		_addClass(_goFirstChild(_li),"ktsSelected");			

		_ktab._saveSelectedState();
		
		_ktab._handle_subs_visibility();
		
		_ktab._handleEvent("OnSelect",{"TabId":this._id});
	},
	unselect:function()
	{
		var _ktab = this._getKTabs();
		if (!_ktab._handleEvent("OnBeforeUnselect",{"TabId":this._id})) return;

		var _li = _obj(this._id);
		var _prev = _goPreviousSibling(_li);
		var _next = _goNextSibling(_li);
		
		if (_exist(_prev) && _index("ktsLI",_getClass(_prev))>-1)
		{
			_removeClass(_goFirstChild(_prev),"ktsBefore");
		}
		if (_exist(_next) && _index("ktsLI",_getClass(_next))>-1)
		{
			_removeClass(_goFirstChild(_next),"ktsAfter");
		}
		_removeClass(_goFirstChild(_li),"ktsSelected");
		_ktab._saveSelectedState();
		
		/*
		var _ul = _obj(this._getKTabs()._id+"."+this._id+".sub");
		if(_exist(_ul)) _setDisplay(_ul,0);
		*/

		_ktab._handle_subs_visibility();		
		
		_ktab._handleEvent("OnUnselect",{"TabId":this._id});				
	},
	getParentId:function()
	{
		var _ul = _goParentNode(_obj(this._id));
		var _parentid = _ul.id.replace(this._getKTabs()._id+".","").replace(".sub","");
		return _parentid;
	},
	moveToFront:function(_tabid)
	{

		if ((new KoolTabsItem(_tabid).getParentId()==this.getParentId()))
		{
			var _linode = _obj(this._id);
			var _rnode = _obj(_tabid);
			_goParentNode(_linode).insertBefore(_linode,_rnode);
			//(new KoolTabsItem(this.getParentId()))._sort();
			this._siblingSort();			
		}
	},
	moveToBehind:function(_tabid)
	{
		if ((new KoolTabsItem(_tabid).getParentId()==this.getParentId()))
		{
			var _linode = _obj(this._id);
			var _rnode = _obj(_tabid);
			var _ul = _goParentNode(_linode);
			if(_ul.lastChild==_rnode)
				_ul.appendChild(_linode);
			else
				_ul.insertBefore(_linode,_goNextSibling(_rnode));				
			
			this._siblingSort();
			//(new KoolTabsItem(this.getParentId()))._sort();
		}
	},
	getChildIds:function()
	{
		//Return child tabs in an array
		var _ktab = this._getKTabs();
		var _ul = _obj(_ktab._id+"."+this._id+".sub");
		var _tabids = new Array();
		if(_exist(_ul))
		{
			var _lis = _getElements("li","ktsLI",_ul);
			for(var i=0;i<_lis.length;i++)
			{
				_tabids.push(_lis[i].id);
			}			
		}
		return _tabids;
	},
	_remove:function()
	{
		//Send message to remove its child tabs first
		var _childids = this.getChildIds();
		if (_childids.length>0)
		{
			//Remove children
			for(var i=0;i<_childids.length;i++)
			{
				(new KoolTabsItem(_childids[i]))._remove();
			}
			//Remove sub
			var _ul = _obj(this._getKTabs()._id+"."+this._id+".sub");
			var _sub_contain = _goParentNode(_ul);
			_sub_contain.removeChild(_ul);
			
			//Remove level if necessary
			var _uls = _getElements("ul","ktsUL",_sub_contain);
			if (_uls.length==0)
			{
				//Find the level first
				_div_level = _sub_contain;
				while(_index("ktsLevel",_getClass(_div_level))<0)
				{
					_div_level = _goParentNode(_div_level);
				}
				//Remove the level
				_goParentNode(_div_level).removeChild(_div_level);
			}

		}
		
		//Remove it self
		var _li = _obj(this._id);
		_purge(_li);
		_goParentNode(_li).removeChild(_li);
	},
	_siblingSort:function()
	{
		//Sort the children
		var _ul = _goParentNode(_obj(this._id));
		var _siblings = _getElements("li","ktsLI",_ul);
		
		var _selected_index = -2;
		
		for(var i=0;i<_siblings.length;i++)
		{
			var _li = _siblings[i];
			var _a = _goFirstChild(_li);
			var _class = "";
			if (i==0 || _getClass(_li.previousSibling)=="ktsBreak")
			{
				_class += " ktsFirst";
			}
			if(i==_siblings.length-1 || _getClass(_li.nextSibling)=="ktsBreak")
			{
				_class += " ktsLast";
			}
			if (_class == "") _class = "ktsMid";
			
			_setClass(_li,"ktsLI "+_class);
			
			if (_index("ktsSelected",_getClass(_a))>-1)
			{
				_selected_index = i;
			}
		}
		//Selection
		
		for(var i=0;i<_siblings.length;i++)
		{
			var _li = _siblings[i];
			var _a = _goFirstChild(_li);
			switch(i)
			{
				case _selected_index:
					//alert(_selected_index);
					_setClass(_a,"ktsA ktsSelected");
					break;
				case (_selected_index+1):
					_setClass(_a,"ktsA ktsAfter");
					break;
				case (_selected_index-1):
					_setClass(_a,"ktsA ktsBefore");
					break;
				default:
					_setClass(_a,"ktsA");
					break;
			}
			
		}	
		
	},
	_removeSameLevelSelection:function()
	{
		//Remove all seleciton in the same level with this tab
		
		var _ul = _goParentNode(_obj(this._id));
		var _items = _getElements("li","ktsLI",_ul);
		for(var i=0;i<_items.length;i++)
		{
			var _tab = new KoolTabsItem(_items[i].id);
			if (_tab.isSelected()) _tab.unselect();
		}
	},
	_handle_click:function(_e)
	{
		if (!this._getKTabs()._handleEvent("OnBeforeClick",{"TabId":this._id})) return;
		if(this.isEnabled() && !this.isSelected())
		{			
			this.select();
		}
		this._getKTabs()._handleEvent("OnClick",{"TabId":this._id});
	},
	_handle_mouseover:function(_e)
	{
		this._getKTabs()._handleEvent("OnMouseOver",{"TabId":this._id});
	},
	_handle_mouseout:function(_e)
	{
		this._getKTabs()._handleEvent("OnMouseOut",{"TabId":this._id});		
	}
}

function KoolTabs(_id,_position)
{
	this.id = _id;
	this._id = _id;
	this._position = _position;
	this._eventhandles = new Array();
	this._scrollulid = null;
	this._scrolltimeoutid = null;
	
	this._checkjusify();
	this._init();	
}
KoolTabs.prototype =
{
	_init:function()
	{
		var _ktab = _obj(this._id);
		//For tab item
		var _items = _getElements("li","ktsLI",_ktab);
		for(var i=0;i<_items.length;i++)
		{
			_addEvent(_items[i],"click",_item_click,false);
			_addEvent(_items[i],"mouseover",_item_mouseover,false);
			_addEvent(_items[i],"mouseout",_item_mouseout,false);			
		}
		//For arrow button
		var _prevs = _getElements("a","ktsPrev",_ktab);
		for(var i=0;i<_prevs.length;i++)
		{
			_addEvent(_prevs[i],"mousedown",_arrow_mousedown,false);
			_addEvent(_prevs[i],"mouseup",_arrow_mouseup,false);			
		}
		var _nexts = _getElements("a","ktsNext",_ktab);
		for(var i=0;i<_nexts.length;i++)
		{
			_addEvent(_nexts[i],"mousedown",_arrow_mousedown,false);
			_addEvent(_nexts[i],"mouseup",_arrow_mouseup,false);			
		}
		
		//align all scrolling
		var _divs = _getElements("div","Scroll",_ktab);
		for(var i=0;i<_divs.length;i++)
		{
			this._align_scroll(_divs[i]);
		}
		
		this._saveSelectedState();
	},
	_checkjusify:function()
	{
		
		//Alignment justify. Find and edit the alignment.
		var _justifylevels = _getElements("div","ktsjustify",_obj(this._id));
		
		
		for(var i=0;i<_justifylevels.length;i++)
		{
			var _uls = _getElements("ul","ktsUL",_justifylevels[i]);
			for(var j=0;j< _uls.length;j++)
			{
				var _lis = _getElements("li","ktsLI",_uls[j]);
				for(var k=0;k<_lis.length;k++)
				{
					_lis[k].style.width = (100/_lis.length)+"%"
				}
			}
		}
		
	},
	getSelectedChain:function()
	{
		_input = _obj(this._id+"_selected");
		return _input.value;
	},
	getTab:function(_tabid)
	{
		return (new KoolTabsItem(_tabid));
	},
	removeTab:function(_tabid)
	{
		var _ul = _goParentNode(_obj(_tabid));//ul sub
		//Remove tab
		(new KoolTabsItem(_tabid))._remove();
		
		//Check if there is no tab in this sub, if so remove sub
		var _lis = _getElements("li","ktsLI",_ul);
		if(_lis.length>0 || _index(".root.",_ul.id)>0)
		{
			//Sort the tab if there is item left
			if (_lis.length>0)
			{
				//Choose the first item to do sibling sorting
				(new KoolTabsItem(_lis[0].id))._siblingSort();
			}
		}
		else
		{
			//Remove the sub
			var _sub_contain = _goParentNode(_ul);
			_sub_contain.removeChild(_ul);
			
			
			//Now continue checking if this is only sub
			_uls = _getElements("ul","ktsUL",_sub_contain);
			if (_uls.length<1)
			{
				//Find the level first
				_div_level = _sub_contain;
				
				while(_index("ktsLevel",_getClass(_div_level))<0)
				{
					_div_level = _goParentNode(_div_level);
				}
				//Remove the level
				_goParentNode(_div_level).removeChild(_div_level);
			}
		}
		
		//Renew the selected
		this._saveSelectedState();
		
		//Re-justify after remove tabs.
		this._checkjusify();
		
	},
	addTab:function(_parentid,_id,_text,_link,_selected,_enabled,_width,_height)
	{
		//Later	
		if (!_exist(_link)) _link = "";
		if (!_exist(_selected)) _selected = 0;
		if (!_exist(_enabled)) _enabled = 1;
		if (!_exist(_width)) _width = "";
		if (!_exist(_height)) _height = "";
		
		var _ul = _obj(this._id+"."+_parentid+".sub");
		
		if(!_exist(_ul))
		{
			//Find out the level of parent tab
			var _parent_tab = new KoolTabsItem(_parentid);
			var _level = 0;
			while(_index("root",_parent_tab.getParentId())<0)
			{
				_parent_tab = new KoolTabsItem(_parent_tab.getParentId()); 
				_level++;
			}
			_level_divs = _getElements("div","ktsLevel"+(_level+1),_obj(this._id));
			//Find out the level div. if not found, create a new one base on ktsLevel0
			if (_level_divs.length==0)
			{
				//Create div level
				_div_level_parent = _getElements("div","ktsLevel"+_level,_obj(this._id))[0];
				_div = _div_level_parent.cloneNode(true);
				var _uls = _getElements("ul","ktsUL",_div);
				var _sub_contain = _goParentNode(_uls[0]);
				_sub_contain.innerHTML = "";
				_goParentNode(_div_level_parent).insertBefore(_div,_div_level_parent);
				if (this._position!="bottom")
				{
					_goParentNode(_div_level_parent).insertBefore(_div_level_parent,_div);	
				}
				//Increase level
				_replaceClass(_level,_level+1,_div);
			}
			else
			{
				//Found div level
				var _div = _level_divs[0];
				var _uls = _getElements("ul","ktsUL",_div);
				var _sub_contain = _goParentNode(_uls[0]);
			}
				
			//Adding sub ul for parent tab
			var _ul = _newNode("ul",_sub_contain);
			_ul.id = this._id+"."+_parentid+".sub";
			_setClass(_ul,"ktsUL");
			_setDisplay(_ul,0);
			
		}
		
		
		//Add tab to ul sub
		var _template = "<a class='ktsA' {link}>{tabtemplate}</a>";
		var _tabtemplate = _obj(this._id+".tab.template").innerHTML;
		var _tab_a = _replace("{tabtemplate}",_tabtemplate,_template);			
		_tab_a = _replace("{link}",(_link!="")?"href='"+_link+"'":"",_tab_a);
		_tab_a = _replace("{tabcontent}","<span class='ktsText'>"+_text+"</span>",_tab_a);
		
		var _li = _newNode("li",_ul);
		_li.id = _id;
		_setClass(_li,"ktsLI");
		_li.style.width = _width;
		_li.style.height = _height;
		_li.innerHTML = _tab_a;
		//Add event
		_addEvent(_li,"click",_item_click,false);
		_addEvent(_li,"mouseover",_item_mouseover,false);
		_addEvent(_li,"mouseout",_item_mouseout,false);			
		var _newtab = new KoolTabsItem(_id);
		
		_newtab._siblingSort();
		_newtab.enable(_enabled);
		if (_selected) _newtab.select();
		
		this._checkjusify();
		
		//align_scroll
	},
	registerEvent:function(_eventname,_handleFunction)
	{
		this._eventhandles[_eventname] = _handleFunction;
	},
	_saveSelectedState:function()
	{
		var _sub = _obj(this._id+".root.sub");
		var _selectedString = "";
		while(_exist(_sub))
		{
			var _a_selected = _getElements("a","ktsSelected",_sub)[0];
			if (_exist(_a_selected))
			{
				var _li = _goParentNode(_a_selected);
				_selectedString+=":"+_li.id;
				_sub = _obj(this._id+"."+_li.id+".sub");				
			}
			else
			{
				_sub = null;
			}
		}
		_selectedString = _selectedString.substring(1);
		var _input = _obj(this._id+"_selected");
		_input.value = _selectedString;
	},
	_handle_subs_visibility:function()
	{
		var _chain = this.getSelectedChain();
		//Make all invisble
		_uls = _getElements("ul","ktsUL",_obj(this._id));
		for(var i=0;i<_uls.length;i++)
			if (_index(".root.",_uls[i].id)<0)
				_setDisplay(_uls[i],0);
		//Make selected chain visible
		var _list = _chain.split(":");
		for(var i=0;i<_list.length;i++)
		{
			_ul = _obj(this._id+"."+_list[i]+".sub");
			if(_exist(_ul))
			{				
				_setDisplay(_ul,1);			
				//Find the div level
				var _div = _goParentNode(_ul);
				while(_index("ktsLevel",_getClass(_div))<0)
				{
					_div = _goParentNode(_div);
				}
				this._align_scroll(_div);
			}
			
		}
			
	},
	_handleEvent:function(_name,_arg)
	{
		return (_exist(this._eventhandles[_name]))?this._eventhandles[_name](this,_arg):true;
	},
	_handle_arrow_mousedown:function(_arrow,_e)
	{			
		if(_index("Disable",_getClass(_arrow))<0)
		{
			//Can scroll
			var _uls = _getElements("ul","ktsUL",_goParentNode(_arrow));
			var _ulid = "";
			for(var i=0;i<_uls.length;i++)
				if (_getDisplay(_uls[i]))
				{
					_ulid = _uls[i].id;
					break;
				}		
			var _direction = (_index("Prev",_getClass(_arrow))<0)?1:-1;
			this._scrolltimeoutid = setTimeout(this._id+".SC('"+_ulid+"',"+_direction+");",15);
		}
	},
	_handle_arrow_mouseup:function(_arrow,_e)
	{
		if(_index("Disable",_getClass(_arrow))<0)
		{
			this._stop_scroll();
		}
		
	},
	_stop_scroll:function()
	{
		if (_exist(this._scrolltimeoutid))
		{
			clearTimeout(this._scrolltimeoutid);	
			this._scrolltimeoutid = null;
		}	
	},
	_align_scroll:function(_div)
	{
		if (_index("Scroll",_getClass(_div))>0)
		{
			var _uls = _getElements("ul","ktsUL",_div);
			var _ul = null;
			for(var i=0;i<_uls.length;i++)
				if (_getDisplay(_uls[i]))
				{
					_ul = _uls[i];
					break;
				}
			if (_exist(_ul))
			{
				if(_ul.style.marginLeft=="") _ul.style.marginLeft="0px";
				var _marginleft = parseInt(_ul.style.marginLeft);
				
				var _prev = _getElements("a","Prev",_div)[0];
				var _next = _getElements("a","Next",_div)[0];
				
				
				if (_marginleft>=0)
				{
					_marginleft=0;
					_setClass(_prev,"ktsPrevDisable");
					this._stop_scroll();
				}
				else
				{
					_setClass(_prev,"ktsPrev");				
				}
				
				//Calculate width of tab;
				var _items = _getElements("li","ktsLI",_ul);
				var _tablength=0;
				for(var i=0;i<_items.length;i++)
					_tablength+=_items[i].offsetWidth;
				//Calculate arrow width
				var _arrowlength = 0;
				if (_index("leftScroll",_getClass(_div))>-1)
				{
					_arrowlength = 0;
				}else if (_index("rightScroll",_getClass(_div))>-1)
				{
					_arrowlength = _prev.offsetWidth + _next.offsetWidth;
				}
				else if (_index("middleScroll",_getClass(_div))>-1)
				{
					_arrowlength = _next.offsetWidth;
				}
				if (_tablength+_marginleft>_div.offsetWidth - _arrowlength)
				{
					_setClass(_next,"ktsNext");
				}
				else
				{
					_setClass(_next,"ktsNextDisable");
					_marginleft = _div.offsetWidth - _arrowlength - _tablength;
					this._stop_scroll();			
				}
				
			}
		}
	},
	SC:function(_ulid,_direction)
	{
		this._scrolltimeoutid = setTimeout(this._id+".SC('"+_ulid+"',"+_direction+");",15);
		var _stepmove = _direction*5;// 5 is the scroll speed;
		var _ul = _obj(_ulid);
		if(_exist(_ul))
		{
			if(_ul.style.marginLeft=="") _ul.style.marginLeft="0px";
			
			_ul.style.marginLeft = (parseInt(_ul.style.marginLeft) - _stepmove) +"px";
			
			//Find the div level
			var _div = _goParentNode(_ul);
			while(_index("ktsLevel",_getClass(_div))<0)
			{
				_div = _goParentNode(_div);
			}
			this._align_scroll(_div);			
		}
	}	
}

function _item_click(_e)
{
	(new KoolTabsItem(this.id))._handle_click(_e);
}
function _item_mouseover(_e)
{
	(new KoolTabsItem(this.id))._handle_mouseover(_e);
}
function _item_mouseout(_e)
{
	(new KoolTabsItem(this.id))._handle_mouseout(_e);	
}

function _arrow_mousedown(_e)
{
	var _div_level = _goParentNode(this);
	var _first_ul = _getElements("ul","ktsUL",_div_level)[0];
	var _ulid = _first_ul.id;
	var _ktabs = eval("__="+_ulid.substring(0,_index(".",_ulid)));
	_ktabs._handle_arrow_mousedown(this,_e);	
}

function _arrow_mouseup(_e)
{
	var _div_level = _goParentNode(this);
	var _first_ul = _getElements("ul","ktsUL",_div_level)[0];
	var _ulid = _first_ul.id;
	var _ktabs = eval("__="+_ulid.substring(0,_index(".",_ulid)));
	_ktabs._handle_arrow_mouseup(this,_e);
}

if(typeof(__KTSInits)!='undefined' && _exist(__KTSInits))
{	
	for(var i=0;i<__KTSInits.length;i++)
	{
		__KTSInits[i]();
	}
}
