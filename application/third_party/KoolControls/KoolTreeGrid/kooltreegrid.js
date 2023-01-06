/**
 * @author Nghiem Anh Tuan
 */
function _exist(_theObj)
{
    return (_theObj != null)
}
function _newNode(_sTag, _oParent)
{
    var _oNode = document.createElement(_sTag);
    _oParent.appendChild(_oNode);
    return _oNode;
}
function _obj(_id)
{
    return document.getElementById(_id);
}
function _goParentNode(_theObj, _level)
{
    if (!_exist(_level))
        _level = 1;
    for (var i = 0; i < _level; i++)
        _theObj = _theObj.parentNode;
    return _theObj;
}
function _goFirstChild(_theObj, _level)
{
    if (!_exist(_level))
        _level = 1;
    for (var i = 0; i < _level; i++)
        _theObj = _theObj.firstChild;
    return _theObj;
}
function _goNextSibling(_theObj, _level)
{
    if (!_exist(_level))
        _level = 1;
    for (var i = 0; i < _level; i++)
        _theObj = _theObj.nextSibling;
    return _theObj;
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
            var _tmp = function() {
                _fn.apply(_ob, [window.event]);
            }
            if (!_ob['ref' + _evType])
                _ob['ref' + _evType] = [];
            else {
                for (var _ref in _ob['ref' + _evType]) {
                    if (_ob['ref' + _evType][_ref]._fn === _fn)
                        return false;
                }
            }
            var _r = _ob.attachEvent('on' + _evType, _tmp);
            if (_r)
                _ob['ref' + _evType].push({_fn: _fn, _tmp: _tmp});
            return _r;
        }
    }
    else {
        return false;
    }
}
;
function _removeEvent(_ob, _evType, _fn, _useCapture)
{
    if (_ob.removeEventListener)
    {
        _ob.removeEventListener(_evType, _fn, _useCapture);
        return true;
    }
    else if (_ob.detachEvent) {
        if (_ob['ref' + _evType]) {
            for (var _ref in _ob['ref' + _evType]) {
                if (_ob['ref' + _evType][_ref]._fn === _fn) {
                    _ob.detachEvent('on' + _evType, _ob['ref' + _evType][_ref]._tmp);
                    _ob['ref' + _evType][_ref]._fn = null;
                    _ob['ref' + _evType][_ref]._tmp = null;
                    delete _ob['ref' + _evType][_ref];
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
function _getClass(theObj)
{
    return theObj.className;
}
function _setClass(theObj, val)
{
    theObj.className = val;
}
function _addClass(_theObj, _class)
{
    if (_theObj.className.indexOf(_class) < 0)
    {
        var _listclass = _theObj.className.split(" ");
        _listclass.push(_class);
        _theObj.className = _listclass.join(" ");
    }
}
function _removeClass(_theObj, _class)
{
    if (_theObj.className.indexOf(_class) > -1)
    {
        _replaceClass(_class, "", _theObj)
        var _listclass = _theObj.className.split(" ");
        _theObj.className = _listclass.join(" ");
    }
}
function _replaceClass(_search, _rep, _o)
{
    _setClass(_o, _getClass(_o).replace(_search, _rep));// Only the first
}
function _getChildByClass(_o, _class)
{
    for (var i = 0; i < _o.childNodes.length; i++)
        if (_o.childNodes[i].className.indexOf(_class) > -1)
            return _o.childNodes[i];
}
function _setDisplay(_theObj, _val)
{
    _theObj.style.display = (_val) ? "block" : "none";
}
function _getDisplay(_theObj)
{
    return (_theObj.style.display != "none");
}
function _json2string(_o)
{
    var _res = "";
    for (var _name in _o)
    {
        switch (typeof (_o[_name]))
        {
            case "string":
                if (_exist(_o.length))
                    _res += "'" + _o[_name] + "',";
                else
                    _res += "'" + _name + "':'" + _o[_name] + "',";
                break;
            case "number":
                if (_exist(_o.length))
                    _res += _o[_name] + ",";
                else
                    _res += "'" + _name + "':" + _o[_name] + ",";
                break;
            case "object":
                if (_exist(_o.length))
                    _res += _json2string(_o[_name]) + ",";
                else
                    _res += "'" + _name + "':" + _json2string(_o[_name]) + ",";
                break;
        }
    }
    if (_res.length > 0)
        _res = _res.substring(0, _res.length - 1);
    _res = (_exist(_o.length)) ? "[" + _res + "]" : "{" + _res + "}";
    if (_res == "{}")
        _res = "null";
    return _res;
}
var _isCtrl = false;
function _Doc_KeyDown(_e)
{
    _key = (window.event) ? event.keyCode : _e.keyCode;
    if (_key == 17)
    {
        _isCtrl = true;
    }
}
function _Doc_KeyUp(_e)
{
    _key = (window.event) ? event.keyCode : _e.keyCode;
    if (_key == 17)
    {
        _isCtrl = false;
    }
}
_addEvent(document, "keyup", _Doc_KeyUp, false);
_addEvent(document, "keydown", _Doc_KeyDown, false);
function _mouseXY(_ev) {
    if (_ev.pageX || _ev.pageY) {
        return {_x: _ev.pageX, _y: _ev.pageY};
    }
    return {
        _x: _ev.clientX + document.body.scrollLeft - document.body.clientLeft,
        _y: _ev.clientY + document.body.scrollTop - document.body.clientTop
    };
}
var _Cookies = {
    _init: function() {
        var _allCookies = document.cookie.split('; ');
        for (var i = 0; i < _allCookies.length; i++) {
            var _cookiePair = _allCookies[i].split('=');
            this[_cookiePair[0]] = _cookiePair[1];
        }
    },
    _create: function(_name, _value, _hours) {
        if (_hours) {
            var _date = new Date();
            _date.setTime(_date.getTime() + (_hours * 60 * 60 * 1000));
            var _expires = "; expires=" + _date.toGMTString();
        }
        else
            var _expires = "";
        document.cookie = _name + "=" + _value + _expires + "; path=/";
        this[_name] = _value;
    },
    _erase: function(_name) {
        this._create(_name, '', -1);
        this[_name] = undefined;
    }
};
_Cookies._init();
function TreeNode(_oid)
{
    this.NodeId = _oid;
    this._id = _oid;
}
TreeNode.prototype =
        {
            getText: function()
            {
                return this._getPart("Text").innerHTML;
            },
            setText: function(_text)
            {
                this._getPart("Text").innerHTML = _text;
                return this;
            },
            getImageSrc: function()
            {
                var _image = this._getPart("Image");
                return (_exist(_image) ? _image.src : "");
            },
            setImageSrc: function(_src)
            {
                var _image = this._getPart("Image");
                if (_exist(_image))
                    _image.src = _src;
                return this;
            },
            disableSelect: function(_bool)
            {
                (_bool) ? this._disable("select") : this._enable("select");
                return this;
            },
            disableDrag: function(_bool)
            {
                (_bool) ? this._disable("drag") : this._enable("drag");
                return this;
            },
            disableDrop: function(_bool)
            {
                (_bool) ? this._disable("drop") : this._enable("drop");
                return this;
            },
            disableEdit: function(_bool)
            {
                (_bool) ? this._disable("edit") : this._enable("edit");
                return this;
            },
            _disable: function(_case)
            {
                var _tree = this.getTree();
                var _clientState = _tree._getClientState();
                var _list = _clientState[_case + "DisableIds"];
                if (_list.join(" ").indexOf(this._id) < 0)
                    _list.push(this._id);
                _tree._saveClientState(_clientState);
            },
            _enable: function(_case)
            {
                var _tree = this.getTree();
                var _clientState = _tree._getClientState();
                var _list = _clientState[_case + "DisableIds"];
                for (var i in _list)
                    if (typeof _list[i] != "function") //Mootools
                        if (_list[i] == this._id)
                        {
                            _list.splice(i, 1);
                            break;
                        }
                _tree._saveClientState(_clientState);
            },
            getData: function(_key)
            {
                var _divnode = _goFirstChild(_obj(this._id));
                var _input = null;
                for (var i = 0; i < _divnode.childNodes.length; i++)
                    if (_divnode.childNodes[i].nodeName == "INPUT")
                        if (_divnode.childNodes[i].type == "hidden")
                            _input = _divnode.childNodes[i];
                if (_exist(_input))
                {
                    var _data = eval("__=" + _input.value);
                    var _value = _data["data"][_key];
                    return (_exist(_value) ? decodeURIComponent(_value) : "");
                }
                else
                {
                    return "";
                }
            },
            addData: function(_key, _value)
            {
                var _divnode = _goFirstChild(_obj(this._id));
                var _input = null;
                var _data = {"url": "", "data": {}};
                for (var i = 0; i < _divnode.childNodes.length; i++)
                    if (_divnode.childNodes[i].nodeName == "INPUT")
                        if (_divnode.childNodes[i].type == "hidden")
                            _input = _divnode.childNodes[i];
                if (_exist(_input))
                {
                    var _data = eval("__=" + _input.value);
                }
                else
                {
                    _input = _newNode("input", _divnode);
                    _input.type = "hidden";
                }
                _data["data"][_key] = _value;
                _input.value = _json2string(_data);
            },
            select: function()
            {
                var _divnode = _goFirstChild(_obj(this._id));
                if (_getClass(_divnode).indexOf("Selected") < 0)
                {
                    if (!this.getTree()._handleEvent("OnBeforeSelect", {'NodeId': this._id}))
                        return;
                    _addClass(_divnode, "ktvSelected");
                    var _tree = this.getTree();
                    var _state = _tree._getClientState();
                    if (!_exist(_state.selectedIds))
                        _state.selectedIds = new Array();
                    _state.selectedIds.push(this._id);
                    _tree._saveClientState(_state);
                    this.getTree()._handleEvent("OnSelect", {'NodeId': this._id});
                }
                return this;
            },
            unselect: function()
            {
                var _divnode = _goFirstChild(_obj(this._id));
                if (_getClass(_divnode).indexOf("Selected") > 0)
                {
                    if (!this.getTree()._handleEvent("OnBeforeUnselect", {'NodeId': this._id}))
                        return;
                    _removeClass(_divnode, "ktvSelected");
                    var _tree = this.getTree();
                    var _state = _tree._getClientState();
                    for (var i = 0; i < _state.selectedIds.length; i++)
                        if (_state.selectedIds[i] == this._id)
                        {
                            _state.selectedIds.splice(i, 1);
                            break;
                        }
                    _tree._saveClientState(_state);
                    this.getTree()._handleEvent("OnUnselect", {'NodeId': this._id});
                }
                return this;
            },
            expand: function()
            {
                var _linode = _obj(this._id);
                var _ul = _goNextSibling(_goFirstChild(_linode));
                if (_exist(_ul))
                {
                    if (!this.getTree()._handleEvent("OnBeforeExpand", {'NodeId': this._id}))
                        return;
                    var _plus = this._getPart("Plus");
                    if (_exist(_plus))
                        _replaceClass("Plus", "Minus", _plus);
                    _setDisplay(_ul, 1);
                    if (this.getTree()._singleExpand)
                    {
                        var _exceptionlist = new Array();
                        var _parentid = this._id;
                        while (_parentid.indexOf(".root") < 0)
                        {
                            _exceptionlist.push(_parentid);
                            _parentid = (new TreeNode(_parentid)).getParentId();
                        }
                        _exceptionlist.push(_parentid);//The root node;
                        this.getTree()._collapseAll(_exceptionlist);
                    }
                    this.getTree()._saveExpandCollapseCookie(this._id, 1);
                    this.getTree()._handleEvent("OnExpand", {'NodeId': this._id});
                }
                else
                {
                    var _divnode = _goFirstChild(_obj(this._id));
                    var _input = null;
                    for (var i = 0; i < _divnode.childNodes.length; i++)
                        if (_divnode.childNodes[i].nodeName == "INPUT")
                            _input = _divnode.childNodes[i];
                    if (_exist(_input))
                    {
                        var _data = eval("__=" + _input.value);
                        if (_exist(_data.url) && _data.url != "")
                        {
                            _data.url = decodeURIComponent(_data.url);
                            if (!this.getTree()._handleEvent("OnBeforeExpand", {'NodeId': this._id}))
                                return;
                            this.loadSubTree(_data.url);
                            _data.loading = 1;
                            _input.value = _json2string(_data);
                        }
                    }
                }
                return this;
            },
            deepExpand: function() {
                var tree = this.getTree();
                var _linode = _obj(this._id);
                var _ul = _goNextSibling(_goFirstChild(_linode));
                if (_exist(_ul))
                {
                    var _plus = this._getPart("Plus");
                    if (_exist(_plus))
                        _replaceClass("Plus", "Minus", _plus);
                    _setDisplay(_ul, 1);
                    tree._saveExpandCollapseCookie(this._id, 1);
                }
                else
                {
                    var _divnode = _goFirstChild(_obj(this._id));
                    var _input = null;
                    for (var i = 0; i < _divnode.childNodes.length; i++)
                        if (_divnode.childNodes[i].nodeName == "INPUT")
                            _input = _divnode.childNodes[i];
                    if (_exist(_input))
                    {
                        var _data = eval("__=" + _input.value);
                        if (_exist(_data.url) && _data.url != "")
                        {
                            _data.url = decodeURIComponent(_data.url);
                            this.loadSubTree(_data.url);
                            _data.loading = 1;
                            _input.value = _json2string(_data);
                        }
                    }
                }
                var childIds = this.getChildIds();
                for (var i = 0; i < childIds.length; i += 1)
                    tree.getNode(childIds[i]).deepExpand();
                return this;
            },
            collapse: function()
            {
                var _linode = _obj(this._id);
                var _ul = _goNextSibling(_goFirstChild(_linode));
                if (_exist(_ul))
                {
                    if (!this.getTree()._handleEvent("OnBeforeCollapse", {'NodeId': this._id}))
                        return;
                    var _minus = this._getPart("Minus");
                    if (_exist(_minus))
                        _replaceClass("Minus", "Plus", _minus);
                    _setDisplay(_ul, 0);
                    this.getTree()._saveExpandCollapseCookie(this._id, 0);
                    this.getTree()._handleEvent("OnCollapse", {'NodeId': this._id});
                }
                return this;
            },
            deepCollapse: function()
            {
                var tree = this.getTree();
                var _linode = _obj(this._id);
                var _ul = _goNextSibling(_goFirstChild(_linode));
                if (_exist(_ul))
                {
                    var _minus = this._getPart("Minus");
                    if (_exist(_minus))
                        _replaceClass("Minus", "Plus", _minus);
                    _setDisplay(_ul, 0);
                    tree._saveExpandCollapseCookie(this._id, 0);
                }
                var childIds = this.getChildIds();
                for (var i = 0; i < childIds.length; i += 1)
                    tree.getNode(childIds[i]).deepCollapse();
                return this;
            },
            getChildIds: function()
            {
                var _ul = _goNextSibling(_goFirstChild(_obj(this._id)));
                var _nodeids = new Array();
                if (_exist(_ul))
                {
                    for (var i = 0; i < _ul.childNodes.length; i++)
                        if (_ul.childNodes[i].nodeName == "LI")
                            _nodeids.push(_ul.childNodes[i].id);
                }
                return _nodeids;
            },
            getParentId: function()
            {
                return _goParentNode(_obj(this._id), 2).id;
            },
            getTree: function()
            {
                var _parentid = this._id;
                while (_parentid.indexOf(".root") < 0)
                {
                    _parentid = (new TreeNode(_parentid)).getParentId();
                }
                return eval(_parentid.replace(".root", ""));
            },
            moveToAbove: function(_nodeid)
            {
                if ((new TreeNode(_nodeid).getParentId() == this.getParentId()))
                {
                    var _linode = _obj(this._id);
                    var _rnode = _obj(_nodeid);
                    _goParentNode(_linode).insertBefore(_linode, _rnode);
                    (new TreeNode(this.getParentId()))._sort();
                }
                return this;
            },
            moveToBelow: function(_nodeid)
            {
                if ((new TreeNode(_nodeid).getParentId() == this.getParentId()))
                {
                    var _linode = _obj(this._id);
                    var _rnode = _obj(_nodeid);
                    var _ul = _goParentNode(_linode);
                    if (_ul.lastChild == _rnode)
                        _ul.appendChild(_linode);
                    else
                        _ul.insertBefore(_linode, _goNextSibling(_rnode));
                    (new TreeNode(this.getParentId()))._sort();
                }
                return this;
            },
            attachTo: function(_nodeid)
            {
                var _tmpid = _nodeid;
                var _ischild = false;
                while (_tmpid.indexOf(".root") < 0)
                {
                    _tmpid = (new TreeNode(_tmpid)).getParentId();
                    if (_tmpid == this._id)
                        _ischild = true;
                }
                if (_ischild)
                {
                    return false;
                }
                var _parentid = this.getParentId();
                if (_parentid == _nodeid)
                {
                    return false;
                }
                var _linode = _obj(_nodeid);
                var _ul = _goNextSibling(_goFirstChild(_linode));
                if (!_exist(_ul))
                {
                    _ul = _newNode("ul", _linode);
                    _setClass(_ul, "ktvUL");
                    (new TreeNode(_nodeid)).getTree()._saveExpandCollapseCookie(_nodeid, 1);
                }
                _ul.appendChild(_obj(this._id));
                (new TreeNode(_nodeid))._sort();
                (new TreeNode(_parentid))._sort();
                return true;
            },
            loadSubTree: function(_url)
            {
                if (typeof koolajax != 'undefined' && _exist(koolajax))
                {
                    var _loading = this._getPart("Loading");
                    if (!_exist(_loading))
                    {
                        if (!this.getTree()._handleEvent("OnBeforeSubTreeLoad", {'NodeId': this._id, 'Url': _url}))
                            return;
                        var _divnode = _goFirstChild(_obj(this._id));
                        _loading = _newNode("span", _divnode);
                        _setClass(_loading, "ktvLoading");
                        koolajax.load(_url, eval("__=function(ct){" + this.getTree()._id + ".OSTLD('" + this._id + "',ct);}"));
                    }
                }
                return this;
            },
            _handleSubTreeLoad: function(_ct)
            {
                var _linode = _obj(this._id);
                var _tree = this.getTree();
                var _ul = _goNextSibling(_goFirstChild(_linode));
                if (_exist(_ul))
                {
                    this.getTree()._removeEvent(_ul);
                }
                else
                {
                    _ul = _newNode("ul", _linode);
                    _setClass(_ul, "ktvUL");
                }
                _ul.innerHTML += _ct;
                var _loading = this._getPart("Loading");
                if (_exist(_loading))
                    _goFirstChild(_linode).removeChild(_loading);
                _tree._addEvent(_ul);
                this._sort();
                _tree._handleEvent("OnSubTreeLoad", {'NodeId': this._id});
                var _divnode = _goFirstChild(_linode);
                var _input = null;
                for (var i = 0; i < _divnode.childNodes.length; i++)
                    if (_divnode.childNodes[i].nodeName == "INPUT")
                        _input = _divnode.childNodes[i];
                if (_exist(_input))
                {
                    var _data = eval("__=" + _input.value);
                    if (_exist(_data.loading) && _data.loading == 1)
                    {
                        delete _data.loading;
                        _input.value = _json2string(_data);
                        var _plusminus = this._getPart("PM");
                        _replaceClass("Plus", "Minus", _plusminus);
                        _tree._saveExpandCollapseCookie(this._id, 1);
                        _tree._handleEvent("OnExpand", {'NodeId': this._id});
                        _tree.rECSFC();
                    }
                }
            },
            addChildNode: function(_nodeid, _text, _image)
            {
                var _li = _obj(this._id);
                var _ul = _goNextSibling(_goFirstChild(_li));
                if (!_exist(_ul))
                {
                    _ul = _newNode("ul", _li);
                    _setClass(_ul, "ktvUL")
                }
                var _linode = _newNode("li", _ul);
                _linode.id = _nodeid;
                _setClass(_linode, "ktvLI");
                var _div = _newNode("div", _linode);
                _setClass(_div, "ktvBot");
                if (_exist(_image))
                {
                    var _img = _newNode("img", _div);
                    _setClass(_img, "ktvImage");
                    _img.src = _image;
                    _img.alt = "";
                }
                _text = (_exist(_text)) ? _text : "";
                var _spantext = _newNode("span", _div);
                _setClass(_spantext, "ktvText");
                _spantext.innerHTML = _text;
                _addEvent(_spantext, "click", _spantextClick, false);
                _addEvent(_spantext, "mouseover", _spantextMouseOver, false);
                _addEvent(_spantext, "mouseout", _spantextMouseOut, false);
                _addEvent(_spantext, "mousedown", _dragMouseDown, false);
                _addEvent(_spantext, "mouseup", _dropMouseUp, false);
                this._sort();
                return this;
            },
            _removeNode: function(_nodeid)
            {
                (new TreeNode(_nodeid)).unselect();
                var _rnode = _obj(_nodeid);
                var _ul = _goParentNode(_rnode);
                this.getTree()._removeEvent(_rnode);
                _ul.removeChild(_rnode);
                this._sort();
            },
            removeAllChildren: function()
            {
                var _linode = _obj(this._id);
                var _ul = _goNextSibling(_goFirstChild(_linode));
                if (_exist(_ul))
                {
                    this.getTree()._removeEvent(_ul);
                    _linode.removeChild(_ul);
                    this._sort();
                }
            },
            _sort: function(_type)
            {
                var _list = this.getChildIds();
                for (var i = 0; i < _list.length; i++)
                {
                    var _linode = _obj(_list[i]);
                    try
                    {
                        var _divnode = _goFirstChild(_linode);
                    }
                    catch (ex)
                    {
                    }
                    _removeClass(_linode, "ktvFirst");
                    _removeClass(_linode, "ktvLast");
                    _replaceClass("ktvTop", "ktvMid", _divnode);
                    _replaceClass("ktvBot", "ktvMid", _divnode);
                    if (i == 0)
                    {
                        _addClass(_linode, "ktvFirst");
                        _replaceClass("ktvMid", "ktvTop", _divnode);
                    }
                    if (i == _list.length - 1)
                    {
                        _addClass(_linode, "ktvLast");
                        _replaceClass("ktvMid", "ktvBot", _divnode);
                        _replaceClass("ktvTop", "ktvBot", _divnode);
                    }
                }
                var _spanplusminus = this._getPart("PM");
                if (_list.length == 0)
                {
                    if (_exist(_spanplusminus))
                    {
                        _removeEvent(_spanplusminus, "click", _tooglePlusMinus, false);
                        _goParentNode(_spanplusminus).removeChild(_spanplusminus);
                    }
                    var _linode = _obj(this._id);
                    var _ul = _goNextSibling(_goFirstChild(_linode));
                    if (_exist(_ul))
                        _linode.removeChild(_ul);
                }
                else
                {
                    if (!_exist(_spanplusminus))
                    {
                        var _divnode = _goFirstChild(_obj(this._id));
                        var _ul = _goNextSibling(_divnode);
                        _spanplusminus = _newNode("span", _divnode);
                        _divnode.insertBefore(_spanplusminus, _goFirstChild(_divnode));
                        _setClass(_spanplusminus, "ktvPM ktv" + (_getDisplay(_ul) ? "Minus" : "Plus"));
                        _addEvent(_spanplusminus, "click", _tooglePlusMinus, false);
                    }
                }
            },
            isExpanded: function()
            {
                return _exist(this._getPart("Minus"));
            },
            isSelected: function()
            {
                var _divnode = _goFirstChild(_obj(this._id));
                return (_getClass(_divnode).indexOf("Selected") > 0)
            },
            startEdit: function(_value)
            {
                if (!this.getTree()._handleEvent("OnBeforeStartEdit", {'NodeId': this._id}))
                    return;
                var _text = this._getPart("Text");
                _divnode = _goFirstChild(_obj(this._id));
                _setDisplay(_text, 0);
                var _input = _newNode("input", _divnode);
                _addEvent(_input, "blur", _editInputBlur, false);
                _addEvent(_input, "keypress", _editInputKeyPress, false);
                _setClass(_input, "ktvEdit");
                _input.value = _exist(_value) ? _value : _text.innerHTML;
                _input.focus();
                _input.select();
                this.getTree()._handleEvent("OnStartEdit", {'NodeId': this._id});
                return this;
            },
            endEdit: function(_update)
            {
                if (!this.getTree()._handleEvent("OnBeforeEndEdit", {'NodeId': this._id}))
                    return;
                var _input = this._getPart("Edit");
                var _text = this._getPart("Text");
                _removeEvent(_input, "blur", _editInputBlur, false);
                _removeEvent(_input, "keypress", _editInputKeyPress, false);
                if (!_exist(_update))
                    _update = true;
                if (_update)
                    _text.innerHTML = _input.value;
                _text.style.display = "";
                _goParentNode(_input).removeChild(_input);
                this.getTree()._handleEvent("OnEndEdit", {'NodeId': this._id});
                return this;
            },
            _getPart: function(_class)
            {
                var _linode = _obj(this._id);
                var _part = _getChildByClass(_goFirstChild(_linode), "ktv" + _class);
                return _part;
            },
            _handleSpanTextClick: function(_e)
            {
                var _tree = this.getTree();
                if (_tree._selectEnable)
                {
                    var _selected = this.isSelected();
                    var _state = _tree._getClientState();
                    var _dlist = " " + _state.selectDisableIds.join(" ");
                    if (_dlist.indexOf(" " + this._id) < 0)
                    {
                        if (!_isCtrl || !_tree._multipleSelectEnable)
                        {
                            _tree.unselectAll();
                        }
                        this.select();
                    }
                    if (_selected && _tree._editNodeEnable)
                    {
                        var _state = _tree._getClientState();
                        var _sIds = " " + _state.editDisableIds.join(" ");
                        if (_sIds.indexOf(" " + this._id) < 0)
                        {
                            this.startEdit();
                        }
                    }
                }
            },
            _handleEditInputEnd: function(_e, _update)
            {
                this.endEdit(_update);
            },
            _handlePlusMinusClick: function(_e)
            {
                if (this.isExpanded())
                    this.collapse();
                else
                    this.expand();
            },
            _handleSpanTextMouseOver: function(_e)
            {
                var _divnode = _goFirstChild(_obj(this._id));
                _addClass(_divnode, "ktvOver");
                if (_bDrag && this._handleAllowedDrop())
                {
                    _addClass(_divnode, "ktvDrop");
                }
            },
            _handleSpanTextMouseOut: function(_e)
            {
                var _divnode = _goFirstChild(_obj(this._id));
                _removeClass(_divnode, "ktvOver");
                if (_bDrag && this._handleAllowedDrop())
                {
                    _removeClass(_divnode, "ktvDrop");
                }
            },
            _handleAllowedDrop: function()
            {
                var _tree = this.getTree();
                var _list = " " + _tree._getClientState().dropDisableIds.join(" ");
                return (_tree._dragAndDropEnable && _list.indexOf(" " + this._id) < 0);
            },
            _handleDropMouseUp: function(_e)
            {
                if (_bDrag && this._handleAllowedDrop())
                {
                    var _divnode = _goFirstChild(_obj(this._id));
                    _removeClass(_divnode, "ktvDrop");
                    if (!this.getTree()._handleEvent("OnBeforeDrop", {'NodeId': this._id, 'DragNodeId': _dragid}))
                        return;
                    var _succeed = false;
                    if (this._id != _dragid)
                    {
                        _succeed = (new TreeNode(_dragid)).attachTo(this._id);
                    }
                    this.getTree()._handleEvent("OnDrop", {'NodeId': this._id, 'DragNodeId': _dragid, 'Succeed': _succeed});
                }
            },
            _handleAllowedDrag: function()
            {
                var _tree = this.getTree();
                var _list = " " + _tree._getClientState().dragDisableIds.join(" ");
                return (_tree._dragAndDropEnable && _list.indexOf(" " + this._id) < 0);
            },
            _handleDragStart: function(_e)
            {
                var _divnode = _goFirstChild(_obj(this._id));
                var _div = _divnode.cloneNode(true);
                var _dumpPM = _getChildByClass(_div, "ktvPM");
                if (_exist(_dumpPM))
                    _div.removeChild(_dumpPM);
                var _dragdiv = _newNode("div", document.body);
                _dragdiv.id = "__" + this._id;
                var _treeclass = _getClass(_obj(this.getTree()._id));
                _setClass(_dragdiv, _treeclass)
                _addClass(_div, "ktvDrag");
                _dragdiv.style.position = "absolute";
                _dragdiv.appendChild(_div);
                var _mpos = _mouseXY(_e);
                _dragdiv.style.top = _mpos._y + "px";
                _dragdiv.style.left = (_mpos._x + 5) + "px";
                this.getTree()._handleEvent("OnDrag", {'NodeId': this._id});
            },
            _handleDragging: function(_e)
            {
                var _dragdiv = _obj("__" + this._id);
                var _mpos = _mouseXY(_e);
                _dragdiv.style.top = _mpos._y + "px";
                _dragdiv.style.left = (_mpos._x + 5) + "px";
            },
            _handleDragEnd: function(_e)
            {
                var _dragdiv = _obj("__" + this._id);
                document.body.removeChild(_dragdiv);
            }
        }
function KoolTreeView(_id, _singleExpand, _selectEnable, _multipleSelectEnable, _dragAndDropEnable, _editNodeEnable, _keepState, _keepStateHours, _clientstate)
{
    this._id = _id;
    this._multipleSelectEnable = _multipleSelectEnable;
    this._selectEnable = _selectEnable;
    this._dragAndDropEnable = _dragAndDropEnable;
    this._editNodeEnable = _editNodeEnable;
    this._singleExpand = _singleExpand;
    this._keepState = _keepState.toLowerCase();
    this._keepStateHours = _keepStateHours;
    this._eventhandles = new Array();
    _obj(_id + ".clientState").value = _clientstate;
    this._init();
}
KoolTreeView.prototype =
        {
            getSelectedIds: function()
            {
                var _clientstate = this._getClientState();
                return (_exist(_clientstate.selectedIds)) ? _clientstate.selectedIds : (new Array());
            },
            unselectAll: function()
            {
                var _list = this.getSelectedIds();
                for (var i = 0; i < _list.length; i++)
                    (new TreeNode(_list[i])).unselect();
                return this;
            },
            removeNode: function(_nodeid)
            {
                var _parentnode = this.getNode(this.getNode(_nodeid).getParentId());
                _parentnode._removeNode(_nodeid);
                return this;
            },
            getNode: function(_nodeid)
            {
                return new TreeNode(_nodeid);
            },
            expandAll: function()
            {
                this._handleEvent("OnBeforeExpandAll", {'NodeId': this._id + '.root'});
                var _ulroot = _obj(this._id + ".root");
                var _ullist = _ulroot.getElementsByTagName("ul");
                for (var i = 0; i < _ullist.length; i++)
                    if (_getClass(_ullist[i]).indexOf("ktvUL") > -1)
                    {
                        _setDisplay(_ullist[i], 1);
                        var _divnode = _goFirstChild(_goParentNode(_ullist[i]));
                        var _plusminus = _getChildByClass(_divnode, "ktvPM");
                        _replaceClass("Plus", "Minus", _plusminus);
                    }
                this._handleEvent("OnExpandAll", {'NodeId': this._id + '.root'});
                return this;
            },
            deepExpandAll: function() {
                this._handleEvent("OnBeforeDeepExpandAll", {'NodeId': this._id + '.root'});
                this.getNode(this._id + ".root").deepExpand();
                this._handleEvent("OnDeepExpandAll", {'NodeId': this._id + '.root'});
                return this;
            },
            collapseAll: function()
            {
                this._handleEvent("OnBeforeCollapseAll", {'NodeId': this._id + '.root'});
                this._collapseAll(new Array()); //Close all no exception
                this._handleEvent("OnCollapseAll", {'NodeId': this._id + '.root'});
                return this;
            },
            deepCollapseAll: function() {
                this._handleEvent("OnBeforeDeepCollapseAll", {'NodeId': this._id + '.root'});
                this.getNode(this._id + ".root").deepCollapse();
                this._handleEvent("OnDeepCollapseAll", {'NodeId': this._id + '.root'});
                return this;
            },
            _collapseAll: function(_exceptionlist)
            {
                var _strexcep = "";
                if (_exist(_exceptionlist))
                    _strexcep = _exceptionlist.join(" ");
                var _ulroot = _obj(this._id + ".root");
                var _ullist = _ulroot.getElementsByTagName("ul");
                for (var i = 0; i < _ullist.length; i++)
                {
                    var _nodeid = _goParentNode(_ullist[i]).id;
                    if (_getClass(_ullist[i]).indexOf("ktvUL") > -1 && _strexcep.indexOf(_nodeid) < 0)
                    {
                        _setDisplay(_ullist[i], 0);
                        var _divnode = _goFirstChild(_goParentNode(_ullist[i]));
                        var _plusminus = _getChildByClass(_divnode, "ktvPM");
                        _replaceClass("Minus", "Plus", _plusminus);
                    }
                }
            },
            _getClientState: function()
            {
                var _csinput = _obj(this._id + ".clientState");
                var _clientstate = eval("__=" + _csinput.value);
                return _clientstate;
            },
            _saveClientState: function(_clientstate)
            {
                var _csinput = _obj(this._id + ".clientState");
                _csinput.value = _json2string(_clientstate);
            },
            OSTLD: function(_nodeid, _ct)
            {
                (new TreeNode(_nodeid))._handleSubTreeLoad(_ct);
            },
            _addEvent: function(_range)
            {
                var _linodes = _range.getElementsByTagName("li");
                for (var i = 0; i < _linodes.length; i++)
                    if (_getClass(_linodes[i]).indexOf("ktvLI") != -1)
                    {
                        _divnode = _goFirstChild(_linodes[i]);//Go to div node
                        _spanplusminus = _getChildByClass(_divnode, "ktvPM");
                        if (_exist(_spanplusminus))
                            _addEvent(_spanplusminus, "click", _tooglePlusMinus, false);
                        _spantext = _getChildByClass(_divnode, "ktvText");
                        _addEvent(_spantext, "click", _spantextClick, false);
                        _addEvent(_spantext, "mouseover", _spantextMouseOver, false);
                        _addEvent(_spantext, "mouseout", _spantextMouseOut, false);
                        _addEvent(_spantext, "mousedown", _dragMouseDown, false);
                        _addEvent(_spantext, "mouseup", _dropMouseUp, false);
                    }
            },
            _removeEvent: function(_range)
            {
                var _linodes = _range.getElementsByTagName("li");
                for (var i = 0; i < _linodes.length; i++)
                    if (_getClass(_linodes[i]).indexOf("ktvLI") != -1)
                    {
                        _divnode = _goFirstChild(_linodes[i]);//Go to div node
                        _spanplusminus = _getChildByClass(_divnode, "ktvPM");
                        if (_exist(_spanplusminus))
                            _removeEvent(_spanplusminus, "click", _tooglePlusMinus, false);
                        _spantext = _getChildByClass(_divnode, "ktvText");
                        _removeEvent(_spantext, "click", _spantextClick, false);
                        _removeEvent(_spantext, "mouseover", _spantextMouseOver, false);
                        _removeEvent(_spantext, "mouseout", _spantextMouseOut, false);
                        _removeEvent(_spantext, "mousedown", _dragMouseDown, false);
                        _removeEvent(_spantext, "mouseup", _dropMouseUp, false);
                    }
            },
            _init: function()
            {
                var _tree = document.getElementById(this._id);
                _tree.onselectstart = _selectStart;
                this._addEvent(_tree);
                setTimeout(this._id + ".rECSFC()", 0);
            },
            rECSFC: function() //recoverExpandCollapseStateFromCookie
            {
                var _openclose_cookiename = "";
                switch (this._keepState)
                {
                    case "onpage":
                        var _questionmark = window.location.href.indexOf("?");
                        _openclose_cookiename = (_questionmark < 0) ? window.location.href : window.location.href.substring(0, _questionmark) + "_" + this._id + "_opcl";
                        break;
                    case "crosspage":
                        _openclose_cookiename = this._id + "_opcl";
                        break;
                    case "none":
                    default:
                        return;
                        break;
                }
                var _text = _Cookies[_openclose_cookiename];
                _text = _exist(_text) ? _text : "{}";
                var _opencloselist = eval("__=" + _text);
                var _linodes = _obj(this._id).getElementsByTagName("li");
                for (var i = 0; i < _linodes.length; i++)
                    if (_getClass(_linodes[i]).indexOf("ktvLI") != -1)
                    {
                        if (_exist(_opencloselist[_linodes[i].id]))
                        {
                            var _node = this.getNode(_linodes[i].id);
                            if (_opencloselist[_node._id] == 1 && !_node.isExpanded())
                            {
                                _node.expand();
                            }
                            else if (_opencloselist[_node._id] == 0 && _node.isExpanded())
                            {
                                _node.collapse();
                            }
                        }
                    }
            },
            _saveExpandCollapseCookie: function(_nodeid, _open)
            {
                var _openclose_cookiename = "";
                switch (this._keepState)
                {
                    case "onpage":
                        var _questionmark = window.location.href.indexOf("?");
                        _openclose_cookiename = ((_questionmark < 0) ? window.location.href : window.location.href.substring(0, _questionmark)) + "_" + this._id + "_opcl";
                        break;
                    case "crosspage":
                        _openclose_cookiename = this._id + "_opcl";
                        break;
                    case "none":
                    default:
                        return;
                        break;
                }
                var _text = _Cookies[_openclose_cookiename];
                _text = _exist(_text) ? _text : "{}";
                var _opencloselist = eval("__=" + _text);
                _opencloselist[_nodeid] = _open;
                _Cookies._create(_openclose_cookiename, _json2string(_opencloselist), this._keepStateHours);
            },
            registerEvent: function(_name, _handle)
            {
                this._eventhandles[_name] = _handle;
            },
            _handleEvent: function(_name, _arg)
            {
                return (_exist(this._eventhandles[_name])) ? this._eventhandles[_name](this, _arg) : true;
            }
        }
function _tooglePlusMinus(_e)
{
    (new TreeNode(_goParentNode(this, 2).id))._handlePlusMinusClick(_e);
}
function _spantextClick(_e)
{
    (new TreeNode(_goParentNode(this, 2).id))._handleSpanTextClick(_e);
}
function _spantextMouseOver(_e)
{
    (new TreeNode(_goParentNode(this, 2).id))._handleSpanTextMouseOver(_e);
}
function _spantextMouseOut(_e)
{
    (new TreeNode(_goParentNode(this, 2).id))._handleSpanTextMouseOut(_e);
}
function _editInputBlur(_e)
{
    (new TreeNode(_goParentNode(this, 2).id))._handleEditInputEnd(_e);
}
/*
 function _editInputKeyUp(_e)
 {
 var _key = (window.event)?event.keyCode:_e.keyCode;
 if (_key==13 || _key==27)
 {
 (new TreeNode(_goParentNode(this,2).id))._handleEditInputEnd(_e,(_key==13));
 }
 }
 */
function _editInputKeyPress(_e)
{
    var _key = (window.event) ? event.keyCode : _e.keyCode;
    if (_key == 13 || _key == 27)
    {
        (new TreeNode(_goParentNode(this, 2).id))._handleEditInputEnd(_e, (_key == 13));
        if (_key == 13)
        {
            if (_e.stopPropagation)
            {
                _e.stopPropagation();
                _e.preventDefault();
            }
            else
            {
                event.cancelBubble = true;
                event.returnValue = false;
            }
            return false;
        }
    }
}
var _treeDragStatus = 0, _mxy, _bDrag, _dragid;
var _bselect = true;//For IE
function _dragMouseDown(_e)
{
    if ((new TreeNode(_goParentNode(this, 2).id))._handleAllowedDrag(_e))
    {
        if (_e.preventDefault)
            _e.preventDefault();
        _bselect = false;//For IE
        _dragid = _goParentNode(this, 2).id;
        _mxy = _mouseXY(_e);
        _treeDragStatus = 1;
        _bDrag = false;
        _addEvent(document, "mousemove", _dragMouseMove, false);
        _addEvent(document, "mouseup", _dragMouseUp, false);
        if (_e.stopPropagation != null)
            _e.stopPropagation();
        else
            event.cancelBubble = true;
    }
}
function _dragMouseMove(_e)
{
    if (_treeDragStatus == 1 || _treeDragStatus == 2)
    {
        if (_bDrag)
        {
            (new TreeNode(_dragid))._handleDragging(_e);
        }
        else
        {
            var _mpos = _mouseXY(_e);
            if (Math.abs(_mpos._x - _mxy._x) > 10 || Math.abs(_mpos._y - _mxy._y) > 10)
            {
                _bDrag = true;
                (new TreeNode(_dragid))._handleDragStart(_e);
            }
        }
    }
    _treeDragStatus = 2;
}
function _dragMouseUp(_e)
{
    if (_treeDragStatus == 1)
    {
    }
    if (_treeDragStatus == 2)
    {
        if (_bDrag)
        {
            (new TreeNode(_dragid))._handleDragEnd(_e);
            _bDrag = false;
        }
    }
    _removeEvent(document, "mousemove", _dragMouseMove, false);
    _removeEvent(document, "mouseup", _dragMouseUp, false);
    _bselect = true;
}
function _dropMouseUp(_e)
{
    (new TreeNode(_goParentNode(this, 2).id))._handleDropMouseUp(_e);
}
function _selectStart()
{
    if (_isCtrl || !_bselect)
        return false;
}
if (typeof (__KTVInits) != 'undefined' && _exist(__KTVInits))
{
    for (var i = 0; i < __KTVInits.length; i++)
    {
        __KTVInits[i]();
    }
}
KoolPHP = {
    recursiveMerge: function(o1, o2) {
        var t1 = typeof o1,
                t2 = typeof o2;
        if (o1 === null || t1 === 'undefined')
            o1 = o2;
        else if (o1 instanceof Array &&
                o2 instanceof Array) {
            for (var i = 0; i < o2.length; i += 1)
                o1[i] = KoolPHP.recursiveMerge(o1[i], o2[i]);
        }
        else if (t2 === 'object' &&
                t1 === 'object') {
            for (var n in o2)
                if (o2.hasOwnProperty(n)) {
                    var t2n = typeof o2[n];
                    if (t2n !== 'object' ||
                            o2[n] === null)
                        o1[n] = o2[n];
                    else
                        o1[n] = KoolPHP.recursiveMerge(o1[n], o2[n]);
                }
        }
        else
            o1 = o2;
        return o1;
    },
    _koolObject: {
        _merge: function(_object) {
            for (var _name in _object)
                if (_object.hasOwnProperty(_name))
                    this[_name] = _object[_name];
            return this;
        },
        _alert: function(_property) {
            alert(this[_property]);
        },
        _koolClass: "koolObject",
        _id: null,
        _setId: function(_id) {
            this._id = _id;
        },
        _getId: function() {
            return this._id;
        },
        _selfIdentify: function() {
            alert(this._id + " " + this._koolClass);
        },
        _loadInput: function(str) {
            var _input = KoolPHP._domObj(this._getId() + str);
            return JSON.parse(_input.value);
        },
        _saveInput: function(str, value) {
            var _input = KoolPHP._domObj(this._getId() + str);
            _input.value = JSON.stringify(value);
        },
        _loadViewstate: function() {
            var _viewstate = this._loadInput("_viewstate");
            if (KoolPHP._isEmpty(_viewstate))
                _viewstate = {};
            return _viewstate;
        },
        _saveViewstate: function(_viewstate) {
            this._saveInput("_viewstate", _viewstate);
        }
    },
    htmlProperty: {
        myName: '',
        myValue: '',
        name: function(n) {
            this.myName = n;
            return this;
        },
        value: function(v) {
            this.myValue = v;
            return this;
        }
    },
    htmlElement: {
        template: '<{tag} {properties} {styles}>{content}</{tag}>',
        myTag: '',
        myContent: '',
        myInnerHtml: '',
        myText: '',
        initProperties: function() {
            this.myProperties = {};
            this.myStyles = {};
            this.myChildElements = [];
        },
        tag: function(s) {
            this.myTag = s;
            return this;
        },
        content: function(s) {
            this.myContent = s;
            return this;
        },
        textContent: function(s) {
            this.content(s);
        },
        text: function(s) {
            this.myText = s;
            return this;
        },
        setInnerHtml: function(s) {
            this.myInnerHtml = s;
            return this;
        },
        addProperty: function(n, v) {
            if (!this.myProperties[n])
                this.myProperties[n] = [];
            this.myProperties[n].push(v);
            return this;
        },
        setProperty: function(n, v) {
            this.myProperties[n] = [v];
            return this;
        },
        removeProperty: function(n) {
            this.myProperties[n] = [];
            return this;
        },
        addStyle: function(n, v) {
            if (!this.myStyles[n])
                this.myStyles[n] = [];
            this.myStyles[n].push(v);
            return this;
        },
        setStyle: function(n, v) {
            this.myStyles[n] = [v];
            return this;
        },
        removeStyle: function(n) {
            this.myStyles[n] = [];
            return this;
        },
        addChildElement: function(v) {
            this.myChildElements.push(v);
            return this;
        },
        getPropertiesString: function() {
            var _properties = '';
            for (var p in this.myProperties)
                if (this.myProperties.hasOwnProperty(p))
                {
                    var s = '';
                    for (var i = 0; i < this.myProperties[p].length; i += 1) {
                        s += ' ' + this.myProperties[p][i];
                    }
                    if (s !== '')
                        _properties += p + '="' + s.trim() + '"';
                }
            return _properties.trim();
        },
        getStylesString: function() {
            var _styles = '';
            for (var p in this.myStyles)
                if (this.myStyles.hasOwnProperty(p))
                {
                    var s = '';
                    for (var i = 0; i < this.myStyles[p].length; i += 1) {
                        s += ' ' + this.myStyles[p][i];
                    }
                    if (s !== '')
                        _styles += p + '="' + s.trim() + '"';
                }
            return _styles.trim();
        },
        getHtml: function() {
            var _html = this.template;
            _html = _html.replace('{tag}', this.myTag);
            var s = this.getPropertiesString();
            _html = _html.replace('{properties}', s);
            _html = _html.replace('{styles}', this.getStylesString());
            var c = this.myContent;
            for (var i = 0; i < this.myChildElements.length; i += 1) {
                var s = this.myChildElements[i].getHtml();
                c += s;
            }
            _html = _html.replace('{content}', c);
            return _html;
        },
        getDomElement: function() {
            if (KoolPHP._notDefined(this.domElement))
                this.domElement = this.createDomElement();
            return this.domElement;
        },
        createDomElement: function() {
            var dom = document.createElement(this.myTag);
            for (var p in this.myProperties)
                if (this.myProperties.hasOwnProperty(p))
                {
                    var v = this.myProperties[p];
                    var s = '';
                    for (var i = 0; i < v.length; i += 1)
                        s += ' ' + v[i];
                    s = s.trim();
                    switch (p) {
                        case 'id':
                            dom.id = s;
                            break;
                        case 'class':
                            dom.className = s;
                            break;
                        default:
                            dom.setAttribute(p, s);
                    }
                }
            for (var p in this.myStyles)
                if (this.myStyles.hasOwnProperty(p))
                {
                    var v = this.myStyles[p];
                    var s = '';
                    for (var i = 0; i < v.length; i += 1)
                        s += ' ' + v[i];
                    dom.style[p] = s;
                }
            if (this.myText)
                dom.text = this.myText;
            if (this.myContent)
                dom.textContent = this.myContent;
            if (this.myInnerHtml)
                dom.innerHTML = this.myInnerHtml;
            for (var i = 0; i < this.myChildElements.length; i += 1)
                dom.appendChild(this.myChildElements[i].createDomElement());
            this.domElement = dom;
            return dom;
        }
    },
    newProperty: function(n, v) {
        var p = KoolPHP._new(KoolPHP.htmlProperty);
        p.name(n).value(v);
        return p;
    },
    newHtmlElement: function(info) {
        info = this.getValue(info, {});
        var properties = info.properties || info.p;
        var styles = info.styles || info.s;
        var t = KoolPHP._new(KoolPHP.htmlElement);
        for (var p in properties)
            if (properties.hasOwnProperty(p)) 
                if (this.notEmpty(properties[p])) {
                    t.addProperty(p, properties[p]);
                }
        for (var s in styles)
            if (styles.hasOwnProperty(s))
                if (this.notEmpty(styles[s]))
                t.addStyle(s, styles[s]);
        if (this.notEmpty(info.textContent))
            t.content(info.textContent);
        if (this.notEmpty(info.innerHtml))
            t.setInnerHtml(info.innerHtml);
        return t;
    },
    newElement: function(info) {
        return this.newHtmlElement(info);
    },
    newTable: function(info) {
        var t = KoolPHP.newHtmlElement(info)
            .tag('table');
        return t;
    },
    newTr: function(info) {
        var t = KoolPHP.newHtmlElement(info)
                .tag('tr');
        return t;
    },
    newTh: function(info) {
        var t = KoolPHP.newHtmlElement(info)
                .tag('th');
        return t;
    },
    newTd: function(info) {
        var t = KoolPHP.newHtmlElement(info)
                .tag('td');
        return t;
    },
    newTextInput: function(info) {
        var t = KoolPHP.newHtmlElement(info)
            .tag('input')
            .addProperty('type', 'text');
        return t;
    },
    newSpan: function(info) {
        var t = KoolPHP.newHtmlElement(info)
                .tag('span');
        return t;
    },
    newSelect: function(info) {
        var t = KoolPHP.newHtmlElement(info)
            .tag('select');
        if (this.defined(info.options) &&
            info.options instanceof Array) {
            var options = info.options;
            var value = this.getValue(info.value, '');
            for (var n = 0; n < options.length; n += 1) {
                info = {
                    properties: {
                        selected: value === options[n]
                    },
                    textContent: options[n]
                };
                var option = KoolPHP.newOption(info);
                t.addChildElement(option);
            }
        }
        return t;
    },
    newOption: function(info) {
        var t = KoolPHP.newHtmlElement(info)
                .tag('option');
        return t;
    },
    newCheckbox: function(info) {
        var t = KoolPHP.newHtmlElement(info)
                .tag('input')
                .addProperty('type', 'checkbox');
        return t;
    },
    newButton: function(info) {
        var t = KoolPHP.newHtmlElement(info)
            .tag('input')
            .addProperty('type', 'button')
            .addStyle('margin', '2px 2px 2px 2px')
            .addStyle('padding', '1px 6px 1px 6px')
        ;
        return t;
    },
    newDiv: function(info) {
        var t = KoolPHP.newHtmlElement(info)
            .tag('div');
        return t;
    },
    newLink: function(info) {
        var t = KoolPHP.newHtmlElement(info)
            .tag('link');
        return t;
    },
    newLinkCSS: function(info) {
        var t = KoolPHP.newHtmlElement(info)
            .tag('link')
            .addProperty('rel', 'stylesheet');
        return t;
    },
    _new: function(_object) {
        var _F = function() {
            for (var p in this) {
                if (!this.hasOwnProperty(p)) {
                    if (this[p] instanceof Array) {
                        this[p] = KoolPHP._cloneArray(this[p]);
                    }
                    else if (typeof this[p] === 'object')
                        this[p] = KoolPHP._cloneObject(this[p]);
                    else
                        this[p] = this[p];
                    if (p === 'initProperties' &&
                            typeof this[p] === 'function') {
                        this.initProperties();
                    }
                }
            }
        };
        _F.prototype = _object;
        return new _F();
    },
    _newObject: function(_baseObject) {
        var _newObject = this._new(this._koolObject);
        _newObject._merge(_baseObject);
        return _newObject;
    },
    _getClassedParentId: function(e, _className) {
        while (
                e &&
                e.className.indexOf(_className) < 0
                )
        {
            e = e.parentNode;
        }
        if (e)
            return e.id;
        else
            return null;
    }, // </editor-fold>
    _utf8_decode: function(str_data) {
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
    }, // </editor-fold>
    _base64_decode: function(data) {
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
    }, // </editor-fold>
    _addEventHandler: function(elem, eventType, handler) {
        if (elem.addEventListener)
            elem.addEventListener(eventType, handler, false);
        else if (elem.attachEvent)
            elem.attachEvent('on' + eventType, handler);
    },
    _domObj: function(_id) {
        return document.getElementById(_id);
    },
    _isEmpty: function(obj) {
        if (typeof obj === 'number' ||
            typeof obj === 'boolean' ||
            typeof obj === 'function')
            return false;
        if (this._notDefined(obj))
            return true;
        if (obj.length > 0)
            return false;
        if (obj.length === 0)
            return true;
        for (var key in obj) {
            if (hasOwnProperty.call(obj, key))
                return false;
        }
        return true;
    }, // </editor-fold>
    _defined: function(_object) {
        return (_object !== null && typeof _object !== 'undefined');
    },
    _notDefined: function(_object) {
        return (_object === null || typeof _object === 'undefined');
    },
    _cloneArray: function(_arr) {
        return _arr.slice(0);
    },
    _cloneObject: function(_object) {
        return JSON.parse(JSON.stringify(_object));
    },
    newObject: function(arg) {
        return this._newObject(arg);
    },
    addEventHandler: function(elem, eventType, handler) {
        this._addEventHandler(elem, eventType, handler);
    },
    domObj: function(_id) {
        return document.getElementById(_id);
    },
    isEmpty: function(obj) {
        return this._isEmpty(obj);
    },
    notEmpty: function(obj) {
        return !this._isEmpty(obj);
    },
    defined: function(_object) {
        return (_object !== null && typeof _object !== 'undefined');
    },
    notDefined: function(_object) {
        return (_object === null || typeof _object === 'undefined');
    },
    cloneArray: function(_arr) {
        return _arr.slice(0);
    },
    cloneObject: function(_object) {
        return JSON.parse(JSON.stringify(_object));
    },
    getValue: function(value, defaultValue) {
        if (KoolPHP._defined(value))
            return value;
        else
            return defaultValue;
    }
};
KoolTreeGridJS = {
    simpleTree: {
        isVisible: function() {
            var tree = this;
            var visible = true;
            while (tree.parent) {
                tree = tree.parent;
                if (KoolPHP._notDefined(tree.meta.expand))
                    tree.meta.expand = true;
                visible = visible && tree.meta.expand;
                if (!visible)
                    return false;
            }
            return true;
        },
        children: []
    },
    _baseTreeGrid: {
        initProperties: function() {
            this.listTreeData = {};
            this.listSimpleTree = {};
            this.listTreeId = [];
        },
        createSimpleTree: function(treeData) {
            var tree = KoolPHP._new(KoolTreeGridJS.simpleTree);
            tree.id = treeData.id;
            tree.row = KoolPHP._cloneObject(treeData.row);
            tree.meta = KoolPHP._cloneObject(treeData.meta);
            var id = (tree.id === 'root') ? this.ktvRoot : tree.id;
            this.listTreeData[id] = treeData;
            this.listSimpleTree[id] = tree;
            this.listTreeId.push(id);
            for (var i = 0; i < treeData.children.length; i += 1) {
                var childTree = this.createSimpleTree(treeData.children[i]);
                childTree.parent = tree;
                tree.children.push(childTree);
            }
            return tree;
        },
        loadData: function() {
            this._viewstate = this._loadViewstate();
            this.data = this._loadInput("_data");
            this.setting = this.data.setting;
            var _st = this.setting,
                _ktvId = _st.ktvId,
                _ktvRoot = _ktvId + '.root'
            ;
            this.ktvRoot = _ktvRoot;
        },
        renderGrid: function() {
            var K = KoolPHP;
            var st = this.setting,
                    ktgId = this._getId();
            var _treeGridDiv = K.domObj(st.divId);
            var info = {};
            var table, tr, th, td, columns, content,
                select, chk, input,
                row, meta, span, id, tree;
            columns = this.setting.columns;
            info = {
                properties: {
                    id: ktgId + '_table',
                    class: 'ktg_table'
                },
                styles: {
                   width: st.width
               }
            };
            table = K.newTable(info);
            info = {
                properties: {
                    id: ktgId + '_th',
                    class: 'ktg_header ktvText'
                }
            };
            tr = K.newTr(info);
            for (var i = 0; i < columns.length; i += 1) {
                if (columns[i].visible) {
                    info = {
                        textContent: K.getValue(
                            columns[i].headerText, columns[i].field),
                        styles: {
                            width: columns[i].width
                        }
                    };
                    th = K.newTh(info);
                    tr.addChildElement(th);
                }
            }
            table.addChildElement(tr);
            for (var j = 0; j < this.listTreeId.length; j += 1) {
                id = this.listTreeId[j];
                tree = this.listSimpleTree[id];
                row = tree.row;
                meta = tree.meta ? tree.meta : {};
                info = {
                    properties: {
                        id: ktgId + '_tr_' + id,
                        class: 'ktvText'
                    }
                };
                tr = K.newTr(info);
                if (j === 0) {
                    content = this.data.treeViewHtml;
                    info = {
                        properties: {
                            id: this._getId() + '_td_ktv',
                            class: 'td_ktv'
                        },
                        innerHtml: content
                    };
                    td = K.newTd(info);
                    tr.addChildElement(td);
                }
                for (var i = 1; i < columns.length; i += 1) {
                    if (columns[i].visible) {
                        var field = columns[i].field;
                        var value = row[field];
                        if (meta.rowEdit === true ||
                            meta.rowEdit && meta.rowEdit[field]) {
                            var inputType = 'text';
                            if (meta.rowEdit[field])
                                inputType = meta.rowEdit[field].type;
                            if (inputType === 'select') {
                                info = {
                                    properties: {
                                        class: 'ktvTextSelect'
                                    },
                                    options: meta.rowEdit[field].options,
                                    value: value
                                };
                                select = K.newSelect(info);
                                td = K.newTd()
                                    .addChildElement(select);
                            }
                            else if (inputType === 'checkbox') {
                                if (typeof value === 'string')
                                    value = (value.toLowerCase() === 'true');
                                info = {
                                    properties: {
                                        class: 'ktvTextCheckbox',
                                        checked: value ? 'checked' : ''
                                    }
                                };
                                chk = K.newCheckbox(info);
                                td = K.newTd()
                                    .addChildElement(chk);
                            }
                            else {
                                info = {
                                    properties: {
                                        value: value,
                                        class: 'ktvTextInput'
                                    },
                                    styles: {
                                        width: '100%'
                                    }
                                };
                                input = K.newTextInput(info);
                                info = {
                                    styles: {
                                        'padding-right': '7px'
                                    }
                                };
                                td = K.newTd(info)
                                    .addChildElement(input)
                                ;
                            }
                            td.addStyle('padding-left', '0px');
                        }
                        else {
                            info = {
                                textContent: value
                            };
                            span = K.newSpan(info);
                            td = K.newTd()
                                .addChildElement(span)
                            ;
                        }
                        tr.addChildElement(td);
                    }
                }
                table.addChildElement(tr);
            }
            _treeGridDiv.appendChild(table.getDomElement());
        },
        render: function() {
            this.initProperties();
            this.loadData();
            this.simpleTree = this.createSimpleTree(this.data.tree);
            this.renderGrid();
        },
        reRender: function() {
            var _st = this.setting;
            var _treeGridDiv = KoolPHP._domObj(_st.divId);
            _treeGridDiv.removeChild(_treeGridDiv.firstChild);
            this.initProperties();
            this.simpleTree = this.createSimpleTree(this.data.tree);
            this.renderGrid();
            this.ktv._init();
            this.init();
        },
        getOriginalTree: function() {
            return KoolPHP._cloneObject(this.data.tree);
        },
        getOriginalTreeData: function() {
            return KoolPHP._cloneObject(this.data.tree);
        },
        setTreeData: function(treeData) {
            this.data.tree = treeData;
            return this;
        },
        getListTree: function(tree, listTree) {
            var id = (tree.id === 'root') ? this.ktvRoot : tree.id;
            listTree[id] = tree;
            for (var i = 0; i < tree.children.length; i += 1) {
                this.getListTree(tree.children[i], listTree);
            }
        },
        getCurrentTree: function() {
            var tree, listTree = {}, columns, child,
                    id, rootTree, tree,
                    tr, td, text,
                    ktgId = this._getId()
                    ;
            rootTree = KoolPHP._cloneObject(this.data.tree);
            this.getListTree(rootTree, listTree);
            for (var i = 0; i < this.listTreeId.length; i += 1) {
                id = this.listTreeId[i];
                tree = listTree[id];
                tr = KoolPHP._domObj(ktgId + '_tr_' + id);
                td = tr.firstChild;
                if (id === this.ktvRoot)
                    td = td.nextSibling;
                columns = this.setting.columns;
                for (var j = 1; j < columns.length; j += 1) {
                    if (columns[j].visible) {
                        child = td.firstChild;
                        if (child.tagName.toLowerCase() === 'span')
                            text = child.textContent;
                        else if (child.tagName.toLowerCase() === 'input') {
                            if (child.type === 'text')
                                text = child.value;
                            else if (child.type === 'checkbox')
                                text = child.checked;
                        }
                        else if (child.tagName.toLowerCase() === 'select')
                            text = child.value;
                        tree.row[columns[j].field] = text;
                        td = td.nextSibling;
                    }
                }
            }
            return rootTree;
        },
        getCurrentTreeData: function() {
            return this.getCurrentTree();
        },
        treeDataToSetting: function(tree, propPrt, valuePrt, typePrt) {
            var setting;
            var type = tree.row[typePrt].trim().toLowerCase();
            if (KoolPHP._isEmpty(tree.children)) {
                setting = tree.row[valuePrt];
                if (type === 'array' || 
                    type === 'number' || 
                    type === 'bool')
                    setting = eval(setting);
            }
            else {
                setting = (type === 'array') ? [] : {};
                for (var i=0; i<tree.children.length; i+=1) {
                    var childTree = tree.children[i];
                    var childSetting = this.treeDataToSetting(childTree, propPrt, valuePrt, typePrt);
                    if (type === 'array')
                        setting[i] = childSetting;
                    else
                        setting[childTree.row[propPrt]] = childSetting;
                }
            }
            return setting; 
        },
        treeDataToPHPArrayStr: function(tree, propPrt, valuePrt, typePrt, level) {
            var 
                s = '', prop, value, t,
                indent = '   ',
                pre = ''
            ;
            t = '';
            for (var j=0; j<level; j+=1)
                t += indent;
            var type = tree.row[typePrt].trim().toLowerCase();
                prop = tree.row[propPrt];
                value = tree.row[valuePrt];
            pre = '"' + prop + '" => ';
            if (KoolPHP._isEmpty(tree.children)) {
                if (
                    type === 'number' || 
                    type === 'bool') {
                    value = eval(value);
                    s += pre + value + ',\n';
                }
                else if (type === 'array')
                    s += pre + value + ',\n';
                else
                    s += pre + '"' + value + '",\n';
            }
            else {
                for (var i=0; i<tree.children.length; i+=1) {
                    var childTree = tree.children[i];
                    var childArray;
                    var childType = childTree.row[typePrt].trim().toLowerCase();
                    childArray = this.treeDataToPHPArrayStr(childTree, propPrt, valuePrt, typePrt, level+1);
                    if (
                        childType==='object'  
                            || (childType==='array' 
                            && !KoolPHP._isEmpty(childTree.children))
                    ) {
                        if (type==='array')
                            childArray = 'array(\n' + childArray + t + '),\n';
                        else
                            childArray = '"' + childTree.row[propPrt] + '" => array(\n' + childArray + t + '),\n';
                    } 
                    s += t;
                    s += childArray;
                }
            }
            if (level===2)
                s = indent + 'array(\n' + s + indent + ')\n';
            return s;
        },
        treeDataToPHPSettingStr: function(tree, propPrt, valuePrt, typePrt, level, pre, index) {
            var 
                s = '', prop, value, t
            ;
            var type = tree.row[typePrt].trim().toLowerCase();
                prop = tree.row[propPrt];
                value = tree.row[valuePrt];
            if (level===2)
                t = pre;
            else if (KoolPHP._defined(index))
                t = pre + '[ ' + index + ' ]';
            else
                t = pre + '[ "' + prop + '" ]';
            if (KoolPHP._isEmpty(tree.children)) {
                if (
                    type === 'number' || 
                    type === 'bool') {
                    value = eval(value);
                    s = t + ' = '+ value + ';\n';
                }
                else if (type === 'array')
                    s = t + ' = '+ value + ';\n';
                else
                    s = t + ' = '+ '"' + value + '";\n';
            }
            else {
                for (var i=0; i<tree.children.length; i+=1) {
                    var childTree = tree.children[i];
                    var childSt;
                    if (type==='array')
                        childSt = this.treeDataToPHPSettingStr(childTree, propPrt, valuePrt, typePrt, level+1, t, i);
                    else
                        childSt = this.treeDataToPHPSettingStr(childTree, propPrt, valuePrt, typePrt, level+1, t);
                    s += childSt;
                }
            }
            return s;
        },
        setStyle: function(style) {
            var st = this.setting;
            var ktgDiv = KoolPHP._domObj(st.divId);
            ktgDiv.className = style + 'KTG';
            this.setNodeProperties();
        },
        setNodeProperties: function() {
            var
                st = this.setting,
                id, row, tree,
                li, tr, td, span, div,
                visible, className, order = 0,
                lev, padLeft, bgPos,
                treeIndent, rootIndent,
                arrVisible = [],
                ktgId = this._getId()
            ;
            for (var i = 0; i < this.listTreeId.length; i += 1) {
                id = this.listTreeId[i];
                tree = this.listSimpleTree[id];
                row = tree.row;
                span = (i === 0) ?
                        KoolPHP._domObj(id + '_text') :
                        KoolPHP._domObj(ktgId + '_' + id + '_text');
                div = span.parentNode.parentNode;
                li = div.parentNode;
                tr = KoolPHP._domObj(ktgId + '_tr_' + id);
                visible = tree.isVisible();
                tr.style.display = visible ? '' : 'none';
                arrVisible.push(visible);
                if (visible) {
                    className = ["ktg_row", "ktg_altRow"];
                    this.setClass(div, className, order);
                    this.setClass(tr, className, order);
                    treeIndent = (KoolPHP._defined(st.treeIndent)) ?
                        st.treeIndent : 12;
                    rootIndent = (KoolPHP._defined(st.rootIndent)) ?
                        st.rootIndent : 20;
                    lev = span.getAttribute("level");
                    padLeft = rootIndent;
                    padLeft += lev <= 1 ? lev * treeIndent : (lev - 1) * treeIndent;
                    bgPos = (padLeft - 20) + "px 0px";
                    div.style.backgroundPosition = bgPos;
                    li.style.backgroundPosition = bgPos;
                    div.style.paddingLeft = padLeft + "px";
                    span.style.height = 'auto';
                    order += 1;
                }
            }
            tr = KoolPHP._domObj(ktgId + "_tr_" + this.ktvRoot);
            td = tr.firstChild;
            td.rowSpan = order;
            for (var i = 0; i < this.listTreeId.length; i += 1)
                if (arrVisible[i]) {
                    id = this.listTreeId[i];
                    span = (i === 0) ?
                            KoolPHP._domObj(id + '_text') :
                            KoolPHP._domObj(ktgId + '_' + id + '_text');
                    tr = KoolPHP._domObj(ktgId + '_tr_' + id);
                    if (id !== this.ktvRoot) {
                        span.style.height = tr.offsetHeight + 'px';
                    }
                    else {
                        td = tr.firstChild.nextSibling;
                        span.style.height = td.offsetHeight - 1 + 'px';
                    }
                }
        },
        removeClass: function(domObj, name) {
            var s = domObj.className;
            var regex = new RegExp('(?:^|\\s)' + name + '(?!\\S)', 'g');
            s = s.replace(regex, '');
            domObj.className = s;
        },
        addClass: function(domObj, name) {
            this.removeClass(domObj, name);
            domObj.className += ' ' + name;
        },
        setClass: function(domObj, names, order) {
            var i, l, r;
            l = names.length;
            for (i = 1; i < l; i += 1) {
                r = (order + i) % l;
                this.removeClass(domObj, names[r]);
            }
            r = order % l;
            this.addClass(domObj, names[r]);
        },
        getNodeCollapse_handle: function(sender, arg)
        {
            var _treeGrid = this;
            return function(sender, arg) {
                _treeGrid.listSimpleTree[arg.NodeId].meta.expand = false;
                _treeGrid.setNodeProperties();
            };
        },
        getNodeExpand_handle: function(sender, arg)
        {
            var _treeGrid = this;
            return function(sender, arg) {
                _treeGrid.listSimpleTree[arg.NodeId].meta.expand = true;
                _treeGrid.setNodeProperties();
            };
        },
        getNodeCollapseAll_handle: function(sender, arg)
        {
            var _treeGrid = this;
            return function(sender, arg) {
                for (var i = 0; i < _treeGrid.listTreeId.length; i += 1) {
                    _treeGrid.listSimpleTree[_treeGrid.listTreeId[i]]
                            .meta.expand = false;
                }
                _treeGrid.setNodeProperties();
            };
        },
        getNodeExpandAll_handle: function(sender, arg)
        {
            var _treeGrid = this;
            return function(sender, arg) {
                for (var i = 0; i < _treeGrid.listTreeId.length; i += 1) {
                    _treeGrid.listSimpleTree[_treeGrid.listTreeId[i]]
                            .meta.expand = true;
                }
                _treeGrid.setNodeProperties();
            };
        },
        init: function() {
            var
                _st = this.setting,
                ktg = this
            ;
            this.ktv = window[_st.ktvId];
            this.ktv.registerEvent("OnExpand", this.getNodeExpand_handle());
            this.ktv.registerEvent("OnCollapse", this.getNodeCollapse_handle());
            this.ktv.registerEvent("OnDeepExpandAll", this.getNodeExpandAll_handle());
            this.ktv.registerEvent("OnDeepCollapseAll", this.getNodeCollapseAll_handle());
            KoolPHP._addEventHandler(window, 'resize', function(event) {
                ktg.setNodeProperties();
            });
            this.setNodeProperties();
        }
    },
    _KTG: {},
    newTreeGrid: function(_id) {
        var _treeGrid = this.getTreeGrid();
        if (!_treeGrid) {
            _treeGrid = KoolPHP._newObject(KoolTreeGridJS._baseTreeGrid);
            _treeGrid._setId(_id);
            this.addTreeGrid(_id, _treeGrid);
        }
        return _treeGrid;
    },
    addTreeGrid: function(_id, _treeGrid) {
        this._KTG[_id] = _treeGrid;
        return this;
    },
    getTreeGrid: function(_id) {
        return this._KTG[_id];
    }
};
