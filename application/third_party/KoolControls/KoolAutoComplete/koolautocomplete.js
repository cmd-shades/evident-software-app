/**
 * @author Nghiem Anh Tuan
 */
function _exist(_theObj) {
  return (_theObj != null)
}
if (!_exist(_identity)) {
  var _identity = 0;
}
function _getIdentity() {
  _identity++;
  return _identity;
}
function _obj(_id) {
  return document.getElementById(_id);
}
function _newNode(_sTag, _oParent) {
  var _oNode = document.createElement(_sTag);
  _oParent.appendChild(_oNode);
  return _oNode;
}
function _goFirstChild(_theObj, _level) {
  if (!_exist(_level))
    _level = 1;
  for (var i = 0; i < _level; i++)
    _theObj = _theObj.firstChild;
  return _theObj;
}
function _goNextSibling(_theObj, _level) {
  if (!_exist(_level))
    _level = 1;
  for (var i = 0; i < _level; i++)
    _theObj = _theObj.nextSibling;
  return _theObj;
}
function _goParentNode(_theObj, _level) {
  if (!_exist(_level))
    _level = 1;
  for (var i = 0; i < _level; i++)
    _theObj = _theObj.parentNode;
  return _theObj;
}
function _setHeight(_theObj, _val) {
  _theObj.style.height = _val + "px";
}
function _setWidth(_theObj, _val) {
  _theObj.style.width = _val + "px";
}
function _getElements(_tag, _class, _parent) {
  _parent = _exist(_parent) ? _parent : document.body;
  var _elements = _parent.getElementsByTagName(_tag);
  var _result = new Array();
  for (var i = 0; i < _elements.length; i++)
    if (_elements[i].className.indexOf(_class) >= 0) {
      _result.push(_elements[i]);
    }
  return _result;
}
function encodeData(s) {
  return encodeURIComponent(s).replace(/\-/g, "%2D").replace(/\./g, "%2E").replace(/\!/g, "%21").replace(/\~/g, "%7E").replace(/\*/g, "%2A").replace(/\'/g, "%27").replace(/\(/g, "%28").replace(/\)/g, "%29");
}
function decodeData(s) {
  try {
    return decodeURIComponent(s.replace(/\%2D/g, "-").replace(/\%2E/g, ".").replace(/\%21/g, "!").replace(/\%7E/g, "~").replace(/\%2A/g, "*").replace(/\%27/g, "'").replace(/\%28/g, "(").replace(/\%29/g, ")"));
  } catch (e) {
  }
  return "";
}
function _setDisplay(_theObj, _val) {
  _theObj.style.display = (_val) ? "block" : "none";
}
function _getDisplay(_theObj) {
  return (_theObj.style.display != "none");
}
function _getClass(_theObj) {
  return _theObj.className;
}
function _setClass(_theObj, _val) {
  _theObj.className = _val;
}
function _replaceClass(_search, _rep, _o) {
  _setClass(_o, _getClass(_o).replace(_search, _rep));// Only the first
}
function _addClass(_theObj, _class) {
  if (_theObj.className.indexOf(_class) < 0) {
    var _listclass = _theObj.className.split(" ");
    _listclass.push(_class);
    _theObj.className = _listclass.join(" ");
  }
}
function _removeClass(_theObj, _class) {
  if (_theObj.className.indexOf(_class) > -1) {
    _replaceClass(_class, "", _theObj)
    var _listclass = _theObj.className.split(" ");
    _theObj.className = _listclass.join(" ");
  }
}
function _addEvent(_ob, _evType, _fn, _useCapture) {
  if (_ob.addEventListener) {
    _ob.addEventListener(_evType, _fn, _useCapture);
    return true;
  } else if (_ob.attachEvent) {
    if (_useCapture) {
      return false;
    } else {
      var _tmp = function () {
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
  } else {
    return false;
  }
}
function _removeEvent(_ob, _evType, _fn, _useCapture) {
  if (_ob.removeEventListener) {
    _ob.removeEventListener(_evType, _fn, _useCapture);
    return true;
  } else if (_ob.detachEvent) {
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
  } else {
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
function _json2string(_o) {
  var _res = "";
  for (var _name in _o) {
    switch (typeof (_o[_name])) {
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
function _getLeft(_o) {
  var _curleft = 0;
  if (_o.offsetParent)
    while (1) {
      if (_o.style.position === 'absolute')
        break;
      _curleft += _o.offsetLeft;
      if (!_o.offsetParent)
        break;
      _o = _o.offsetParent;
    }
  else if (_o.x)
    _curleft += _o.x;
  return _curleft;
}
function _getTop(_o) {
  var _curtop = 0;
  if (_o.offsetParent)
    while (1) {
      if (_o.style.position === 'absolute')
        break;
      _curtop += _o.offsetTop;
      if (!_o.offsetParent)
        break;
      _o = _o.offsetParent;
    }
  else if (_o.y)
    _curtop += _o.y;
  return _curtop;
}
function _index(_search, _original) {
  return _original.indexOf(_search);
}
function _preventDefaut(_e) {
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
var _getOffsetParents = function (_o) {
  var _offsetParents = [];
  var _parent = _o;
  while (_parent !== null) {
    _offsetParents.push(_parent);
    if (_parent.style.position === 'absolute')
      break;
    _parent = _parent.offsetParent;
  }
  return _offsetParents;
};
var _getCommonOffsetParent = function (_o1, _o2) {
  var _offsetParents1 = _getOffsetParents(_o1);
  var _offsetParents2 = _getOffsetParents(_o2);
  for (var i = 0; i < _offsetParents1.length; i += 1) {
    var _parent1 = _offsetParents1[ i ];
    for (var j = 0; j < _offsetParents2.length; j += 1)
      if (_parent1 === _offsetParents2[ j ])
        return _parent1;
  }
  return null;
};
var _getOffsetTopToParent = function (_o, _p) {
  var _offset = 0;
  var _parent = _o;
  while (_parent && _parent !== _p) {
    _offset += _parent.offsetTop;
    _parent = _parent.offsetParent;
  }
  return _offset;
};
var _getOffsetLeftToParent = function (_o, _p) {
  var _offset = 0;
  var _parent = _o;
  while (_parent && _parent !== _p) {
    _offset += _parent.offsetLeft;
    _parent = _parent.offsetParent;
  }
  return _offset;
};
function KoolAutoCompleteItem(_id) {
  this._id = _id;
  this.id = _id;
}
KoolAutoCompleteItem.prototype =
{
  _getAutoComplete: function () {
    return eval(this._id.substring(0, _index(".", this._id)));
  },
  getData: function () {
    var _data = eval("__=" + _goFirstChild(_obj(this._id)).value);
    for (var i in _data) {
      if (typeof _data[i] != "function") {
        try {
          _data[i] = decodeURIComponent(_data[i]);
        } catch (ex) {
          _data[i] = unescape(_data[i]);
        }
      }
    }
    return _data;
  },
  enable: function (_bool) {
    /*
     * Make the item enable if it is disabled.
     */
    var _li = _obj(this._id);
    (_bool) ? _removeClass(_li, "kacDisable") : _addClass(_li, "kacDisable");
  },
  isEnabled: function () {
    return _index("kacDisable", _getClass(_obj(this._id))) < 0;
  },
  setVisible: function (_visible) {
    _setDisplay(_obj(this._id), _visible);
  },
  select: function () {
    var kac = this._getAutoComplete();
    if (!kac._handleEvent("OnBeforeSelect", {"Item": this}))
      return;
    /*
     * Make this item selected
     */
    var _data = this.getData();
    var _textbox = _obj(kac._attachTo);
    _textbox.value = _data["text"];
    kac._handleEvent("OnSelect", {"Item": this});
  },
  _handle_click: function (_e) {
    if (this.isEnabled()) {
      this.select();
      var kac = this._getAutoComplete();
      kac.close();
      kac._handleEvent("OnSelectAndClose", {"Item": this});
    }
    var _textbox = _obj(this._getAutoComplete()._attachTo);
    _textbox.focus();
  },
  _handle_mouseover: function (_e) {
    this._getAutoComplete()._removeSelectFocus();
    if (this.isEnabled()) {
      var _li = _obj(this._id);
      _addClass(_li, "kacSelectFocus");
    }
  },
  _handle_mouseout: function (_e) {
    var _li = _obj(this._id);
    _removeClass(_li, "kacSelectFocus");
  }
}
function KoolAutoComplete(_id, _attachTo, _saveTo, _defaultSave, _saveTemplate, _delayTime, _searchFilter, _highLight, _boxWidth, _boxHeight, _minBoxHeight, _maxBoxHeight, _verticalOffset, _horizontalOffset, _openDirection, _align, _serviceFunction, _servicePage, _stylePath, _clientEvents) {
  this._id = _id;
  this.id = _id;
  this._attachTo = _attachTo;
  this._saveTo = _saveTo;
  this._defaultSave = _defaultSave;
  this._saveTemplate = _saveTemplate;
  this._delayTime = _delayTime;
  this.searchFilter = _searchFilter;
  this.highLight = _highLight;
  this._boxWidth = (_boxWidth == "auto") ? -1 : parseInt(_boxWidth);
  this._boxHeight = (_boxHeight == "auto") ? -1 : parseInt(_boxHeight);
  this._minBoxHeight = (_minBoxHeight == "auto") ? -1 : parseInt(_minBoxHeight);
  this._maxBoxHeight = (_maxBoxHeight == "auto") ? -1 : parseInt(_maxBoxHeight);
  this._verticalOffset = parseInt(_verticalOffset);
  this._horizontalOffset = parseInt(_horizontalOffset);
  this._openDirection = _openDirection;
  this._align = _align;
  this._serviceFunction = (_serviceFunction != "") ? _serviceFunction : null;
  this._servicePage = (_servicePage != "") ? _servicePage : null;
  this._stylePath = _stylePath;
  this._eventhandles = JSON.parse(_clientEvents);
  this._init();
  this._timeoutid = null;
}
KoolAutoComplete.prototype =
{
  _init: function () {
    var _textbox = _obj(this._attachTo);
    var _box = _goFirstChild(_obj(this._id));
    _textbox.acid = this._id;
    _addEvent(_textbox, "keydown", _textbox_keydown, false);
    _addEvent(_textbox, "change", _textbox_change, false);
    _addEvent(_textbox, "input", _textbox_change, false);
    _addEvent(document, "mousedown", _get_doc_mousedown_listener(this), false);
    _addEvent(_box, "mousedown", _box_mousedown, false);
    var _items = _getElements("li", "kacItem", _obj(this._id));
    for (var i = 0; i < _items.length; i++) {
      _items[i].id = this._id + ".i" + _getIdentity();
      _addEvent(_items[i], "click", _item_click, false);
      _addEvent(_items[i], "mouseover", _item_mouseover, false);
      _addEvent(_items[i], "mouseout", _item_mouseout, false);
    }
    this._SPOrder = 0;
  },
  getItemIds: function () {
    var _items = _getElements("li", "kacItem", _obj(this._id));
    var _ids = new Array();
    for (var i = 0; i < _items.length; i++) {
      _ids.push(_items[i].id);
    }
    return _ids;
  },
  getItem: function (_itemid) {
    return new KoolAutoCompleteItem(_itemid);
  },
  open: function () {
    this.HT();
    var _textbox = _obj(this._attachTo);
    _textbox.focus();
  },
  _open: function () {
    /*
     * Open the div panel of combobox
     */
    if (!this._handleEvent("OnBeforeOpen", {}))
      return;
    var _box = _goFirstChild(_obj(this._id));
    var _iframe = _goNextSibling(_box);
    var _textbox = _obj(this._attachTo);
    var _boxwidth = (this._boxWidth > 0) ? this._boxWidth : _textbox.offsetWidth;
    _setWidth(_box, _boxwidth);
    var _itembox = _getElements("div", "kacItemBox", _box)[0];
    _itembox.style.height = "auto";
    /*
     * Open the box
     */
    _addClass(_box, "kacOpen");
    if (_exist(_iframe))
      _addClass(_iframe, "kacOpen");
    if (this._boxHeight > 0) {
      _setHeight(_itembox, this._boxHeight);
    } else {
      if (_itembox.offsetHeight < this._minBoxHeight && this._minBoxHeight > 0) {
        _setHeight(_itembox, this._minBoxHeight);
      } else if (_itembox.offsetHeight > this._maxBoxHeight && this._maxBoxHeight > 0) {
        _setHeight(_itembox, this._maxBoxHeight);
      }
    }
    _itembox.scrollTop = 0;
    var _parent = _getCommonOffsetParent(_textbox, _box);
    var _offsetTop = _getOffsetTopToParent(_textbox, _parent);
    var _offsetLeft = _getOffsetLeftToParent(_textbox, _parent);
    switch (this._openDirection) {
      case "up":
        var _top = _offsetTop - _box.offsetHeight;
        _box.style.top = _offsetTop - _box.offsetHeight + "px";
        if (_top >= 0)
          _box.style.top = _top + "px";
        else
          _box.style.top = _offsetTop + _textbox.offsetHeight + "px";
        break;
      case "auto":
      case "down":
      default:
        _box.style.top = _offsetTop + _textbox.offsetHeight + "px";
        break;
    }
    _box.style.left = (this._align == "right" ? _offsetLeft + _textbox.offsetWidth - _boxwidth : _offsetLeft) + "px";
    if (_exist(_iframe)) {
      _setWidth(_iframe, _box.offsetWidth);
      _setHeight(_iframe, _box.offsetHeight);
      _iframe.style.top = _box.style.top;
      _iframe.style.left = _box.style.left;
    }
    this._handleEvent("OnOpen", {});
  },
  isOpening: function () {
    var _kacEl = _obj(this._id);
    if (_kacEl) {
      var _box = _goFirstChild(_kacEl);
      return _index("Open", _getClass(_box)) > 0;
    } else
      return false;
  },
  close: function () {
    if (!this.isOpening())
      return;
    if (!this._handleEvent("OnBeforeClose", {}))
      return;
    /*
     * Close the div panel of combobox
     */
    var _box = _goFirstChild(_obj(this._id));
    this.hideLoadingImage( );
    this.checkSaveTo( );
    var _iframe = _goNextSibling(_box);
    _removeClass(_box, "kacOpen");
    if (_exist(_iframe))
      _removeClass(_iframe, "kacOpen");
    this._removeSelectFocus();
    this._handleEvent("OnClose", {});
  },
  removeItem: function (_id) {
    /*
     * Remove a item with id
     */
    var _li = _obj(_id);
    if (_exist(_li) && _index("Item", _getClass(_li)) > 0 && _index(this._id, _id) == 0) {
      var _ul = _goParentNode(_li);
      _purge(_li);
      _ul.removeChild(_li);
    }
  },
  addItem: function (_text, _extradata) {
    /*
     * _id is auto generated
     * Add a new item to the list.
     */
    var _data = new Object();
    _data["text"] = _text;
    if (_exist(_extradata)) {
      for (var i in _extradata) {
        if (typeof _extradata[i] != "function")
          _data[i] = _extradata[i];
      }
    }
    var _itembox = _getElements("div", "kacItemBox", _obj(this._id))[0];
    var _ul = _goFirstChild(_itembox);
    var _li = _newNode("li", _ul);
    _li.id = this._id + ".i" + _getIdentity();
    _setClass(_li, "kacLI kacItem");
    var _template = _obj(this._id + ".itemTemplate").innerHTML;
    var itemrender = unescape(_template);
    for (var _key in _data) {
      if (typeof _data[_key] != "function") {
        itemrender = itemrender.replace(eval("/{" + _key + "}/g"), _data[_key]);
        _data[_key] = encodeURIComponent(_data[_key]);// Prepare to save with json2string				
      }
    }
    _li.innerHTML = "<input type='hidden' value=\"" + _json2string(_data) + "\"/><a class='kacA' href='javascript:void 0'><div class='kacIn'>" + itemrender + "</div></a>";
    _addEvent(_li, "click", _item_click, false);
    _addEvent(_li, "mouseover", _item_mouseover, false);
    _addEvent(_li, "mouseout", _item_mouseout, false);
    return (new KoolAutoCompleteItem(_li.id));
  },
  attachTo: function (_newtextboxid) {
    var _textbox = _obj(this._attachTo);
    _removeEvent(_textbox, "keydown", _textbox_keydown, false);
    _removeEvent(_textbox, "change", _textbox_change, false);
    _textbox.acid = null;
    _textbox = _obj(_newtextboxid);
    _textbox.acid = this._id;
    _addEvent(_textbox, "keydown", _textbox_keydown, false);
    _addEvent(_textbox, "change", _textbox_change, false);
    this._attachTo = _newtextboxid;
  },
  sort: function (_type, _sortby) {
    /*
     * Sort the option by _type
     * _type = "desc","asc"
     * _sortby = the name of the variable
     */
  },
  registerEvent: function (_name, _handle) {
    /*
     * Register event
     */
    this._eventhandles[_name] = _handle;
  },
  _handleEvent: function (_name, _arg) {
    var eventHandler = this._eventhandles[_name];
    if (typeof eventHandler !== 'function' )
      eventHandler = window[eventHandler];
    if (typeof eventHandler === 'function')
      return eventHandler(this, _arg);
    else
      return true;
  },
  _moveSelectFocus: function (_next) {
    var _selectFocusPosition = -1;
    var _items = _getElements("li", "kacItem", _obj(this._id));
    var _textbox = _obj(this._attachTo);
    for (var i = 0; i < _items.length; i++) {
      if (_index("kacSelectFocus", _getClass(_items[i])) > -1) {
        _selectFocusPosition = i;
        break;
      }
    }
    if (_selectFocusPosition < 0 && _next < 0) {
      _selectFocusPosition = _items.length;
    }
    var _count = 0, maxCount = Math.abs(_next);
    var _direction = _next / maxCount;
    var _nextPosition = _selectFocusPosition + _direction;
    while (_nextPosition > -1 && _nextPosition < _items.length && _count < maxCount) {
      if (_index("Disable", _getClass(_items[_nextPosition])) < 0 && _getDisplay(_items[_nextPosition])) {
        _count++;
      }
      _nextPosition += _direction;
    }
    if (_count < maxCount && (_nextPosition < 0 || _nextPosition >= _items.length)) {
      _removeClass(_items[_selectFocusPosition], "kacSelectFocus");
      _textbox.value = this._lastText;
      return;
    }
    if (_count == maxCount) {
      if (_selectFocusPosition > -1 && _selectFocusPosition < _items.length) {
        _removeClass(_items[_selectFocusPosition], "kacSelectFocus");
      }
      _selectFocusPosition = _nextPosition - _direction;
      if (_selectFocusPosition > -1 && _selectFocusPosition < _items.length) {
        _addClass(_items[_selectFocusPosition], "kacSelectFocus");
        var _item = _items[_selectFocusPosition];
        (new KoolAutoCompleteItem(_item.id)).select();
        var _itembox = _goParentNode(_item, 2);
        if (_item.offsetTop + _item.offsetHeight > _itembox.scrollTop + _itembox.offsetHeight) {
          _itembox.scrollTop = _item.offsetTop;
        } else if (_item.offsetTop < _itembox.scrollTop && _itembox.scrollTop > 0) {
          _itembox.scrollTop = _item.offsetTop + _item.offsetHeight - _itembox.offsetHeight;
        }
      }
    }
  },
  _removeSelectFocus: function () {
    var _items = _getElements("li", "kacItem", _obj(this._id));
    for (var i = 0; i < _items.length; i++)
      if (_index("kacSelectFocus", _getClass(_items[i])))
        _removeClass(_items[i], "kacSelectFocus");
  },
  _handle_keyup: function () {
    if (this.isOpening()) {
      this._moveSelectFocus(-1);
      return false;
    }
    return true;
  },
  _handle_keydown: function () {
    if (this.isOpening()) {
      this._moveSelectFocus(1);
      return false;
    }
    return true;
  },
  _handle_keyenter: function () {
    var _selectFocusPosition = -1;
    var _items = _getElements("li", "kacItem", _obj(this._id));
    for (var i = 0; i < _items.length; i++) {
      if (_index("kacSelectFocus", _getClass(_items[i])) > -1) {
        _selectFocusPosition = i;
        break;
      }
    }
    this._removeSelectFocus();
    this.close();
    if (_selectFocusPosition >= 0) {
      var item = new KoolAutoCompleteItem(_items[_selectFocusPosition].id);
      item.select();
      this._handleEvent("OnSelectAndClose", {"Item": item});
      return false;//Enter to select, no need to propagate event
    }
    return true;//Common. Propagate event
  },
  _handle_keyesc: function () {
    this.close();
    return false;//Stop propagating the esc
  },
  _filter: function (_keyword, _datakey, _type, _casesensitive) {
    /*
     * _keyword: keyword to filter
     * _datakey: the key to get data
     * _type: type of filter (0: none;1: startwith; 2:contain)
     */
    var _itemdata = new Array();
    var _itemids = new Array();
    var _items = _getElements("li", "kacItem", _obj(this._id));
    for (var i = 0; i < _items.length; i++) {
      var _oitem = new KoolAutoCompleteItem(_items[i].id);
      _itemids.push(_oitem._id);
      _itemdata.push((_casesensitive) ? _oitem.getData()[_datakey] : _oitem.getData()[_datakey].toLowerCase());
    }
    if (!_casesensitive)
      _keyword = _keyword.toLowerCase();
    var _result = new Array();
    switch (_type) {
      case 0:
        break;
      case 1:
        for (var i = 0; i < _itemids.length; i++)
          if (_index(_keyword, _itemdata[i]) == 0)
            _result.push(_itemids[i]);
        break;
      case 2:
        for (var i = 0; i < _itemids.length; i++)
          if (_index(_keyword, _itemdata[i]) > -1)
            _result.push(_itemids[i]);
        break;
    }
    return _result;
  },
  HT: function (_e) //_handle_typing
  {
    this._timeoutid = null;
    var _textbox = _obj(this._attachTo);
    if (!_textbox)
      return;
    _textbox.style.color = '';
    var _text = _textbox.value;
    this._lastText = _text;
    if (_text == "") {
      this.close();
      return;
    }
    if (!_exist(this._serviceFunction) && !_exist(this._servicePage)) {
      var _type = (this.searchFilter == "startwith") ? 1 : 2;
      var _filterids = this._filter(_text, "text", _type, 0);
      var _list = _filterids.join(" ") + " ";
      var _items = _getElements("li", "kacItem", _obj(this._id));
      for (var i = 0; i < _items.length; i++) {
        _setDisplay(_items[i], (_index(_items[i].id + " ", _list) > -1));
      }
      this.close();
      if (_filterids.length > 0) {
        this._open();
      }
    } else {
      if (!this._handleEvent("OnBeforeSendUpdateRequest", {"Text": _text}))
        return;
      if (_exist(this._serviceFunction)) {
        this._SPOrder += 1;
        koolajax.callback(eval(this._serviceFunction)(_text), eval("__=function (_r){" + this._id + ".SFR(_r,0," + this._SPOrder + ")}"));
        this.showLoadingImage( );
      } else if (_exist(this._servicePage)) {
        this._SPOrder += 1;
        var _request = new KoolAjaxRequest({
          url: this._servicePage,
          method: "post",
          onDone: eval("__=function(_r){" + this._id + ".SFR(_r,1," + this._SPOrder + ")}")
        });
        _request.addArg("text", _text);
        koolajax.sendRequest(_request);
        this.showLoadingImage( );
      }
      this._handleEvent("OnSendUpdateRequest", {"Text": _text});
    }
  },
  SFR: function (_res, _bxml, _SPOrder)//Service function return.
  {
    if (_SPOrder < this._SPOrder)
      return 0;
    var _xml;
    if (_bxml) {
      var _xmlItems = koolajax.parseXml(_res).firstChild;
      _res = new Array();
      for (var i = 0; i < _xmlItems.childNodes.length; i++) {
        var _childitem = _xmlItems.childNodes[i];
        if (_childitem.nodeName.toLowerCase() == "item") {
          var _datum = new Object();
          for (var j = 0; j < _childitem.attributes.length; j++) {
            _datum[_childitem.attributes[j].name] = _childitem.attributes[j].value;
          }
          _res.push(_datum);
        }
      }
    }
    if (!this._handleEvent("OnBeforeUpdateItemList", {"Data": _res}))
      return;
    var _itembox = _getElements("div", "kacItemBox", _obj(this._id))[0];
    var _ul = _goFirstChild(_itembox);
    _purge(_ul);
    _ul.innerHTML = "";
    var _template = _obj(this._id + ".itemTemplate").innerHTML;
    var _ulhtml = "";
    for (i in _res) {
      if (typeof _res[i] != "function") {
        var _itemdata = new Object();
        _itemdata["text"] = "";
        var _itemrender = unescape(_template);
        for (_key in _res[i]) {
          _itemdata[_key] = _res[i][_key];
          _itemrender = _itemrender.replace(eval("/{" + _key + "}/g"), _itemdata[_key]);
          _itemdata[_key] = encodeData(_itemdata[_key]);
        }
        _ulhtml += "<li id='" + this._id + ".i" + _getIdentity() + "' class='kacLI kacItem'><input type='hidden' value='" + JSON.stringify(_itemdata) + "'/><a class='kacA' href='javascript:void 0'><div class='kacIn'>" + _itemrender + "</div></a></li>";
      }
    }
    _ul.innerHTML = _ulhtml;
    this.hideLoadingImage();
    var _items = _getElements("li", "kacItem", _obj(this._id));
    for (var i = 0; i < _items.length; i++) {
      _items[i].id = this._id + ".i" + _getIdentity();
      _addEvent(_items[i], "click", _item_click, false);
      _addEvent(_items[i], "mouseover", _item_mouseover, false);
      _addEvent(_items[i], "mouseout", _item_mouseout, false);
    }
    this.close();
    if (_items.length > 0) {
      this._open();
    }
    this._handleEvent("OnUpdateItemList", {});
  },
  showLoadingImage: function ( ) {
    var _textbox = _obj(this._attachTo);
    if (_textbox) {
      _textbox.style.backgroundImage = 'url("' + this._stylePath + '/Loading.gif")';
      _textbox.style.backgroundRepeat = 'no-repeat';
      _textbox.style.backgroundPosition = 'right center';
      _textbox.style.backgroundSize = 'contain';
    }
  },
  hideLoadingImage: function ( ) {
    var _textbox = _obj(this._attachTo);
    if (_textbox) {
      _textbox.style.backgroundImage = '';
    }
  },
  checkSaveTo: function ( ) {
    var _textbox = _obj(this._attachTo);
    var _saveTo = _obj(this._saveTo);
    if (_textbox && _saveTo) {
      var found = false;
      var foundData = {};
      var value = _textbox.value;
      var itemIds = this.getItemIds( );
      var len = itemIds.length;
      for (var i = 0; i < len; i += 1) {
        var item = this.getItem(itemIds[ i ]);
        var data = item.getData( );
        if (value === data['text']) {
          foundData = data;
          found = true;
          break;
        }
      }
      if (!found) {
        _textbox.style.color = 'red';
        _saveTo.value = this._defaultSave;
      } else {
        _textbox.style.color = '';
        var savedValue = this._saveTemplate;
        for (var _key in foundData) {
          if (foundData.hasOwnProperty(_key) && typeof foundData[_key] !== "function") {
            savedValue = savedValue.replace(eval("/{" + _key + "}/ig"), foundData[_key]);
          }
        }
        _saveTo.value = savedValue;
      }
    }
  },
  getClientEvents: function () {
    return this._eventhandles;
  },
  getAttachTo: function () {
    return _obj(this._attachTo);
  },
  getSaveTo: function () {
    return _obj(this._saveTo);
  }
};
function _item_click(_e) {
  (new KoolAutoCompleteItem(this.id))._handle_click(_e);
}
function _item_mouseover(_e) {
  (new KoolAutoCompleteItem(this.id))._handle_mouseover(_e);
}
function _item_mouseout(_e) {
  (new KoolAutoCompleteItem(this.id))._handle_mouseout(_e);
}
function _textbox_keydown(_e) {
  var kac = window[this.acid];
  var _key = _e.keyCode;
  if (!kac._handleEvent("OnBeforeKeyPress", {"keyCode": _key})) {
    return; //_preventDefaut(_e);
  }
  var _propagateEvent = true;
  switch (_key) {
    case 40:
      _propagateEvent = kac._handle_keydown();
      break;
    case 38:
      _propagateEvent = kac._handle_keyup();
      break;
    case 13:
      _propagateEvent = kac._handle_keyenter();
      break;
    case 27:
      _propagateEvent = kac._handle_keyesc();
    case 39: // Right
    case 37: // Left
    case 16: //Shift
    case 17: //Ctrl
    case 18: //Alt
      break;
    case 9:  //Tabs
      kac.close();
      kac._handleEvent("OnBlurAndClose", {});
      break;
    default:
      if (kac._timeoutid != null)
        clearTimeout(kac._timeoutid);
      kac._timeoutid = setTimeout(kac._id + ".HT()", kac._delayTime);
      break;
  }
  kac._handleEvent("OnKeyPress", {"KeyCode": _key});
  if (!_propagateEvent)
    return _preventDefaut(_e);
}
function _textbox_change(_e) {
  var _autocom = eval("__=" + this.acid);
  if (_autocom) {
    _autocom.checkSaveTo();
    _autocom._handleEvent("OnChange", {"item": _autocom});
  }
}
function _box_mousedown(_e) {
  if (_e.stopPropagation)
    _e.stopPropagation();
  else
    _e.cancelBubble = true;
}
function _get_doc_mousedown_listener(kac) {
  return function (_e) {
    if (kac && kac.isOpening()) {
      kac.close();
      kac._handleEvent("OnBlurAndClose", {});
    }
  };
}
if (typeof (__KACInits) != 'undefined' && _exist(__KACInits)) {
  for (var i = 0; i < __KACInits.length; i++) {
    __KACInits[i]();
  }
}
