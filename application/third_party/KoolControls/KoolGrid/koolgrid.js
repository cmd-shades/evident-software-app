/**
 * @author Nghiem Anh Tuan
 */
function _exist(_theObj) {
  if (typeof (_theObj) == "undefined") {
    return false;
  }
  return (_theObj != null);
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
function _getWidth(_theObj) {
  return parseInt(_theObj.style.width);
}
function _getHeight(_theObj) {
  return parseInt(_theObj.style.height);
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
function _setDisplay(_theObj, _val) {
  _theObj.style.display = (_val) ? "" : "none";
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
function _stopPropagation(_e) {
  if (_e.stopPropagation)
    _e.stopPropagation();
  else
    _e.cancelBubble = true;
}
function _preventDefaut(_e) {
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
      if (a[i])
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
/*
 function _json2string(_o)
 {
 var _res="";
 for (var _name in _o)
 {
 switch(typeof(_o[_name]))
 {
 case "string":
 if(_exist(_o.length))
 _res+="\""+_o[_name]+"\",";
 else
 _res+="\""+_name+"\":\""+_o[_name]+"\",";
 break;
 case "number":
 if(_exist(_o.length))
 _res+=_o[_name]+",";
 else
 _res+="\""+_name+"\":"+_o[_name]+",";
 break;
 case "boolean":
 if(_exist(_o.length))
 _res+=(_o[_name]?"true":"false")+",";
 else
 _res+="\""+_name+"\":"+(_o[_name]?"true":"false")+",";
 break;			
 case "object":
 if(_exist(_o.length))
 _res+=_json2string(_o[_name])+",";
 else			
 _res+="\""+_name+"\":"+_json2string(_o[_name])+",";					
 break;								
 }
 }
 if (_res.length>0)
 _res = _res.substring(0,_res.length-1);
 _res=(_exist(_o.length))?"["+_res+"]":"{"+_res+"}";
 if (_res=="{}") _res="null";
 return _res;
 }
 */
function _json2string(_o) {
  var _res = "";
  for (var _name in _o) {
    switch (typeof (_o[_name])) {
      case "string":
        _res += "\"" + _name + "\":\"" + _o[_name] + "\",";
        break;
      case "number":
        _res += "\"" + _name + "\":" + _o[_name] + ",";
        break;
      case "boolean":
        _res += "\"" + _name + "\":" + (_o[_name] ? "true" : "false") + ",";
        break;
      case "object":
        _res += "\"" + _name + "\":" + _json2string(_o[_name]) + ",";
        break;
    }
  }
  if (_res.length > 0)
    _res = _res.substring(0, _res.length - 1);
  _res = "{" + _res + "}";
  if (_res == "{}")
    _res = "null";
  return _res;
}
function _index(_search, _original) {
  return _original.indexOf(_search);
}
function _mouseXY(_ev) {
  if (_ev.pageX || _ev.pageY) {
    return {_x: _ev.pageX, _y: _ev.pageY};
  } else if (_ev.clientX || _ev.clientY) {
    return {
      _x: _ev.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft),
      _y: _ev.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop)
    };
  } else {
    return {_x: null, _y: null};
  }
}
var _patterns = {
  HYPHEN: /(-[a-z])/i, // to normalize get/setStyle
  ROOT_TAG: /^body|html$/i, // body for quirks mode, html for standards,
  OP_SCROLL: /^(?:inline|table-row)$/i
};
function _getStyle(oElm, strCssRule) {
  var strValue = "";
  if (document.defaultView && document.defaultView.getComputedStyle) {
    var oStyle = document.defaultView.getComputedStyle(oElm, null);
    if (!oStyle) {
      try {
        if (oElm.style.display == "none") {
          oElm.style.display = "";
          oStyle = document.defaultView.getComputedStyle(oElm, null);
          if (oStyle) {
            strValue = oStyle.getPropertyValue(strCssRule);
          }
          oElm.style.display = "none";
        }
      } catch (ex) {
      }
    }
    if (oStyle && strValue == "") {
      strValue = oStyle.getPropertyValue(strCssRule);
    }
  } else if (oElm.currentStyle) {
    try {
      strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1) {
        return p1.toUpperCase();
      });
      strValue = oElm.currentStyle[strCssRule];
    } catch (ex) {/*used to avoid an exception in IE 5.0*/
    }
  }
  return strValue;
}
;
var _getXY = function () {
  if (document.documentElement.getBoundingClientRect) { // IE
    return function (el) {
      var box = el.getBoundingClientRect();
      return {
        _left: box.left + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft),
        _top: box.top + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop)
      };
    };
  } else {
    return function (el) { // manually calculate by crawling up offsetParents
      var pos = [el.offsetLeft, el.offsetTop];
      var parentNode = el.offsetParent;
      var _browser = _getBrowser();
      var accountForBody = (_browser == "safari" &&
              _getStyle(el, 'position') == 'absolute' &&
              el.offsetParent == document.body);
      if (parentNode != el) {
        while (parentNode) {
          pos[0] += parentNode.offsetLeft;
          pos[1] += parentNode.offsetTop;
          if (!accountForBody && _getBrowser() == "safari" &&
                  _getStyle(parentNode, 'position') == 'absolute') {
            accountForBody = true;
          }
          parentNode = parentNode.offsetParent;
        }
      }
      if (accountForBody) { //safari doubles in this case
        pos[0] -= el.ownerDocument.body.offsetLeft;
        pos[1] -= el.ownerDocument.body.offsetTop;
      }
      parentNode = el.parentNode;
      while (parentNode.tagName && !_patterns.ROOT_TAG.test(parentNode.tagName)) {
        if (parentNode.scrollTop || parentNode.scrollLeft) {
          if (!_patterns.OP_SCROLL.test(_getStyle(parentNode, 'display'))) {
            if (_browser != "opera" || _getStyle(parentNode, 'overflow') !== 'visible') { // opera inline-block misreports when visible
              pos[0] -= parentNode.scrollLeft;
              pos[1] -= parentNode.scrollTop;
            }
          }
        }
        parentNode = parentNode.parentNode;
      }
      return {_left: pos[0], _top: pos[1]};
    };
  }
}(); // NOTE: Executing for loadtime branching
function _getBrowser() {
  var _agent = navigator.userAgent.toLowerCase();
  if (_index("opera", _agent) != -1) {
    return "opera";
  } else if (_index("firefox", _agent) != -1) {
    return "firefox";
  } else if (_index("safari", _agent) != -1) {
    return "safari";
  } else if ((_index("msie 6", _agent) != -1) && (_index("msie 7", _agent) == -1) && (_index("msie 8", _agent) == -1) && (_index("opera", _agent) == -1)) {
    return "ie6";
  } else if ((_index("msie 7", _agent) != -1) && (_index("opera", _agent) == -1)) {
    return "ie7";
  } else if ((_index("msie 8", _agent) != -1) && (_index("opera", _agent) == -1)) {
    return "ie8";
  } else if ((_index("msie", _agent) != -1) && (_index("opera", _agent) == -1)) {
    return "ie";
  } else if (_index("chrome", _agent) != -1) {
    return "chrome";
  } else {
    return "firefox";
  }
}
function _utf8_decode(str_data) {
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
function _utf8_encode(argString) {
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
function _base64_decode(data) {
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
function _base64_encode(data) {
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
function GridGroup(_id) {
  this._id = _id;
  this.id = _id;
}
GridGroup.prototype =
        {
          expand: function () {
            var _this = _obj(this._id);
            var _grid = _getGrid(_this);
            if (_grid._handleEvent("OnGroupBeforeExpand", {"Group": this})) {
              _grid._addCommand(this._id, "Expand", {});
              _grid._registerPostLoadEvent("OnGroupExpand", {"Group": this});
            }
          },
          collapse: function () {
            var _this = _obj(this._id);
            var _grid = _getGrid(_this);
            if (_grid._handleEvent("OnGroupBeforeCollapse", {"Group": this})) {
              _grid._addCommand(this._id, "Collapse", {});
              _grid._registerPostLoadEvent("OnGroupCollapse", {"Group": this});
            }
          }
        }
function _GridGroupPanel(_id) {
  this._id = _id;
}
_GridGroupPanel.prototype =
        {
          _init: function () {
            var _this = _obj(this._id);
            var _sort_inputs = _getElements("input", "kgrSort", _this);
            for (var i = 0; i < _sort_inputs.length; i++) {
              _addEvent(_sort_inputs[i], "mousedown", _stopPropagation, false);
            }
            this._enable_groupitems_dragging();
          },
          _handle_column_header_start_dragging: function () {
            this._disable_groupitems_dragging();
            this._enable_placeholders();
            _dropped_groupitem_id = null;
          },
          _handle_column_header_dropping: function (_column_id) {
            this._disable_placeholders();
            this._enable_groupitems_dragging();
            this._show_indicator("", false);
            if (_dropped_groupitem_id != null) {
              var _pos = null;
              if (_index("_gm", _dropped_groupitem_id) > 0) {
                _pos = parseInt(_dropped_groupitem_id.replace(this._id.replace("_gp", "_gm"), ""));
              }
              var _column = new GridColumn(_column_id);
              var _grid = _getGrid(_obj(this._id));
              if (_column.put_to_group(_pos)) {
                _grid.commit();
              }
            }
          },
          _handle_groupitem_start_dragging: function () {
            this._disable_groupitems_dragging();
            this._enable_placeholders();
            _dropped_groupitem_id = null;
          },
          _handle_groupitem_dropping: function () {
            this._disable_placeholders();
            this._enable_groupitems_dragging();
            this._show_indicator("", false);
            if (_dropped_groupitem_id != null) {
              var _pos = null;
              if (_index("_gm", _dropped_groupitem_id) > 0) {
                _pos = parseInt(_dropped_groupitem_id.replace(this._id.replace("_gp", "_gm"), ""));
              }
              var _this = _obj(this._id);
              var _grid = _getGrid(_this);
              var _tableview = _getTableView(_this);
              var _viewstate = _grid._loadViewState();
              var _group_field = _viewstate[_dragging_groupitem_id]["GroupField"];
              if (_tableview.change_group_order(_group_field, _pos)) {
                _grid.commit();
              }
            } else {
              var _this = _obj(this._id);
              var _grid = _getGrid(_this);
              var _tableview = _getTableView(_this);
              var _viewstate = _grid._loadViewState();
              var _group_field = _viewstate[_dragging_groupitem_id]["GroupField"];
              var _column_id = _viewstate[_dragging_groupitem_id]["ColumnUniqueID"];
              if (_column_id != null) {
                var _column = new GridColumn(_column_id);
                if (_column.remove_group()) {
                  _grid.commit();
                }
              } else {
                if (_tableview.remove_group(_group_field)) {
                  _grid.commit();
                }
              }
            }
          },
          _enable_placeholders: function () {
            var _this = _obj(this._id);
            var _tail = _obj(this._id + "_tail");
            var _groupitems = _getElements("th", "kgrGroupItem", _this);
            _addEvent(_tail, "mouseover", _groupitem_and_tail_mouseover, false);
            _addEvent(_tail, "mouseout", _groupitem_and_tail_mouseout, false);
            _addEvent(_tail, "mouseup", _groupitem_and_tail_mouseup, false);
            for (var i = 0; i < _groupitems.length; i++) {
              _addEvent(_groupitems[i], "mouseover", _groupitem_and_tail_mouseover, false);
              _addEvent(_groupitems[i], "mouseout", _groupitem_and_tail_mouseout, false);
              _addEvent(_groupitems[i], "mouseup", _groupitem_and_tail_mouseup, false);
            }
          },
          _disable_placeholders: function () {
            var _this = _obj(this._id);
            var _tail = _obj(this._id + "_tail");
            var _groupitems = _getElements("th", "kgrGroupItem", _this);
            _removeEvent(_tail, "mouseover", _groupitem_and_tail_mouseover, false);
            _removeEvent(_tail, "mouseout", _groupitem_and_tail_mouseout, false);
            _removeEvent(_tail, "mouseup", _groupitem_and_tail_mouseup, false);
            for (var i = 0; i < _groupitems.length; i++) {
              _removeEvent(_groupitems[i], "mouseover", _groupitem_and_tail_mouseover, false);
              _removeEvent(_groupitems[i], "mouseout", _groupitem_and_tail_mouseout, false);
              _removeEvent(_groupitems[i], "mouseup", _groupitem_and_tail_mouseup, false);
            }
          },
          _enable_groupitems_dragging: function () {
            var _this = _obj(this._id);
            var _groupitems = _getElements("th", "kgrGroupItem", _this);
            for (var i = 0; i < _groupitems.length; i++) {
              _groupitems[i].style.cursor = "move";
              _addEvent(_groupitems[i], "mousedown", _groupitem_mousedown, false);
              _groupitems[i].onselectstart = _cancel_textselection;
              _groupitems[i].ondragstart = _cancel_textselection;
              _groupitems[i].onmousedown = _cancel_textselection;
            }
          },
          _disable_groupitems_dragging: function () {
            var _this = _obj(this._id);
            var _groupitems = _getElements("th", "kgrGroupItem", _this);
            for (var i = 0; i < _groupitems.length; i++) {
              _groupitems[i].style.cursor = "default";
              _removeEvent(_groupitems[i], "mousedown", _groupitem_mousedown, false);
            }
          },
          _show_indicator: function (_item_id, _bool) {
            var _this = _obj(this._id);
            var _top_indicator = _getElements("div", "kgrTopIndicator", _this)[0];
            var _bottom_indicator = _getElements("div", "kgrBottomIndicator", _this)[0];
            if (_bool) {
              _item = _obj(_item_id);
              var _parent = _item;
              var _item_top = 0, _item_left = 0;
              while (_parent.id != this._id) {
                _item_top += _parent.offsetTop;
                _item_left += _parent.offsetLeft;
                _parent = _parent.offsetParent;
              }
              _top_indicator.style.display = "block";
              _bottom_indicator.style.display = "block";
              _indicator_height = _top_indicator.offsetHeight;
              _indicator_width = _top_indicator.offsetWidth;
              var _item_height = _item.offsetHeight;
              _top_indicator.style.top = (_item_top - _indicator_height) + "px";
              _top_indicator.style.left = (_item_left - _indicator_width / 2) + "px";
              _bottom_indicator.style.top = (_item_top + _item_height) + "px";
              _bottom_indicator.style.left = (_item_left - _indicator_width / 2) + "px";
            } else {
              _top_indicator.style.display = "none";
              _bottom_indicator.style.display = "none";
            }
          },
          _handle_groupitem_and_tail_mouseover: function (_e, _item_id) {
            this._show_indicator(_item_id, true);
          },
          _handle_groupitem_and_tail_mouseout: function (_e, _item_id) {
            this._show_indicator(_item_id, false);
          },
          _handle_groupitem_and_tail_mouseup: function (_e, _item_id) {
            _dropped_groupitem_id = _item_id;
          },
          _handle_groupitem_mousedown: function (_e, _item_id) {
            _dragging_groupitem_id = _item_id;
            _addEvent(document, "mousemove", _groupitem_document_mousemove, false);
            _addEvent(document, "mouseup", _groupitem_document_mouseup, false);
          },
          _handle_groupitem_mousemove: function (_e) {
            var _this = _obj(this._id);
            var _grid = _getGrid(_this);
            var _dummy_dragging_groupitem = _obj(_dragging_groupitem_id + "_dummy");
            var _dragging_groupitem = _obj(_dragging_groupitem_id);
            var _mpos = _mouseXY(_e);
            if (!_exist(_dummy_dragging_groupitem)) {
              var _div_grid = _obj(_grid._id);
              var _class = _getClass(_div_grid).replace("KGR", "DummyGroupItem");
              _dummy_dragging_groupitem = _newNode("div", document.body);
              _dummy_dragging_groupitem.className = _class;
              _dummy_dragging_groupitem.style.position = "absolute";
              _dummy_dragging_groupitem.style.width = _dragging_groupitem.offsetWidth + "px";
              _dummy_dragging_groupitem.style.height = _dragging_groupitem.offsetHeight + "px";
              _dummy_dragging_groupitem.innerHTML = _dragging_groupitem.innerHTML;
              _dummy_dragging_groupitem.id = _dragging_groupitem_id + "_dummy";
              this._handle_groupitem_start_dragging();//Notify starting dragging.
              this._handle_groupitem_and_tail_mouseover(_e, _dragging_groupitem_id);
            }
            _dummy_dragging_groupitem.style.left = (_mpos._x + 1) + "px";
            _dummy_dragging_groupitem.style.top = (_mpos._y + 1) + "px";
          },
          _handle_groupitem_mouseup: function (_e) {
            _removeEvent(document, "mousemove", _groupitem_document_mousemove, false);
            _removeEvent(document, "mouseup", _groupitem_document_mouseup, false);
            var _dummy_dragging_groupitem = _obj(_dragging_groupitem_id + "_dummy");
            if (_exist(_dummy_dragging_groupitem)) {
              document.body.removeChild(_dummy_dragging_groupitem);
            }
            this._handle_groupitem_dropping();
          }
        }
function _groupitem_and_tail_mouseover(_e) {
  var _gp_div = _goParentNode(this, 4);
  (new _GridGroupPanel(_gp_div.id))._handle_groupitem_and_tail_mouseover(_e, this.id);
}
function _groupitem_and_tail_mouseout(_e) {
  var _gp_div = _goParentNode(this, 4);
  (new _GridGroupPanel(_gp_div.id))._handle_groupitem_and_tail_mouseout(_e, this.id);
}
function _groupitem_and_tail_mouseup(_e) {
  var _gp_div = _goParentNode(this, 4);
  (new _GridGroupPanel(_gp_div.id))._handle_groupitem_and_tail_mouseup(_e, this.id);
}
function _groupitem_mousedown(_e) {
  var _gp_div = _goParentNode(this, 4);
  (new _GridGroupPanel(_gp_div.id))._handle_groupitem_mousedown(_e, this.id);
}
function _groupitem_document_mousemove(_e) {
  var _dragging_groupitem = _obj(_dragging_groupitem_id);
  var _gp_div = _goParentNode(_dragging_groupitem, 4);
  (new _GridGroupPanel(_gp_div.id))._handle_groupitem_mousemove(_e);
}
function _groupitem_document_mouseup(_e) {
  var _dragging_groupitem = _obj(_dragging_groupitem_id);
  var _gp_div = _goParentNode(_dragging_groupitem, 4);
  (new _GridGroupPanel(_gp_div.id))._handle_groupitem_mouseup(_e);
}
function _cancel_textselection() {
  return false;
}
function GridCell(_id) {
  this._id = _id;
  this.id = _id;
}
GridCell.prototype =
{
  getElement: function () {
    return _obj(this._id);
  },
  getInputElement: function () {
    return _obj(this._id + "_input");
  },
  getRow: function () {
    var _this = _obj(this._id);
    var _row = _goParentNode(_this);
    if (_index("kgrRow", _getClass(_row)) > -1) {
      return new GridRow(_row.id);
    }
    return null;
  },
  getColumn: function () {
    var _row = this.getRow();
    var _col_id = this._id.replace(_row._id + "_", "");
    return new GridColumn(_col_id);
  },
  getData: function () {
    var _row = this.getRow();
    if (_exist(_row)) {
      var _this = _obj(this._id);
      var _column = this.getColumn();
      var _dataitem = _row.getDataItem();
      var _grid = _getGrid(_this);
      var _viewstate = _grid._loadViewState();
      var _field = _viewstate[_column._id]["Name"];
      if (_exist(_field)) {
        return _dataitem[_field];
      }
    }
    return null;
  },
  _handle_cell_onmouseover: function (_e) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    _grid._handleEvent("OnCellMouseOver", {"Cell": this, "Event": _e});
  },
  _handle_cell_onmouseout: function (_e) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    _grid._handleEvent("OnCellMouseOut", {"Cell": this, "Event": _e});
  },
  _handle_cell_onclick: function (_e) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    _grid._handleEvent("OnCellClick", {"Cell": this, "Event": _e});
  }
}
function _cell_onmouseover(_e) {
  (new GridCell(this.id))._handle_cell_onmouseover(_e);
}
function _cell_onmouseout(_e) {
  (new GridCell(this.id))._handle_cell_onmouseout(_e);
}
function _cell_onclick(_e) {
  (new GridCell(this.id))._handle_cell_onclick(_e);
}
function GridRow(_id) {
  this._id = _id;
  this.id = _id;
}
GridRow.prototype =
{
  getDataItem: function (decode) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    var _dataitem = {};
    for (var i in _viewstate[this._id]["DataItem"]) {
      if (typeof _viewstate[this._id]["DataItem"][i] != "function") //Mootools
      {
        _dataitem[i] = decode ? 
          decodeURIComponent(escape(_viewstate[this._id]["DataItem"][i])) 
            : decodeURIComponent(_viewstate[this._id]["DataItem"][i]);
      }
    }
    return _dataitem;
  },
  getElement: function () {
    return _obj(this._id);
  },
  del: function () {
    _grid = _getGrid(_obj(this._id));
    if (_grid._handleEvent("OnBeforeRowDelete", {"Row": this})) {
      _grid._addCommand(this._id, "Delete", {});
      _grid._registerPostLoadEvent("OnRowDelete", {"Row": this});
    }
  },
  startEdit: function () {
    _grid = _getGrid(_obj(this._id));
    if (_grid._handleEvent("OnBeforeRowStartEdit", {"Row": this})) {
      _grid._addCommand(this._id, "StartEdit", {});
      _grid._registerPostLoadEvent("OnRowStartEdit", {"Row": this});
    }
  },
  cancelEdit: function () {
    _grid = _getGrid(_obj(this._id));
    if (_grid._handleEvent("OnBeforeRowCancelEdit", {"Row": this})) {
      _grid._addCommand(this._id, "CancelEdit", {});
      _grid._registerPostLoadEvent("OnRowCancelEdit", {"Row": this});
    }
  },
  confirmEdit: function () {
    _grid = _getGrid(_obj(this._id));
    var _new_values = {};
    var _viewstate = _grid._loadViewState();
    _tableview = _getTableView(_obj(this._id));
    var _cols = _tableview.getColumns();
    var _viewstate = _grid._loadViewState();
    var _cells = this.getCells( );
    for (var i = 0; i < _cells.length; i += 1) {
      var _input = _cells[ i ].getInputElement( );
      if (_input !== null) {
        var _editValue = eval(_input.getAttribute('getEditValue'));
        if (_editValue !== null && _editValue !== false) {
          _input.value = _editValue;
        } else if (_editValue === false) {
        }
        _new_values[_viewstate[_cells[i].getColumn().id].Name] = _input.value;
      }
    }
    if (_grid._handleEvent("OnBeforeRowConfirmEdit", {"Row": this, 'EditValues': _new_values})) {
      _grid._addCommand(this._id, "ConfirmEdit", {});
      _grid._registerPostLoadEvent("OnRowConfirmEdit", {"Row": this});
    }
    return true;
  },
  getCells: function () {
    var _this = _obj(this._id);
    var _cells = _getElements("td", "kgrCell", _this);
    var _grid_cells = new Array();
    for (var i = 0; i < _cells.length; i++) {
      _grid_cells.push(new GridCell(_cells[i].id));
    }
    return _grid_cells;
  },
  select: function () {
    if (!this.isSelected()) {
      var _this = _obj(this._id);
      var _grid = _getGrid(_this);
      var _tableview = _getTableView(_this);
      var _viewstate = _grid._loadViewState();
      if (!_grid._handleEvent("OnBeforeRowSelect", {"Row": this}))
        return;
      _addClass(_this, "kgrRowSelected");
      _viewstate[this._id]["Selected"] = true;
      _grid._saveViewState(_viewstate);
      _checks = _getElements("input", "kgrSelectSingleRow", _this);
      for (var i = 0; i < _checks.length; i++) {
        _checks[i].checked = true;
      }
      _tableview._check_allrow_selected();
      _grid._handleEvent("OnRowSelect", {"Row": this});
    }
  },
  deselect: function () {
    if (this.isSelected()) {
      var _this = _obj(this._id);
      var _grid = _getGrid(_this);
      var _tableview = _getTableView(_this);
      var _viewstate = _grid._loadViewState();
      if (!_grid._handleEvent("OnBeforeRowDeselect", {"Row": this}))
        return;
      _removeClass(_this, "kgrRowSelected");
      _viewstate[this._id]["Selected"] = false;
      _grid._saveViewState(_viewstate);
      _checks = _getElements("input", "kgrSelectSingleRow", _this);
      for (var i = 0; i < _checks.length; i++) {
        _checks[i].checked = false;
      }
      _tableview._check_allrow_selected();
      _grid._handleEvent("OnRowDeselect", {"Row": this});
    }
  },
  expand: function () {
    _grid = _getGrid(_obj(this._id));
    if (!_grid._handleEvent("OnBeforeDetailTablesExpand", {"Row": this}))
      return;
    _grid._addCommand(this._id, "Expand", {});
    _grid._registerPostLoadEvent("OnDetailTablesExpand", {"Row": this});
  },
  collapse: function () {
    _grid = _getGrid(_obj(this._id));
    if (!_grid._handleEvent("OnBeforeDetailTableCollapse", {"Row": this}))
      return;
    _grid._addCommand(this._id, "Collapse", {});
    _grid._registerPostLoadEvent("OnDetailTableCollapse", {"Row": this});
  },
  getDetailTables: function () {
    var _this = _obj(this._id);
    var _next_tr = _goNextSibling(_this);
    var _detail_tables = new Array();
    if (_exist(_next_tr)) {
      _tableview_divs = _getElements("div", "kgrTableView", _next_tr);
      for (var i = 0; i < _tableview_divs.length; i++) {
        _tableview = new GridTableView(_tableview_divs[i].id);
        _detail_tables.push(_tableview);
      }
    }
    return _detail_tables;
  },
  isSelected: function () {
    var _tr = _obj(this._id);
    return (_index("kgrRowSelected", _getClass(_tr)) > -1);
  },
  isEditing: function () {
    var _tr = _obj(this._id);
    return (_index("kgrRowEdit", _getClass(_tr)) > -1);
  },
  setHeight: function (_height) {
  },
  _handle_mouseover: function (_e) {
    var _this = _obj(this._id);
    var _tableview = _getTableView(_this);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    if (_viewstate[_tableview._id]["AllowHovering"]) {
      _addClass(_this, "kgrRowOver");
    }
    _grid._handleEvent("OnRowMouseOver", {"Row": this, "Event": _e});
  },
  _handle_mouseout: function (_e) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    _removeClass(_this, "kgrRowOver");
    _grid._handleEvent("OnRowMouseOut", {"Row": this, "Event": _e});
  },
  _handle_click: function (_e) {
    var _this = _obj(this._id);
    var _tableview = _getTableView(_this);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    _grid._handleEvent("OnRowClick", {"Row": this, "Event": _e});
    if (_viewstate[_tableview._id]["AllowSelecting"]) {
      if (this.isSelected()) {
        this.deselect();
      } else {
        if (!_viewstate[_tableview._id]["AllowMultiSelecting"]) {
          _tableview.deselectAllRows();
        }
        this.select();
      }
    }
  },
  _handle_dblclick: function (_e) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    _grid._handleEvent("OnRowDoubleClick", {"Row": this, "Event": _e});
  }
}
function GridColumn(_id) {
  this._id = _id;
  this.id = _id;
}
GridColumn.prototype =
{
  getFooterText: function () {
    var _td_footer = _obj(this._id + "_ft");
    if (_exist(_td_footer)) {
      var _span_footer_text = _goFirstChild(_td_footer, 2);
      if (_exist(_span_footer_text)) {
        return _span_footer_text.innerHTML;
      }
    }
    return "";
  },
  getElement: function () {
    return _obj(this._id);
  },
  setFooterText: function (_text) {
    var _td_footer = _obj(this._id + "_ft");
    if (_exist(_td_footer)) {
      var _span_footer_text = _goFirstChild(_td_footer, 2);
      if (_exist(_span_footer_text)) {
        _span_footer_text.innerHTML = _text;
      }
    }
  },
  setVisible: function (_bool) {
    var _this = _obj(this._id);
    var _tableview = _getTableView(_this);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    var _rows_count = _viewstate[_tableview._id]["RowsCount"];
    var _col_header = _obj(this._id + "_hd");
    var _col_footer = _obj(this._id + "_ft");
    var _col_filter = _obj(this._id + "_flt");
    var _browser = _getBrowser();
    if (_browser != "ie7" && _browser != "ie6") {
      for (var i = 0; i < _rows_count; i++) {
        var _cell = _obj(_tableview._id + "_r" + i + "_" + this._id);
        if (_bool) {
          _removeClass(_cell, "kgrHidden");
        } else {
          _addClass(_cell, "kgrHidden");
        }
      }
    }
    var _this_group = document.getElementsByName(this._id);
    if (_bool) {
      for (var i = 0; i < _this_group.length; i++) {
        _removeClass(_this_group[i], "kgrHidden");
      }
      if (_browser != "ie7" && _browser != "ie6") {
        if (_exist(_col_header))
          _removeClass(_col_header, "kgrHidden");
        if (_exist(_col_footer))
          _removeClass(_col_footer, "kgrHidden");
        if (_exist(_col_filter))
          _removeClass(_col_filter, "kgrHidden");
      }
    } else {
      for (var i = 0; i < _this_group.length; i++) {
        _addClass(_this_group[i], "kgrHidden");
      }
      if (_browser != "ie7" && _browser != "ie6") {
        if (_exist(_col_header))
          _addClass(_col_header, "kgrHidden");
        if (_exist(_col_footer))
          _addClass(_col_footer, "kgrHidden");
        if (_exist(_col_filter))
          _addClass(_col_filter, "kgrHidden");
      }
    }
    _viewstate[this._id]["Visible"] = _bool;
    _grid._saveViewState(_viewstate);
  },
  setWidth: function (_width, noViewState) {
    var _this = _obj(this._id);
    var _tableview = _getTableView(_this);
    var _tableview_div = _obj(_tableview._id);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    var _allowScrolling = _viewstate[_tableview._id]["AllowScrolling"];
    var _resize_grid_on_column_resize = _viewstate[_grid._id]["ClientSettings"]["Resizing"]["ResizeGridOnColumnResize"];
    if (_resize_grid_on_column_resize || _allowScrolling) {
      var _old_width = (_index("px", _this.style.width) < 0) ? _this.offsetWidth : _getWidth(_this);
      var _groups = document.getElementsByName(_this.id);
      for (var i = 0; i < _groups.length; i++) {
        _groups[i].style.width = _width;
      }
      var _this_header = _obj(this._id + "_hd");
      var _this_footer = _obj(this._id + "_ft");
      if (_exist(_this_header)) {
        _this_header.style.width = _width;
      }
      if (_exist(_this_footer)) {
        _this_footer.style.width = _width;
      }
      if (_getBrowser() == "safari" || _getBrowser() == "chrome") {
        var _grid_rows = _tableview.getRows();
        if (_grid_rows.length > 0) {
          var _cell = _obj(_grid_rows[0]._id + "_" + this._id);
          _old_width = (_index("px", _cell.style.width) < 0) ? _cell.offsetWidth : _getWidth(_cell);
          _cell.style.width = _width;
        }
      }
      var _different = parseInt(_width) - _old_width;
      if (_allowScrolling) {
        if ( ! noViewState) 
          _tableview._table_part_add_width(_different);
        _viewstate = _grid._loadViewState();
      } else {
        if (_index("%", _width) < 0) {
          var _table_width = (_index("px", _tableview_div.style.width) < 0) ? _tableview_div.offsetWidth : _getWidth(_tableview_div);
          var _table_new_width = _table_width + _different;
          _tableview.setWidth(_table_new_width + "px");
          _viewstate = _grid._loadViewState();
        }
      }
      if ( ! noViewState)
        _viewstate[_this.id]["Width"] = _width;
    } else {
      var _parent = _goParentNode(_this);
      if (_this == _parent.lastChild) {
        return;
      }
      var _groups = document.getElementsByName(_this.id);
      for (var i = 0; i < _groups.length; i++) {
        _groups[i].style.width = _width;
      }
      var _this_header = _obj(this._id + "_hd");
      var _this_footer = _obj(this._id + "_ft");
      if (_exist(_this_header)) {
        _this_header.style.width = _width;
      }
      if (_exist(_this_footer)) {
        _this_footer.style.width = _width;
      }
      if ( ! noViewState)
        _viewstate[_this.id]["Width"] = _width;
      var _next = _this.nextSibling;
      while (_exist(_next)) {
        var _groups = document.getElementsByName(_next.id);
        for (var i = 0; i < _groups.length; i++) {
          _groups[i].style.width = "";
        }
        var _next_header = _obj(_next.id + "_hd");
        var _next_footer = _obj(_next.id + "_ft");
        if (_exist(_next_header)) {
          _next_header.style.width = "";
        }
        if (_exist(_next_footer)) {
          _next_footer.style.width = "";
        }
        _viewstate[_next.id]["Width"] = "";
        _next = _next.nextSibling;
      }
    }
    _grid._saveViewState(_viewstate);
  },
  sort: function (_direction, _order) {
    var _grid = _getGrid(_obj(this._id));
    if (!_grid._handleEvent("OnBeforeColumnSort", {"Column": this, "Order": _direction, "SortOrder": _order}))
      return;
    _grid._addCommand(this._id, "Sort", {"Sort": _direction, "SortOrder": _order});
    _grid._registerPostLoadEvent("OnColumnSort", {"Column": this, "Order": _direction, "SortOrder": _order});
  },
  filter: function (_expression, _value, _isPost) {
    var _grid = _getGrid(_obj(this._id));
    if (!_grid._handleEvent("OnBeforeColumnFilter", {"Column": this, "Exp": _expression, "Value": _value}))
      return;
    _grid._addCommand(this._id, "Filter", {"Filter": {"Exp": _expression, "Value": (_value) ? escape(_value) : _value}, "Post": _isPost});
    _grid._registerPostLoadEvent("OnColumnFilter", {"Column": this, "Exp": _expression, "Value": _value});
  },
  addFilter: function() {
    var _grid = _getGrid(_obj(this._id));
    if (!_grid._handleEvent("OnBeforeColumnAddFilter", {"Column": this}))
      return;
    _grid._addCommand(this._id, "AddFilter", {"Column": this});
    _grid._registerPostLoadEvent("OnColumnAddFilter", {"Column": this});
  },
  removeFilter: function(_index) {
    var _grid = _getGrid(_obj(this._id));
    if (!_grid._handleEvent("OnBeforeColumnAddFilter", {"Column": this, "Index": _index}))
      return;
    _grid._addCommand(this._id, "RemoveFilter", {"Column": this, "Index": _index});
    _grid._registerPostLoadEvent("OnColumnAddFilter", {"Column": this, "Index": _index});
  },
  put_to_group: function (_position) {
    var _this = _obj(this._id);
    var _tableview = _getTableView(_this);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    if (!_grid._handleEvent("OnBeforeColumnGroup", {"Column": this, "Position": _position}))
      return false;
    _grid._addCommand(this._id, "Group", {"Position": _position});
    _grid._registerPostLoadEvent("OnColumnGroup", {"Column": this, "Position": _position});
    return true;
  },
  change_group_order: function (_position) {
    var _this = _obj(this._id);
    var _tableview = _getTableView(_this);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    _tableview.change_group_order(_viewstate[this._id]["Name"], _position);
  },
  remove_group: function () {
    var _this = _obj(this._id);
    var _tableview = _getTableView(_this);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    if (!_grid._handleEvent("OnBeforeColumnRemoveGroup", {"Column": this}))
      return false;
    _grid._addCommand(this._id, "UnGroup", {});
    _grid._registerPostLoadEvent("OnColumnRemoveGroup", {"Column": this});
    return true;
  },
  isVisible: function () {
    var _this = _obj(this._id);
    return (_index("kgrHidden", _getClass(_this)) < 0);
  },
  _isResizable: function () {
    var _this = _obj(this._id);
    return (_index("kgrResizable", _getClass(_this)) > -1);
  },
  _isGroupable: function () {
    var _this = _obj(this._id);
    return (_index("kgrGroupable", _getClass(_this)) > -1);
  },
  _handle_column_header_mouseout: function (_e) {
    var _this = _obj(this._id);
    var _tableview = _getTableView(_this);
    var _grid = _getGrid(_this);
    if (this._isResizable() && !_is_resizing) {
      _resizing_column_id = null;
      _obj(_tableview._id).style.cursor = "";
      _removeEvent(document, "mousemove", _resizing_document_mousemove, false);
    }
    if (this._isGroupable() && !_is_dragging_to_group) {
      _dragging_to_group_column_id = null;
      _remove_mousemove_event = true;
    }
    _grid._handleEvent("OnColumnMouseOut", {"Column": this, "Event": _e});
  },
  _handle_column_header_mouseover: function (_e) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    if (this._isResizable() && !_is_resizing) {
      _resizing_column_id = this._id;
      _addEvent(document, "mousemove", _resizing_document_mousemove, false);
    }
    if (this._isGroupable() && !_is_dragging_to_group) {
      _dragging_to_group_column_id = this._id;
    }
    _grid._handleEvent("OnColumnMouseOver", {"Column": this, "Event": _e});
  },
  _handle_column_header_click: function (_e) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    _grid._handleEvent("OnColumnClick", {"Column": this, "Event": _e});
  },
  _handle_column_header_dblclick: function (_e) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    _grid._handleEvent("OnColumnDblClick", {"Column": this, "Event": _e});
  },
  _handle_column_header_mousedown: function (_e) {
    if (this._isResizable()) {
      var _mpos = _mouseXY(_e);
      if (!_is_resizing) {
        var _this = _obj(this._id);
        var _grid = _getGrid(_this);
        var _column_header = _obj(this._id + "_hd");
        var _pos_column_header = _getXY(_column_header);
        var _column_header_left = _pos_column_header._left;
        var _column_header_top = _pos_column_header._top;
        var _column_header_width = _column_header.offsetWidth;
        var _column_header_height = _column_header.offsetHeight;
        if ((_mpos._y > _column_header_top && _mpos._y < _column_header_top + _column_header_height) && (_mpos._x < _column_header_left + _column_header_width && _mpos._x > _column_header_left + _column_header_width - 5)) {
          if (!_grid._handleEvent("OnBeforeColumnResize", {"Column": this}))
            return;
          _is_resizing = true;
          _resizing_current_x = _mpos._x;
          this.setWidth(_column_header_width + "px");
          _addEvent(document, "mouseup", _resizing_document_mouseup, false);
          return;// Not go to below grouping
        }
      }
    }
    if (this._isGroupable()) {
      _is_dragging_to_group = true;
      _dragging_to_group_column_id = this._id;
      _addEvent(document, "mousemove", _dragging_to_group_document_mousemove, false);
      _addEvent(document, "mouseup", _dragging_to_group_document_mouseup, false);
      var _tableview = _getTableView(_obj(this._id));
      var _group_panel = _tableview._get_group_panel();
      _group_panel._handle_column_header_start_dragging();
    }
  },
  _handle_dragging_to_group_column_header_mousemove: function (_e) {
    var _header = _obj(this._id + "_hd");
    var _dummy_header = _obj(this._id + "_hd_dummy");
    var _grid = _getGrid(_header);
    var _mpos = _mouseXY(_e);
    if (!_exist(_dummy_header)) {
      var _div_grid = _obj(_grid._id);
      var _class = _getClass(_div_grid).replace("KGR", "DummyHeader");
      _dummy_header = _newNode("div", document.body);
      _dummy_header.className = _class;
      _dummy_header.style.position = "absolute";
      _dummy_header.style.width = _header.offsetWidth + "px";
      _dummy_header.style.height = _header.offsetHeight + "px";
      _dummy_header.innerHTML = _header.innerHTML;
      _dummy_header.id = this._id + "_hd_dummy";
    }
    _dummy_header.style.left = (_mpos._x + 1) + "px";
    _dummy_header.style.top = (_mpos._y + 1) + "px";
  },
  _handle_dragging_to_group_column_header_mouseup: function (_e) {
    _removeEvent(document, "mousemove", _dragging_to_group_document_mousemove, false);
    _removeEvent(document, "mouseup", _dragging_to_group_document_mouseup, false);
    var _dummy_header = _obj(this._id + "_hd_dummy");
    if (_exist(_dummy_header)) {
      document.body.removeChild(_dummy_header);
    }
    _dragging_to_group_column_id = null;
    _is_dragging_to_group = false;
    var _tableview = _getTableView(_obj(this._id));
    var _group_panel = _tableview._get_group_panel();
    _group_panel._handle_column_header_dropping(this._id);
  },
  _handle_resizing_column_header_mousemove: function (_e) {
    if (this._isResizable()) {
      var _mpos = _mouseXY(_e);
      if (!_is_resizing) {
        var _column_header = _obj(this._id + "_hd");
        var _pos_column_header = _getXY(_column_header);
        var _column_header_left = _pos_column_header._left;
        var _column_header_top = _pos_column_header._top;
        var _column_header_width = _column_header.offsetWidth;
        var _column_header_height = _column_header.offsetHeight;
        var _this = _obj(this._id);
        var _tableview = _getTableView(_this);
        if ((_mpos._y > _column_header_top && _mpos._y < _column_header_top + _column_header_height) && (_mpos._x < _column_header_left + _column_header_width && _mpos._x > _column_header_left + _column_header_width - 7)) {
          _obj(_tableview._id).style.cursor = "w-resize";
        } else {
          _obj(_tableview._id).style.cursor = "";
        }
      } else {
        _this = _obj(this._id);
        var _column_new_width = _getWidth(_this) + (_mpos._x - _resizing_current_x);
        _column_new_width = (_column_new_width < 0) ? 0 : _column_new_width;
        this.setWidth(_column_new_width + "px");
        _resizing_current_x = _mpos._x;
      }
    }
  },
  _handle_resizing_column_header_mouseup: function (_e) {
    if (this._isResizable()) {
      if (_is_resizing) {
        var _this = _obj(this._id);
        var _grid = _getGrid(_this);
        var _tableview = _getTableView(_this);
        var _column_header = _obj(this._id + "_hd");
        _removeEvent(document, "mouseup", _resizing_document_mouseup, false);
        _obj(_tableview._id).style.cursor = "";
        _is_resizing = false;
        _grid._handleEvent("OnColumnResize", {"Column": this});
      }
    }
  }
}
function GridTableView(_id) {
  this._id = _id;
  this.id = _id;
}
GridTableView.prototype =
{
  _init: function (_grid) {
    var _viewstate = _grid._loadViewState();
    var _this = _obj(this._id);
    var _allowScrolling = _viewstate[this._id]["AllowScrolling"];
    var _virtual_scrolling = _viewstate[this._id]["VirtualScrolling"];
    var _frozen_columns_count = _viewstate[this._id]["FrozenColumnsCount"];
    var _group_panel = new _GridGroupPanel(this._id + "_gp");
    _group_panel._init();
    if (_allowScrolling) {
      var _header_part_div = _goParentNode(_obj(this._id + "_header"));
      var _data_part_div = _goParentNode(_obj(this._id + "_data"));
      var _footer_part_div = _goParentNode(_obj(this._id + "_footer"));
      var _data_table = _obj(this._id + "_data");
      if (_data_part_div.offsetWidth >= _data_part_div.scrollWidth) {
        _data_part_div.style.overflowX = "hidden";
      }
      if (_frozen_columns_count > 0) {
        _data_part_div.style.overflowX = "hidden";
        var _frozen_scroller_div = _newNode("div", _this);
        _this.insertBefore(_frozen_scroller_div, _footer_part_div);
        _frozen_scroller_div.id = this._id + "_frozen_scroller";
        _setClass(_frozen_scroller_div, "kgrFrozenScroller");
        var _frozen_inner_div = _newNode("div", _frozen_scroller_div);
        _setWidth(_frozen_inner_div, _data_table.offsetWidth);
      }
      if (_this.style.height != "") {
        var _this_height = _getHeight(_this);
        var _other_height = 0;
        for (var i = 0; i < _this.childNodes.length; i++)
          if (_this.childNodes[i].nodeName == "DIV" && _getClass(_this.childNodes[i]) != "kgrPartData") {
            if (!isNaN(_this.childNodes[i].offsetHeight)) {
              _other_height += _this.childNodes[i].offsetHeight;
            }
          }
        var _part_data_height = _this_height - _other_height;
        _setHeight(_data_part_div, _part_data_height);
        _viewstate[this._id]["PartDataHeight"] = _part_data_height;
      }
      if (_index("ie", _getBrowser()) > -1) {
        _addEvent(window, "load", eval("__=function(){_itch(\"" + this._id + "\");}"), false);
      }
      _data_part_div.scrollTop = _viewstate[this._id]["scrollTop"];
      if (_frozen_columns_count > 0) {
        _addEvent(_frozen_scroller_div, "scroll", _data_part_div_onscroll, false);
        _frozen_scroller_div.scrollLeft = _viewstate[this._id]["scrollLeft"];
        _frozen_scroller_div.setAttribute('getScrollLeftViewState', true);
      } else {
        _footer_part_div.scrollLeft = _header_part_div.scrollLeft = _data_part_div.scrollLeft = _viewstate[this._id]["scrollLeft"];
      }
      _addEvent(_data_part_div, "scroll", _data_part_div_onscroll, false);
      _grid._saveViewState(_viewstate);
      var _edit_forms = _getElements("div", "kgrEditForm", _this);
      for (var i = 0; i < _edit_forms.length; i++) {
        if (!isNaN(_this.offsetWidth)) {
          _setWidth(_edit_forms[i], _this.offsetWidth - ((_allowScrolling) ? 26 : 0));
        }
      }
      var _insert_forms = _getElements("div", "kgrInsertForm", _this);
      for (var i = 0; i < _insert_forms.length; i++) {
        if (!isNaN(_this.offsetWidth)) {
          _setWidth(_insert_forms[i], _this.offsetWidth - ((_allowScrolling) ? 26 : 0));
        }
      }
      if (_virtual_scrolling) {
        var _total_pages = _viewstate[this._id + "_pg"]["_TotalPages"];
        var _page_index = _viewstate[this._id + "_pg"]["PageIndex"];
        var _rows_count = _viewstate[this._id]["RowsCount"];
        var _total_rows = _viewstate[this._id + "_pg"]["_TotalRows"];
        var _data_table_height = _data_table.offsetHeight;
        var _div_top = _newNode("div", _data_part_div);
        var _div_bottom = _newNode("div", _data_part_div);
        var _data_part_div_height = _data_part_div.offsetHeight;
        _data_part_div.insertBefore(_div_top, _data_table);
        if (_page_index < _total_pages - 1) {
          _setHeight(_div_top, (_page_index) * _data_table_height);
          _setHeight(_div_bottom, (_total_pages - _page_index - 1) * _data_table_height);
          _data_part_div.scrollTop = (_page_index) * _data_table_height;
        } else {
          _setHeight(_div_top, (_data_table_height * (_total_rows - _rows_count) / _rows_count));
          if (_data_table_height < _data_part_div_height) {
            _setHeight(_div_bottom, _data_part_div_height - _data_table_height - 17);
          }
          _data_part_div.scrollTop = (_data_table_height * (_total_rows - _rows_count) / _rows_count);
        }
      }
    }
  },
  _check_height_in_scrolling_mode: function () {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    var _this = _obj(this._id);
    var _allowScrolling = _viewstate[this._id]["AllowScrolling"];
    if (_allowScrolling) {
      if (_this.style.height != "") {
        var _data_part_div = _goParentNode(_obj(this._id + "_data"));
        var _this_height = _getHeight(_this);
        var _other_height = 0;
        for (var i = 0; i < _this.childNodes.length; i++)
          if (_this.childNodes[i].nodeName == "DIV" && _getClass(_this.childNodes[i]) != "kgrPartData") {
            if (!isNaN(_this.childNodes[i].offsetHeight)) {
              _other_height += _this.childNodes[i].offsetHeight;
            }
          }
        var _part_data_height = _this_height - _other_height;
        _setHeight(_data_part_div, _part_data_height);
        _viewstate[this._id]["PartDataHeight"] = _part_data_height;
        _grid._saveViewState(_viewstate);
      }
    }
  },
  selectAllRows: function () {
    var _rows = this.getRows();
    for (var i = 0; i < _rows.length; i++) {
      _rows[i].select();
    }
  },
  deselectAllRows: function () {
    var _rows = this.getRows();
    for (var i = 0; i < _rows.length; i++) {
      _rows[i].deselect();
    }
  },
  _check_allrow_selected: function () {
    var _this = _obj(this._id);
    var _allrow_checks = _getElements("input", "kgrSelectAllRows", _this);
    if (_allrow_checks.length > 0) {
      var _rows = this.getRows();
      var _selected = true;
      for (var i = 0; i < _rows.length; i++) {
        if (!_rows[i].isSelected())
          _selected = false;
      }
      for (var i = 0; i < _allrow_checks.length; i++) {
        _allrow_checks[i].checked = _selected;
      }
    }
  },
  setWidth: function (_width, noViewState) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    var _allowScrolling = _viewstate[this._id]["AllowScrolling"];
    _this.style.width = _width;
    if (!_allowScrolling) {
      var _table = _goFirstChild(_this);
      _table.style.width = (_index("%", _width) < 0) ? _width : "100%";
    }
    if (noViewState)
      return;
    _viewstate[this._id]["Width"] = _width;
    _grid._saveViewState(_viewstate);
  },
  _table_part_add_width: function (_different) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    var _table_header = _obj(this._id + "_header");
    var _table_data = _obj(this._id + "_data");
    var _table_footer = _obj(this._id + "_footer");
    var _width = 0;
    if (_exist(_table_data)) {
      _width = _table_data.offsetWidth + _different;
      _setWidth(_table_data, _width);
    }
    if (_exist(_table_header))
      _setWidth(_table_header, _width);
    if (_exist(_table_footer))
      _setWidth(_table_footer, _width);
    _viewstate[this._id]["TablePartWidth"] = _width + "px";
    _grid._saveViewState(_viewstate);
  },
  getName: function () {
    var _grid = _getGrid(_obj(this._id));
    var _viewstate = _grid._loadViewState();
    return _viewstate[this._id]["Name"];
  },
  getRows: function () {
    var _grid_rows = new Array();
    var _row = _obj(this._id + "_r0");
    while (_exist(_row)) {
      if (_index("kgrRow", _getClass(_row)) > -1) {
        _grid_rows.push(new GridRow(_row.id));
      }
      _row = _goNextSibling(_row);
    }
    return _grid_rows;
  },
  getColumns: function () {
    var _grid_cols = new Array();
    var _col = _obj(this._id + "_c0");
    while (_exist(_col)) {
      _grid_cols.push(new GridColumn(_col.id));
      _col = _goNextSibling(_col);
    }
    return _grid_cols;
  },
  getSelectedRows: function () {
    var _grid_selected_rows = new Array();
    var _row = _obj(this._id + "_r0");
    while (_exist(_row)) {
      if (_index("kgrRowSelected", _getClass(_row)) > -1) {
        _grid_selected_rows.push(new GridRow(_row.id));
      }
      _row = _goNextSibling(_row);
    }
    return _grid_selected_rows;
  },
  goPage: function (_page_index) {
    var _grid = _getGrid(_obj(this._id));
    if (!_grid._handleEvent("OnBeforePageChange", {"TableView": this, "PageIndex": _page_index}))
      return;
    _grid._addCommand(this._id + "_pg", "GoPage", {"PageIndex": _page_index});
    _grid._registerPostLoadEvent("OnPageChange", {"TableView": this, "PageIndex": _page_index});
  },
  changePageSize: function (_page_size) {
    var _grid = _getGrid(_obj(this._id));
    _grid._addCommand(this._id + "_pg", "ChangePageSize", {"PageSize": _page_size});
  },
  changePageOverlap: function (_page_overlap) {
    var _grid = _getGrid(_obj(this._id));
    _grid._addCommand(this._id + "_pg", "ChangePageOverlap", {"PageOverlap": _page_overlap});
  },
  refresh: function () {
    var _grid = _getGrid(_obj(this._id));
    _grid._addCommand(this._id, "Refresh", {});
  },
  getPageIndex: function () {
    var _grid = _getGrid(_obj(this._id));
    var _viewstate = _grid._loadViewState();
    return _viewstate[this._id + "_pg"]["PageIndex"];
  },
  startInsert: function () {
    var _grid = _getGrid(_obj(this._id));
    if (_grid._handleEvent("OnBeforeStartInsert", {"TableView": this})) {
      _grid._addCommand(this._id, "StartInsert", {});
      _grid._registerPostLoadEvent("OnStartInsert", {"TableView": this});
    }
  },
  getInsertInputs: function ( ) {
    var _inputs = [];
    var _cols = this.getColumns();
    var s = this.id + '_nr_';
    for (var i = 0; i < _cols.length; i += 1) {
      var _input = _obj(s + _cols[ i ]._id + '_input');
      if (_input)
        _inputs.push(_input);
    }
    return _inputs;
  },
  confirmInsert: function () {
    var _grid = _getGrid(_obj(this._id));
    var _newInput = this.getInsertInputs( );
    for (var i = 0; i < _newInput.length; i += 1) {
      var _input = _newInput[ i ];
      if (_input !== null) {
        var _editValue = eval(_input.getAttribute('getEditValue'));
        if (_editValue !== null && _editValue !== false) {
          _input.value = _editValue;
        } else if (_editValue === false) {
          return false;
        }
      }
    }
    if (_grid._handleEvent("OnBeforeConfirmInsert", {"TableView": this})) {
      _grid._addCommand(this._id, "ConfirmInsert", {});
      _grid._registerPostLoadEvent("OnConfirmInsert", {"TableView": this});
    }
    return true;
  },
  cancelInsert: function () {
    var _grid = _getGrid(_obj(this._id));
    if (_grid._handleEvent("OnBeforeCancelInsert", {"TableView": this})) {
      _grid._addCommand(this._id, "CancelInsert", {});
      _grid._registerPostLoadEvent("OnCancelInsert", {"TableView": this});
    }
  },
  add_group: function (_group_field, _position) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    if (!_grid._handleEvent("OnBeforeAddGroup", {"GroupField": _group_field, "Position": _position}))
      return;
    _grid._addCommand(this._id, "AddGroup", {"GroupField": _group_field, "Position": _position});
    _grid._registerPostLoadEvent("OnAddGroup", {"GroupField": _group_field, "Position": _position});
  },
  change_group_order: function (_group_field, _position) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    if (!_grid._handleEvent("OnBeforeChangeGroupOrder", {"GroupField": _group_field, "Position": _position}))
      return false;
    _grid._addCommand(this._id, "ChangeGroupOrder", {"GroupField": _group_field, "Position": _position});
    _grid._registerPostLoadEvent("OnChangeGroupOrder", {"GroupField": _group_field, "Position": _position});
    return true;
  },
  remove_group: function (_group_field) {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    if (!_grid._handleEvent("OnBeforeRemoveGroup", {"GroupField": _group_field}))
      return false;
    _grid._addCommand(this._id, "RemoveGroup", {"GroupField": _group_field});
    _grid._registerPostLoadEvent("OnRemoveGroup", {"GroupField": _group_field});
    return true;
  },
  _get_group_panel: function () {
    return (new _GridGroupPanel(this._id + "_gp"));
  },
  get_group_list: function () {
    var _this = _obj(this._id);
    var _grid = _getGrid(_this);
    var _viewstate = _grid._loadViewState();
    var _groupsize = _viewstate[this._id]["GroupSize"];
    var _list = new Array();
    for (var i = 0; i < _groupsize; i++) {
      _list.push(_viewstate[this._id + "_gm" + i]["GroupField"]);
    }
    return _list;
  },
  excuteDelete: function (_dataitem) {
  },
  excuteUpdate: function (_dataitem) {
  },
  excuteInsert: function (_dataitem) {
  },
  _handle_data_part_div_onscroll: function (_e) {
    var _header_part_div = _goParentNode(_obj(this._id + "_header"));
    var _data_part_div = _goParentNode(_obj(this._id + "_data"));
    var _footer_part_div = _goParentNode(_obj(this._id + "_footer"));
    var _grid = _getGrid(_obj(this._id));
    var _viewstate = _grid._loadViewState();
    var _frozen_columns_count = _viewstate[this._id]["FrozenColumnsCount"];
    if (_frozen_columns_count > 0) {
      var _frozen_scroller_div = _obj(this._id + "_frozen_scroller");
      if (_viewstate[this._id]["scrollLeft"] != _frozen_scroller_div.scrollLeft ||
              _frozen_scroller_div.getAttribute('getScrollLeftViewState')) {
        _frozen_scroller_div.removeAttribute('getScrollLeftViewState');
        var _frozen_inner_div = _goFirstChild(_frozen_scroller_div);
        var _max_scrolling_width = _getWidth(_frozen_inner_div) - _frozen_scroller_div.offsetWidth;
        var _cols = this.getColumns();
        var _num_cols = _cols.length - _frozen_columns_count - 1;//Minus 1 for last column;
        if (_num_cols > 0) {
          var _step_width = (_max_scrolling_width) / _num_cols;
          var _start_showing_col = Math.floor(_frozen_scroller_div.scrollLeft / _step_width);
          for (var i = 0; i < _cols.length - _frozen_columns_count - 1; i++) {
            var _visible = (i < _start_showing_col) ? false : true;
            var col = _cols[_frozen_columns_count + i];
            if (col.isVisible() !== _visible) {
              col.setVisible(_visible);
            }
          }
          i = _cols.length - 1;
          var col = _cols[i];
          col.setWidth('100%', true);
          _header_table = _obj(this._id + "_header");
          _data_table = _obj(this._id + "_data");
          _footer_table = _obj(this._id + "_footer");
          _header_table.style.tableLayout = "auto";
          _data_table.style.tableLayout = "auto";
          _footer_table.style.tableLayout = "auto";
          _header_table.style.tableLayout = "fixed";
          _data_table.style.tableLayout = "fixed";
          _footer_table.style.tableLayout = "fixed";
          if (_index("ie", _getBrowser()) > -1) {
            if (_obj(_cols[_cols.length - 1].id).style.width != "100%") {
              _cols[_cols.length - 1].setWidth("100%");
            }
          }
        }
      }
    }
    if (_viewstate[this._id]["VirtualScrolling"] && _viewstate[this._id]["scrollTop"] != _data_part_div.scrollTop) {
      if (_scrolling_timeout_id) {
        clearTimeout(_scrolling_timeout_id);
      }
      _tableview_id = this._id;
      _scrolling_timeout_id = setTimeout(_scrolling_timeout, 750);
      var _total_pages = _viewstate[this._id + "_pg"]["_TotalPages"];
      var _page_index = _viewstate[this._id + "_pg"]["PageIndex"];
      var _rows_count = _viewstate[this._id]["RowsCount"];
      var _total_rows = _viewstate[this._id + "_pg"]["_TotalRows"];
      var _page_size = _viewstate[this._id + "_pg"]["PageSize"];
      var _page_overlap = _viewstate[this._id + "_pg"]["PageOverlap"];
      var _data_table = _obj(this._id + "_data");
      var _data_table_height = _data_table.offsetHeight;
      var _data_part_div_height = _data_part_div.offsetHeight;
      var _scrollTop = _data_part_div.scrollTop;
      var _view_page_index = _page_index;
      if (_page_index < _total_pages - 1) {
        if (_scrollTop < (_data_table_height * _page_index)) {
          _view_page_index = Math.floor((_scrollTop + 60) / _data_table_height);
        } else {
          _view_page_index = Math.floor((_scrollTop + (_data_part_div_height - 60)) / _data_table_height);
        }
      } else {
        if (_scrollTop < (_data_table_height * (_total_rows - _rows_count) / _rows_count)) {
          _view_page_index = Math.floor((_scrollTop + 60) / (_data_table_height * (_page_size - _page_overlap) / _rows_count));
        }
      }
      this._show_scrolling_indicator(1);
      var _indicator = _obj(this._id + "_scrolling_indicator");
      var _template = "Page {page_number}";
      _indicator.innerHTML = _template.replace("{page_number}", _view_page_index + 1);
      _indicator.style.right = "17px";
      _indicator.style.top = (_header_part_div.offsetHeight + ((_data_part_div_height - 48) * (_data_part_div_height - 48) / (2 * _data_part_div.scrollHeight)) + Math.round(_scrollTop * (_data_part_div_height - 48) / _data_part_div.scrollHeight)) + "px";
      if (_scrolling_indicator_timeout_id) {
        clearTimeout(_scrolling_indicator_timeout_id);
      }
      _scrolling_indicator_timeout_id = setTimeout(_scrolling_off_page_indicator, 2000);
    }
    _footer_part_div.scrollLeft = _header_part_div.scrollLeft = _data_part_div.scrollLeft;
    if (_viewstate[_grid._id]["ClientSettings"]["Scrolling"]["SaveScrollingPosition"]) {
      _viewstate[this._id]["scrollTop"] = _data_part_div.scrollTop;
      _viewstate[this._id]["scrollLeft"] = (_frozen_columns_count > 0) ? _frozen_scroller_div.scrollLeft : _data_part_div.scrollLeft;
      _grid._saveViewState(_viewstate);
    }
  },
  _handle_scrolling_timeout: function () {
    var _data_part_div = _goParentNode(_obj(this._id + "_data"));
    var _grid = _getGrid(_obj(this._id));
    var _viewstate = _grid._loadViewState();
    var _total_pages = _viewstate[this._id + "_pg"]["_TotalPages"];
    var _page_index = _viewstate[this._id + "_pg"]["PageIndex"];
    var _rows_count = _viewstate[this._id]["RowsCount"];
    var _total_rows = _viewstate[this._id + "_pg"]["_TotalRows"];
    var _page_size = _viewstate[this._id + "_pg"]["PageSize"];
    var _page_overlap = _viewstate[this._id + "_pg"]["PageOverlap"];
    var _data_table = _obj(this._id + "_data");
    var _data_table_height = _data_table.offsetHeight;
    var _data_part_div_height = _data_part_div.offsetHeight;
    var _scrollTop = _data_part_div.scrollTop;
    var _view_page_index = _page_index;
    if (_page_index < _total_pages - 1) {
      if (_scrollTop < (_data_table_height * _page_index)) {
        _view_page_index = Math.floor((_scrollTop + 60) / _data_table_height);
      } else {
        _view_page_index = Math.floor((_scrollTop + (_data_part_div_height - 60)) / _data_table_height);
      }
    } else {
      if (_scrollTop < (_data_table_height * (_total_rows - _rows_count) / _rows_count)) {
        _view_page_index = Math.floor((_scrollTop + 60) / (_data_table_height * (_page_size - _page_overlap) / _rows_count));
      }
    }
    if (_view_page_index != _page_index) {
      this.goPage(_view_page_index);
      _grid.commit();
    }
  },
  _show_scrolling_indicator: function (_bool) {
    var _data_part_div = _goParentNode(_obj(this._id + "_data"));
    var _indicator = _obj(this._id + "_scrolling_indicator");
    if (!_indicator) {
      _indicator = _newNode("div", _data_part_div);
      _indicator.id = this._id + "_scrolling_indicator";
      _setClass(_indicator, "kgrScrollingIndicator");
    }
    _setDisplay(_indicator, _bool);
  }
}
var _scrolling_timeout_id;
var _scrolling_indicator_timeout_id;
var _tableview_id;
function _scrolling_timeout() {
  if (_exist(_tableview_id)) {
    (new GridTableView(_tableview_id))._handle_scrolling_timeout();
  }
}
function _scrolling_off_page_indicator() {
  if (_exist(_tableview_id)) {
    (new GridTableView(_tableview_id))._show_scrolling_indicator(0);
  }
}
function KoolGrid(_id, _ajaxEnabled, _ajaxHandlePage) {
  this._id = _id;
  this.id = _id;
  this._ajaxEnabled = _ajaxEnabled;
  this._ajaxHandlePage = _ajaxHandlePage;
  this._eventhandles = new Array();
  this._init();
}
KoolGrid.prototype =
{
  _init: function () {
    var _this = _obj(this._id);
    var _grid_cmd = _obj(this._id + "_cmd");
    _grid_cmd.value = "";
    if (_getBrowser() == "firefox" && this._ajaxEnabled) {
      /*
       * Fix the width in % in Firefox. The issue is that when width of grid is 100% and ajax is enabled, grid in firefox
       * does not expand correctly.
       */
      var _width = _this.style.width;
      if (_index("%", _width) > -1) {
        var _grid_updatepanel = _obj(this._id + "_updatepanel");
        _grid_updatepanel.style.width = _width;
        _this.style.width = "100%";
      }
    }
    var _column_headers = _getElements("th", "kgrHeader", _this);
    for (var i = 0; i < _column_headers.length; i++) {
      var _column_header = _column_headers[i];
      _addEvent(_column_header, "mouseover", _column_header_mouseover, false);
      _addEvent(_column_header, "mouseout", _column_header_mouseout, false);
      _addEvent(_column_header, "mousedown", _column_header_mousedown, false);
      _addEvent(_column_header, "click", _column_header_click, false);
      _addEvent(_column_header, "dblclick", _column_header_dblclick, false);
      _column_header.onselectstart = _cancel_textselection;
      _column_header.ondragstart = _cancel_textselection;
      _column_header.onmousedown = _cancel_textselection;
      _column_header.style.MozUserSelect = "none";//Firefox
    }
    var _rows = _getElements("tr", "kgrRow", _this);
    for (var i = 0; i < _rows.length; i++) {
      _addEvent(_rows[i], "mouseover", _row_onmouseover, false);
      _addEvent(_rows[i], "mouseout", _row_onmouseout, false);
      _addEvent(_rows[i], "click", _row_onclick, false);
      _addEvent(_rows[i], "dblclick", _row_ondblclick, false);
      var _cells = _getElements("td", "kgrCell", _rows[i]);
      for (var j = 0; j < _cells.length; j++) {
        _addEvent(_cells[j], "mouseover", _cell_onmouseover, false);
        _addEvent(_cells[j], "mouseout", _cell_onmouseout, false);
        _addEvent(_cells[j], "click", _cell_onclick, false);
      }
    }
    var _elementnames = ["span", "div"];
    for (var j = 0; j < _elementnames.length; j++) {
      var _event_capture_elements = _getElements(_elementnames[j], "kgrECap", _this);
      for (var i = 0; i < _event_capture_elements.length; i++) {
        _addEvent(_event_capture_elements[i], "click", _stopPropagation, false);
      }
    }
    var _tableview_divs = _getElements("div", "kgrTableView", _this);
    for (var i = 0; i < _tableview_divs.length; i++) {
      (new GridTableView(_tableview_divs[i].id))._init(this);
    }
    var _filter_inputs = _getElements("input", "kgrFiEnTr", _this);
    for (var i = 0; i < _filter_inputs.length; i++) {
      _addEvent(_filter_inputs[i], "keypress", _filter_input_onkeypress, false);
    }
    var _text_inputs = _getElements("input", "kgrEnNoPo", _this);
    for (var i = 0; i < _text_inputs.length; i++) {
      _addEvent(_text_inputs[i], "keypress", _text_input_onkeypress, false);
    }
    if (_exist(_grid_list[this._id]) && _exist(_grid_list[this._id]["Focus"])) {
      var _object = _obj(_grid_list[this._id]["Focus"]);
      if (_exist(_object)) {
        try {
          _object.focus();
        } catch (ex) {
        }
      }
    }
    var _inputfocus_divs = _getElements("div", "kgrInputFocus", _this);
    if (_inputfocus_divs.length > 0) {
      if (_index("kgrBlurGrid", _getClass(_inputfocus_divs[0])) > 0) {
        var _blur_div = _newNode("div", _this);
        _blur_div.style.position = "absolute";
        _blur_div.style.backgroundColor = "white";
        _blur_div.style.opacity = "0.6";
        _blur_div.style.filter = "alpha(opacity=60)";
        _blur_div.style.left = "0px";
        _blur_div.style.top = "0px";
        _setHeight(_blur_div, _this.scrollHeight);
        _setWidth(_blur_div, _this.scrollWidth);
        var _dummy_inputfocus_div = _newNode("div", _goParentNode(_inputfocus_divs[0]));
        var _inputfocus_height = _inputfocus_divs[0].offsetHeight;
        var _inputfocus_width = _inputfocus_divs[0].offsetWidth;
        _setHeight(_dummy_inputfocus_div, _inputfocus_height);
        _setWidth(_dummy_inputfocus_div, _inputfocus_width);
        _inputfocus_divs[0].style.position = "absolute";
        _inputfocus_divs[0].style.zIndex = "1000";
        _setHeight(_inputfocus_divs[0], _inputfocus_height - parseInt(_getStyle(_inputfocus_divs[0], "padding-top")) - parseInt(_getStyle(_inputfocus_divs[0], "padding-bottom")));
        _setWidth(_inputfocus_divs[0], _inputfocus_width - parseInt(_getStyle(_inputfocus_divs[0], "padding-left")) - parseInt(_getStyle(_inputfocus_divs[0], "padding-right")));
      } else {
        var _grid_mt = _obj(this._id + "_mt");
        _grid_mt.style.display = "none";
        _this.appendChild(_inputfocus_divs[0]);
      }
    }
    var _viewstate = this._loadViewState();
    var _client_events = _viewstate[this._id]["ClientSettings"]["ClientEvents"];
    for (var _name in _client_events) {
      if (typeof _client_events[_name] != "function") //Mootools
        if (eval("typeof " + _client_events[_name] + " =='function'")) {
          this._eventhandles[_name] = eval(_client_events[_name]);
        }
    }
    if (!_exist(_grid_list[this._id])) {
      try {
        this._handleEvent("OnInit", {});
      } catch (ex) {
      }
    }
    try {
      this._handleEvent("OnLoad", {});
    } catch (ex) {
    }
    if (_exist(_grid_list[this._id])) {
      _post_load_events = _grid_list[this._id]["PostLoadEvent"];
      for (_name in _post_load_events) {
        if (typeof _post_load_events[_name] != "function") //Mootools
        {
          try {
            this._handleEvent(_name, _post_load_events[_name]);
          } catch (ex) {
          }
        }
      }
    }
    _grid_list[this._id] = {"PostLoadEvent": {}};
  },
  _addCommand: function (_id, _command, _args) {
    var _grid_cmd = _obj(this._id + "_cmd");
    var _cmds = new Object();
    if (_grid_cmd.value != "") {
      _cmds = eval("__=" + _base64_decode(_grid_cmd.value));
    }
    _cmds[_id] = {
      "Command": _command,
      "Args": _args
    };
    _grid_cmd.value = _base64_encode(JSON.stringify(_cmds));
  },
  _loadViewState: function () {
    var _input_viewstate = _obj(this._id + "_viewstate");
    var viewstate = JSON.parse(_base64_decode(_input_viewstate.value));
    return viewstate;
  },
  _saveViewState: function (_viewstate) {
    var _input_viewstate = _obj(this._id + "_viewstate");
    _input_viewstate.value = _base64_encode(JSON.stringify(_viewstate));
  },
  getMasterTable: function () {
    return (new GridTableView(this._id + "_mt"));
  },
  refresh: function () {
    this._addCommand(this._id, "Refresh", {});
  },
  attachData: function (_name, _value) {
    if (this._ajaxEnabled) {
      var _updatepanel = eval(this._id + "_updatepanel");
      _updatepanel.attachData(_name, _value);
    }
  },
  commit: function () {
    /* Not work with IE,
     if(this._isLoading)
     {
     return; //Make sure that there is no other requests while grid is uploading.
     }
     this._isLoading = true;
     */
    if (!this._handleEvent("OnBeforeGridCommit", {}))
      return;
    if (this._ajaxEnabled) {
      var _updatepanel = eval(this._id + "_updatepanel");
      _updatepanel.update((this._ajaxHandlePage != "") ? this._ajaxHandlePage : null);
    } else {
      var _form = _obj(this._id);
      while (_form.nodeName != "FORM") {
        if (_form.nodeName == "BODY")
          return;//do nothing
        _form = _goParentNode(_form);
      }
      _form.submit();
    }
    var _status_divs = _getElements("div", "kgrStatus", _obj(this._id));
    for (var i = 0; i < _status_divs.length; i++) {
      _addClass(_status_divs[i], "kgrLoading");
    }
    this._registerPostLoadEvent("OnGridCommit", {});
  },
  _handleEvent: function (_name, _arg) {
    return (_exist(this._eventhandles[_name])) ? this._eventhandles[_name](this, _arg) : true;
  },
  _registerPostLoadEvent: function (_name, _arg) {
    _grid_list[this._id]["PostLoadEvent"][_name] = _arg;
  }
}
function _getGrid(_this) {
  var _div_grid = _goParentNode(_this);
  while (_div_grid.nodeName != "DIV" || _index("KGR", _getClass(_div_grid)) < 0) {
    _div_grid = _goParentNode(_div_grid);
    if (_div_grid.nodeName == "BODY")
      return null;
  }
  return eval(_div_grid.id);
}
function _getTableView(_this) {
  var _element = _goParentNode(_this);
  while (_index("kgrTableView", _getClass(_element)) < 0 && _index("kgrInsertForm", _getClass(_element)) < 0) {
    _element = _goParentNode(_element);
  }
  var _id = _element.id;
  if (_index("kgrTableView", _getClass(_element)) < 0) {
    _id = _id.replace("_nr_insertform", "");
  }
  return (new GridTableView(_id));
}
function _getRow(_this) {
  var _element = _goParentNode(_this);
  while (_index("kgrRow", _getClass(_element)) < 0 && _index("kgrEditForm", _getClass(_element)) < 0) {
    _element = _goParentNode(_element);
  }
  var _id = _element.id;
  if (_index("kgrRow", _getClass(_element)) < 0) {
    _id = _id.replace("_editform", "");
  }
  return (new GridRow(_id));
}
function get_row(_this) {
  return _getRow(_this);
}
function get_tableview(_this) {
  return _getTableView(_this);
}
function get_grid(_this) {
  return _getGrid(_this);
}
function grid_gopage(_this, _page_index) {
  _getTableView(_this).goPage(_page_index);
  _getGrid(_this).commit();
}
function grid_pagesize_select_onchange(_this) {
  var _page_size = _this.options[_this.selectedIndex].value;
  _getTableView(_this).changePageSize(_page_size);
  _getGrid(_this).commit();
}
function grid_pageoverlap_select_onchange(_this) {
  var _page_overlap = _this.options[_this.selectedIndex].value;
  _getTableView(_this).changePageOverlap(_page_overlap);
  _getGrid(_this).commit();
}
function grid_delete(_this) {
  var _grid = _getGrid(_this);
  var _viewstate = _grid._loadViewState();
  var _message = _viewstate[_grid._id]["ClientSettings"]["ClientMessages"]["DeleteConfirm"];
  if (_message != "") {
    if (confirm(_message)) {
      _getRow(_this).del();
      _getGrid(_this).commit();
    }
  } else {
    _getRow(_this).del();
    _getGrid(_this).commit();
  }
}
function grid_toggle_select(_this) {
  if (_index("kgrSelectAllRows", _getClass(_this)) > -1) {
    var _tableview = _getTableView(_this);
    if (_this.checked) {
      _tableview.selectAllRows();
    } else {
      _tableview.deselectAllRows();
    }
  } else if (_index("kgrSelectSingleRow", _getClass(_this)) > -1) {
    var _row = _getRow(_this);
    if (_this.checked) {
      _row.select();
    } else {
      _row.deselect();
    }
  }
}
function grid_edit(_this) {
  _getRow(_this).startEdit();
  _getGrid(_this).commit();
}
function grid_confirm_edit(_this) {
  if (_getRow(_this).confirmEdit())
    _getGrid(_this).commit();
}
function grid_cancel_edit(_this) {
  _getRow(_this).cancelEdit();
  _getGrid(_this).commit();
}
function grid_confirm_insert(_this) {
  if (_getTableView(_this).confirmInsert())
    _getGrid(_this).commit();
}
function grid_cancel_insert(_this) {
  _getTableView(_this).cancelInsert();
  _getGrid(_this).commit();
}
function grid_insert(_this) {
  _getTableView(_this).startInsert();
  _getGrid(_this).commit();
}
function grid_refresh(_this) {
  var _grid = _getGrid(_this);
  _grid.refresh();
  _grid.commit();
}
function tableview_refresh(_this) {
  var _tableview = _getTableView(_this);
  _tableview.refresh();
  _getGrid(_this).commit();
}
function grid_expand(_this) {
  _getRow(_this).expand();
  _getGrid(_this).commit();
}
function grid_collapse(_this) {
  _getRow(_this).collapse();
  _getGrid(_this).commit();
}
function grid_sort(_id, _direction, _order) {
  (new GridColumn(_id)).sort(_direction, _order);
  _getGrid(_obj(_id)).commit();
}
function grid_group_toogle(_this) {
  var _tr = _goParentNode(_this, 3);
  var _span_expands = _getElements("span", "kgrExpand", _tr);
  if (_span_expands.length > 0) {
    (new GridGroup(_tr.id)).collapse();
  } else {
    (new GridGroup(_tr.id)).expand();
  }
  _getGrid(_this).commit();
}
function grid_groupitem_sort(_groupitem_id, _sort) {
  var _groupitem_th = _obj(_groupitem_id);
  var _grid = _getGrid(_groupitem_th);
  _grid._addCommand(_groupitem_id, "Sort", {"Sort": _sort});
  _grid.commit();
}
function grid_filter_trigger(_col_id, _this) {
  var _grid = _getGrid(_this);
  var _column = new GridColumn(_col_id);
  var kac = window['kac_' + _this.id];
  var actions = ['AddFilter', 'RemoveFilter'];
  if (_index("_filter_select", _this.id) > 0) {
    var _expression = _this.options[_this.selectedIndex].value;
    if (actions.indexOf(_expression) === -1) {
      _column.filter(_expression, null, true);
      _grid.commit();
    }
    else 
      grid_filter_action(_col_id, _this, _expression);
  } else if (! kac || ! kac.isOpening()) {
    var _el = _this;
    while (_el.className.indexOf('kgrIn') === -1)
      _el = _el.parentElement;
    var _filter_select = _el.getElementsByTagName('select')[0];
    var _expression = _filter_select.options[_filter_select.selectedIndex].value;
    if (_expression != "No_Filter") {
      if (_this.nodeName == "INPUT" && _this.type == "text") {
        var _viewstate = _grid._loadViewState();
        var _old_value = unescape(_viewstate[_col_id]["Filter"]["Value"]);
        if (_this.value != _old_value) {
          _column.filter(_expression, null, true);
          _grid.commit();
        }
      } else {
        _column.filter(_expression, null, true);
        _grid.commit();
      }
    }
  }
}
function grid_filter_action(_col_id, _select, _expression) {
  var _grid = _getGrid(_select);
  var _column = new GridColumn(_col_id);
  if (_expression === 'AddFilter') {
    _column.addFilter();
    _grid.commit();
  }
  else if (_expression === 'RemoveFilter') {
    var _el = _select;
    while (_el.className.indexOf('kgrIn') === -1)
      _el = _el.parentElement;
    var _filterCell = _el.parentElement;
    var _index = -1;
    for (var i=0; i<_filterCell.children.length; i+=1)
      if (_filterCell.children[i] === _el) {
        _index = i;
        break;
      }
    _column.removeFilter(_index);
    _grid.commit();
  }
}
function grid_autocomplete_filter_trigger(kac) {
  var filterInput = kac.getAttachTo();
  if (filterInput) {
    var colId = filterInput.id.replace('_filter_input', '');
    grid_filter_trigger(colId, filterInput);
  }
}
function _filter_input_onkeypress(_e) {
  var _key = _e.keyCode;
  if (_key == 13) {
    var _grid = _getGrid(this);
    var _col_id = this.id.replace("_filter_input", "");
    _grid_list[_grid._id]["Focus"] = this.id;
    grid_filter_trigger(_col_id, this);
    return _preventDefaut(_e);
  }
}
function _text_input_onkeypress(_e) {
  if (_e.keyCode == 13) {
    return _preventDefaut(_e);
  }
}
var _resizing_column_id = null;
var _is_resizing = false;
var _resizing_current_x = 0;
var _is_dragging_to_group = false;
var _dragging_to_group_column_id = null;
var _dropped_groupitem_id = null;
var _dragging_groupitem_id = null;
function _column_header_mouseover(_e) {
  var _id = this.id.replace("_hd", "");
  (new GridColumn(_id))._handle_column_header_mouseover(_e);
}
function _column_header_mouseout(_e) {
  var _id = this.id.replace("_hd", "");
  (new GridColumn(_id))._handle_column_header_mouseout(_e);
}
function _column_header_click(_e) {
  var _id = this.id.replace("_hd", "");
  (new GridColumn(_id))._handle_column_header_click(_e);
}
function _column_header_dblclick(_e) {
  var _id = this.id.replace("_hd", "");
  (new GridColumn(_id))._handle_column_header_dblclick(_e);
}
function _column_header_mousedown(_e) {
  var _id = this.id.replace("_hd", "");
  (new GridColumn(_id))._handle_column_header_mousedown(_e);
  return false;
}
function _resizing_document_mouseup(_e) {
  (new GridColumn(_resizing_column_id))._handle_resizing_column_header_mouseup(_e);
}
function _resizing_document_mousemove(_e) {
  (new GridColumn(_resizing_column_id))._handle_resizing_column_header_mousemove(_e);
}
function _dragging_to_group_document_mouseup(_e) {
  (new GridColumn(_dragging_to_group_column_id))._handle_dragging_to_group_column_header_mouseup(_e);
}
function _dragging_to_group_document_mousemove(_e) {
  (new GridColumn(_dragging_to_group_column_id))._handle_dragging_to_group_column_header_mousemove(_e);
}
function _row_onmouseover(_e) {
  (new GridRow(this.id))._handle_mouseover(_e);
}
function _row_onmouseout(_e) {
  (new GridRow(this.id))._handle_mouseout(_e);
}
function _row_onclick(_e) {
  (new GridRow(this.id))._handle_click(_e);
}
function _row_ondblclick(_e) {
  (new GridRow(this.id))._handle_dblclick(_e);
}
function _data_part_div_onscroll(_e) {
  _tableview = _getTableView(this);
  _tableview._handle_data_part_div_onscroll(_e);
}
function _itch(_tableview_id) //TableView Check Height
{
  (new GridTableView(_tableview_id))._check_height_in_scrolling_mode();
}
function grid_on_datetimepicker_open(_id) {
  var _this = _obj(_id + "_bound");
  var _in = _goParentNode(_this);
  var _out = _goParentNode(_in);
  var _width = _out.offsetWidth;
  _in.style.width = _width + "px";
  _out.style.width = _width + "px";
  _addClass(_out, "kgrDateTimePickerOpening");
  if (_getBrowser() != "firefox") {
    var _tableview = _getTableView(_this);
    var _div = _goParentNode(_out, 2);
    var _div_part_data = _goParentNode(_obj(_tableview._id + "_data"));
    _in.style.left = (_in.offsetLeft - _div_part_data.scrollLeft) + "px";
    if (_getClass(_div) != "kgrIn") {
      _in.style.top = (_in.offsetTop - _div_part_data.scrollTop) + "px";
    }
  }
}
function grid_on_datetimepicker_close(_id) {
  var _this = _obj(_id + "_bound");
  var _in = _goParentNode(_this);
  var _out = _goParentNode(_in);
  _removeClass(_out, "kgrDateTimePickerOpening");
  _in.style.width = _in.style.top = _in.style.left = "";
  _out.style.width = "";
}
var ExpandList = function (_id) {
  var _ul = _obj(_id);
  var _children = _ul.children, l = _children.length, i = 0;
  var _expand = function ( ) {
    var _child = _children[ i ];
    if (_child)
      _child.style.display = '';
    i += 1;
    if (i < l)
      setTimeout(_expand, 10);
    else if (i === l) {
      var _lastLi = _ul.lastChild;
      _lastLi.style.display = 'none';
      var _a = _ul.nextSibling;
      _a.style.display = 'none';
      _a = _a.nextSibling;
      _a.style.display = '';
    }
  };
  setTimeout(_expand, 10);
};
var CollapseList = function (_id, _numberOfItems) {
  var _ul = _obj(_id);
  var _children = _ul.children, l = _children.length, i = l - 1;
  var _collapse = function ( ) {
    if (i > _numberOfItems - 1) {
      var _child = _children[ i ];
      if (_child)
        _child.style.display = 'none';
      if (i === l - 1) {
        var _lastLi = _ul.lastChild;
        _lastLi.style.display = '';
      }
      setTimeout(_collapse, 10);
    } else if (i === _numberOfItems - 1) {
      var _a = _ul.nextSibling;
      _a.style.display = '';
      _a = _a.nextSibling;
      _a.style.display = 'none';
    }
    i -= 1;
  };
  setTimeout(_collapse, 10);
};
var _grid_list = new Array();
if (typeof (__KGRInits) != 'undefined' && _exist(__KGRInits)) {
  for (var i = 0; i < __KGRInits.length; i++) {
    __KGRInits[i]();
  }
}
/*
 * Client-event list:
 * 
 * OnInit
 * OnBeforeGridCommit
 * OnGridCommit
 * OnRowMouseOver
 * OnRowMouseOut
 * OnBeforeRowSelect
 * OnRowSelect
 * OnBeforeRowDeselect
 * OnRowDeselect
 * OnRowClick
 * OnRowDoubleClick
 * OnColumnMouseOver
 * OnColumnMouseOut
 * OnColumnClick
 * OnColumnDoubleClick
 * OnBeforeColumnResize
 * OnColumnResize
 * OnBeforeDetailTablesExpand
 * OnDetailTablesExpand
 * OnBeforeDetailTableCollapse
 * OnDetailTableCollapse
 * OnBeforeColumnSort
 * OnColumnSort
 * OnBeforeColumnGroup
 * OnColumnGroup
 * OnBeforeColumnFilter
 * OnColumnFilter
 * OnBeforePageChange
 * OnPageChange
 * OnBeforeRowStartEdit
 * OnRowStartEdit
 * OnBeforeRowConfirmEdit
 * OnRowConfirmEdit
 * OnBeforeRowCancelEdit
 * OnRowCancelEdit
 * 
 */
