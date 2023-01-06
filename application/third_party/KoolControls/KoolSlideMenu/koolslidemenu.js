/**
 * @author Nghiem Anh Tuan
 */
function _exist(_theObj)
{
    return (_theObj!=null)
}
function _obj(_id)
{
	return document.getElementById(_id);
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
function _getClass(_theObj)
{
    return _theObj.className;
}
function _setClass(_theObj,_val)
{
    _theObj.className = _val;
}

function _setHeight(_theObj,_val)
{
    _theObj.style.height=_val+"px";
}

function _getHeight(_theObj)
{
	return parseInt(_theObj.style.height);
}


function _setDisplay(_theObj,_val)
{
    _theObj.style.display=(_val)?"block":"none";
}

function _getDisplay(_theObj)
{
    return (_theObj.style.display!="none");
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
function _index(_search,_original)
{
	return _original.indexOf(_search);
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
/*--------------------------------------------------------*/

var _anim = 
{
	_reset:function()
	{
		this._parentids = new Array();
		this._bexpands = new Array();
		this._dones = new Array();
		this._heights = new Array();
		this._borders = new Array();		
		this._tick = 10;
	},
	_prepareNode:function(_pos)
	{
			var _linode = _obj(this._parentids[_pos]);
			var _childbox = _goNextSibling(_goFirstChild(_linode));

			//If there is no childbox -> done and quit
			if (!_exist(_childbox))
			{
				this._dones[_pos] = 1;
				return;
			}

			_childbox.style.overflow = "hidden";		
			_addClass(_linode,"ksmEffect");
			_setDisplay(_childbox,1);			
			//Get height of the box
			this._heights[_pos] = (new SlideMenuPanel(this._parentids[_pos])).getSlideMenu()._boxHeight;
			
			this._borders[_pos] = _childbox.offsetHeight - ((this._heights[_pos]<0)?_childbox.scrollHeight:this._heights[_pos]) +1;
			
			if (this._bexpands[_pos])
			{
				_setHeight(_childbox,1);
			}
			else
			{
				_setHeight(_childbox,((this._heights[_pos]<0)?_childbox.scrollHeight:this._heights[_pos])-this._borders[_pos]);
				//_setHeight(_childbox,this._heights[_pos]-this._borders[_pos]);
			}
	},
	_add:function(_parentid,_expand)
	{
		if(this._isRunning())
		{
			var _pos = -1;
			for(var i=0;i<this._parentids.length;i++)
				if(this._parentids[i]==_parentid)
				{
					_pos=i;
				}
			if(_pos<0)
			{
				this._parentids.push(_parentid);
				this._bexpands.push(_expand);
				this._heights.push(-1);
				this._borders.push(0);						
				this._dones.push(0);
				this._prepareNode(this._parentids.length-1);				
			}
			else
			{
				this._bexpands[_pos] = _expand;

				if(this._dones[_pos])
				{
					this._dones[_pos] = 0;
					this._prepareNode(_pos);					
				}
			}					
		}
		else
		{
			this._parentids.push(_parentid);
			this._bexpands.push(_expand);
			this._heights.push(-1);
			this._borders.push(0);						
			this._dones.push(0);			
		}
		
	},
	_start:function(_jump)
	{
		this._jump = _exist(_jump)?_jump:10;
		for(var i=0;i<this._dones.length;i++)
		{
			this._prepareNode(i);
		}
		
		this._timeoutid = setTimeout(function(){_anim._run()},this._tick);
	},
	_run:function()
	{
		var _alldone = true;
		
		for(var i=0;i<this._dones.length;i++)
		{
			var _linode = _obj(this._parentids[i]);
			var _childbox = _goNextSibling(_goFirstChild(_linode));
			if (!this._dones[i])
			{
				_alldone = false;
				if (this._bexpands[i])
				{
					_setHeight(_childbox,_getHeight(_childbox)+this._jump);
					
					if (_getHeight(_childbox)>=((this._heights[i]<0)?_childbox.scrollHeight:this._heights[i])-this._borders[i])
					{
						this._dones[i]=1;
						_childbox.style.height ="";
						_childbox.style.display ="";
						_childbox.style.overflow = "";
						_removeClass(_linode,"ksmEffect");
					}
				}
				else
				{
					var _newheight = _getHeight(_childbox)-this._jump;
					_setHeight(_childbox,(_newheight<0)?0:_newheight);
					if (_getHeight(_childbox)<=0)
					{
						this._dones[i]=1;
						_childbox.style.height ="";
						_childbox.style.display ="";
						_childbox.style.overflow = "";
						_removeClass(_linode,"ksmEffect");
					}
				}				
			}
					
			
		}
		if (_alldone)
		{
			this._stop();
		}	
		else
		{
			this._timeoutid = setTimeout(function(){_anim._run()},this._tick);	
		}	
	},
	_stop:function()
	{
		clearTimeout(this._timeoutid);
		this._timeoutid = null;
		this._reset();
	},
	_isRunning:function()
	{
		return _exist(this._timeoutid);
	}
}
_anim._reset();

/*--------------------------------------------------------*/
function SlideMenuParent(_id)
{
	this._id = _id;
}
SlideMenuParent.prototype = 
{
	expand:function()
	{
		if (!this.isExpanded())
		{
			if (!this.getSlideMenu()._handleEvent("OnBeforeExpand",{'ItemId':this._id})) return;				
			var _sm = this.getSlideMenu();
			var _linode = _obj(this._id);
			//Close all same level parent
			if (_sm._singleExpand)
			{
				var _ul = _goParentNode(_linode);
				for(var i=0;i<_ul.childNodes.length;i++)
				{
					//alert(_ul.childNodes.id);
					var _parent = new SlideMenuParent(_ul.childNodes[i].id);
					if (_parent.isExpanded())
					{
						_parent.collapse();	
					}
					
				}			
			}
			
			//Now expand this parent		
			_removeClass(_linode,"ksmCollapse");
			if(!_anim._isRunning())
			{
				_anim._reset();				
				_anim._add(this._id,1);
				_anim._start(_sm._slidingSpeed);
			}
			else
			{
				_anim._add(this._id,1);
			}
			this.getSlideMenu()._handleEvent("OnExpand",{'ItemId':this._id});										
		}
		
	},
	collapse:function()
	{
		if(this.isExpanded())
		{
			if (!this.getSlideMenu()._handleEvent("OnBeforeCollapse",{'ItemId':this._id})) return;				
			
			var _linode = _obj(this._id);
			var _childbox = _goNextSibling(_goFirstChild(_linode));
			_addClass(_linode,"ksmCollapse");						
			if(!_anim._isRunning())
			{
				_anim._reset();
				_anim._add(this._id,0);			
				_anim._start(this.getSlideMenu()._slidingSpeed);	
			}
			else
			{
				_anim._add(this._id,0);
			}
			this.getSlideMenu()._handleEvent("OnCollapse",{'ItemId':this._id});			
		}
	},
	isExpanded:function()
	{
		var _linode = _obj(this._id);
		return (_index("Collapse",_getClass(_linode))<0)
	},
	getSlideMenu:function()
	{
		//Return slidemenu object.
		var _node = _obj(this._id);
		while(_index("KSM",_getClass(_node))<0)
		{
			_node = _goParentNode(_node);
		}
		return eval(_node.id);
	},
	getParentId:function()
	{
		//Return id of slidemenu parent id
		var _node = _goParentNode(_obj(this._id));
		while(_index("smParent",_getClass(_goFirstChild(_node)))<0)
		{
			_node = _goParentNode(_node);
		}
		return _node.id;
	},
	_handleClick:function(_e)
	{
		if(this.isExpanded())
			this.collapse();
		else
			this.expand();
	},
	_handleMouseOver:function(_e)
	{
		this.getSlideMenu()._handleEvent("OnParentMouseOver",{'ItemId':this._id});
	},
	_handleMouseOut:function(_e)
	{
		this.getSlideMenu()._handleEvent("OnParentMouseOut",{'ItemId':this._id});
	}	
}

/*--------------------------------------------------------*/
function SlideMenuChild(_id)
{
	this._id = _id;	
}
SlideMenuChild.prototype = 
{
	select:function()
	{
		
		var _sm = this.getSlideMenu();
		var _state = _sm._getClientState();
		var _selectedid = _state.selectedId;
		if (_selectedchild!=this._id)
		{
			if (!this.getSlideMenu()._handleEvent("OnBeforeSelect",{'ItemId':this._id})) return;
			if (_selectedid!="")
			{
				var _selectedchild = new SlideMenuChild(_selectedid);
				_selectedchild.unselect();
			}
			var _a = _goFirstChild(_obj(this._id));
			_addClass(_a,"ksmSelected");
			_state.selectedId = this._id;
			_sm._saveClientState(_state);
			this.getSlideMenu()._handleEvent("OnSelect",{'ItemId':this._id});			
		}
	},
	unselect:function()
	{
		var _sm = this.getSlideMenu();
		var _state = _sm._getClientState();
		var _selectedid = _state.selectedId;
		if (_selectedid==this._id)
		{
			if (!this.getSlideMenu()._handleEvent("OnBeforeUnselect",{'ItemId':this._id})) return;

			var _a = _goFirstChild(_obj(this._id));
			_removeClass(_a,"ksmSelected");			
			_state.selectedId = this._id;
			_sm._saveClientState(_state);
			this.getSlideMenu()._handleEvent("OnUnselect",{'ItemId':this._id});
		}
	},
	_isSelected:function()
	{
		var _a = _goFirstChild(_obj(this._id));
		return (_index("smSelected",_getClass(_a))>0);
	},
	getSlideMenu:function()
	{
		//Return slidemenu object.
		var _node = _obj(this._id);
		while(_index("KSM",_getClass(_node))<0)
		{
			_node = _goParentNode(_node);
		}
		return eval(_node.id);
	},
	getParentId:function()
	{
		//Return id of slidemenu parent id
		var _node = _goParentNode(_obj(this._id));
		while(_index("smParent",_getClass(_goFirstChild(_node)))<0)
		{
			_node = _goParentNode(_node);
		}
		return _node.id;
	},
	_handleClick:function(_e)
	{
		if (this.getSlideMenu()._selectEnable)
		{
			if(!this._isSelected())
			{
				this.select();
			}			
		}
	},
	_handleMouseOver:function(_e)
	{
		this.getSlideMenu()._handleEvent("OnChildMouseOver",{'ItemId':this._id});
	},
	_handleMouseOut:function(_e)
	{
		this.getSlideMenu()._handleEvent("OnChildMouseOut",{'ItemId':this._id});		
	}
}

/*--------------------------------------------------------*/
function SlideMenuPanel(_id)
{
	this._id = _id;	
}
SlideMenuPanel.prototype = 
{
	getSlideMenu:function()
	{
		//Return slidemenu object.
		var _node = _obj(this._id);
		while(_index("KSM",_getClass(_node))<0)
		{
			_node = _goParentNode(_node);
		}
		return eval(_node.id);
	},
	getParentId:function()
	{
		//Return id of slidemenu parent id
		var _node = _goParentNode(_obj(this._id));
		while(_index("smParent",_getClass(_goFirstChild(_node)))<0)
		{
			_node = _goParentNode(_node);
		}
		return _node.id;
	}
}
/*--------------------------------------------------------*/

function KoolSlideMenu(_id,_selectEnable,_slidingSpeed,_singleExpand,_boxHeight,_clientState)
{
	this._id = _id;
	this._singleExpand = _singleExpand;
	this._boxHeight = _boxHeight;
	this._slidingSpeed = _slidingSpeed;
	this._selectEnable = _selectEnable;
	this._eventhandles = new Array();	
	_obj(_id+".clientState").value=_clientState;
	this._init();
}
KoolSlideMenu.prototype = 
{
	_init:function()
	{
		//Attach mouseover,mouseleave,click event to parent and child
		var _sm = _obj(this._id);
		var _linodes = _sm.getElementsByTagName("li");
		for(var i=0;i<_linodes.length;i++)
		{
			if(_index("smLI",_getClass(_linodes[i]))>0)
			{
				var _a = _goFirstChild(_linodes[i]);
				if(_index("smParent",_getClass(_a))>0 || _index("smChild",_getClass(_a))>0)
				{
					_addEvent(_a,"click",_item_click,false);
					_addEvent(_a,"mouseover",_item_mouseover,false);
					_addEvent(_a,"mouseout",_item_mouseout,false);					
				}
			}
		}		
	},
	_getClientState:function()
	{
		//Return the clientstate object
		var _csinput =_obj(this._id+".clientState");
		var _clientstate = eval("__="+_csinput.value);
		return _clientstate;
	},
	_saveClientState:function(_clientstate)
	{
		//Save to hidden input
		var _csinput =_obj(this._id+".clientState");
		_csinput.value = _json2string(_clientstate);				
	},
	collapseAll:function()
	{
		var _sm = _obj(this._id);
		var _linodes = _sm.getElementsByTagName("li");
		for(var i=0;i<_linodes.length;i++)
		{
			if(_index("smLI",_getClass(_linodes[i]))>0)
			{
				if(_index("smParent",_getClass(_goFirstChild(_linodes[i])))>0)
				{
					var _parent = new SlideMenuParent(_linodes[i].id);
					if (_parent.isExpanded())
					{
						_parent.collapse();
					}
				}
			}
		}				
	},
	expandAll:function()
	{
		var _sm = _obj(this._id);
		var _linodes = _sm.getElementsByTagName("li");
		for(var i=0;i<_linodes.length;i++)
		{
			if(_index("smLI",_getClass(_linodes[i]))>0)
			{
				if(_index("smParent",_getClass(_goFirstChild(_linodes[i])))>0)
				{
					var _parent = new SlideMenuParent(_linodes[i].id);
					if (!_parent.isExpanded())
					{
						_parent.expand();
					}
				}
			}
		}		
	},
	getItem:function(_itemid)
	{
		var _class = _getClass(_goFirstChild(_obj(_itemid)));
		var _item = null;
		if(_index("smParent",_class)>0)
		{
			_item = new SlideMenuParent(_itemid);
		}
		else if (_index("smChild",_class)>0)
		{
			_item = new SlideMenuChild(_itemid);
			
		}
		else if (_index("smPanel",_class)>0)
		{
			_item = new SlideMenuPanel(_itemid);			
		}
		return _item;
	},
	getSelectedId:function()
	{
		return this._getClientState().selectedId;
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
/*--------------------------------------------------------*/
function _item_click(_e)
{
	if(_index("smChild",_getClass(this))>0)
	{
		//Child
		var _item = new SlideMenuChild(_goParentNode(this).id); 
	}
	else
	{
		//Parent
		var _item = new SlideMenuParent(_goParentNode(this).id); 
	}
	_item._handleClick(_e);
}
function _item_mouseover(_e)
{
	if(_index("smChild",_getClass(this))>0)
	{
		//Child
		var _item = new SlideMenuChild(_goParentNode(this).id); 
	}
	else
	{
		//Parent
		var _item = new SlideMenuParent(_goParentNode(this).id); 
	}
	_item._handleMouseOver(_e);
	
}
function _item_mouseout(_e)
{
	if(_index("smChild",_getClass(this))>0)
	{
		//Child
		var _item = new SlideMenuChild(_goParentNode(this).id); 
	}
	else
	{
		//Parent
		var _item = new SlideMenuParent(_goParentNode(this).id); 
	}
	_item._handleMouseOut(_e);	
}

if(typeof(__KSMInits)!='undefined' && _exist(__KSMInits))
{	
	for(var i=0;i<__KSMInits.length;i++)
	{
		__KSMInits[i]();
	}
}