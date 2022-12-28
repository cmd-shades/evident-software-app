(function (global) {
  global.hasOwnProperty = global.hasOwnProperty || global.Object.prototype.hasOwnProperty;
  if (!String.prototype.trim) {
    String.prototype.trim = function () {
      return this.replace(/^\s+|\s+$/g, '');
    };
  }
  if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (searchElement, fromIndex) {
      if (this === undefined || this === null) {
        throw new TypeError('"this" is null or not defined');
      }
      var length = this.length >>> 0; // Hack to convert object.length to a UInt32
      fromIndex = +fromIndex || 0;
      if (Math.abs(fromIndex) === Infinity) {
        fromIndex = 0;
      }
      if (fromIndex < 0) {
        fromIndex += length;
        if (fromIndex < 0) {
          fromIndex = 0;
        }
      }
      for (; fromIndex < length; fromIndex++) {
        if (this[fromIndex] === searchElement) {
          return fromIndex;
        }
      }
      return -1;
    };
  }
  function _obj(_id)
  {
    return document.getElementById(_id);
  }
  var _exist = function (_theObj)
  {
    return (_theObj != null);
  };
  if (!_exist(_identity))
  {
    var _identity = 0;
  }
  function _getIdentity()
  {
    _identity++;
    return _identity;
  }
  var _index = function (_search, _original)
  {
    return _original.indexOf(_search);
  };
  function _replace(_search, _rep, _str)
  {
    return _str.replace(eval("/" + _search + "/g"), _rep);
  }
  var _setAttribute = function (_o, _name, _value)
  {
    _o.attr(_name, _value);
  };
  function _newNode(_sTag, _oParent)
  {
    var _oNode = document.createElement(_sTag);
    _oParent.appendChild(_oNode);
    return _oNode;
  }
  var _newSVGNode = function (_sTag, _oParent)
  {
    var _oNode = document.createElementNS("http://www.w3.org/2000/svg", _sTag);
    _oParent.appendChild(_oNode);
    return _oNode;
  };
  function _getClass(_theObj)
  {
    return _theObj.className;
  }
  function _getElements(_tag, _class, _parent)
  {
    _parent = _exist(_parent) ? _parent : document.body;
    var _elements = _parent.getElementsByTagName(_tag);
    var _result = new Array();
    for (var i = 0; i < _elements.length; i++)
      if (_elements[i].className.indexOf(_class) >= 0)
      {
        _result.push(_elements[i]);
      }
    return _result;
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
    }
    else {
      return false;
    }
  }
  var _goParentNode = function (_theObj, _level)
  {
    if (!_exist(_level))
      _level = 1;
    for (var i = 0; i < _level; i++)
      _theObj = _theObj.parentNode;
    return _theObj;
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
    dec = _utf8_decode(dec);
    return dec;
  }
  var _addCircle = function (_x, _y, _radius, _pp, _group)
  {
    var _circle = _pp.circle(_x, _y, _radius);
    if (_group)
      _group.push(_circle);
    return _circle;
  };
  var _get_triagles_d = function (_x, _y, _length)
  {
    var _x1 = _x - _length / 2;
    var _x2 = _x + _length / 2;
    var _y1 = _y + _length * Math.sqrt(3) / 2;
    var _y2 = _y1;
    return "M" + _x + " " + _y + " L" + _x1 + " " + _y1 + " L" + _x2 + " " + _y2 + " z";
  };
  var _addTriangles = function (_x, _y, _length, _pp, _group)
  {
    var _path = _pp.path(_get_triagles_d(_x, _y, _length));
    if (_group)
      _group.push(_path);
    return _path;
  };
  var _get_arc_d = function (_cx, _cy, _radius, _start_angle, _degree)
  {
    var _start_angle_rad = (_start_angle * Math.PI / 180);
    var _start_point_x = _cx + _radius * Math.cos(_start_angle_rad);
    var _start_point_y = _cy + _radius * Math.sin(_start_angle_rad);
    var _degree_rad = (_degree * Math.PI / 180);
    var _end_point_x = _cx + _radius * Math.cos(_start_angle_rad + _degree_rad);
    var _end_point_y = _cy + _radius * Math.sin(_start_angle_rad + _degree_rad);
    if (_degree !== 360) {
      var _path = "M" + _cx + "," + _cy + " L " + _start_point_x + "," + _start_point_y + " A" + _radius + "," + _radius + " 0 " + ((_degree > 180) ? 1 : 0) + ",1 " + _end_point_x + "," + _end_point_y;
      _path += ' z';
    }
    else {
      var _degree_rad2 = (_degree/2 * Math.PI / 180);
      var _end_point_x2 = _cx + _radius * Math.cos(_start_angle_rad + _degree_rad2);
      var _end_point_y2 = _cy + _radius * Math.sin(_start_angle_rad + _degree_rad2);
      var _path = "M" + _start_point_x + "," + _start_point_y + " A" + _radius + "," + _radius + " 0 " + ((_degree > 180) ? 1 : 0) + ",1 " + _end_point_x2 + "," + _end_point_y2;
      _path += " M" + _end_point_x2 + "," + _end_point_y2;
      _path += " A" + _radius + "," + _radius + " 0 " + ((_degree > 180) ? 1 : 0) + ",1 " + _end_point_x + "," + _end_point_y;
    }
    return _path;
  };
  var _addArc = function (_cx, _cy, _radius, _start_angle, _degree, _pp, _group)
  {
    var _path = _pp.path(_get_arc_d(_cx, _cy, _radius, _start_angle, _degree));
    if (_group)
      _group.push(_path);
    return _path;
  };
  var _get_fourangles_d = function (x1, y1, x2, y2, x3, y3, x4, y4)
  {
    return "M" + x1 + " " + y1 + " L" + x2 + " " + y2 + " L" + x2 + " " + y2 + " L" + x3 + " " + y3 + " L" + x4 + " " + y4 + " z";
  };
  var _addFourAngles = function (x1, y1, x2, y2, x3, y3, x4, y4, _pp, _group)
  {
    var _path = _pp.path(_get_fourangles_d(x1, y1, x2, y2, x3, y3, x4, y4));
    if (_group)
      _group.push(_path);
    return _path;
  };
  var _colorNameToHex = function (colour)
  {
    var colours = {"aliceblue": "#f0f8ff", "antiquewhite": "#faebd7", "aqua": "#00ffff", "aquamarine": "#7fffd4", "azure": "#f0ffff",
      "beige": "#f5f5dc", "bisque": "#ffe4c4", "black": "#000000", "blanchedalmond": "#ffebcd", "blue": "#0000ff", "blueviolet": "#8a2be2", "brown": "#a52a2a", "burlywood": "#deb887",
      "cadetblue": "#5f9ea0", "chartreuse": "#7fff00", "chocolate": "#d2691e", "coral": "#ff7f50", "cornflowerblue": "#6495ed", "cornsilk": "#fff8dc", "crimson": "#dc143c", "cyan": "#00ffff",
      "darkblue": "#00008b", "darkcyan": "#008b8b", "darkgoldenrod": "#b8860b", "darkgray": "#a9a9a9", "darkgreen": "#006400", "darkkhaki": "#bdb76b", "darkmagenta": "#8b008b", "darkolivegreen": "#556b2f",
      "darkorange": "#ff8c00", "darkorchid": "#9932cc", "darkred": "#8b0000", "darksalmon": "#e9967a", "darkseagreen": "#8fbc8f", "darkslateblue": "#483d8b", "darkslategray": "#2f4f4f", "darkturquoise": "#00ced1",
      "darkviolet": "#9400d3", "deeppink": "#ff1493", "deepskyblue": "#00bfff", "dimgray": "#696969", "dodgerblue": "#1e90ff",
      "firebrick": "#b22222", "floralwhite": "#fffaf0", "forestgreen": "#228b22", "fuchsia": "#ff00ff",
      "gainsboro": "#dcdcdc", "ghostwhite": "#f8f8ff", "gold": "#ffd700", "goldenrod": "#daa520", "gray": "#808080", "green": "#008000", "greenyellow": "#adff2f",
      "honeydew": "#f0fff0", "hotpink": "#ff69b4",
      "indianred ": "#cd5c5c", "indigo ": "#4b0082", "ivory": "#fffff0", "khaki": "#f0e68c",
      "lavender": "#e6e6fa", "lavenderblush": "#fff0f5", "lawngreen": "#7cfc00", "lemonchiffon": "#fffacd", "lightblue": "#add8e6", "lightcoral": "#f08080", "lightcyan": "#e0ffff", "lightgoldenrodyellow": "#fafad2",
      "lightgrey": "#d3d3d3", "lightgreen": "#90ee90", "lightpink": "#ffb6c1", "lightsalmon": "#ffa07a", "lightseagreen": "#20b2aa", "lightskyblue": "#87cefa", "lightslategray": "#778899", "lightsteelblue": "#b0c4de",
      "lightyellow": "#ffffe0", "lime": "#00ff00", "limegreen": "#32cd32", "linen": "#faf0e6",
      "magenta": "#ff00ff", "maroon": "#800000", "mediumaquamarine": "#66cdaa", "mediumblue": "#0000cd", "mediumorchid": "#ba55d3", "mediumpurple": "#9370d8", "mediumseagreen": "#3cb371", "mediumslateblue": "#7b68ee",
      "mediumspringgreen": "#00fa9a", "mediumturquoise": "#48d1cc", "mediumvioletred": "#c71585", "midnightblue": "#191970", "mintcream": "#f5fffa", "mistyrose": "#ffe4e1", "moccasin": "#ffe4b5",
      "navajowhite": "#ffdead", "navy": "#000080",
      "oldlace": "#fdf5e6", "olive": "#808000", "olivedrab": "#6b8e23", "orange": "#ffa500", "orangered": "#ff4500", "orchid": "#da70d6",
      "palegoldenrod": "#eee8aa", "palegreen": "#98fb98", "paleturquoise": "#afeeee", "palevioletred": "#d87093", "papayawhip": "#ffefd5", "peachpuff": "#ffdab9", "peru": "#cd853f", "pink": "#ffc0cb", "plum": "#dda0dd", "powderblue": "#b0e0e6", "purple": "#800080",
      "red": "#ff0000", "rosybrown": "#bc8f8f", "royalblue": "#4169e1",
      "saddlebrown": "#8b4513", "salmon": "#fa8072", "sandybrown": "#f4a460", "seagreen": "#2e8b57", "seashell": "#fff5ee", "sienna": "#a0522d", "silver": "#c0c0c0", "skyblue": "#87ceeb", "slateblue": "#6a5acd", "slategray": "#708090", "snow": "#fffafa", "springgreen": "#00ff7f", "steelblue": "#4682b4",
      "tan": "#d2b48c", "teal": "#008080", "thistle": "#d8bfd8", "tomato": "#ff6347", "turquoise": "#40e0d0",
      "violet": "#ee82ee",
      "wheat": "#f5deb3", "white": "#ffffff", "whitesmoke": "#f5f5f5",
      "yellow": "#ffff00", "yellowgreen": "#9acd32"};
    if (typeof colours[colour.toLowerCase()] != 'undefined')
      return colours[colour.toLowerCase()];
    return false;
  };
  var _hexToRgb = function (hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? [
      parseInt(result[1], 16),
      parseInt(result[2], 16),
      parseInt(result[3], 16)
    ] : null;
  };
  var _rgbToHex = function (r, g, b) {
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
  };
  var _brightness = function (_color, _change)
  {
    if (_index("#", _color) < 0)
    {
      _color = _colorNameToHex(_color);
      if (!_color)
        alert("Color is unknown");
    }
    var _rgb = _hexToRgb(_color);
    for (var i = 0; i < 3; i++)
    {
      _rgb[i] = Math.round(_rgb[i] * _change);
    }
    return _rgbToHex(_rgb[0], _rgb[1], _rgb[2]);
  };
  var _blendwhite = function (_color, _portion)
  {
    if (_index("#", _color) < 0)
    {
      _color = _colorNameToHex(_color);
      if (!_color)
        alert("Color is unknown");
    }
    var _rgb = _hexToRgb(_color);
    for (var i = 0; i < 3; i++)
    {
      _rgb[i] = Math.round(_rgb[i] * (1 - _portion) + _portion * 256);
    }
    return _rgbToHex(_rgb[0], _rgb[1], _rgb[2]);
  };
  var _addGroup = function (_pp, _group) {
    var _sub_group = _pp.set();
    if (_group)
      _group.push(_sub_group);
    return _sub_group;
  };
  var _addRec = function (_x1, _y1, _x2, _y2, _pp, _group) {
    var _width = Math.abs(_x2 - _x1);
    var _height = Math.abs(_y2 - _y1);
    var _x = (_x1 < _x2) ? _x1 : _x2;
    var _y = (_y1 < _y2) ? _y1 : _y2;
    var _rec = _pp.rect(_x, _y, _width, _height);
    _setAttribute(_rec, "stroke-linejoin", "round");
    _setAttribute(_rec, "stroke-linecap", "square");
    _stroke(_rec, "none", 0, 0);
    if (_group)
      _group.push(_rec);
    return _rec;
  };
  var _addText = function (_content, _pp, _group, _anchor) {
    var _text = _pp.text(0, 0, _content);
    if (_group)
      _group.push(_text);
    if (_anchor)
      _text.attr("text-anchor", _anchor);
    return _text;
  };
  var _get_line_d = function (_x1, _y1, _x2, _y2) {
    return "M" + _x1 + " " + _y1 + " L" + _x2 + " " + _y2;
  };
  var _addLine = function (_x1, _y1, _x2, _y2, _pp, _group) {
    var _line = _pp.path(_get_line_d(_x1, _y1, _x2, _y2));
    if (_group !== null)
      _group.push(_line);
    return _line;
  };
  var _style = function (_o, _font_size, _font_family, _font_color, _font_style, _font_weight) {
    if (_font_size)
      _o.attr("font-size", _font_size);
    if (_font_family)
      _o.attr("font-family", _font_family);
    if (_font_color)
      _o.attr("fill", _font_color);
    if (_font_style)
      _o.attr("font-style", _font_style);
    if (_font_weight)
      _o.attr("font-weight", _font_weight);
  };
  var _stroke = function (_o, _color, _width, _opacity, _fill, _fill_opacity) {
    _o.attr("stroke", _color);
    _o.attr("stroke-width", _width);
    _o.attr("stroke-opacity", _opacity);
    _o.attr("fill", _fill);
    _o.attr("fill-opacity", _fill_opacity);
  };
  var _setPosition = function (_o, _x, _y) {
    if (_x)
      _setAttribute(_o, "x", _x);
    if (_y)
      _setAttribute(_o, "y", _y);
  };
  var _setRec = function (_path, _x1, _y1, _x2, _y2) {
    var _width = Math.abs(_x2 - _x1);
    var _height = Math.abs(_y2 - _y1);
    var _x = (_x1 < _x2) ? _x1 : _x2;
    var _y = (_y1 < _y2) ? _y1 : _y2;
    _path.attr("x", _x);
    _path.attr("y", _y);
    _path.attr("width", _width);
    _path.attr("height", _height);
  };
  var _fill = function (_o, _color) {
    _o.attr("fill", _color);
  };
  var _translate = function (_o, _x, _y, _clear) {
    _o.translate(_x, _y);
  };
  var _rotate = function (_o, _degree, _dx, _dy) {
    if (_dx !== null && _dy !== null)
    {
      _o.rotate(_degree, _dx, _dy);
    }
    else
    {
      _o.rotate(_degree);
    }
  };
  var _number_format = function (format, decimals, dec_point, thousands_sep, number) {
    if (!format)
    {
      return number;
    }
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
              var k = Math.pow(10, prec);
              return '' + Math.round(n * k) / k;
            };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
  };
  var _createText = function (_pp, _info, _parent) {
    var _kf = _key._font,
            _container;
    if (_parent)
      _container = _addGroup(_pp, _parent);
    else
      _container = _addGroup(_pp);
    if (_info.id)
      _container.id = _info.id;
    var _textElement = _addText(_info.text, _pp, _container);
    if (_info.format) {
      var _format = _info.format;
      _style(_textElement, _format[_kf._size], _format[_kf._fam],
              _format[_kf._color], _format[_kf._style], _format[_kf._weight]);
    }
    return _container;
  };
  var _createLine = function (_pp, _info, _parent) {
    var _line = _addLine(_info[0], _info[1], _info[2], _info[3], _pp, _parent);
    _stroke(_line, _info[4]);
    return _line;
  };
  var _get_chart = function (_this) {
    var _id = (_goParentNode(_this, 2)).id;
    return KoolChartJS.getChart(_id);
  };
  var _getFunctionByName = function (functionName, context) {
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for (var i = 0; i < namespaces.length; i++) {
      context = context[namespaces[i]];
    }
    return context[func];
  };
  var _defined = function (_object) {
    return (_object !== null && typeof _object !== 'undefined');
  };
  var _notDefined = function (_object) {
    return (_object === null || typeof _object === 'undefined');
  };
  var KoolPHP = {
    recursiveMerge: function (o1, o2) {
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
      _merge: function (_object) {
        for (var _name in _object)
          if (_object.hasOwnProperty(_name))
            this[_name] = _object[_name];
        return this;
      },
      _alert: function (_property) {
        alert(this[_property]);
      },
      _koolClass: "koolObject",
      _id: null,
      _setId: function (_id) {
        this._id = _id;
      },
      _getId: function () {
        return this._id;
      },
      _selfIdentify: function () {
        alert(this._id + " " + this._koolClass);
      },
      _loadInput: function (str) {
        var _input = KoolPHP._domObj(this._getId() + str);
        return JSON.parse(_input.value);
      },
      _saveInput: function (str, value) {
        var _input = KoolPHP._domObj(this._getId() + str);
        _input.value = JSON.stringify(value);
      },
      _loadViewstate: function () {
        var _viewstate = this._loadInput("_viewstate");
        if (KoolPHP._isEmpty(_viewstate))
          _viewstate = {};
        return _viewstate;
      },
      _saveViewstate: function (_viewstate) {
        this._saveInput("_viewstate", _viewstate);
      }
    },
    _new: function (_object) {
      var _F = function () {
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
    _newObject: function (_baseObject) {
      var _newObject = this._new(this._koolObject);
      _newObject._merge(_baseObject);
      return _newObject;
    },
    _addEventHandler: function (elem, eventType, handler) {
      if (elem.addEventListener)
        elem.addEventListener(eventType, handler, false);
      else if (elem.attachEvent)
        elem.attachEvent('on' + eventType, handler);
    },
    _domObj: function (_id) {
      return document.getElementById(_id);
    },
    _isEmpty: function (obj) {
      if (typeof obj === 'number' ||
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
    }, 
    _defined: function (_object) {
      return (_object !== null && typeof _object !== 'undefined');
    },
    _notDefined: function (_object) {
      return (_object === null || typeof _object === 'undefined');
    },
    isArray: function( obj ) {
      return Object.prototype.toString.call( obj ) === "[object Array]";
    },
    _cloneArray: function (_arr) {
      return _arr.slice(0);
    },
    _cloneObject: function (_object) {
      return JSON.parse(JSON.stringify(_object));
    }
  };
  var _key = {
    _name: "Name",
    _title: "Title",
    _legend: "Legend",
    _appearance: "Appearance",
    _appearances: {
      _title: "TitleAppearance",
      _label: "LabelsAppearance",
      _tip: "TooltipsAppearance"
    },
    _visible: "Visible",
    _text: "Text",
    _color: "Color",
    _bgcolor: "BackgroundColor",
    _start: "start",
    _rotateAngle: "RotationAngle",
    _font: {
      _fam: "FontFamily",
      _size: "FontSize",
      _color: "FontColor",
      _style: "FontStyle",
      _weight: "FontWeight"
    },
    _align: "Align",
    _aligns: {
      _left: "left",
      _right: "right",
      _center: "center"
    },
    _transisions: "Transitions",
    _position: "Position",
    _positions: {
      _top: "top",
      _bottom: "bottom",
      _left: "left",
      _right: "right",
      _center: "center",
      _middle: "middle",
      _outside: "outside",
      _insideBase: "insidebase",
      _insideEnd: "insideend",
      _outsideEnd: "outsideend",
      _outsideBegin: "outsideBegin",
      _above: "above",
      _below: "below"
    },
    _plotArea: "PlotArea",
    _chart: {
      _type: "ChartType",
      _area: "area",
      _pie: "pie",
      _bar: "bar",
      _col: "column",
      _line: "line",
      _scat: "scatter",
      _scali: "scatterline"
    },
    _listSeries: "ListOfSeries",
    _items: "Items",
    _coordinate: {
      _yAxis: "YAxis",
      _xAxis: "XAxis",
      _yValue: "YValue",
      _xValue: "XValue",
      _minValue: "MinValue",
      _maxValue: "MaxValue",
      _minorStep: "MinorStep",
      _majorStep: "MajorStep",
      _minorLines: "MinorGridLines",
      _majorLines: "MajorGridLines",
      _minorTick: "MinorTickType",
      _majorTick: "MajorTickType",
      _minorTickSize: "MinorTickSize",
      _majorTickSize: "MajorTickSize"
    },
    _missing: {
      _value: "MissingValue",
      _zero: "zero",
      _inter: "interpolated",
      _gap: "gap"
    },
    _format: {
      _data: "DataFormatString",
      _number: "NumberFormat",
      _decimal: "DecimalNumber",
      _decSepa: "DecimalSeparator",
      _thousandSepa: "ThousandSeparator"
    },
    _region: {
      _width: "Width",
      _height: "Height",
      _padding: "Padding",
      _pad: 'Pad'
    },
    _direction: {
      _nirvana: -1,
      _vertical: 0,
      _horizontal: 1,
      _north: 2,
      _south: 4,
      _west: 3,
      _east: 5
    },
    _elementType: {
      _container: 'container',
      _text: 'text',
      _line: 'line',
      _rect: 'rectangular',
      _triAngle: 'triAngle',
      _fourAngle: 'fourangle',
      _cirle: 'cirle',
      _polygon: 'polygon'
    },
    _class: {
      _panel: 'panel',
      _text: 'text',
      _fig: 'fig',
      _title: 'title',
      _legend: 'legend',
      _LEGENDS: 'LEGENDS',
      _default: '_default'
    },
    _float: {
      _left: "left",
      _right: "right",
      _clearLeft: "clearLeft",
      _clearRight: "clearRight"
    },
    _pad: {
      _top: 0,
      _bottom: 1,
      _left: 2,
      _right: 3
    }
  };
  var _plotArea = (function () {
    var _kpad = _key._pad,
        _kp = _key._positions;
    return {
      _w: 0,
      _h: 0,
      _pad: [0, 0, 0, 0],
      _padTop: 0,
      _padBottom: 0,
      _padLeft: 0,
      _padRight: 0,
      _getAxisWidth: function () {
        return this._w - this._padLeft - this._padRight;
      },
      _getAxisHeight: function () {
        return this._h - this._padTop - this._padBottom;
      },
      _getWidth: function () {
        return this._w;
      },
      _getHeight: function () {
        return this._h;
      },
      _getPad: function (_p) {
        if (_p !== null)
          return this._pad[_p];
        else
          return this._pad;
      },
      _setPad: function (_pad) {
        if (_pad) {
          this._pad = _pad;
          this._padTop = _pad[_kpad._top];
          this._padBottom = _pad[_kpad._bottom];
          this._padLeft = _pad[_kpad._left];
          this._padRight = _pad[_kpad._right];
        }
        return this;
      },
      _setWidth: function (_w) {
        if (_w !== null)
          this._w = _w;
        return this;
      },
      _setHeight: function (_h) {
        if (_h !== null)
          this._h = _h;
        return this;
      },
      _addPad: function (_p, _s) {
        if (_p === _kp._top)
          this._padTop += _s;
        else if (_p === _kp._bottom)
          this._padBottom += _s;
        else if (_p === _kp._left)
          this._padLeft += _s;
        else if (_p === _kp._right)
          this._padRight += _s;
        return this;
      },
      _clone: function () {
        var _pl = _new(_plotArea);
        _pl._setWidth(this._w)
            ._setHeight(this._h)
            ._setPad([
              this._padTop,
              this._padBottom,
              this._padLeft,
              this._padRight
            ])
            ;
        return _pl;
      },
      _print: function (value) {
        alert(value
                + " \nWidth=" + this._w.toFixed()
                + " \nHeight=" + this._h.toFixed()
                + " \npadTop=" + this._padTop.toFixed()
                + " \npadBottom=" + this._padBottom.toFixed()
                + " \npadLeft=" + this._padLeft.toFixed()
                + " \npadRight=" + this._padRight.toFixed());
      },
      _getY: function (_height, _minusRange) {
        if (_notDefined(_minusRange))
          _minusRange = 0;
        return this._h - this._padBottom - _minusRange - _height;
      },
      _getX: function (_width, _minusRange) {
        if (_notDefined(_minusRange))
          _minusRange = 0;
        return this._padLeft + _minusRange + _width;
      }
    };
  }());
  var KC = {
    pad: {
      x: 3,
      y: 3,
      fig: 7,
      text: 15,
      legend: 10,
      _default: 10,
      element: 5,
      label_column: 10,
      label_point: 5
    }
  };
  var _element = {
    _paper: null,
    _container: null,
    _width: 0,
    _height: 0,
    _elements: null,
    _shape: null,
    _plotArea: null,
    _pad: KC.pad,
    _panel: null,
    _attributes: null,
    _mapProperty: function (_value) {
      var _map = {
        id: '_myId',
        hGap: '_myHGap',
        _class: '_myClass',
        clear: '_myClear',
        type: '_myType',
        defaultAngle: '_defaultAngle'
      };
      if (_map[_value])
        return _map[_value];
      else
        return _value;
    },
    _set: function (_attributes) {
      for (var _name in _attributes)
        if (_name && _attributes.hasOwnProperty(_name)) {
          this._attributes[this._mapProperty(_name)] = _attributes[_name];
        }
      return this;
    },
    _get: function (_name, _default) {
      var _value = this._attributes[this._mapProperty(_name)];
      if (_defined(_value))
        return _value;
      else
        return _default;
    },
    _isShape: function () {
      var
              _ket = _key._elementType,
              _type = this._get('type')
              ;
      return (
              _type &&
              _type !== _ket._container &&
              this._shape
              );
    },
    _isContainer: function () {
      var _ket = _key._elementType,
              _type = this._get('type')
              ;
      return (
              _type &&
              _type === _ket._container
              );
    },
    _addElements: function () {
      if (this._isContainer()) {
        for (var i = 0; i < arguments.length; i += 1) {
          this._elements.push(arguments[i]);
          var _shapes = arguments[i]._getShapes();
          for (var j = 0; j < _shapes.length; j += 1) {
            this._container.push(_shapes[j]);
          }
        }
      }
      return this;
    },
    _setPlotArea: function (_pl) {
      this._plotArea = _pl;
      return this;
    },
    _getPlotArea: function () {
      return this._plotArea;
    },
    _formatPanel: function (_f) {
      this._panel._format(_f);
      return this;
    },
    _getX: function () {
      if (this._isShape())
        return this._shape.getBBox().x;
      else
        return 0;
    },
    _getY: function () {
      if (this._isShape())
        return this._shape.getBBox().y;
      else
        return 0;
    },
    _getMinX: function () {
      if (this._isShape())
        return this._shape.getBBox().x;
      else if (this._isContainer()) {
        var _x = Number.MAX_VALUE;
        for (var i = 0; i < this._elements.length; i += 1) {
          var _ex = this._elements[i]._getMinX();
          if (_ex < _x)
            _x = _ex;
        }
        return _x;
      }
    },
    _getMinY: function () {
      if (this._isShape())
        return this._shape.getBBox().y;
      else if (this._isContainer()) {
        var _y = Number.MAX_VALUE;
        for (var i = 0; i < this._elements.length; i += 1) {
          var _ey = this._elements[i]._getMinY();
          if (_ey < _y)
            _y = _ey;
        }
        return _y;
      }
    },
    _getMaxX: function () {
      if (this._isShape())
        return this._shape.getBBox().x + this._shape.getBBox().width;
      else if (this._isContainer()) {
        var _x = -Number.MAX_VALUE;
        for (var i = 0; i < this._elements.length; i += 1) {
          var _ex = this._elements[i]._getMaxX();
          if (_ex > _x)
            _x = _ex;
        }
        return _x;
      }
    },
    _getMaxY: function () {
      if (this._isShape())
        return this._shape.getBBox().y + this._shape.getBBox().height;
      else if (this._isContainer()) {
        var _y = -Number.MAX_VALUE;
        for (var i = 0; i < this._elements.length; i += 1) {
          var _ey = this._elements[i]._getMaxY();
          if (_ey > _y)
            _y = _ey;
        }
        return _y;
      }
    },
    _fill: function (_color) {
      if (this._isShape()) {
        this._shape.attr("fill", _color);
      }
      else if (this._isContainer()) {
        this._panel._fill(_color);
      }
      return this;
    },
    _stroke: function (_color, _width, _opacity, _fill, _fill_opacity) {
      if (this._isShape()) {
        this._shape.attr("stroke", _color);
        this._shape.attr("stroke-width", _width);
        this._shape.attr("stroke-opacity", _opacity);
        this._shape.attr("fill", _fill);
        this._shape.attr("fill-opacity", _fill_opacity);
      }
      return this;
    },
    _getShape: function () {
      if (this._isShape())
        return this._shape;
    },
    _getBox: function () {
      if (this._isShape())
        return this._shape.getBBox();
    },
    _getShapes: function () {
      if (this._isShape())
        return [this._shape];
      else if (this._isContainer()) {
        var _shapes = [];
        for (var i = 0; i < this._elements.length; i += 1)
          _shapes = _shapes.concat(this._elements[i]._getShapes());
        return _shapes;
      }
    },
    _getMeasurement: function () {
      if (this._isShape()) {
        return [this._getWidth(), this._getHeight(), 1, 1];
      }
      else if (this._isContainer()) {
        var
                _kcl = _key._class,
                _kfl = _key._float,
                _pad = _clone(this._pad), _hGap,
                _maxWL = 0, _maxWR = 0, _maxH = 0,
                _x0 = 0, _y0 = 0,
                _xl = _x0, _xr = _x0, _y = _y0 + _pad.y,
                i, _e, _m, _w, _h, _class, _float, _clear,
                _lastPad = {l: 0, r: 0, y: 0},
        _fixedW, _fixedH, _xItems = 0, _yItems = 0,
                _maxW = 0, _maxH = 0,
                _maxWidth, _maxHeight,
                _currentW = 0, _currentH = 0,
                _maxWidthSet = false, _maxHeightSet = false
                ;
        _fixedW = this._get('fixedWidth');
        _fixedH = this._get('fixedHeight');
        _maxWidth = this._get('maxWidth');
        _maxHeight = this._get('maxHeight');
        var _arr = this._elements;
        for (i = 0; i < _arr.length; i++) {
          if (i === 0) {
            _xItems += 1;
            _yItems += 1;
          }
          _e = _arr[i];
          _class = _e._get('_class', _kcl._default);
          if (typeof _pad[_class] === 'undefined')
            _pad[_class] = 0;
          if (_fixedW)
            _pad[_class] = 0;
          _m = _e._getMeasurement();
          _w = _m[0];
          _h = _m[1];
          _float = _e._get('_float');
          _clear = _e._get('clear');
          _hGap = _e._get('hGap');
          if (!_hGap)
            _hGap = 0;
          if (_maxH < _h + _hGap)
            _maxH = _h + _hGap;
          if (_float === _kfl._left) {
            if (_clear === _kfl._left) {
              _xl = _w + _x0 + _pad.x;
              if (i > 0) {
                _y += _maxH;
                _yItems += 1;
              }
            }
            else {
              _xl += _w + _pad[_class];
              _lastPad.l = _pad[_class];
              if (i > 0)
                _xItems += 1;
            }
            if (_currentW < _xl)
              _currentW = _xl;
            if (_currentH < _y)
              _currentH = _y;
            if (i === _arr.length - 1)
              _y += _maxH;
            if (_maxWL < _xl)
              _maxWL = _xl;
          }
          else if (_float === _kfl._right) {
            if (_clear === _kfl._right) {
              if (_maxHeight
                      && _y + _maxH > _maxHeight
                      && !_fixedH)
              {
                _x0 = _maxWR + _pad[_class];
                _y = _pad.y;
              }
              else if (i > 0) {
                _y += _maxH;
                _yItems += 1;
              }
              _xr = _w + _x0 + _pad.x;
            }
            else {
              if (_maxWidth
                      && _xr + _w > _maxWidth
                      && !_fixedW
                      && !_maxHeight
                      )
              {
                _xr = _pad.x;
                _y += _maxH;
              }
              _xr += _w + _pad[_class];
              _lastPad.r = _pad[_class];
              if (i > 0)
                _xItems += 1;
            }
            if (i === _arr.length - 1)
              _y += _maxH;
            if (_maxWR < _xr) {
              _maxWR = _xr;
            }
          }
          else {
            _xl = _w + _pad.x;
            _lastPad.l = _pad.x;
            if (i > 0) {
              _y += _h;
              _xItems += 1;
              _yItems += 1;
            }
            if (_maxWL < _xl)
              _maxWL = _xl;
            if (i === _arr.length - 1)
              _y += _h;
            if (_currentW < _xl)
              _currentW = _xl;
            if (_currentH < _y)
              _currentH = _y;
          }
        }
        this._width = _maxWL - _lastPad.l + _pad.x + _maxWR - _lastPad.r + _pad.x;
        if (_maxWL > 0 && _maxWR > 0)
          this._width += _pad.x;
        this._height = _y + _pad.y;
        if (_fixedW)
          this._width = _fixedW;
        if (_fixedH)
          this._height = _fixedH;
        if (!_fixedW && _maxWidth)
          this._width = _maxWidth;
        if (!_fixedH && _maxHeight)
          this._height = _maxHeight;
        return [this._width, this._height, _xItems, _yItems];
      }
    },
    _printMeasurement: function () {
      var _c = this._getMeasurement();
      var _s = '';
      for (var i = 0; i < _c.length; i += 1)
        _s += _c[i].toFixed(0) + ' ';
      alert(_s);
    },
    _projectChildElemens: function () {
      if (this._isShape()) {
      }
      else if (this._isContainer()) {
        var
                _kcl = _key._class,
                _kfl = _key._float,
                _pad = _clone(this._pad),
                _float, _clear,
                _x = 0, _y = 0, _w, _h,
                _xl, _xr,
                i, _class, _hGap = 0,
                _e, _lastPad = {x: 0, y: 0},
        _fixedW, _fixedH,
                _maxWidth, _maxHeight,
                _currentW = 0, _currentH = 0,
                _maxWidthSet = false, _maxHeightSet = false,
                _xGap, _yGap, _xSlide, _ySlide
                ;
        _fixedW = this._get('fixedWidth');
        _fixedH = this._get('fixedHeight');
        var _arr = this._elements;
        this._resetPosition();
        for (i = 0; i < this._elements.length; i += 1)
          this._elements[i]._projectChildElemens();
        var _m = this._getMeasurement(),
                _maxW = _m[0], _maxH = _m[1],
                _xItems = (_m[2] > 1) ? _m[2] - 1 : _m[2],
                _yItems = (_m[3] > 1) ? _m[3] - 1 : _m[3]
                ;
        _maxWidth = this._get('maxWidth', _maxW + 1);
        if (_maxWidth !== _maxW + 1)
          _maxWidthSet = true;
        _maxW = Math.min(_maxW, _maxWidth);
        _maxHeight = this._get('maxHeight', _maxH + 1);
        if (_maxHeight !== _maxH + 1)
          _maxHeightSet = true;
        _maxH = Math.min(_maxH, _maxHeight);
        var _padX = _pad.x,
                _padY = _pad.y;
        if (_fixedW)
          _pad.x = 0;
        if (_fixedH)
          _pad.y = 0;
        _xl = _x + _pad.x;
        _xr = _maxW - _pad.x;
        _y += _pad.y;
        for (i = 0; i < _arr.length; i += 1) {
          _e = _arr[i];
          _class = _e._get('_class', _kcl._default);
          if (typeof _pad[_class] === 'undefined')
            _pad[_class] = 0;
          if (_fixedW)
            _pad[_class] = 0;
          _w = _e._getWidth();
          _h = _e._getHeight();
          _xGap = (_fixedW) ? _fixedW / _xItems : _w;
          _yGap = (_fixedH) ? _fixedH / _yItems : _h;
          _xSlide = (_fixedW) ? _w / 2 : 0;
          _ySlide = (_fixedH) ? _h / 2 : 0;
          _float = _e._get('_float');
          _clear = _e._get('clear');
          _hGap = _e._get('hGap', 0);
          if (_float === _kfl._left) {
            if (_clear === _kfl._left) {
              if (_y + _yGap > _maxH
                      && !_fixedH
                      )
              {
                _x = _currentW;
                _y = _pad.y;
              }
              else if (i > 0)
                _y += _yGap;
              _xl = _x + _pad.x;
            }
            if (_xl + _xGap > _maxW
                    && !_fixedW
                    && !_maxHeightSet
                    )
            {
              _xl = _pad.x;
              _y += _yGap;
            }
            _e._translateAbsolute(
                    _xl - _xSlide, _y + _hGap - _ySlide);
            _xl += _xGap + _pad[_class];
            _lastPad.x = _pad[_class];
            if (i === _arr.length - 1) {
              _y += _h;
              _currentH += _h;
            }
            if (_currentW < _xl)
              _currentW = _xl;
            if (_currentH < _y)
              _currentH = _y;
          }
          else if (_float === _kfl._right) {
            if (_clear === _kfl._right) {
              if (_y + _yGap > _maxH
                      && !_fixedH)
              {
                _x = _currentW;
                _y = _pad.y;
              }
              else if (i > 0) {
                _y += _yGap;
              }
              _xr = _maxW - _x - _pad.x;
            }
            if (_xr - _xGap <= 0
                    && !_fixedW
                    && !_maxHeightSet
                    )
            {
              _xr = _maxW - _x - _pad.x;
              _y += _yGap;
            }
            if (_fixedW && i === 0)
              _xGap = 0;
            _e._translateAbsolute(
                    _xr - _xGap - _xSlide, _y + _hGap - _ySlide);
            _xr -= _xGap + _pad[_class];
            _lastPad.x = _pad[_class];
            if (i === _arr.length - 1) {
              _y += _h;
              _currentH += _h;
            }
            if (_currentW < _maxW - _xr)
              _currentW = _maxW - _xr;
            if (_currentH < _y)
              _currentH = _y;
          }
          else {
            if (_y + _hGap > _maxH
                    && !_fixedH)
            {
              _x = _currentW + _pad.x;
              _y = _pad.y;
            }
            if (_xl + _xGap > _maxW
                    && !_fixedW)
            {
              _xl = _pad.x;
              _y += _yGap;
            }
            else if (i > 0)
              _y += _yGap;
            _xl = _x + _pad.x;
            _e._translateAbsolute(
                    _xl - _xSlide, _y + _hGap - _ySlide);
            _xl += _xGap + _pad[_class];
            _lastPad.x = _pad[_class];
            if (i === _arr.length - 1) {
              _y += _h;
              _currentH += _h;
            }
            if (_currentW < _xl)
              _currentW = _xl;
            if (_currentH < _y)
              _currentH = _y;
          }
        }
        if (this._width < _currentW)
          this._width = _currentW;
        if (this._height < _currentH)
          this._height = _currentH;
        if (_fixedW)
          this._width = _fixedW;
        if (_fixedH)
          this._height = _fixedH;
        this._panel._setWidth(this._width)._setHeight(this._height);
      }
      return this;
    },
    _setWidth: function (_w) {
      if (this._isShape()) {
        this._shape.attr("width", _w);
      }
      else {
        this._width = _w;
      }
      return this;
    },
    _setHeight: function (_h) {
      if (this._isShape()) {
        this._shape.attr("height", _h);
      }
      else {
        this._height = _h;
      }
      return this;
    },
    _getWidth: function () {
      if (this._isShape())
        return this._shape.getBBox().width;
      else
        return this._width;
    },
    _getHeight: function () {
      if (this._isShape())
        return this._shape.getBBox().height;
      else
        return this._height;
    },
    _resetSelf: function () {
      this._translateAbsolute(-this._get('x', 0), -this._get('y', 0));
      this._set({x: 0, y: 0});
      return this;
    },
    _projectSelf: function (_p) {
      var _kcl = _key._class,
              _kp = _key._positions;
      if (!_p)
        _p = this._get('position');
      if (!_p)
        _p = [_kp._center, _kp._center];
      var _vAlign = _p[0], _hAlign = _p[1],
              _pl = this._getPlotArea(),
              _w = this._getWidth(),
              _h = this._getHeight(),
              _x, _y,
              _x0, _y0
              ;
      _x0 = this._get('x', 0);
      _y0 = this._get('y', 0);
      this._translateAbsolute(-_x0, -_y0);
      switch (_vAlign)
      {
        case _kp._top:
          _y = _pl._padTop;
          break;
        case _kp._bottom:
          _y = _pl._h - _pl._padBottom - _h;
          break;
        case _kp._center:
        default:
          _y = (_pl._h + _pl._padTop - _pl._padBottom - _h) / 2;
          break;
      }
      switch (_hAlign)
      {
        case _kp._left:
          _x = _pl._padLeft;
          break;
        case _kp._right:
          _x = _pl._w - _pl._padRight - _w;
          break;
        case _kp._center:
        default:
          _x = (_pl._w + _pl._padLeft - _pl._padRight - _w) / 2;
          break;
      }
      ;
      this._set({x: _x, y: _y});
      this._translateAbsolute(_x, _y);
      return this;
    },
    _retractPlotArea: function () {
      return this._resizePlotArea(false);
    },
    _expandPlotArea: function () {
      return this._resizePlotArea(true);
    },
    _resizePlotArea: function (_expand) {
      var _p = this._get('position');
      var _vAlign = _p[0], _hAlign = _p[1],
              _kp = _key._positions,
              _pl = this._getPlotArea(),
              _pad = KC._clone(this._pad),
              _w = this._getWidth(),
              _h = this._getHeight(),
              _i = (_expand) ? -1 : +1
              ;
      switch (_vAlign)
      {
        case _kp._top:
          _pl._padTop += _i * (_h + _pad.element);
          break;
        case _kp._bottom:
          _pl._padBottom += _i * (_h + _pad.element);
          break;
        default:
          break;
      }
      switch (_hAlign)
      {
        case _kp._left:
          _pl._padLeft += _i * (_w + _pad.element);
          break;
        case _kp._right:
          _pl._padRight += _i * (_w + _pad.element);
          break;
        default:
          break;
      }
      ;
      return this;
    },
    _resizedPlotArea: function (_myPl, _expand) {
      var _p = this._get('position'),
              _vAlign = _p[0], _hAlign = _p[1],
              _kp = _key._positions,
              _kd = _key._direction,
              _pad = _clone(this._pad),
              _pe = _pad.element,
              _w = this._getWidth(),
              _h = this._getHeight(),
              _i = (_expand) ? -1 : +1,
              _d = this._get('affectedPlotDirection'),
              _kcl = _key._class
              ;
      if (_notDefined(_myPl)) {
        _myPl = _new(this._getPlotArea());
      }
      if (!_d)
        _d = [_kd._nirvana];
      if (_d.indexOf(_kd._vertical) > -1) {
        if (_vAlign === _kp._top)
          _myPl._padTop += _i * (_h + _pe);
        else if (_vAlign === _kp._bottom)
          _myPl._padBottom += _i * (_h + _pe);
      }
      if (_d.indexOf(_kd._horizontal) > -1) {
        if (_hAlign === _kp._left)
          _myPl._padLeft += _i * (_w + _pe);
        else if (_hAlign === _kp._right)
          _myPl._padRight += _i * (_w + _pe);
      }
      return _myPl;
    },
    _mergePlotArea: function (_pl) {
      var _myPl = this._getPlotArea(),
              _p = this._get('position'),
              _vAlign = _p[0], _hAlign = _p[1],
              _kp = _key._positions,
              _kd = _key._direction,
              _d = this._get('affectedPlotDirection')
              ;
      if (!_d)
        _d = [_kd._nirvana];
      if (_d.indexOf(_kd._horizontal) === -1)
        if (_vAlign === _kp._top || _vAlign === _kp._bottom) {
          _myPl._padLeft = _pl._padLeft;
          _myPl._padRight = _pl._padRight;
        }
      if (_d.indexOf(_kd._vertical) === -1)
        if (_hAlign === _kp._left || _hAlign === _kp._right)
        {
          _myPl._padTop = _pl._padTop;
          _myPl._padBottom = _pl._padBottom;
        }
      this._setPlotArea(_myPl);
      return this;
    },
    _move: function (_dx, _dy) {
      if (this._isShape()) {
        this._shape.attr('x', this._getX() + _dx);
        this._shape.attr('y', this._getY() + _dy);
      }
      else if (this._isContainer()) {
        this._panel._move(_dx, _dy);
        for (var i = 0; i < this._elements.length; i += 1)
          this._elements[i]._move(_dx, _dy);
      }
      return this;
    },
    selfTranslate: function (arg) {
      this._selfTranslate(arg[0], arg[1], true);
      return this;
    },
    _selfTranslate: function (_fx, _fy, _absolute) {
      if (this._isShape()) {
        this._translate(this._getWidth() * _fx, this._getHeight() * _fy, _absolute);
      }
      else if (this._isContainer()) {
        this._panel._selfTranslate(_fx, _fy, _absolute);
        for (var i = 0; i < this._elements.length; i += 1)
          this._elements[i]._selfTranslate(_fx, _fy, _absolute);
      }
      return this;
    },
    selfWholeTranslate: function (arg) {
      this._selfWholeTranslate(arg[0], arg[1], true);
      return this;
    },
    _selfWholeTranslate: function (_fx, _fy, _absolute) {
      this._translate(this._getWidth() * _fx, this._getHeight() * _fy, _absolute);
      return this;
    },
    translate: function (arg) {
      this._translate(arg[0], arg[1], true);
      return this;
    },
    _translate: function (_dx, _dy, _absolute) {
      if (this._isShape()) {
        var _t = '...';
        _t += (_absolute) ? 'T' : 't';
        if (_dx !== 0 || _dy !== 0)
          this._shape.transform(_t + _dx + "," + _dy);
      }
      else if (this._isContainer()) {
        this._panel._translate(_dx, _dy, _absolute);
        for (var i = 0; i < this._elements.length; i += 1)
          this._elements[i]._translate(_dx, _dy, _absolute);
      }
      return this;
    },
    _translateAbsolute: function (_dx, _dy) {
      return this._translate(_dx, _dy, true);
    },
    _rotate: function (_angle, _absolute) {
      if (this._isShape()) {
        var
                _w = this._getWidth(),
                _x, _y, _sin
                ;
        var _r = '...';
        _r += (_absolute) ? 'R' : 'r';
        this._translate(0, -_w / 2, _absolute);
        this._shape.transform(_r + _angle);
        this._translate(0, +_w / 2, _absolute);
      }
      else if (this._isContainer()) {
        this._panel._rotate(_angle, _absolute);
        for (var i = 0; i < this._elements.length; i += 1)
          this._elements[i]._rotate(_angle, _absolute);
      }
      return this;
    },
    _rotateAbsolute: function (_angle) {
      return this._rotate(_angle, true);
    },
    _resetSize: function () {
      this._width = 0;
      this._height = 0;
      return this;
    },
    _resetPosition: function () {
      if (this._isShape()) {
        var _x, _y;
        _x = this._getX();
        _y = this._getY();
        if (_x !== 0 || _y !== 0)
          this._translateAbsolute(-_x, -_y);
      }
      else if (this._isContainer()) {
        this._panel._resetPosition();
        for (var i = 0; i < this._elements.length; i += 1)
          this._elements[i]._resetPosition();
      }
      return this;
    },
    _toBack: function () {
      if (this._isShape())
        this._getShape().toBack();
      else if (this._isContainer()) {
        this._container.toBack();
      }
      return this;
    },
    _toFront: function () {
      if (this._isShape()) {
        this._getShape().toFront();
      }
      else if (this._isContainer()) {
        this._container.toFront();
      }
      return this;
    },
    _id: function (_id) {
      if (this._isShape())
        this._getShape().id = _id;
      else if (this._isContainer())
        this._container.id = _id;
      return this;
    },
    _setTransition: function (_option) {
      if (this._isShape()) {
        var _anim = {};
        if (_option.type.indexOf('opacity') > -1) {
          this._attr("opacity", 0);
          var _opacity = 1;
          if (_defined(_option.opacity))
            _opacity = _option.opacity;
          _anim["opacity"] = _opacity;
        }
        if (_option.type.indexOf('slide') > -1) {
          this._attr(_option.dimension, _option.slide[0]);
          _anim[_option.dimension] = _option.slide[1];
        }
        _anim = Raphael.animation(_anim, 500, "linear");
        this._animate(_anim.delay(_option.delay));
      }
      return this;
    },
    _setTransitionHeight: function () {
      if (this._isContainer()) {
        for (var i = 0; i < this._elements.length; i += 1)
          this._elements[i]._setTransitionHeight();
      }
      return this;
    },
    _setTransitionWidth: function () {
      if (this._isContainer()) {
        for (var i = 0; i < this._elements.length; i += 1)
          this._elements[i]._setTransitionWidth();
      }
      return this;
    },
    _stalk: function (_e, _direction, _position) {
      if (this._isShape()) {
        var
                _kd = _key._direction,
                _kp = _key._positions,
                _pad = _clone(this._pad)[
                this._get('_class') +
                '_' + _e._get('_class')
        ],
                _x = _e._getX(),
                _y = _e._getY(),
                _w = _e._getWidth(),
                _h = _e._getHeight(),
                _b = this._getBox(),
                _stalkedPoint = _e._get('stalkedPoint')
                ;
        if (_notDefined(_pad))
          _pad = _clone(this._pad)._default;
        if (_defined(_stalkedPoint)) {
          _x = _stalkedPoint[0];
          _y = _stalkedPoint[1];
        }
        if (_direction === _kd._vertical) {
          if (_notDefined(_stalkedPoint))
            _x += _w / 2;
          else
            _y -= _h / 2;
          switch (_position)
          {
            case _kp._center:
              _y += _h / 2;
              break;
            case _kp._insideBase:
              _y += _h - _b.height / 2 - _pad;
              break;
            case _kp._insideEnd:
              _y += _b.height / 2 + _pad;
              break;
            case _kp._outsideEnd:
              _y += -_b.height / 2 - _pad;
              break;
            case _kp._outsideBegin:
            default:
              _y += _h + _b.height / 2 + _pad;
              break;
          }
          this._move(_x + _b.width / 2, _y + _b.height / 2);
        }
        else if (_direction === _kd._horizontal) {
          if (_notDefined(_stalkedPoint))
            _y += _h / 2;
          else {
            _x -= _w / 2;
            _pad = _pad * 2;
          }
          switch (_position)
          {
            case _kp._center:
              _x += _w / 2;
              break;
            case _kp._insideBase:
              _x += _b.width / 2 + _pad;
              break;
            case _kp._insideEnd:
              _x += _w - _b.width / 2 - _pad;
              break;
            case _kp._outsideEnd:
              _x += _w + _b.width / 2 + _pad;
              break;
            case _kp._outsideBegin:
            default:
              _x += -_b.width / 2 - _pad;
              break;
          }
          this._move(_x + _b.width / 2, _y + _b.height / 2);
        }
      }
      return this;
    },
    _dblclick: function (_f) {
      if (this._isShape()) {
        this._shape.dblclick(_f);
      }
      return this;
    },
    _click: function (_f) {
      if (this._isShape()) {
        this._shape.click(_f);
      }
      return this;
    },
    _mouseover: function (_f) {
      if (this._isShape()) {
        this._shape.mouseover(_f);
      }
      return this;
    },
    _attr: function (_name, _value) {
      if (this._isShape()) {
        this._shape.attr(_name, _value);
      }
      return this;
    },
    _animate: function (_a) {
      if (this._isShape()) {
        this._shape.animate(_a);
      }
      return this;
    },
    _act: function (_settings, _arg) {
      for (var s in _settings) {
        if (_settings.hasOwnProperty(s))
          if (_defined(_arg[_settings[s]]))
            this[s](_arg[_settings[s]]);
      }
      return this;
    },
    _remove: function () {
      if (this._isShape()) {
        this._shape.remove();
      }
      else if (this._isContainer()) {
        this._panel._remove();
        for (var i = 0; i < this._elements.length; i += 1)
          this._elements[i]._remove();
      }
      return this;
    }
  };
  var _clone = function (_object) {
    return JSON.parse(JSON.stringify(_object));
  };
  var _new = function (_object) {
    var _F = function () {
      for (var p in this)
        if (!this.hasOwnProperty(p) &&
                typeof this[p] !== 'function')
          this[p] = this[p];
    };
    _F.prototype = _object;
    return new _F();
  };
  var _newPlotArea = function (_w, _h, _pad) {
    var _pl = _new(_plotArea);
    _pl._setWidth(_w)._setHeight(_h)._setPad(_pad);
    return _pl;
  };
  var _newElement = function (_paper) {
    var
            _e;
    _e = K._newObject(_element);
    _e._paper = _paper;
    _e._elements = [];
    _e._attributes = {};
    return _e;
  };
  var _newText = function (_paper) {
    var
      _k = _key,
      _ket = _key._elementType,
      _kf = _key._font,
      _t;
    _t = _newElement(_paper);
    _t._set({type: _ket._text});
    _t._shape = _t._paper.text(0, 0, '');
    _t._merge({
      _setText: function (_text) {
        this._shape.attr('text', _text);
        return this;
      },
      _setAnchor: function (_anchor) {
        this._shape.attr('text-anchor', _anchor);
        return this;
      },
      _style: function (_font_size, _font_family,
              _font_color, _font_style, _font_weight) {
        if (_font_size)
          this._shape.attr("font-size", _font_size);
        if (_font_family)
          this._shape.attr("font-family", _font_family);
        if (_font_color)
          this._shape.attr("fill", _font_color);
        if (_font_style)
          this._shape.attr("font-style", _font_style);
        if (_font_weight)
          this._shape.attr("font-weight", _font_weight);
      },
      _format: function (_f) {
        this._style(_f[_kf._size], _f[_kf._fam],
                _f[_kf._color], _f[_kf._style], _f[_kf._weight]);
        var _defaultAngle = this._get('defaultAngle', 0);
        if (_notDefined(_f[_k._rotateAngle]))
          _f[_k._rotateAngle] = 0;
        this._rotateAbsolute(_defaultAngle + _f[_k._rotateAngle]);
        return this;
      },
      _setTransitionOpaque: function (_delay) {
        this._setTransition({
          type: ['opacity'],
          delay: _delay
        });
        return this;
      }
    });
    return _t;
  };
  var _newLine = function (_paper) {
    var
            _k = _key,
            _ket = _key._elementType,
            _kd = _key._direction;
    var _l = _newElement(_paper);
    _l._set({type: _ket._line});
    _l._merge({
      _createLine: function () {
        var
                _x = this._get('x', 0),
                _y = this._get('y', 0),
                _d = this._get('direction'),
                _len = this._get('length'),
                _format = this._get('format'),
                _color = this._get('color')
                ;
        var _pathStr = 'M' + _x + "," + _y;
        if (_d === _kd._horizontal)
          _pathStr += 'h';
        else if (_d === _kd._vertical)
          _pathStr += 'v';
        _pathStr += _len;
        this._shape = this._paper.path(_pathStr);
        if (_format)
          this._shape.attr('stroke', _format[_k._color]);
        if (_color)
          this._shape.attr('stroke', _color);
        return this;
      },
      _get_line_d: function (_x1, _y1, _x2, _y2)
      {
        return "M" + _x1 + " " + _y1 + " L" + _x2 + " " + _y2;
      },
      _setCoordinate: function (_x1, _y1, _x2, _y2) {
        var
                _pathStr = this._get_line_d(_x1, _y1, _x2, _y2);
        this._coordinate = [_x1, _y1, _x2, _y2];
        this._shape = this._paper.path(_pathStr);
        return this;
      },
      _setTransitionLine: function () {
        var
                _c = this._coordinate,
                _x1 = _c[0], _y1 = _c[1], _x2 = _c[2], _y2 = _c[3],
                _oldHeight = this._get('oldHeight', 0),
                _newHeight = this._get('newHeight', 0),
                _animWait = this._get('animWait', 0),
                _path0 = this._get_line_d(_x1, _y1 + _oldHeight,
                        _x2, _y2 + _newHeight),
                _path = this._get_line_d(_x1, _y1, _x2, _y2)
                ;
        this._setTransition({
          type: ['opacity', 'slide'],
          dimension: 'path',
          slide: [_path0, _path],
          delay: _animWait * 500
        });
        return this;
      },
      _setTransitionPlain: function () {
        var _animWait = this._get('animWait', 0);
        this._setTransition({
          type: ['opacity'],
          delay: 500 + _animWait * 120
        });
        return this;
      }
    });
    return _l;
  };
  var _newRect = function (_paper) {
    var
            _k = _key,
            _ket = _key._elementType,
            _r;
    _r = _newElement(_paper);
    _r._set({type: _ket._rect});
    _r._shape = _paper.rect(0, 0, 0, 0);
    _r._shape.attr("stroke-linejoin", "round");
    _r._shape.attr("stroke-linecap", "square");
    _r._stroke("none", 0, 0);
    _r._merge({
      _setCoordinate: function (_x1, _y1, _x2, _y2) {
        var _width = Math.abs(_x2 - _x1);
        var _height = Math.abs(_y2 - _y1);
        var _x = (_x1 < _x2) ? _x1 : _x2;
        var _y = (_y1 < _y2) ? _y1 : _y2;
        this._shape.attr("x", _x);
        this._shape.attr("y", _y);
        this._shape.attr("width", _width);
        this._shape.attr("height", _height);
        return this;
      },
      _setSize: function (_w, _h) {
        this._shape.attr("width", Math.abs(_w));
        this._shape.attr("height", Math.abs(_h));
        this._transitionWidthSign = 1;
        this._transitionHeightSign = 1;
        if (_w < 0) {
          this._shape.attr('x', this._getX() + _w);
          this._transitionWidthSign = -1;
        }
        if (_h < 0) {
          this._shape.attr('y', this._getY() + _h);
          this._transitionHeightSign = -1;
        }
        return _r;
      },
      _fill: function (_color) {
        this._shape.attr("fill", _color);
        return this;
      },
      _format: function (_f) {
        this._stroke(_f[_k._bgcolor], 1, 1, _f[_k._bgcolor], 1);
        return this;
      },
      _setTransitionHeight: function () {
        if (this._isShape()) {
          if (_defined(this._transitionHeightSign)) {
            var _animWait = this._get('animWait', 0);
            var _h = -this._getHeight() * this._transitionHeightSign;
            this._shape.attr("height", 0);
            if (_h < 0) {
              var _anim = Raphael.animation({"height": -_h}, 700, "<");
            }
            else if (_h >= 0) {
              var _y = this._getY();
              this._shape.attr("y", _y + _h);
              var _anim = Raphael.animation({"y": _y, "height": _h}, 700, "<");
            }
            this._shape.animate(_anim.delay(700 * _animWait));
          }
        }
        else if (this._isContainer()) {
          for (var i = 0; i < this._elements.length; i += 1)
            this._elements[i]._setTransitionHeight();
        }
        return this;
      },
      _setTransitionWidth: function () {
        if (this._isShape()) {
          if (_defined(this._transitionWidthSign)) {
            var _animWait = this._get('animWait', 0);
            var _w = -this._getWidth() * this._transitionWidthSign;
            this._shape.attr("width", 0);
            if (_w < 0) {
              var _anim = Raphael.animation({"width": -_w}, 700, "<");
            }
            else if (_w >= 0) {
              var _x = this._getX();
              this._shape.attr("x", _x + _w);
              var _anim = Raphael.animation({"x": _x, "width": _w}, 700, "<");
            }
            this._shape.animate(_anim.delay(700 * _animWait));
          }
        }
        else if (this._isContainer()) {
          for (var i = 0; i < this._elements.length; i += 1)
            this._elements[i]._setTransitionWidth();
        }
        return this;
      }
    });
    return _r;
  };
  var _newTriangle = function (_paper) {
    var
            _ket = _key._elementType,
            _tri;
    _tri = _newElement(_paper);
    _tri._set({type: _ket._triAngle});
    _tri._merge({
      _get_triagles_d: function (_x, _y, _length)
      {
        var _x1 = _x - _length / 2;
        var _x2 = _x + _length / 2;
        var _y1 = _y + _length * Math.sqrt(3) / 2;
        var _y2 = _y1;
        return "M" + _x + " " + _y + " L" +
                _x1 + " " + _y1 + " L" + _x2 + " " + _y2 + " z";
      },
      _addTriangles: function (_x, _y, _length)
      {
        this._shape = _paper.path(
                this._get_triagles_d(_x, _y, _length));
        return this;
      }
    });
    return _tri;
  };
  var _newFourangle = function (_paper) {
    var
            _ket = _key._elementType,
            _four;
    _four = _newElement(_paper);
    _four._set({type: _ket._fourAngle});
    _four._merge({
      _get_fourangles_d: function (
              x1, y1, x2, y2, x3, y3, x4, y4) {
        return "M" + x1 + " " + y1 +
                " L" + x2 + " " + y2 +
                " L" + x2 + " " + y2 +
                " L" + x3 + " " + y3 +
                " L" + x4 + " " + y4 +
                " z";
      },
      _addFourAngles: function (
              x1, y1, x2, y2, x3, y3, x4, y4) {
        this._coordinate = [x1, y1, x2, y2, x3, y3, x4, y4];
        this._shape = _paper.path(this._get_fourangles_d(
                x1, y1, x2, y2, x3, y3, x4, y4));
        return this;
      },
      _setTransitionArea: function () {
        var
                _c = this._coordinate,
                _x1 = _c[0], _y1 = _c[1], _x2 = _c[2], _y2 = _c[3],
                _oldHeight = this._get('oldHeight', 0),
                _newHeight = this._get('newHeight', 0),
                _animWait = this._get('animWait', 0),
                _path0 = this._get_fourangles_d(
                        _x1, _y1 + _oldHeight, _x2, _y2 + _newHeight,
                        _x2, _y2 + _newHeight, _x1, _y1 + _oldHeight),
                _path = this._get_fourangles_d(
                        _x1, _y1, _x2, _y2,
                        _x2, _y2 + _newHeight, _x1, _y1 + _oldHeight)
                ;
        this._setTransition({
          type: ['opacity', 'slide'],
          opacity: 0.25,
          dimension: 'path',
          slide: [_path0, _path],
          delay: _animWait * 500
        });
        return this;
      }
    });
    return _four;
  };
  var _newCircle = function (_paper) {
    var
            _ket = _key._elementType,
            _c;
    _c = _newElement(_paper);
    _c._set({type: _ket._cirle});
    _c._merge({
      _addCircle: function (_x, _y, _radius) {
        this._shape = _paper.circle(_x, _y, _radius);
        return this;
      }
    });
    return _c;
  };
  var _newPolygon = function (_paper) {
    var
            _ket = _key._elementType,
            _p;
    _p = _newElement(_paper);
    _p._set({type: _ket._polygon});
    _p._points = [];
    return _p;
  };
  var _newContainer = function (_paper) {
    var _ket = _key._elementType,
            _kcl = _key._class,
            _c;
    _c = _newElement(_paper);
    _c._set({type: _ket._container});
    _c._container = _c._paper.set();
    _c._panel = _newRect(_paper)._set({_class: _kcl._panel});
    _c._container.push(_c._panel._getShape());
    return _c;
  };
  var _inversePosition = function (_position) {
    var _kp = _key._positions;
    switch (_position)
    {
      case _kp._insideBase:
        return _kp._insideEnd;
      case _kp._insideEnd:
        return _kp._insideBase;
      case _kp._outsideEnd:
        return _kp._outsideBegin;
      case _kp._outsideBegin:
        return _kp._outsideEnd;
      case _kp._center:
      default:
        return _position;
    }
  };
  var K = KoolPHP;
  var _k = _key,
      _ka = _key._appearances,
      _kc = _key._chart,
      _kd = _key._direction,
      _kf = _key._font,
      _km = _key._missing,
      _ko = _key._coordinate,
      _kp = _key._positions,
      _kfo = _key._format,
      _kr = _key._region,
      _kcl = _key._class,
      _kfl = _key._float
      ;
  var KoolChartJS = {
    _listKoolChart: {},
    mixin: function(o1, o2) {
      for (var p in o2)
        if (o2.hasOwnProperty(p))
          o1[p] = o2[p];
    },
    _new: function(object) {
      function F() {}
      F.prototype = object;
      return new F();
    },
    newChart: function (_id) {
      this._listKoolChart[_id] = generateChart(_id);
      this._listKoolChart[_id]._init();
    },
    getChart: function (_id) {
      return this._listKoolChart[_id];
    },
    defaultChart: {
      Height: 500,
      Width: 800,
      Transitions: true,
      BackgroundColor: '#ffffff',
      FontColor: '#3c4c30',
      FontFamily: "Arial,Helvetica,sans-serif",
      FontSize: '12px',
      FontStyle: 'normal',
      FontWeight: 'normal',
      NumberFormat: true,
      DecimalNumber: 0,
      ThousandSeparator: ',',
      DecimalSeparator: '.',
      Padding: 20,
      BarGapRatio: 0.5,
      ColorArray: [
        "Green", "Blue", "Orange", "Maroon", "Purple", "Aqua", "Navy", 
        "Fuchsia", "Yellow", "Gray", "Silver", "Teal", "Lime", "Black", "Olive"
      ],
      Title: {
        Text: '',
        Appearance: {
          Visible: true,
          Position: 'top',
          Align: 'center',
          FontSize: '16px'
        }
      },
      Legend: {
        Appearance: {
          Visible: true,
          Position: 'top'
        },
        SeriesOrder: []
      },
      PlotArea: {
        Appearance: {},
        XAxis: {
          Visible: true,
          Title: 'X axis',
          Color: '#b3b3b3',
          MajorTickSize: 2,
          MajorTickType: 'outside',
          MinorTickSize: 1,
          MinorTickType: 'outside',
          Reserved: false,
          Width: 1,
          Items: [],
          LabelsAppearance: {
            Visible: true,
            DataFormatString: '{0}',
            RotationAngle: 0
          },
          TitleAppearance: {
            Visible: true,
            RotationAngle: 0,
            FontSize: '16px',
            Position: ''
          },
          MajorGridLines: {
            Visible: true,
            Color: '#efefef',
            Width: 1
          },
          MinorGridLines: {
            Visible: true,
            Color: '#f7f7f7',
            Width: 1
          }
        },
        YAxis: {
          Visible: true,
          Title: 'Y axis',
          Color: '#b3b3b3',
          MajorTickSize: 2,
          MajorTickType: 'outside',
          MinorTickSize: 1,
          MinorTickType: 'outside',
          Reserved: false,
          Width: 1,
          Items: [],
          LabelsAppearance: {
            Visible: true,
            DataFormatString: '{0}',
            RotationAngle: 0
          },
          TitleAppearance: {
            Visible: true,
            RotationAngle: 0,
            FontSize: '16px',
            Position: ''
          },
          MajorGridLines: {
            Visible: true,
            Color: 'gray',
            Width: 1
          },
          MinorGridLines: {
            Visible: true,
            Color: 'gray',
            Width: 1
          }
        },
        ListOfSeries: [],
        ExtraYAxis: [],
        SeriesOrder: []
      }
    },
    defaultSeries: {
      MissingValue: 'gap',
      Name: '',
      ChartType: 'column',
      Appearance: {},
      LabelsAppearance: {
        Visible: true,
        RotationAngle: 0
      },
      MarkerAppearance: {
        Visible: true,
        MarkersType: 'circle'      
      },
      TooltipsAppearance: {
        Visible: true,
        DataFormatString: '{0}'
      }
    },
    defaultSeriesType: {
      column: {
        ChartType: 'column',
        Stacked: false,
        internalGapRatio: 0.5,
        LabelsAppearance: {
          Position: 'outsideend'
        }
      },
      pie: {
        ChartType: 'pie',
        StartAngle: 90,
        ShowRealValue: false,
        DecimalNumber: 1,
        ExplodedRatio: 1.15,
        LabelsAppearance: {
          DataFormatString: '{0}%'
        },
        PieLabel: {
          ArcTickGap: 5,
          ArcTickSize: 25,
          LabelTickGap: 3,
          LabelTickSize: 12
        }
      },
      bar: {
        ChartType: 'bar',
        Stacked: false,
        LabelsAppearance: {
          Position: 'outsideend'
        }
      },
      line: {
        ChartType: 'line',
        LabelsAppearance: {
          Position: 'above'
        }
      },
      area: {
        ChartType: 'area',
        LabelsAppearance: {
          Position: 'above'
        }
      },
      scatter: {
        ChartType: 'scatter',
        ItemConnected: false,
        LabelsAppearance: {
          Position: 'above'
        }
      },
      scatterline: {
        ChartType: 'scatter',
        ItemConnected: true,
        MissingValue: 'gap',
        LabelsAppearance: {
          Position: 'above'
        }
      }
    },
    defaultItemType: {
      pie: {
      }
    },
    propertiesMerge: function(prop1, prop2) {
      if (!K._defined(prop1))
        prop1 = prop2;
      else if (prop1 instanceof Array &&
              prop2 instanceof Array) {
        for (var i = 0; i < prop2.length; i += 1)
          prop1[i] = this.propertiesMerge(prop1[i], prop2[i]);
      }
      else if (typeof prop1 === 'object' &&
              typeof prop2 === 'object') {
        for (var n in prop2)
          if (prop2.hasOwnProperty(n)) {
            prop1[n] = this.propertiesMerge(prop1[n], prop2[n]);
          }
      }
      return prop1;
    },
    propertiesToLowerCase: function(obj) {
      var arr = ['ChartType', 'Position', 'Align', 'MajorTickType', 
        'MinorTickType', 'MissingValue', 'MarkersType'];
      for (var p in obj)
        if (obj[p] !== null && obj.hasOwnProperty(p)) {
          if (arr.indexOf(p) > -1)
            obj[p] = obj[p].toLowerCase();
          if (typeof obj[p] === 'object')
            this.propertiesToLowerCase(obj[p]);
        }
    },
    decodeURI: function(s) {
      try {
        s = decodeURIComponent(s);
      }
      catch (ex) {}
      return s;
    },
    off_tooltip: function (_id)
    {
      this.getChart(_id)._off_tooltip();
    }
  };
  var KCJS = KoolChartJS;
  var koolchart = (function() {
    var _id, _settings, _st, _st, _st_ti, _st_le, _st_pl, _st_xAxis, _st_yAxis,
        _w, _h, _padding, _pad, _pl;
    var _st_xTitle, _st_xLabel, _st_yTitle, _st_yLabel;
    var _majColor, _minColor, _xColor, _yColor, _bgcolor;
    var _items_count;
    var _listSeries, _chartType;
    var _postSettings = {
      selfTranslate: 'SelfTranslate',
      selfWholeTranslate: 'SelfWholeTranslate',
      translate: 'Translate'
    };
    var _axis = {
      _getRange: function () {
        return _st_yAxis[_ko._maxValue] - _st_yAxis[_ko._minValue];
      },
      _getRangeX: function () {
        return _st_xAxis[_ko._maxValue] - _st_xAxis[_ko._minValue];
      },
      _getMinusRange: function (_direction, _xValue) {
        var _length, _value, _range;
        if (_direction === _kd._vertical)
          _length = _pl._getAxisHeight();
        else if (_direction === _kd._horizontal)
          _length = _pl._getAxisWidth();
        if (_defined(_xValue) && _xValue === "xValue")
        {
          _value = _st_xAxis[_ko._minValue];
          _range = this._getRangeX();
        }
        else
        {
          _value = _st_yAxis[_ko._minValue];
          _range = this._getRange();
        }
        var _minusRange = (_value >= 0) ? 0 :
                (Math.abs(_value) / _range) * _length;
        return _minusRange;
      },
      _getColHeight: function (_value, _direction, _xValue) {
        var _length, _range, _start;
        if (_direction === _kd._vertical)
          _length = _pl._getAxisHeight();
        else if (_direction === _kd._horizontal)
          _length = _pl._getAxisWidth();
        if (_defined(_xValue) && _xValue === "xValue")
        {
          _range = this._getRangeX();
          _start = (_st_xAxis[_ko._minValue] > 0) ?
                  _st_xAxis[_ko._minValue] : 0;
        }
        else
        {
          _range = this._getRange();
          _start = (_st_yAxis[_ko._minValue] > 0) ?
                  _st_yAxis[_ko._minValue] : 0;
        }
        var _width = ((_value - _start) / _range) * _length;
        return _width;
      }
    };
    var kc = {
      getSt: function() {return _st;},
      initVariables: function() {
        _st = _settings = this._settings;
        this._id = _id = _settings._id;
        _st_ti = _st[_k._title];
        _st_le = _st[_k._legend];
        _st_pl = _st[_k._plotArea];
        _st_xAxis = _st_pl[_ko._xAxis];
        _st_yAxis = _st_pl[_ko._yAxis];
        _w = _st[_kr._width];
        _h = _st[_kr._height];
        _padding = _st[_kr._padding];
        _pad = _defined(_st[_kr._pad]) ?
              _st[_kr._pad] : [_padding, _padding, _padding, _padding];
        _pl = _newPlotArea(_w, _h, _pad);
        _bgcolor = _st[_k._bgcolor];
        _st_xTitle = _st_xAxis[_ka._title];
        _st_xLabel = _st_xAxis[_ka._label];
        _st_yTitle = _st_yAxis[_ka._title];
        _st_yLabel = _st_yAxis[_ka._label];
        _majColor = _st_xAxis[_ko._majorLines][_k._color];
        _minColor = _st_xAxis[_ko._minorLines][_k._color];
        _xColor = _st_xAxis[_k._color];
        _yColor = _st_yAxis[_k._color];
        _items_count = _st_xAxis[_k._items].length;
        _st = _settings;
        _st_ti = _st[_k._title];
        _st_le = _st[_k._legend];
        _st_pl = _st[_k._plotArea];
        _st_xAxis = _st_pl[_ko._xAxis];
        _st_yAxis = _st_pl[_ko._yAxis];
        _w = _st[_kr._width];
        _h = _st[_kr._height];
        _padding = _st[_kr._padding];
        _pad = _defined(_st[_kr._pad]) ? _st[_kr._pad] : 
                [_padding, _padding, _padding, _padding];
        _pl = _newPlotArea(_w, _h, _pad);
        if (K.isArray(_st_pl.SeriesOrder) &&
                ! K._isEmpty(_st_pl.SeriesOrder)) {
          _listSeries = [];
          var seriesOrder = _st_pl.SeriesOrder;
          for (var i=0; i<seriesOrder.length; i+=1)
            _listSeries.push(_st_pl[_k._listSeries][seriesOrder[i]-1]);
          if (K._isEmpty(_st_le.SeriesOrder))
            _st_le.SeriesOrder = _st_pl.SeriesOrder;
        }
        else {
          _listSeries = _st_pl[_k._listSeries];
        }
        _bgcolor = _st[_k._bgcolor];
        _st_xTitle = _st_xAxis[_ka._title];
        _st_xLabel = _st_xAxis[_ka._label];
        _st_yTitle = _st_yAxis[_ka._title];
        _st_yLabel = _st_yAxis[_ka._label];
        _majColor = _st_xAxis[_ko._majorLines][_k._color];
        _minColor = _st_xAxis[_ko._minorLines][_k._color];
        _xColor = _st_xAxis[_k._color];
        _yColor = _st_yAxis[_k._color];
        _items_count = _st_xAxis[_k._items].length;
        _st_xTitle = _st_xAxis[_ka._title];
        _st_xLabel = _st_xAxis[_ka._label];
        _st_yTitle = _st_yAxis[_ka._title];
        _st_yLabel = _st_yAxis[_ka._label];
        if (K._isEmpty(_listSeries[0][_kc._type]))
          _listSeries[0][_kc._type] = 'column';
        _chartType = _listSeries[0][_kc._type];
      },
      _init: function ()
      {
        this.fillSetting(this._settings);
        this.initVariables();
        var _pp = Raphael(_id, _pl._w, _pl._h);
        this._pp = _pp;
        this._area = _newRect(_pp)
                ._id(this._id + "_area")
                ._setCoordinate(0, 0, _pl._w, _pl._h)
                ._format(_st)
                ;
        var _parent = this._area._getShape().node.parentNode;
        if (_parent.nodeName === "svg")
        {
          _parent.removeChild(_parent.firstChild);
          this._is_svg = true;
        }
        else
        {
          this._is_svg = false;
        }
        if (_listSeries.length > 0)
        {
          this._originalPlotArea = _pl._clone();
          this._autoMinMaxStep = true;
          if (_defined(_st_yAxis[_ko._minValue]) &&
              _defined(_st_yAxis[_ko._maxValue]) &&
              _defined(_st_yAxis[_ko._majorStep]) &&
              _defined(_st_yAxis[_ko._minorStep]))
            this._autoMinMaxStep = false;
          if (_chartType !== _kc._pie)
            this._generateYMinMaxStep();
          if (_chartType === _kc._scat ||
              _chartType === _kc._scali)
            this._generateXMinMaxStep();
          this._generateSeriesAutoProperties();
          this._settings = K._cloneObject(_settings);
          this._create_title(_pp);
          this._create_legend(_pp);
          switch (_chartType)
          {
            case _kc._bar:
              this._create_bar_plotarea(_pp);
              break;
            case _kc._col:
            case _kc._line:
            case _kc._area:
              this._create_column_line_area_plotarea(_pp);
              break;
            case _kc._pie:
              this._create_pie_plotarea(_pp);
              break;
            case _kc._scat:
            case _kc._scali:
              this._create_scatter_plotarea(_pp);
              break;
          }
        }
        this._plot = _pl._clone();
      },
      getSvgString: function() {
        var _thisDiv = K._domObj(_id);
        var svg = _thisDiv.getElementsByTagName('svg')[0];
        var serializer = new XMLSerializer();
        var str = serializer.serializeToString(svg);
        return str;
      },
      exportToImage: function(imgMimeType) {
        if (!imgMimeType)
          imgMimeType = 'jpeg';
        var _thisDiv = K._domObj(_id);
        var canvasEl = document.createElement('canvas');
        _thisDiv.appendChild(canvasEl);
        canvg(canvasEl, this.getSvgString());
        canvasEl.style.display = 'none';
        var imageMimes = ['image/png', 'image/bmp', 'image/gif', 'image/jpeg', 'image/tiff']; 
        var imgData = canvasEl.toDataURL('image/' + imgMimeType);
        var a = document.createElement('a');
        a.id = _id + '_download';
        a.style.display = 'none';
        _thisDiv.appendChild(a);
        a.href = imgData;
        a.setAttribute('download', _id + '.' + imgMimeType);
        a.click();
      },
      clear: function ()
      {
        this._pp.remove();
      },
      redraw: function ()
      {
        this._pp.remove();
        this._init();
      },
      _create_title: function (_pp)
      {
        var _st_ap = _st_ti[_k._appearance];
        var _titleText;
        this._elements = [];
        this._elementSettings = [];
        this._titleLegendCount = 0;
        if (_st_ap[_k._visible])
        {
          _pl = this._originalPlotArea._clone();
          if (_notDefined(_st_ap[_k._position]))
            _st_ap[_k._position] = _kp._top;
          if (_notDefined(_st_ap[_k._align]))
            _st_ap[_k._align] = _kp._center;
          this._title = _newContainer(_pp)
            ._set({
              _class: _kcl._title
              , position: [_st_ap[_k._position], _st_ap[_k._align]]
              , affectedPlotDirection: [_kd._vertical]
            })
            ._fill(_st_ap[_k._bgcolor])
          ;
          _st_ti[_k._text] = KCJS.decodeURI(_st_ti[_k._text]);
          _titleText = _newText(_pp)
            ._setText(_st_ti[_k._text])
            ._set({
              _class: _kcl._text
            })
            ._format(_st_ap)
          ;
          this._title
            ._addElements(_titleText)
            ._projectChildElemens()
            ._setPlotArea(_pl)
          ;
          _pl = this._title._resizedPlotArea();
          this._elements.push(this._title);
          this._elementSettings.push(_st_ap);
          this._titleLegendCount += 1;
        }
      },
      _create_legend: function (_pp)
      {
        var _st_ap = _st_le[_k._appearance],
            _colLineArea = [_kc._col, _kc._line, _kc._area],
            _types = [], _data = [], _egde_length = 8,
            _legendFontSize = _defined(_st[_kf._size]) ?
            parseInt(_st[_kf._size]) : 12,
            _items, _series, _rect, _text, i,
            _legend, _listSeries
            ;
        if (K.isArray(_st_le.SeriesOrder) &&
                ! K._isEmpty(_st_le.SeriesOrder)) {
          _listSeries = [];
          var seriesOrder = _st_le.SeriesOrder;
          for (var i=0; i<seriesOrder.length; i+=1)
            _listSeries.push(_st_pl[_k._listSeries][seriesOrder[i]-1]);
        }
        else {
          _listSeries = _st_pl[_k._listSeries];
        }
        if (K._isEmpty(_listSeries[0][_kc._type]))
          _listSeries[0][_kc._type] = 'column';
        _chartType = _listSeries[0][_kc._type];
        if (_st_ap[_k._visible])
        {
          if (_listSeries.length === 0)
            return;
          else if (_chartType === _kc._pie) {
            _items = _listSeries[0][_k._items];
            for (i = 0; i < _items.length; i++)
              _data.push({
                "BackgroundColor": _items[i][_k._bgcolor],
                "Name": _items[i][_k._name]
              });
          }
          else {
            _types = (_colLineArea.indexOf(_chartType) > -1) ?
                    _colLineArea : [_chartType];
            for (i = 0; i < _listSeries.length; i++) {
              _series = _listSeries[i];
              if (_types.indexOf(_series[_kc._type]) > -1)
                _data.push({
                  "BackgroundColor": _series[_k._appearance][_k._bgcolor],
                  "Name": _series[_k._name]
                });
            }
          }
          if (_notDefined(_st_ap[_k._position]))
            _st_ap[_k._position] = _kp._top;
          var _p = _st_ap[_k._position],
              _d = [_kd._vertical],
              _f = K._newObject({_float: _kfl._left}),
              _fr = K._newObject({_float: _kfl._left}),
              _ft = K._newObject({_float: _kfl._left})
              ;
          if (_p === _kp._top || _p === _kp._bottom) {
            _p = [_p, _kp._center];
          }
          else if (_p === _kp._left || _p === _kp._right) {
            if (_p === _kp._left) {
              _f._float = _kfl._right;
              _f.clear = _kfl._right;
              _fr._float = _kfl._right;
              _ft._float = _kfl._right;
            }
            else {
              _f._float = _kfl._left;
              _f.clear = _kfl._left;
            }
            _p = [_kp._center, _p];
            _d = [_kd._horizontal];
          }
          ;
          this._legends = _newContainer(_pp)
            ._set({
              _class: _kcl._LEGENDS
              , position: _p
              , affectedPlotDirection: _d
            })
            ._fill(_st_ap[_k._bgcolor])
          ;
          for (i = 0; i < _data.length; i++)
          {
            _legend = _newContainer(_pp)
                    ._set(_f._merge({_class: _kcl._legend}))
                    ;
            _rect = _newRect(_pp)
                    ._set(_fr._merge({
                      _class: _kcl._fig
                      , hGap: (_legendFontSize - _egde_length) / 2 + 1
                    }))
                    ._setSize(_egde_length, _egde_length)
                    ._fill(_data[i][_k._bgcolor])
                    ;
            _text = _newText(_pp)
                    ._set(_ft._merge({_class: _kcl._text}))
                    ._setText(KCJS.decodeURI(_data[i][_k._name]))
                    ._format(_st)
            ;
            _legend._addElements(_rect, _text);
            this._legends._addElements(_legend);
          }
          this._legends
            ._projectChildElemens()
            ._setPlotArea(_pl)
          ;
          _pl = this._legends._resizedPlotArea();
          this._elements.push(this._legends);
          this._elementSettings.push(_st_ap);
          this._titleLegendCount += 1;
        }
      },
      _generateYMinMaxStep: function (_reCalculate)
      {
        var _max_number_of_items = 0,
            _lineAreaScat = [_kc._line, _kc._area, _kc._scat, _kc._scali],
            _colBarAreaLine = [_kc._col, _kc._bar, _kc._area, _kc._line],
            _scatColBarAreaLine = [_kc._scat, _kc._scali, _kc._col, _kc._bar, _kc._area, _kc._line]
            ;
        for (var i = 0; i < _listSeries.length; i++)
        {
          if (_lineAreaScat.indexOf(_listSeries[i][_kc._type]) > -1)
          {
            for (var j = 0; j < _listSeries[i][_k._items].length; j++)
            {
              if (_notDefined(_listSeries[i][_k._items][j][_ko._yValue]))
              {
                switch (_listSeries[i][_km._value])
                {
                  case _km._zero:
                    _listSeries[i][_k._items][j][_ko._yValue] = 0;
                    break;
                  case _km._inter:
                    break;
                  case _km._gap:
                    break;
                }
              } 
            }
          }
        }
        for (var i = 0; i < _listSeries.length; i++)
          if (_colBarAreaLine.indexOf(_listSeries[i][_kc._type]) > -1)
            if (_max_number_of_items < _listSeries[i][_k._items].length)
            {
              _max_number_of_items = _listSeries[i][_k._items].length;
            }
        while (_st_xAxis[_k._items].length < _max_number_of_items)
        {
          _st_xAxis[_k._items].push({"Text": ""});
        }
        var _min_value = 9999999;
        var _max_value = -9999999;
        for (var i = 0; i < _listSeries.length; i++)
        {
          if (_scatColBarAreaLine.indexOf(_listSeries[i][_kc._type]) > -1)
          {
            for (var j = 0; j < _listSeries[i][_k._items].length; j++)
            {
              var _value = parseFloat(_listSeries[i][_k._items][j][_ko._yValue]);
              if (_min_value > _value)
                _min_value = _value;
              if (_max_value < _value)
                _max_value = _value;
            }
          }
        }
        if (_min_value === _max_value)
        {
          if (_min_value > 0)
            _min_value = 0;
          else
            _max_value = 0;
        }
        if (this._autoMinMaxStep)
        {
          var _step_options = [0.0001, 0.00025, 0.0005];
          if (_min_value >= 0 && _max_value >= 0)
          {
            if ((_max_value - _min_value) / _max_value > 0.3 || this._stacked)
            {
              _st_yAxis[_ko._minValue] = 0;
              var _step = _max_value / 100;
              i = 0;
              while (_max_value / _step > 5)
              {
                _step = _step_options[i % 3] * Math.floor(Math.pow(10, Math.floor(i / 3)));
                i++;
              }
              _st_yAxis[_ko._maxValue] = Math.ceil(_max_value / _step + 1) * _step;
            }
            else
            {
              var _step = (_max_value - _min_value) / 100;
              i = 0;
              while ((_max_value - _min_value) / _step > 5)
              {
                _step = _step_options[i % 3] * Math.floor(Math.pow(10, Math.floor(i / 3)));
                i++;
              }
              _st_yAxis[_ko._minValue] = Math.floor(_min_value / _step - 1) * _step;
              _st_yAxis[_ko._maxValue] = Math.ceil(_max_value / _step + 1) * _step;
            }
          }
          else if (_min_value < 0 && _max_value >= 0)
          {
            var _step = (_max_value - _min_value) / 100;
            i = 0;
            while ((_max_value - _min_value) / _step > 5)
            {
              _step = _step_options[i % 3] * Math.floor(Math.pow(10, Math.floor(i / 3)));
              i++;
            }
            _st_yAxis[_ko._minValue] = Math.floor(_min_value / _step - 0.5) * _step;
            _st_yAxis[_ko._maxValue] = Math.ceil(_max_value / _step + 0.5) * _step;
          }
          else
          {
            _st_yAxis[_ko._maxValue] = 0;
            var _step = _min_value / 100;
            i = 0;
            while (Math.abs(_min_value / _step) > 5)
            {
              _step = _step_options[i % 3] * Math.floor(Math.pow(10, Math.floor(i / 3)));
              i++;
            }
            _st_yAxis[_ko._minValue] = Math.floor(_min_value / _step + 1) * _step;
          }
          if (_notDefined(_st_yAxis[_ko._majorStep]) || _reCalculate === true) {
            _st_yAxis[_ko._majorStep] = _step;
          }
          if (_notDefined(_st_yAxis[_ko._minorStep]) || _reCalculate === true) {
            _st_yAxis[_ko._minorStep] = _step / 5;
          }
        }
        else
        {
          var _normal = (_st_yAxis[_ko._maxValue] - _st_yAxis[_ko._minValue]) / _st_yAxis[_ko._majorStep];
          var _ceil = Math.ceil(_normal);
          if (_ceil !== _normal)
          {
            _st_yAxis[_ko._maxValue] = _st_yAxis[_ko._minValue] + _st_yAxis[_ko._majorStep] * _ceil;
          }
        }
      },
      _generateXMinMaxStep: function (_reCalculate)
      {
        for (var i = 0; i < _listSeries.length; i++)
        {
          if (_listSeries[i][_kc._type] === _kc._scat || 
              _listSeries[i][_kc._type] === _kc._scali)
          {
            for (var j = 0; j < _listSeries[i][_k._items].length; j++)
            {
              if (_notDefined(_listSeries[i][_k._items][j][_ko._xValue]))
              {
                switch (_listSeries[i][_km._value])
                {
                  case _km._zero:
                    _listSeries[i][_k._items][j][_ko._xValue] = 0;
                    break;
                  case _km._inter:
                    break;
                  case _km._inter:
                    break;
                }
              }
            }
          }
        }
        var _min_value = 9999999;
        var _max_value = -9999999;
        for (var i = 0; i < _listSeries.length; i++)
          if (_listSeries[i][_kc._type] === _kc._scat || 
              _listSeries[i][_kc._type] === _kc._scali)
          {
            for (var j = 0; j < _listSeries[i][_k._items].length; j++)
            {
              var _value = parseFloat(_listSeries[i][_k._items][j][_ko._xValue]);
              if (_min_value > _value)
                _min_value = _value;
              if (_max_value < _value)
                _max_value = _value;
            }
          }
        if (_min_value === _max_value)
        {
          if (_min_value > 0)
          {
            _min_value = 0;
          }
          else
          {
            _max_value = 0;
          }
        }
        if (this._autoMinMaxStep)
        {
          var _step_options = [0.0001, 0.00025, 0.0005];
          if (_min_value >= 0 && _max_value >= 0)
          {
            if ((_max_value - _min_value) / _max_value > 0.3)
            {
              _st_xAxis[_ko._minValue] = 0;
              var _step = _max_value / 100;
              i = 0;
              while (_max_value / _step > 5)
              {
                _step = _step_options[i % 3] * Math.floor(Math.pow(10, Math.floor(i / 3)));
                i++;
              }
              _st_xAxis[_ko._maxValue] = Math.ceil(_max_value / _step + 1) * _step;
            }
            else
            {
              var _step = (_max_value - _min_value) / 100;
              i = 0;
              while ((_max_value - _min_value) / _step > 5)
              {
                _step = _step_options[i % 3] * Math.floor(Math.pow(10, Math.floor(i / 3)));
                i++;
              }
              _st_xAxis[_ko._minValue] = Math.floor(_min_value / _step - 1) * _step;
              _st_xAxis[_ko._maxValue] = Math.ceil(_max_value / _step + 1) * _step;
            }
          }
          else if (_min_value < 0 && _max_value >= 0)
          {
            var _step = (_max_value - _min_value) / 100;
            i = 0;
            while ((_max_value - _min_value) / _step > 5)
            {
              _step = _step_options[i % 3] * Math.floor(Math.pow(10, Math.floor(i / 3)));
              i++;
            }
            _st_xAxis[_ko._minValue] = Math.floor(_min_value / _step - 0.5) * _step;
            _st_xAxis[_ko._maxValue] = Math.ceil(_max_value / _step + 0.5) * _step;
          }
          else
          {
            _st_xAxis[_ko._maxValue] = 0;
            var _step = _min_value / 100;
            i = 0;
            while (Math.abs(_min_value / _step) > 5)
            {
              _step = _step_options[i % 3] * Math.floor(Math.pow(10, Math.floor(i / 3)));
              i++;
            }
            _st_xAxis[_ko._minValue] = Math.floor(_min_value / _step + 1) * _step;
          }
          if (_notDefined(_st_xAxis[_ko._majorStep]) || _reCalculate === true)
          {
            _st_xAxis[_ko._majorStep] = _step;
          }
          if (_notDefined(_st_xAxis[_ko._minorStep]) || _reCalculate === true)
          {
            _st_xAxis[_ko._minorStep] = _step / 5;
          }
        }
        else
        {
          if (_defined(_st_xAxis[_ko._majorStep]))
          {
            var _normal = (_st_xAxis[_ko._maxValue] - _st_xAxis[_ko._minValue]) / _st_xAxis[_ko._majorStep];
            var _ceil = Math.ceil(_normal);
            if (_ceil !== _normal)
            {
              _st_xAxis[_ko._maxValue] = _st_xAxis[_ko._minValue] + _st_xAxis[_ko._majorStep] * _ceil;
            }
          }
        }
      },
      _generateSeriesAutoProperties: function () {
        var count = 0;
        for (var i = 0; i < _listSeries.length; i += 1) {
          var _series = _listSeries[i];
          if (_series[_kc._type] === _chartType) {
            if (K._isEmpty(_series[_k._name]))
              _series[_k._name] = 'Series ' + i;
            var appArr = [_k._appearance, _ka._tip, _ka._label];
            for (var j = 0; j < appArr.length; j += 1) {
              if (K._isEmpty(_series[appArr[j]]))
                _series[appArr[j]] = {};
            }
            if (K._isEmpty(_series[_k._appearance][_k._bgcolor]))
              _series[_k._appearance][_k._bgcolor] = KCJS.defaultChart.ColorArray[count % 15];
            _st[_kfo._data] = '{0}';
            _st[_k._visible] = true;
            var arr = ['FontColor', 'FontFamily', 'FontSize', 'FontStyle', 'FontWeight', _kfo._data];
            for (var j = 0; j < arr.length; j += 1) {
              if (K._isEmpty(_series[_ka._tip][arr[j]])) {
                _series[_ka._tip][arr[j]] = _st[arr[j]];
              }
              if (K._isEmpty(_series[_ka._label][arr[j]]))
                _series[_ka._label][arr[j]] = _st[arr[j]];
            }
            var arr = [_k._visible];
            for (var j = 0; j < arr.length; j += 1) {
              if (K._notDefined(_series[_ka._tip][arr[j]])) {
                _series[_ka._tip][arr[j]] = _st[arr[j]];
              }
              if (K._notDefined(_series[_ka._label][arr[j]]))
                _series[_ka._label][arr[j]] = _st[arr[j]];
            }
            if (K._isEmpty(_series[_ka._label][_k._position])) {
              _series[_ka._label][_k._position] = _kp._outsideEnd;
            }
            if (K._isEmpty(_series[_ka._tip][_k._bgcolor]))
              _series[_ka._tip][_k._bgcolor] = _st[_k._bgcolor];
            count += 1;
          }
        }
        if (_chartType === 'pie') {
          var _items = _listSeries[0][_k._items];
          for (var i = 0; i < _items.length; i++) {
            if (K._isEmpty(_items[i][_k._bgcolor]))
              _items[i][_k._bgcolor] = KCJS.defaultChart.ColorArray[i % 15];
          }
        }
      },
      _create_pie_plotarea: function (_pp)
      {
        if (_defined(this._legends))
        {
          var
                  _le_w = this._legends._getWidth(),
                  _le_h = this._legends._getHeight(),
                  _ax_w = _pl._getAxisWidth(),
                  _ax_h = _pl._getAxisHeight(),
                  _p = _st_le[_k._appearance][_k._position];
          if (_le_w > _ax_w && (_p === _kp._top || _p === _kp._bottom)) {
            _pl = this._legends._resizedPlotArea(_pl, true);
            this._legends._set({maxWidth: _ax_w * 100 / 100})
                    ._projectChildElemens();
            _pl = this._legends._resizedPlotArea(_pl);
          }
          if (_le_h > _ax_h && (_p === _kp._left || _p === _kp._right)) {
            _pl = this._legends._resizedPlotArea(_pl, true);
            this._legends._set({maxHeight: _ax_h * 100 / 100})
                    ._projectChildElemens();
            _pl = this._legends._resizedPlotArea(_pl);
          }
        }
        for (i = 0; i < this._elements.length; i++)
          this._elements[i]
                  ._mergePlotArea(_pl)
                  ._projectSelf()
                  ;
        this._plotArea = _newRect(_pp)
                ._setCoordinate(_pl._padLeft, _pl._padTop, _pl._w - _pl._padRight, _pl._h - _pl._padBottom)
                ._format(_st_pl[_k._appearance])
                ._toBack()
                ;
        _pp.getById(this._id + "_area").toBack();
        this._draw_pie_series(_pp, _settings);
      },
      _create_scatter_plotarea: function (_pp)
      {
        var _labelText, _info, _minusRange = {x: 0, y: 0},
            i, _format, _xAxis, _yAxis, _axes,
            _xMinorItems = _axis._getRangeX() / _st_xAxis[_ko._minorStep],
            _xMajorItems = _axis._getRangeX() / _st_xAxis[_ko._majorStep],
            _yMinorItems = _axis._getRange() / _st_yAxis[_ko._minorStep],
            _yMajorItems = _axis._getRange() / _st_yAxis[_ko._majorStep],
            _maxX = Number.MAX_VALUE,
            _minX = -Number.MAX_VALUE,
            _maxY = Number.MAX_VALUE,
            _minY = -Number.MAX_VALUE,
            _min, _max,
            _pl2, _resetPl = false,
            _addedValue
            ;
        while (_maxX > _pl._w || _minX < 0
                || _maxY > _pl._h || _minY < 0
                ) {
          _maxX = -Number.MAX_VALUE;
          _minX = Number.MAX_VALUE;
          _maxY = -Number.MAX_VALUE;
          _minY = Number.MAX_VALUE;
          _pl2 = _pl._clone();
          _resetPl = false;
          if (_st_yAxis[_k._title]
                  && _st_yTitle[_k._visible])
          {
            _info = {
              text: KCJS.decodeURI(_st_yAxis[_k._title]),
              format: _st_yTitle,
              id: this._id + "_plotarea_yaxis_title",
              properties: {
                position: [_st_yTitle[_k._position], _kp._left],
                affectedPlotDirection: [_kd._horizontal]
              },
              plotArea: _pl,
              defaultAngle: -90
            };
            this._yTitle = this._drawTitle(_info);
            _pl = this._yTitle._resizedPlotArea();
            this._elements.push(this._yTitle);
            this._elementSettings.push(_st_yTitle);
          }
          if (_st_xAxis[_k._title]
                  && _st_xTitle[_k._visible])
          {
            _info = {
              text: KCJS.decodeURI(_st_xAxis[_k._title]),
              format: _st_xTitle,
              id: this._id + "_plotarea_xaxis_title",
              properties: {
                position: [_kp._bottom, _st_xTitle[_k._position]],
                affectedPlotDirection: [_kd._vertical]
              },
              plotArea: _pl
            };
            this._xTitle = this._drawTitle(_info);
            _pl = this._xTitle._resizedPlotArea();
            this._elements.push(this._xTitle);
            this._elementSettings.push(_st_xTitle);
          }
          if (_st_xAxis[_ko._majorStep] && _st_xLabel[_k._visible])
          {
            _info = {
              id: this._id + "_plotarea_xaxis_labels",
              properties: {
                _class: 'xLabels'
                , position: [_kp._bottom, _kp._center]
                , affectedPlotDirection: (_st_yAxis[_ko._minValue] < 0) ?
                        [] : [_kd._vertical]
              },
              plotArea: _pl,
              items: []
            };
            for (i = 0; i <= _xMajorItems; i += 1) {
              var _values = [(_st_xAxis[_ko._minValue] * 10 +
                        (i * 10) * _st_xAxis[_ko._majorStep]) / 10];
              _labelText = this._getFormatedString(
                      _st_xLabel[_kfo._data], _values, _st);
              _info.items[i] = {
                text: _labelText,
                properties: {
                  _float: _kfl._left
                },
                format: _st_xLabel
              };
            }
            this._xLabels = this._drawLabels(_info);
            _pl = this._xLabels._resizedPlotArea();
            this._elements.push(this._xLabels);
            this._elementSettings.push(_st_xLabel);
          }
          if (_st_yAxis[_ko._majorStep] && _st_yLabel[_k._visible])
          {
            _info = {
              id: this._id + "_plotarea_yaxis_labels",
              properties: {
                fixedHeight: _pl._getAxisHeight()
                , _class: 'yLabels'
                , position: [_kp._center, _kp._left]
                , affectedPlotDirection: (_st_xAxis[_ko._minValue] < 0)
                        ? [] : [_kd._horizontal]
              },
              plotArea: _pl,
              items: []
            };
            for (i = _yMajorItems; i > -1; i -= 1) {
              var _values = [(_st_yAxis[_ko._minValue] * 10 +
                        (i * 10) * _st_yAxis[_ko._majorStep]) / 10];
              _labelText = this._getFormatedString(
                      _st_yLabel[_kfo._data], _values, _st);
              _info.items[_yMajorItems - i] = {
                text: _labelText,
                properties: {
                  _float: _kfl._right
                  , clear: _kfl._right
                },
                format: _st_yLabel
              };
            }
            this._yLabels = this._drawLabels(_info);
            _pl = this._yLabels._resizedPlotArea();
            this._elements.push(this._yLabels);
            this._elementSettings.push(_st_yLabel);
          }
          if (_defined(this._legends))
          {
            var
                    _le_w = this._legends._getWidth(),
                    _le_h = this._legends._getHeight(),
                    _ax_w = _pl._getAxisWidth(),
                    _ax_h = _pl._getAxisHeight(),
                    _p = _st_le[_k._appearance][_k._position];
            if (_le_w > _ax_w && (_p === _kp._top || _p === _kp._bottom)) {
              _pl = this._legends._resizedPlotArea(_pl, true);
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._legends._set({maxWidth: _ax_w * 100 / 100})
                      ._resetSelf()
                      ._projectChildElemens();
              _pl = this._legends._resizedPlotArea(_pl);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
            if (_le_h > _ax_h && (_p === _kp._left || _p === _kp._right)) {
              _pl = this._legends._resizedPlotArea(_pl, true);
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._legends._set({maxHeight: _ax_h * 100 / 100})
                      ._resetSelf()
                      ._projectChildElemens();
              _pl = this._legends._resizedPlotArea(_pl);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
          }
          if (this._xLabels)
            this._xLabels
                    ._set({fixedWidth: _pl._getAxisWidth()})
                    ._projectChildElemens()
                    ;
          for (i = 0; i < this._elements.length; i++) {
            this._elements[i]._act(_postSettings, this._elementSettings[i]);
          }
          for (i = 1; i < this._elements.length; i++)
            this._elements[i]
                    ._setPlotArea(this._elements[i - 1]._resizedPlotArea());
          for (i = 0; i < this._elements.length; i++)
            this._elements[i]
                    ._mergePlotArea(_pl)
                    ._projectSelf()
                    ;
          for (i = 0; i < this._elements.length; i++) {
            var _e = this._elements[i],
                    _es = this._elementSettings[i],
                    _d = _e._get('affectedPlotDirection')
                    ;
            if (_es[_k._visible]
                    && _es['FullyVisible']
                    && _d.indexOf(_kd._horizontal) === -1
                    ) {
              _min = _e._getMinX();
              if (_minX > _min)
                _minX = _min;
              _max = _e._getMaxX();
              if (_maxX < _max)
                _maxX = _max;
            }
            if (_es[_k._visible]
                    && _es['FullyVisible']
                    && _d.indexOf(_kd._vertical) === -1
                    ) {
              _min = _e._getMinY();
              if (_minY > _min)
                _minY = _min;
              _max = _e._getMaxY();
              if (_maxY < _max)
                _maxY = _max;
            }
          }
          var _changedPl = {
            _padTop: 0,
            _padBottom: 0,
            _padLeft: 0,
            _padRight: 0
          };
          if (_minX < 0) {
            _addedValue = -_minX + 5;
            _pl2._padLeft += _addedValue;
            _changedPl._padLeft = _addedValue;
            _resetPl = true;
          }
          if (_maxX > _pl2._w) {
            _addedValue = _maxX - _pl2._w + 5;
            _pl2._padRight += _addedValue;
            _changedPl._padRight = _addedValue;
            _resetPl = true;
          }
          if (_minY < 0) {
            _addedValue = -_minY + 5;
            _pl2._padTop += _addedValue;
            _changedPl._padTop = _addedValue;
            _resetPl = true;
          }
          if (_maxY > _pl2._h) {
            _addedValue = _maxY - _pl2._h + 5;
            _pl2._padBottom += _addedValue;
            _changedPl._padBottom = _addedValue;
            _resetPl = true;
          }
          if (_resetPl) {
            if (_defined(this._title)) {
              var _oPl = this._originalPlotArea;
              if (_changedPl._padTop)
                _oPl._padTop += _changedPl._padTop;
              if (_changedPl._padBottom)
                _oPl._padBottom += _changedPl._padBottom;
              if (_changedPl._padLeft)
                _oPl._padLeft += _changedPl._padLeft;
              if (_changedPl._padRight)
                _oPl._padRight += _changedPl._padRight;
            }
            for (i = 0; i < this._elements.length; i++)
              this._elements[i]._remove();
            this._elements.splice(0, this._elements.length);
            this._create_title(_pp);
            if (this._legends) {
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._create_legend(_pp);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
            _pl = _pl2._clone();
          }
        }
        if (_st_yAxis[_ko._minValue] < 0) {
          _minusRange.y = (Math.abs(_st_yAxis[_ko._minValue]) / _axis._getRange())
                  * _pl._getAxisHeight();
          if (this._xLabels)
            this._xLabels._translateAbsolute(
                    0, -_minusRange.y + this._xLabels._getHeight() + KC.pad.element);
        }
        if (_st_xAxis[_ko._minValue] < 0) {
          _minusRange.x = (Math.abs(_st_xAxis[_ko._minValue]) / _axis._getRangeX())
                  * _pl._getAxisWidth();
          if (this._yLabels)
            this._yLabels._translateAbsolute(
                    _minusRange.x - this._yLabels._getWidth() - KC.pad.element, 0);
        }
        _xAxis = {
          x: _pl._w - _pl._padRight,
          y: _pl._h - _pl._padBottom,
          direction: _kd._vertical,
          length: _pl._getAxisHeight(),
          color: _xColor,
          major: {
            items: _xMajorItems + 1,
            step: _pl._getAxisWidth() / _xMajorItems,
            color: _majColor,
            tickSize: _st_xAxis[_ko._majorTickSize],
            show: {
              line: (_st_xAxis[_ko._majorLines][_k._visible]) ?
                      true : false,
              tick: (_st_xAxis[_ko._majorTick] === _kp._outside) ?
                      true : false
            }
          },
          minor: {
            items: _xMinorItems + 1,
            step: _pl._getAxisWidth() / _xMinorItems,
            color: _minColor,
            tickSize: _st_xAxis[_ko._minorTickSize],
            show: {
              line: (_st_xAxis[_ko._minorLines][_k._visible]) ?
                      true : false,
              tick: (_st_xAxis[_ko._minorTick] === _kp._outside) ?
                      true : false
            }
          },
          sign: -1,
          minusRange: _minusRange.y
        };
        _xAxis = this._drawCooridnateGrid(_xAxis);
        _yAxis = {
          x: _pl._padLeft,
          y: _pl._padTop,
          direction: _kd._horizontal,
          length: _pl._getAxisWidth(),
          color: _yColor,
          major: {
            items: _yMajorItems + 1,
            step: _pl._getAxisHeight() / _yMajorItems,
            color: _majColor,
            tickSize: _st_yAxis[_ko._majorTickSize],
            show: {
              line: (_st_yAxis[_ko._majorLines][_k._visible]) ?
                      true : false,
              tick: (_st_yAxis[_ko._majorTick] === _kp._outside) ?
                      true : false
            }
          },
          minor: {
            items: _yMinorItems + 1,
            step: _pl._getAxisHeight() / _yMinorItems,
            color: _minColor,
            tickSize: _st_yAxis[_ko._minorTickSize],
            show: {
              line: (_st_yAxis[_ko._minorLines][_k._visible]) ?
                      true : false,
              tick: (_st_yAxis[_ko._minorTick] === _kp._outside) ?
                      true : false
            }
          },
          sign: 1,
          minusRange: _minusRange.x
        };
        _yAxis = this._drawCooridnateGrid(_yAxis);
        _axes = {
          x: _pl._padLeft,
          y: _pl._h - _pl._padBottom,
          width: _pl._getAxisWidth(),
          height: -_pl._getAxisHeight(),
          format: {
            x: _st_xAxis,
            y: _st_yAxis
          },
          minusRange: {
            x: _minusRange.x,
            y: -_minusRange.y
          }
        };
        _axes = this._drawAxes(_axes);
        this._xLabels._toFront();
        this._yLabels._toFront();
        this._plotArea = _newRect(_pp)
                ._setCoordinate(_pl._padLeft, _pl._padTop,
                        _pl._w - _pl._padRight, _pl._h - _pl._padBottom)
                ._format(_st_pl[_k._appearance])
                ._toBack()
                ;
        _pp.getById(this._id + "_area").toBack();
        this._draw_scatter_series(_pp, _settings);
      },
      _create_bar_plotarea: function (_pp)
      {
        var _listSeries2 = [],
                _clone = [],
                _count = 0,
                _stackedIndex = -1,
                _stackedItems, _items
                ;
        for (var i = 0; i < _listSeries.length; i++) {
          if (_listSeries[i]['Stacked']) {
            this._stacked = true;
            if (_stackedIndex < 0) {
              _listSeries2[_count] = _listSeries[i];
              _listSeries2[_count]._set = [_listSeries[i]];
              _listSeries2[_count]._indices = [i];
              _stackedIndex = _count;
              _stackedItems = _listSeries[i][_k._items];
              for (var j = 0; j < _stackedItems.length; j++) {
                _clone[j] = {};
                _clone[j][_ko._yValue] = _stackedItems[j][_ko._yValue];
              }
              _listSeries2[_count]._clone = _clone;
              _count += 1;
            }
            else {
              _items = _listSeries[i][_k._items];
              for (var j = 0; j < _items.length; j++) {
                _stackedItems[j][_ko._yValue] += _items[j][_ko._yValue];
              }
              _listSeries2[_stackedIndex]._set.push(_listSeries[i]);
              _listSeries2[_stackedIndex]._indices.push(i);
            }
          }
          else {
            _listSeries2[_count] = _listSeries[i];
            _listSeries2[_count]._indices = [i];
            _count += 1;
          }
        }
        _listSeries = _listSeries2;
        this._generateYMinMaxStep(true);
        var _format, _labelText, _minusRange = 0,
            _info, _xAxis, _yAxis, _axes,
            _yMinorItems = _axis._getRange() / _st_yAxis[_ko._minorStep],
            _yMajorItems = _axis._getRange() / _st_yAxis[_ko._majorStep],
            _xItems = _st_xAxis[_k._items].length,
            _maxX = Number.MAX_VALUE,
            _minX = -Number.MAX_VALUE,
            _maxY = Number.MAX_VALUE,
            _minY = -Number.MAX_VALUE,
            _min, _max,
            _pl2, _resetPl = false,
            _addedValue,
            _loop = 10
            ;
        while ((_maxX > _pl._w || _minX < 0
                || _maxY > _pl._h || _minY < 0)
                && _loop > 0)
        {
          _loop -= 1;
          _maxX = -Number.MAX_VALUE;
          _minX = Number.MAX_VALUE;
          _maxY = -Number.MAX_VALUE;
          _minY = Number.MAX_VALUE;
          _pl2 = _pl._clone();
          _resetPl = false;
          if (_st_xAxis[_k._title]
                  && _st_xTitle[_k._visible])
          {
            _info = {
              text: KCJS.decodeURI(_st_xAxis[_k._title]),
              format: _st_xTitle,
              id: this._id + "_plotarea_xaxis_title",
              properties: {
                position: [_st_xTitle[_k._position], _kp._left],
                affectedPlotDirection: [_kd._horizontal]
              },
              plotArea: _pl,
              defaultAngle: -90
            };
            this._xTitle = this._drawTitle(_info);
            _pl = this._xTitle._resizedPlotArea();
            this._elements.push(this._xTitle);
            this._elementSettings.push(_st_xTitle);
          }
          if (_st_yAxis[_k._title]
                  && _st_yTitle[_k._visible])
          {
            _info = {
              text: KCJS.decodeURI(_st_yAxis[_k._title]),
              format: _st_yTitle,
              id: this._id + "_plotarea_yaxis_title",
              properties: {
                _class: "yTitle",
                position: [_kp._bottom, _st_yTitle[_k._position]],
                affectedPlotDirection: [_kd._vertical]
              },
              plotArea: _pl
            };
            this._yTitle = this._drawTitle(_info);
            _pl = this._yTitle._resizedPlotArea();
            this._elements.push(this._yTitle);
            this._elementSettings.push(_st_yTitle);
          }
          if (_st_xLabel[_k._visible])
          {
            _info = {
              id: this._id + "_plotarea_xaxis_labels",
              properties: {
                _class: 'xLabels'
                , position: [_kp._center, _kp._left]
                , affectedPlotDirection: (_st_yAxis[_ko._minValue] < 0) ?
                        [] : [_kd._horizontal]
              },
              plotArea: _pl,
              items: []
            };
            for (i = 0; i < _xItems; i++) {
              _info.items[i] = {
                text: _st_xAxis[_k._items][_xItems - i - 1][_k._text],
                properties: {
                  _float: _kfl._right
                  , clear: _kfl._right
                },
                format: _st_xLabel
              };
            }
            this._xLabels = this._drawLabels(_info);
            _pl = this._xLabels._resizedPlotArea();
            this._elements.push(this._xLabels);
            this._elementSettings.push(_st_xLabel);
          }
          if (_st_yAxis[_ko._majorStep] && _st_yLabel[_k._visible])
          {
            _info = {
              id: this._id + "_plotarea_yaxis_labels",
              properties: {
                fixedWidth: _pl._getAxisWidth()
                , _class: 'yLabels'
                , position: [_kp._bottom, _kp._center]
                , affectedPlotDirection: [_kd._vertical]
              },
              plotArea: _pl,
              items: []
            };
            for (i = 0; i <= _yMajorItems; i += 1) {
              var _values = [(_st_yAxis[_ko._minValue] * 10 +
                        (i * 10) * _st_yAxis[_ko._majorStep]) / 10];
              _labelText = this._getFormatedString(
                      _st_yLabel[_kfo._data], _values, _st);
              _info.items[i] = {
                text: _labelText,
                properties: {_float: _kfl._left},
                format: _st_yLabel
              };
              if (i === _yMajorItems)
                _info.items[i]._resize = _kp._right;
            }
            this._yLabels = this._drawLabels(_info);
            _pl = this._yLabels._resizedPlotArea();
            this._elements.push(this._yLabels);
            this._elementSettings.push(_st_yLabel);
          }
          if (_defined(this._legends))
          {
            var
                    _le_w = this._legends._getWidth(),
                    _le_h = this._legends._getHeight(),
                    _ax_w = _pl._getAxisWidth(),
                    _ax_h = _pl._getAxisHeight(),
                    _p = _st_le[_k._appearance][_k._position];
            if (_le_w > _ax_w && (_p === _kp._top || _p === _kp._bottom)) {
              _pl = this._legends._resizedPlotArea(_pl, true);
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._legends._set({maxWidth: _ax_w * 100 / 100})
                      ._resetSelf()
                      ._projectChildElemens();
              _pl = this._legends._resizedPlotArea(_pl);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
            if (_le_h > _ax_h && (_p === _kp._left || _p === _kp._right)) {
              _pl = this._legends._resizedPlotArea(_pl, true);
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._legends._set({maxHeight: _ax_h * 100 / 100})
                      ._resetSelf()
                      ._projectChildElemens();
              _pl = this._legends._resizedPlotArea(_pl);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
          }
          if (this._xLabels)
            this._xLabels
                ._set({fixedHeight: _pl._getAxisHeight() / (_xItems) * (_xItems - 1)})
                ._projectChildElemens();
          if (this._yLabels)
            this._yLabels
                ._set({fixedWidth: _pl._getAxisWidth()})
                ._projectChildElemens();
          for (i = 0; i < this._elements.length; i++) {
            this._elements[i]._act(_postSettings, this._elementSettings[i]);
          }
          for (i = 1; i < this._elements.length; i++)
            this._elements[i]
                    ._setPlotArea(this._elements[i - 1]._resizedPlotArea());
          for (i = 0; i < this._elements.length; i++)
          {
            this._elements[i]
                    ._mergePlotArea(_pl)
                    ._projectSelf();
          }
          for (i = 0; i < this._elements.length; i++) {
            var _e = this._elements[i],
                    _es = this._elementSettings[i],
                    _d = _e._get('affectedPlotDirection')
                    ;
            if (_es[_k._visible]
                    && _es['FullyVisible']
                    && _d.indexOf(_kd._horizontal) === -1
                    ) {
              _min = _e._getMinX();
              if (_minX > _min)
                _minX = _min;
              _max = _e._getMaxX();
              if (_maxX < _max)
                _maxX = _max;
            }
            if (_es[_k._visible]
                    && _es['FullyVisible']
                    && _d.indexOf(_kd._vertical) === -1
                    ) {
              _min = _e._getMinY();
              if (_minY > _min)
                _minY = _min;
              _max = _e._getMaxY();
              if (_maxY < _max)
                _maxY = _max;
            }
          }
          var _changedPl = {
            _padTop: 0,
            _padBottom: 0,
            _padLeft: 0,
            _padRight: 0
          };
          if (_minX < 0) {
            _addedValue = -_minX + 5;
            _pl2._padLeft += _addedValue;
            _changedPl._padLeft = _addedValue;
            _resetPl = true;
          }
          if (_maxX > _pl2._w) {
            _addedValue = _maxX - _pl2._w + 5;
            _pl2._padRight += _addedValue;
            _changedPl._padRight = _addedValue;
            _resetPl = true;
          }
          if (_minY < 0) {
            _addedValue = -_minY + 5;
            _pl2._padTop += _addedValue;
            _changedPl._padTop = _addedValue;
            _resetPl = true;
          }
          if (_maxY > _pl2._h) {
            _addedValue = _maxY - _pl2._h + 5;
            _pl2._padBottom += _addedValue;
            _changedPl._padBottom = _addedValue;
            _resetPl = true;
          }
          if (_resetPl) {
            if (_defined(this._title)) {
              var _oPl = this._originalPlotArea;
              if (_changedPl._padTop)
                if (_changedPl._padTop < _oPl._getAxisHeight())
                  _oPl._padTop += _changedPl._padTop;
                else
                  break;
              if (_changedPl._padBottom)
                if (_changedPl._padBottom < _oPl._getAxisHeight())
                  _oPl._padBottom += _changedPl._padBottom;
                else
                  break;
              if (_changedPl._padLeft)
                if (_changedPl._padLeft < _oPl._getAxisWidth())
                  _oPl._padLeft += _changedPl._padLeft;
                else
                  break;
              if (_changedPl._padRight)
                if (_changedPl._padRight < _oPl._getAxisWidth())
                  _oPl._padRight += _changedPl._padRight;
                else
                  break;
            }
            for (i = 0; i < this._elements.length; i++)
              this._elements[i]._remove();
            this._elements.splice(0, this._elements.length);
            this._create_title(_pp);
            if (this._legends) {
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._create_legend(_pp);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
            _pl = _pl2._clone();
          }
        }
        if (_st_yAxis[_ko._minValue] < 0) {
          _minusRange = (Math.abs(_st_yAxis[_ko._minValue]) / _axis._getRange())
                  * _pl._getAxisWidth();
          if (this._xLabels)
            this._xLabels._translateAbsolute(
                    _minusRange - this._xLabels._getWidth() - KC.pad.element, 0);
        }
        this._axes = _newContainer(_pp)
                ._id(this._id + "_plotarea_grid")
                ;
        var _xAxis, _yAxis;
        _xAxis = {
          x: _pl._padLeft,
          y: _pl._padTop,
          direction: _kd._horizontal,
          length: _pl._getAxisWidth(),
          color: _xColor,
          major: {
            items: _xItems + 1,
            step: _pl._getAxisHeight() / _xItems,
            color: _majColor,
            tickSize: _st_xAxis[_ko._majorTickSize],
            show: {
              line: (_st_xAxis[_ko._majorLines][_k._visible]) ?
                      true : false,
              tick: (_st_xAxis[_ko._majorTick] === _kp._outside) ?
                      true : false
            }
          },
          minor: {
            items: _xItems * 2 + 1,
            step: _pl._getAxisHeight() / _xItems / 2,
            color: _minColor,
            tickSize: _st_xAxis[_ko._minorTickSize],
            show: {
              line: (_st_xAxis[_ko._minorLines][_k._visible]) ?
                      true : false,
              tick: (_st_xAxis[_ko._minorTick] === _kp._outside) ?
                      true : false
            }
          },
          sign: 1,
          minusRange: _minusRange
        };
        _xAxis = this._drawCooridnateGrid(_xAxis);
        _yAxis = {
          x: _pl._w - _pl._padRight,
          y: _pl._h - _pl._padBottom,
          direction: _kd._vertical,
          length: _pl._getAxisHeight(),
          color: _yColor,
          major: {
            items: _yMajorItems + 1,
            step: _pl._getAxisWidth() / _yMajorItems,
            color: _majColor,
            tickSize: _st_yAxis[_ko._majorTickSize],
            show: {
              line: (_st_yAxis[_ko._majorLines][_k._visible]) ?
                      true : false,
              tick: (_st_yAxis[_ko._majorTick] === _kp._outside) ?
                      true : false
            }
          },
          minor: {
            items: _yMinorItems + 1,
            step: _pl._getAxisWidth() / _yMinorItems,
            color: _minColor,
            tickSize: _st_yAxis[_ko._minorTickSize],
            show: {
              line: (_st_yAxis[_ko._minorLines][_k._visible]) ?
                      true : false,
              tick: (_st_yAxis[_ko._minorTick] === _kp._outside) ?
                      true : false
            }
          },
          sign: -1,
          minusRange: 0
        };
        _yAxis = this._drawCooridnateGrid(_yAxis);
        var _axes;
        _axes = {
          x: _pl._padLeft,
          y: _pl._h - _pl._padBottom,
          width: _pl._getAxisWidth(),
          height: -_pl._getAxisHeight(),
          format: {
            x: _st_xAxis,
            y: _st_yAxis
          },
          minusRange: {
            x: _minusRange,
            y: 0
          }
        };
        _axes = this._drawAxes(_axes);
        this._xLabels._toFront();
        this._plotArea = _newRect(_pp)
                ._setCoordinate(_pl._padLeft, _pl._padTop,
                        _pl._w - _pl._padRight, _pl._h - _pl._padBottom)
                ._format(_st_pl[_k._appearance])
                ._toBack()
                ;
        _pp.getById(this._id + "_area").toBack();
        this._draw_bar_series(_pp);
      },
      _create_column_line_area_plotarea: function (_pp)
      {
        var _listSeries2 = [],
                _clone = [],
                _count = 0,
                _stackedIndex = -1
                ;
        for (var i = 0; i < _listSeries.length; i++) {
          if (_listSeries[i]['Stacked']) {
            this._stacked = true;
            if (_stackedIndex < 0) {
              _listSeries2[_count] = _listSeries[i];
              _listSeries2[_count]._set = [_listSeries[i]];
              _listSeries2[_count]._indices = [i];
              _stackedIndex = _count;
              for (var j = 0; j < _listSeries[_stackedIndex][_k._items].length; j++) {
                _clone[j] = {};
                _clone[j][_ko._yValue] = _listSeries2[_stackedIndex][_k._items][j][_ko._yValue];
              }
              _listSeries2[_count]._clone = _clone;
              _count += 1;
            }
            else {
              for (var j = 0; j < _listSeries[i][_k._items].length; j++) {
                _listSeries2[_stackedIndex][_k._items][j][_ko._yValue] += _listSeries[i][_k._items][j][_ko._yValue];
              }
              _listSeries2[_stackedIndex]._set.push(_listSeries[i]);
              _listSeries2[_stackedIndex]._indices.push(i);
            }
          }
          else {
            _listSeries2[_count] = _listSeries[i];
            _listSeries2[_count]._indices = [i];
            _count += 1;
          }
        }
        _listSeries = _listSeries2;
        this._generateYMinMaxStep(true);
        var _labelText, _xItems,
                _info, _minusRange = 0,
                i, _format, _xAxis, _yAxis, _axes,
                _yMinorItems = _axis._getRange() / _st_yAxis[_ko._minorStep],
                _yMajorItems = _axis._getRange() / _st_yAxis[_ko._majorStep],
                _xItems = _st_xAxis[_k._items].length, _settings,
                _maxX = Number.MAX_VALUE,
                _minX = -Number.MAX_VALUE,
                _maxY = Number.MAX_VALUE,
                _minY = -Number.MAX_VALUE,
                _min, _max,
                _pl2, _resetPl = false,
                _addedValue,
                _loop = 10
                ;
        while ((_maxX > _pl._w || _minX < 0
                || _maxY > _pl._h || _minY < 0)
                && _loop > 0)
        {
          _loop -= 1;
          _maxX = -Number.MAX_VALUE;
          _minX = Number.MAX_VALUE;
          _maxY = -Number.MAX_VALUE;
          _minY = Number.MAX_VALUE;
          _pl2 = _pl._clone();
          _resetPl = false;
          if (_st_yAxis[_k._title]
                  && _st_yTitle[_k._visible])
          {
            _info = {
              text: KCJS.decodeURI(_st_yAxis[_k._title]),
              format: _st_yTitle,
              id: this._id + "_plotarea_yaxis_title",
              properties: {
                _class: 'yTitle',
                position: [_st_yTitle[_k._position], _kp._left],
                affectedPlotDirection: [_kd._horizontal]
              },
              plotArea: _pl,
              defaultAngle: -90
            };
            this._yTitle = this._drawTitle(_info);
            _pl = this._yTitle._resizedPlotArea();
            this._elements.push(this._yTitle);
            this._elementSettings.push(_st_yTitle);
          }
          if (_st_xAxis[_k._title]
                  && _st_xTitle[_k._visible])
          {
            _info = {
              text: KCJS.decodeURI(_st_xAxis[_k._title]),
              format: _st_xTitle,
              id: this._id + "_plotarea_xaxis_title",
              properties: {
                position: [_kp._bottom, _st_xTitle[_k._position]],
                affectedPlotDirection: [_kd._vertical]
              },
              plotArea: _pl
            };
            this._xTitle = this._drawTitle(_info);
            _pl = this._xTitle._resizedPlotArea();
            this._elements.push(this._xTitle);
            this._elementSettings.push(_st_xTitle);
          }
          if (_st_xLabel[_k._visible])
          {
            _info = {
              id: this._id + "_plotarea_xaxis_labels",
              properties: {
                _class: 'xLabels'
                , position: [_kp._bottom, _kp._center]
                , affectedPlotDirection: (_st_yAxis[_ko._minValue] < 0) ? [] : [_kd._vertical]
              },
              plotArea: _pl,
              items: []
            };
            for (i = 0; i < _xItems; i++) {
              _info.items[i] = {
                text: _st_xAxis[_k._items][i][_k._text],
                properties: {_float: _kfl._left},
                format: _st_xLabel
              };
            }
            this._xLabels = this._drawLabels(_info);
            _pl = this._xLabels._resizedPlotArea();
            this._elements.push(this._xLabels);
            this._elementSettings.push(_st_xLabel);
          }
          if (_st_yAxis[_ko._majorStep] && _st_yLabel[_k._visible])
          {
            _info = {
              id: this._id + "_plotarea_yaxis_labels",
              properties: {
                fixedHeight: _pl._getAxisHeight()
                , _class: 'yLabels'
                , position: [_kp._center, _kp._left]
                , affectedPlotDirection: [_kd._horizontal]
              },
              plotArea: _pl,
              items: []
            };
            for (i = _yMajorItems; i > -1; i -= 1) {
              var _values = [(_st_yAxis[_ko._minValue] * 10 +
                        (i * 10) * _st_yAxis[_ko._majorStep]) / 10];
              _labelText = this._getFormatedString(
                      _st_yLabel[_kfo._data], _values, _st);
              _info.items[_yMajorItems - i] = {
                text: _labelText,
                properties: {
                  _float: _kfl._right
                  , clear: _kfl._right
                },
                format: _st_yLabel
              };
            }
            this._yLabels = this._drawLabels(_info);
            _pl = this._yLabels._resizedPlotArea();
            this._elements.push(this._yLabels);
            this._elementSettings.push(_st_yLabel);
          }
          if (_defined(this._legends))
          {
            var
                    _le_w = this._legends._getWidth(),
                    _le_h = this._legends._getHeight(),
                    _ax_w = _pl._getAxisWidth(),
                    _ax_h = _pl._getAxisHeight(),
                    _p = _st_le[_k._appearance][_k._position];
            if (_le_w > _ax_w && (_p === _kp._top || _p === _kp._bottom)) {
              _pl = this._legends._resizedPlotArea(_pl, true);
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._legends._set({maxWidth: _ax_w * 100 / 100})
                      ._resetSelf()
                      ._projectChildElemens()
                      ;
              _pl = this._legends._resizedPlotArea(_pl);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
            if (_le_h > _ax_h && (_p === _kp._left || _p === _kp._right)) {
              _pl = this._legends._resizedPlotArea(_pl, true);
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._legends._set({maxHeight: _ax_h * 100 / 100})
                      ._resetSelf()
                      ._projectChildElemens()
                      ;
              _pl = this._legends._resizedPlotArea(_pl);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
          }
          if (this._xLabels) {
            this._xLabels
                    ._set({fixedWidth: _pl._getAxisWidth() / (_xItems) * (_xItems - 1)})
                    ._projectChildElemens()
                    ;
          }
          if (this._yLabels) {
            this._yLabels
                    ._set({fixedHeight: _pl._getAxisHeight()})
                    ._projectChildElemens()
                    ;
          }
          for (i = 0; i < this._elements.length; i++) {
            this._elements[i]._act(_postSettings, this._elementSettings[i]);
          }
          for (i = 1; i < this._elements.length; i++) {
            var _pl3 = this._elements[i - 1]._resizedPlotArea();
            this._elements[i]
                    ._setPlotArea(_pl3);
          }
          for (i = 0; i < this._elements.length; i++) {
            this._elements[i]
                    ._mergePlotArea(_pl)
                    ._projectSelf()
                    ;
          }
          for (i = 0; i < this._elements.length; i++) {
            var _e = this._elements[i],
                    _es = this._elementSettings[i],
                    _d = _e._get('affectedPlotDirection')
                    ;
            if (_es[_k._visible]
                    && _es['FullyVisible']
                    && _d.indexOf(_kd._horizontal) === -1
                    ) {
              _min = _e._getMinX();
              if (_minX > _min)
                _minX = _min;
              _max = _e._getMaxX();
              if (_maxX < _max)
                _maxX = _max;
            }
            if (_es[_k._visible]
                    && _es['FullyVisible']
                    && _d.indexOf(_kd._vertical) === -1
                    ) {
              _min = _e._getMinY();
              if (_minY > _min)
                _minY = _min;
              _max = _e._getMaxY();
              if (_maxY < _max)
                _maxY = _max;
            }
          }
          var _changedPl = {
            _padTop: 0,
            _padBottom: 0,
            _padLeft: 0,
            _padRight: 0
          };
          if (_minX < 0) {
            _addedValue = -_minX + 5;
            _pl2._padLeft += _addedValue;
            _changedPl._padLeft = _addedValue;
            _resetPl = true;
          }
          if (_maxX > _pl2._w) {
            _addedValue = _maxX - _pl2._w + 5;
            _pl2._padRight += _addedValue;
            _changedPl._padRight = _addedValue;
            _resetPl = true;
          }
          if (_minY < 0) {
            _addedValue = -_minY + 5;
            _pl2._padTop += _addedValue;
            _changedPl._padTop = _addedValue;
            _resetPl = true;
          }
          if (_maxY > _pl2._h) {
            _addedValue = _maxY - _pl2._h + 5;
            _pl2._padBottom += _addedValue;
            _changedPl._padBottom = _addedValue;
            _resetPl = true;
          }
          if (_resetPl) {
            if (_defined(this._title)) {
              var _oPl = this._originalPlotArea;
              if (_changedPl._padTop)
                if (_changedPl._padTop < _oPl._getAxisHeight())
                  _oPl._padTop += _changedPl._padTop;
                else
                  break;
              if (_changedPl._padBottom)
                if (_changedPl._padBottom < _oPl._getAxisHeight())
                  _oPl._padBottom += _changedPl._padBottom;
                else
                  break;
              if (_changedPl._padLeft)
                if (_changedPl._padLeft < _oPl._getAxisWidth())
                  _oPl._padLeft += _changedPl._padLeft;
                else
                  break;
              if (_changedPl._padRight)
                if (_changedPl._padRight < _oPl._getAxisWidth())
                  _oPl._padRight += _changedPl._padRight;
                else
                  break;
            }
            for (i = 0; i < this._elements.length; i++)
              this._elements[i]._remove();
            this._elements.splice(0, this._elements.length);
            this._create_title(_pp);
            if (this._legends) {
              _pl2 = this._legends._resizedPlotArea(_pl2, true);
              this._create_legend(_pp);
              _pl2 = this._legends._resizedPlotArea(_pl2);
            }
            _pl = _pl2._clone();
          }
        }
        if (_st_yAxis[_ko._minValue] < 0) {
          _minusRange = (Math.abs(_st_yAxis[_ko._minValue]) / _axis._getRange())
                  * _pl._getAxisHeight();
          if (this._xLabels)
            this._xLabels._translateAbsolute(
                    0, -_minusRange + this._xLabels._getHeight() + KC.pad.element);
        }
        _xAxis = {
          x: _pl._w - _pl._padRight,
          y: _pl._h - _pl._padBottom,
          direction: _kd._vertical,
          length: _pl._getAxisHeight(),
          color: _xColor,
          major: {
            items: _xItems + 1,
            step: _pl._getAxisWidth() / _xItems,
            color: _majColor,
            tickSize: _st_xAxis[_ko._majorTickSize],
            show: {
              line: (_st_xAxis[_ko._majorLines][_k._visible]) ? true : false,
              tick: (_st_xAxis[_ko._majorTick] === _kp._outside) ? true : false
            }
          },
          minor: {
            items: _xItems * 2 + 1,
            step: _pl._getAxisWidth() / _xItems / 2,
            color: _minColor,
            tickSize: _st_xAxis[_ko._minorTickSize],
            show: {
              line: (_st_xAxis[_ko._minorLines][_k._visible]) ? true : false,
              tick: (_st_xAxis[_ko._minorTick] === _kp._outside) ? true : false
            }
          },
          sign: -1,
          minusRange: _minusRange
        };
        _xAxis = this._drawCooridnateGrid(_xAxis);
        _yAxis = {
          x: _pl._padLeft,
          y: _pl._padTop,
          direction: _kd._horizontal,
          length: _pl._getAxisWidth(),
          color: _yColor,
          major: {
            items: _yMajorItems + 1,
            step: _pl._getAxisHeight() / _yMajorItems,
            color: _majColor,
            tickSize: _st_yAxis[_ko._majorTickSize],
            show: {
              line: (_st_yAxis[_ko._majorLines][_k._visible]) ? true : false,
              tick: (_st_yAxis[_ko._majorTick] === _kp._outside) ? true : false
            }
          },
          minor: {
            items: _yMinorItems + 1,
            step: _pl._getAxisHeight() / _yMinorItems,
            color: _minColor,
            tickSize: _st_yAxis[_ko._minorTickSize],
            show: {
              line: (_st_yAxis[_ko._minorLines][_k._visible]) ? true : false,
              tick: (_st_yAxis[_ko._minorTick] === _kp._outside) ? true : false
            }
          },
          sign: 1,
          minusRange: 0
        };
        _yAxis = this._drawCooridnateGrid(_yAxis);
        _axes = {
          x: _pl._padLeft,
          y: _pl._h - _pl._padBottom,
          width: _pl._getAxisWidth(),
          height: -_pl._getAxisHeight(),
          format: {
            x: _st_xAxis,
            y: _st_yAxis
          },
          minusRange: {
            x: 0,
            y: -_minusRange
          }
        };
        _axes = this._drawAxes(_axes);
        if (this._xLabels)
          this._xLabels._toFront();
        this._plotArea = _newRect(_pp)
                ._setCoordinate(_pl._padLeft, _pl._padTop,
                        _pl._w - _pl._padRight, _pl._h - _pl._padBottom)
                ._format(_st_pl[_k._appearance])
                ._toBack()
                ;
        _pp.getById(this._id + "_area").toBack();
        var
                _group_visible_columns = this._draw_column_series(_pp, _settings),
                _group_line_points = this._draw_line_series(_pp, _settings),
                _group_area_points = this._draw_area_series(_pp, _settings)
                ;
        var _separator = _pp.rect(_pl._padLeft, _pl._padTop,
                _pl._getAxisWidth(), _pl._getAxisHeight());
        _stroke(_separator, "#fff", 1, 0, "#fff", 0);
        _separator.mouseout(function (e) {
          var _chart = _get_chart(this.node);
          _chart._tooltip_timeout_id = setTimeout(
                  "KoolChartJS.off_tooltip('" + _chart._id + "')", 50);
        });
        if (_group_visible_columns)
          _group_visible_columns._toFront();
        if (_group_line_points)
          _group_line_points._toFront();
        if (_group_area_points)
          _group_area_points._toFront();
        var _tooltip_rec = _addRec(0, 0, 0, 0, _pp);
        _tooltip_rec.id = "tooltip_rec";
        var _tooltip_text = _addText("", _pp, null);
        _tooltip_text.id = "tooltip_text";
        _tooltip_rec.attr("opacity", 0);
        _tooltip_text.attr("opacity", 0);
        _tooltip_rec.mouseover(function () {
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
        });
        _tooltip_text.mouseover(function () {
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
        });
      },
      _draw_area_series: function (_pp)
      {
        var
                _group_points, _group_series,
                _axisWidth = _pl._getAxisWidth(),
                _items_count, _item_width,
                _bgcolorSeries, _area, _item,
                _lapp, _info, _mapp,
                _label_text, _label,
                _direction, _position,
                i, j, k, _minusRange,
                _shape, _value, _line,
                _x1, _y1, _x2, _y2, _values,
                _old_point_height, _point_height
                ;
        _minusRange = _axis._getMinusRange(_kd._vertical);
        _items_count = _st_xAxis[_k._items].length;
        _item_width = _axisWidth / _items_count;
        _group_points = _newContainer(_pp)
                ._id("group_area_points");
        for (i = 0; i < _listSeries.length; i++)
        {
          if (_listSeries[i][_kc._type] === _kc._area)
          {
            _bgcolorSeries = _listSeries[i][_k._appearance][_k._bgcolor];
            _group_series = _newContainer(_pp)
                    ._id(this._id + "_plotarea_series" + i);
            var _old_point_height = null;
            for (j = 0; j < _listSeries[i][_k._items].length; j++)
            {
              _item = _listSeries[i][_k._items][j];
              if (_notDefined(_item[_ko._yValue]))
              {
                _old_point_height = null;
              }
              else
              {
                _value = _item[_ko._yValue];
                _point_height = _axis._getColHeight(_value, _kd._vertical);
                _x2 = _pl._getX(_item_width * (j + 0.5));
                _y2 = _pl._getY(_point_height, _minusRange);
                if (_defined(_old_point_height) && _defined(_point_height))
                {
                  _x1 = _x2 - _item_width;
                  _y1 = _pl._getY(_old_point_height, _minusRange);
                  _line = _newLine(_pp)
                          ._setCoordinate(_x1, _y1, _x2, _y2)
                          ._set({
                            _class: 'line',
                            oldHeight: _old_point_height,
                            newHeight: _point_height,
                            animWait: i
                          })
                          ._stroke(_bgcolorSeries, 1, 1, "none")
                          ;
                  _area = _newFourangle(_pp)
                          ._set({
                            _class: 'area',
                            oldHeight: _old_point_height,
                            newHeight: _point_height,
                            animWait: i
                          })
                          ._addFourAngles(
                                  _x1, _y1, _x2, _y2, _x2, _y2 + _point_height,
                                  _x1, _y1 + _old_point_height)
                          ._stroke(_bgcolorSeries, 0, 0, _bgcolorSeries)
                          ._attr("opacity", 0.25)
                          ;
                  _group_series._addElements(_line);
                  if (_defined(_shape))
                    _shape._toFront();
                  if (_settings["Transitions"])
                  {
                    _line._setTransitionLine();
                    _area._setTransitionArea();
                  }
                }
                _mapp = _listSeries[i]["MarkersAppearance"];
                if (_mapp["Visible"]) {
                  _info = {
                    marker: _mapp["MarkersType"],
                    width: _item_width * (j + 0.5),
                    height: _point_height,
                    minusRange: _minusRange,
                    color: _bgcolorSeries,
                    transitionType: ['opacity', 'slide'],
                    animWait: i,
                    id: "point_" + i + "_" + j
                  };
                  _shape = this._drawMarker(_info);
                  _group_points._addElements(_shape);
                  _shape._dblclick(this._getDblClickHandler());
                  _shape._click(this._getClickHandler());
                  _shape._mouseover(this._getMouseOverPointHandler());
                }
                _old_point_height = _point_height;
                _lapp = _listSeries[i][_ka._label];
                if (_lapp[_k._visible]) {
                  _values = [_item[_ko._yValue]];
                  _label_text = this._getFormatedString(
                          _lapp[_kfo._data], _values, _st);
                  _label_text = _label_text.replace("{1}",
                          _st_xAxis[_k._items][j][_k._text]);
                  _direction = _kd._vertical;
                  _position = _lapp[_k._position];
                  _position = _kp._right;
                  switch (_position)
                  {
                    case _kp._above:
                      _position = _kp._outsideEnd;
                      break;
                    case _kp._below:
                      _position = _kp._outsideBegin;
                      break;
                    case _kp._left:
                      _direction = _kd._horizontal;
                      _position = _kp._outsideBegin;
                      break;
                    case _kp._right:
                      _direction = _kd._horizontal;
                      _position = _kp._outsideEnd;
                      break;
                  }
                  _info = {
                    _class: 'label',
                    text: _label_text,
                    direction: _direction,
                    position: _position,
                    appearance: _lapp
                  };
                  _label = this._drawChartLabel(_info, _shape);
                  if (_st["Transitions"])
                    _label._setTransitionOpaque(
                            500 * _listSeries.length + 100 * j);
                }
              }
            }
          }
        }
        return _group_points;
      },
      _draw_scatter_series: function (_pp)
      {
        var
                _group_points, _group_series,
                _axisWidth = _pl._getAxisWidth(),
                _items_count, _item_width,
                _bgcolorSeries, _item,
                _lapp, _info, _mapp,
                _label_text, _label,
                _direction, _position,
                i, j, k, _minusRange,
                _shape, _value, _line,
                _old_point_height, _point_height,
                _minusRange = {x: 0, y: 0}
        ;
        _items_count = _st_xAxis[_k._items].length;
        _item_width = _axisWidth / _items_count;
        _group_points = _newContainer(_pp)
                ._id("group_scatter_points");
        _minusRange.x = _axis._getMinusRange(_kd._horizontal, "xValue");
        _minusRange.y = _axis._getMinusRange(_kd._vertical);
        var _old_item_x = null;
        var _old_item_y = null;
        for (i = 0; i < _listSeries.length; i++)
        {
          if (_listSeries[i][_kc._type] === _kc._scat || 
              _listSeries[i][_kc._type] === _kc._scali)
          {
            _bgcolorSeries = _listSeries[i][_k._appearance][_k._bgcolor];
            _group_series = _newContainer(_pp)
                    ._id(this._id + "_plotarea_series" + i);
            for (j = 0; j < _listSeries[i][_k._items].length; j++)
            {
              _item = _listSeries[i][_k._items][j];
              if (_notDefined(_item[_ko._yValue])
                      && _notDefined(_item[_ko._xValue]))
              {
                _old_item_x = null;
                _old_item_y = null;
              }
              else
              {
                _value = _item[_ko._xValue];
                var _item_x = _axis._getColHeight(_value, _kd._horizontal, "xValue");
                _item_x = _pl._getX(_item_x, _minusRange.x);
                _value = _item[_ko._yValue];
                var _item_y = _axis._getColHeight(_value, _kd._vertical);
                _item_y = _pl._getY(_item_y, _minusRange.y);
                if (_listSeries[i]["ItemConnected"])
                {
                  if (_defined(_old_item_x)
                          && _defined(_old_item_y))
                  {
                    _line = _newLine(_pp)
                            ._setCoordinate(
                                    _old_item_x, _old_item_y, _item_x, _item_y)
                            ._set({
                              _class: 'line',
                              oldHeight: _old_point_height,
                              newHeight: _point_height,
                              animWait: j
                            })
                            ._stroke(_bgcolorSeries, 1, 1, "none")
                            ;
                    _group_series._addElements(_line);
                    if (_defined(_shape))
                      _shape._toFront();
                    if (_settings["Transitions"])
                      _line._setTransitionPlain();
                  }
                  _old_item_x = _item_x;
                  _old_item_y = _item_y;
                }
                _mapp = _listSeries[i]["MarkersAppearance"];
                if (_mapp["Visible"]) {
                  _info = {
                    marker: _mapp["MarkersType"],
                    x: _item_x,
                    y: _item_y,
                    color: _bgcolorSeries,
                    transitionType: ['opacity'],
                    animWait: 0,
                    id: "point_" + i + "_" + j
                  };
                  _shape = this._drawMarker(_info);
                  _group_points._addElements(_shape);
                  _shape._dblclick(this._getDblClickHandler());
                  _shape._click(this._getClickHandler());
                  _shape._mouseover(this._getMouseOverScatterHandler());
                }
                _lapp = _listSeries[i][_ka._label];
                if (_lapp[_k._visible]) {
                  _values = [_item[_ko._xValue], _item[_ko._yValue]];
                  _label_text = this._getFormatedString(
                          _lapp[_kfo._data], _values, _st);
                  _direction = _kd._vertical;
                  _position = _lapp[_k._position];
                  switch (_position)
                  {
                    case _kp._above:
                      _position = _kp._outsideEnd;
                      break;
                    case _kp._below:
                      _position = _kp._outsideBegin;
                      break;
                    case _kp._left:
                      _direction = _kd._horizontal;
                      _position = _kp._outsideBegin;
                      break;
                    case _kp._right:
                      _direction = _kd._horizontal;
                      _position = _kp._outsideEnd;
                      break;
                  }
                  _info = {
                    _class: 'label',
                    text: _label_text,
                    direction: _direction,
                    position: _position,
                    appearance: _lapp
                  };
                  _label = this._drawChartLabel(_info, _shape);
                  if (_st["Transitions"])
                    _label._setTransitionOpaque(500 + j * 120);
                }
              }
            }
            _old_item_x = null;
            _old_item_y = null;
          }
        }
        var _separator = _pp.rect(_pl._padLeft, _pl._padTop,
                _pl._getAxisWidth(), _pl._getAxisHeight());
        _stroke(_separator, "#fff", 1, 0, "#fff", 0);
        _separator.mouseout(function (e) {
          var _chart = _get_chart(this.node);
          _chart._tooltip_timeout_id = setTimeout("KoolChartJS.off_tooltip('" + _chart._id + "')", 50);
        });
        _group_points._toFront();
        var _tooltip_rec = _addRec(0, 0, 0, 0, _pp);
        _tooltip_rec.id = "tooltip_rec";
        var _tooltip_text = _addText("", _pp, null);
        _tooltip_text.id = "tooltip_text";
        _tooltip_rec.attr("opacity", 0);
        _tooltip_text.attr("opacity", 0);
        _tooltip_rec.mouseover(function () {
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
        });
        _tooltip_text.mouseover(function () {
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
        });
      },
      _draw_line_series: function (_pp)
      {
        var
                _group_points, _group_series,
                _axisWidth = _pl._getAxisWidth(),
                _items_count, _item_width,
                _bgcolorSeries, _item,
                _lapp, _info, _mapp,
                _label_text, _label,
                _direction, _position,
                i, j, k, _minusRange,
                _shape, _value, _line,
                _x1, _y1, _x2, _y2,
                _old_point_height, _point_height
                ;
        _minusRange = _axis._getMinusRange(_kd._vertical);
        _items_count = _st_xAxis[_k._items].length;
        _item_width = _axisWidth / _items_count;
        _group_points = _newContainer(_pp)
                ._id("group_line_points");
        for (i = 0; i < _listSeries.length; i++)
        {
          if (_listSeries[i][_kc._type] === _kc._line)
          {
            _group_series = _newContainer(_pp)
                    ._id(this._id + "_plotarea_series" + i);
            _old_point_height = null;
            for (j = 0; j < _listSeries[i][_k._items].length; j++)
            {
              _item = _listSeries[i][_k._items][j];
              if (_notDefined(_item[_ko._yValue]))
              {
                _old_point_height = null;
              }
              else
              {
                _bgcolorSeries = _listSeries[i][_k._appearance][_k._bgcolor];
                _value = _item[_ko._yValue];
                _point_height = _axis._getColHeight(_value, _kd._vertical);
                _x2 = _pl._getX(_item_width * (j + 0.5));
                _y2 = _pl._getY(_point_height, _minusRange);
                if (_defined(_old_point_height) && _defined(_point_height))
                {
                  _x1 = _x2 - _item_width;
                  _y1 = _pl._getY(_old_point_height, _minusRange);
                  _line = _newLine(_pp)
                          ._setCoordinate(_x1, _y1, _x2, _y2)
                          ._set({
                            _class: 'line',
                            oldHeight: _old_point_height,
                            newHeight: _point_height,
                            animWait: i
                          })
                          ._stroke(_bgcolorSeries, 1, 1, "none")
                          ;
                  _group_series._addElements(_line);
                  if (_defined(_shape))
                    _shape._toFront();
                  if (_settings["Transitions"])
                    _line._setTransitionLine();
                }
                _mapp = _listSeries[i]["MarkersAppearance"];
                if (_mapp["Visible"]) {
                  _info = {
                    marker: _mapp["MarkersType"],
                    width: _item_width * (j + 0.5),
                    height: _point_height,
                    minusRange: _minusRange,
                    color: _bgcolorSeries,
                    transitionType: ['opacity', 'slide'],
                    animWait: i,
                    id: "point_" + i + "_" + j
                  };
                  _shape = this._drawMarker(_info);
                  _group_points._addElements(_shape);
                  _shape._dblclick(this._getDblClickHandler());
                  _shape._click(this._getClickHandler());
                  _shape._mouseover(this._getMouseOverPointHandler());
                }
                _old_point_height = _point_height;
                _lapp = _listSeries[i][_ka._label];
                if (_lapp[_k._visible]) {
                  _values = [_item[_ko._yValue]];
                  _label_text = this._getFormatedString(
                          _lapp[_kfo._data], _values, _st);
                  _label_text = _label_text.replace("{1}",
                          _st_xAxis[_k._items][j][_k._text]);
                  _direction = _kd._vertical;
                  _position = _lapp[_k._position];
                  switch (_position)
                  {
                    case _kp._above:
                      _position = _kp._outsideEnd;
                      break;
                    case _kp._below:
                      _position = _kp._outsideBegin;
                      break;
                    case _kp._left:
                      _direction = _kd._horizontal;
                      _position = _kp._outsideBegin;
                      break;
                    case _kp._right:
                      _direction = _kd._horizontal;
                      _position = _kp._outsideEnd;
                      break;
                  }
                  _info = {
                    _class: 'label',
                    text: _label_text,
                    direction: _direction,
                    position: _position,
                    appearance: _lapp
                  };
                  _label = this._drawChartLabel(_info, _shape);
                  if (_st["Transitions"])
                    _label._setTransitionOpaque(
                            500 * _listSeries.length + 100 * j);
                }
              }
            }
          }
        }
        return _group_points;
      },
      _draw_column_series: function (_pp)
      {
        var
          _axisWidth = _pl._getAxisWidth(),
          _items_count, _item_width,
          _externalGapRatio, _internalGapRatio,
          _total_columns, _column_width, _col_height,
          _start, _base_x, _value,
          _columns, _group_visible_columns,
          _bgcolorSeries, _column,
          _visible_column, _lapp,
          _label_text, _label, _item,
          _direction, _position,
          _highlight,
          i, j, k, _minusRange,
          _y0, _y, _info,
          _series, _value, _index,
          _stack = 0, _labelWait,
          _ratio
          ;
        _minusRange = _axis._getMinusRange(_kd._vertical);
        _y0 = _pl._h - _pl._padBottom - _minusRange;
        _items_count = _st_xAxis[_k._items].length;
        _item_width = _axisWidth / _items_count;
        _total_columns = 0;
        for (i = 0; i < _listSeries.length; i++)
          if (_listSeries[i][_kc._type] === _kc._col)
            _total_columns++;
        if (!K._isEmpty(_st["CategoryGapRatio"])) {
          _ratio = _st["CategoryGapRatio"];
          _externalGapRatio = _ratio / (_ratio + 1);
        }
        else {
          _externalGapRatio = 1.4 / (1 + 1.4 * _total_columns);
        }
        _internalGapRatio = _st["BarGapRatio"];
        _column_width = _item_width * (1 - _externalGapRatio) /
                (_total_columns + (_total_columns - 1) * _internalGapRatio);
        _start = _item_width * _externalGapRatio / 2;
        _group_visible_columns = _newContainer(_pp)
                ._id("group_visible_columns");
        _columns = [];
        for (i = 0; i < _listSeries.length; i++) {
          if (_listSeries[i][_kc._type] === _kc._col) {
            _columns[i] = _newContainer(_pp)
                    ._id(this._id + "_plotarea_series" + i);
            for (j = 0; j < _listSeries[i][_k._items].length; j++) {
              _labelWait = 0;
              _item = _listSeries[i][_k._items][j];
              _index = _listSeries[i]._indices[0];
              _base_x = _pl._padLeft + _item_width * j;
              if (_defined(_item[_ko._yValue])) {
                _y = 0;
                _info = K._newObject({
                  id: "column_" + (_index) + "_" + j,
                  gradientDirection: _kd._horizontal,
                  width: _column_width,
                  x: _base_x + _start,
                  y: _y0,
                  _class: 'column'
                });
                if (_listSeries[i]['Stacked']) {
                  for (k = 0; k < _listSeries[i]._set.length; k++) {
                    _series = _listSeries[i]._set[k];
                    _index = _listSeries[i]._indices[k];
                    _value = (k === 0) ? _series._clone[j][_ko._yValue]
                            : _series[_k._items][j][_ko._yValue];
                    _series[_k._items][j]["tipValue"] = _value;
                    _col_height = _axis._getColHeight(
                            _value, _kd._vertical);
                    _bgcolorSeries = _series[_k._appearance][_k._bgcolor];
                    _info._merge({
                      color: _bgcolorSeries,
                      height: -_col_height,
                      y: _y0 - _y,
                      animWait: k
                    });
                    _column = this._drawColumn(_info);
                    _columns[i]._addElements(_column);
                    _info._merge({
                      id: "visible_column_" + (_index) + "_" + j,
                      height: -_col_height,
                      y: _y0 - _y
                    });
                    _visible_column = this._drawColumn(_info)
                            ._stroke("#fff", 0, 0, "#fff", 0);
                    _group_visible_columns._addElements(_visible_column);
                    _visible_column._dblclick(this._getDblClickHandler());
                    _visible_column._click(this._getClickHandler());
                    _visible_column._mouseover(this._getMouseOverHandler());
                    _y += _col_height;
                    _labelWait += 1;
                  }
                }
                else {
                  _value = _item[_ko._yValue];
                  _col_height = _axis._getColHeight(_value, _kd._vertical);
                  _bgcolorSeries = _listSeries[i][_k._appearance][_k._bgcolor];
                  _index = _listSeries[i]._indices[0];
                  _info._merge({
                    color: _bgcolorSeries,
                    height: -_col_height
                  });
                  _column = this._drawColumn(_info);
                  _columns[i]._addElements(_column);
                  _info._merge({
                    id: "visible_column_" + (_index) + "_" + j,
                    height: -_col_height,
                    y: _y0
                  });
                  _visible_column = this._drawColumn(_info)
                          ._stroke("#fff", 0, 0, "#fff", 0);
                  _group_visible_columns._addElements(_visible_column);
                  _visible_column._dblclick(this._getDblClickHandler());
                  _visible_column._click(this._getClickHandler());
                  _visible_column._mouseover(this._getMouseOverHandler());
                  _y += _col_height;
                  _labelWait += 1;
                }
                _col_height = _y;
                _lapp = _listSeries[i][_ka._label];
                if (_lapp[_k._visible]) {
                  _values = [_item[_ko._yValue]];
                  _label_text = this._getFormatedString(
                          _lapp[_kfo._data], _values, _st);
                  _label_text = _label_text.replace("{1}",
                          _st_xAxis[_k._items][j][_k._text]);
                  _direction = _kd._vertical;
                  _position = (_col_height >= 0) ? _lapp[_k._position]
                          : _inversePosition(_lapp[_k._position]);
                  _info = {
                    _class: 'label',
                    text: _label_text,
                    direction: _direction,
                    position: _position,
                    appearance: _lapp
                  };
                  _label = this._drawChartLabel(_info, _column);
                  if (_st["Transitions"])
                    _label._setTransitionOpaque(700 * _labelWait);
                }
              }
            }
            if (_st["Transitions"])
              _columns[i]._setTransitionHeight();
            _start += _column_width * (1 + _internalGapRatio);
            if (_listSeries[i]['Stacked'])
              _stack += _listSeries[i]._set.length - 1;
          }
        }
        _highlight = _newRect(_pp)
                ._id('highlight')
                ._stroke("none", 0, 0, "#fff", 0.2)
                ;
        return _group_visible_columns;
      },
      _draw_bar_series: function (_pp)
      {
        var _axisWidth = _pl._getAxisWidth(),
                _axisHeight = _pl._getAxisHeight(),
                _items_count, _item_width,
                _externalGapRatio, _internalGapRatio,
                _total_columns, _column_width, _col_height,
                _start, _base_y, _value,
                _columns, _group_visible_columns,
                _bgcolorSeries, _column,
                _visible_column, _lapp,
                _label_text, _label, _item,
                _direction, _position,
                _highlight,
                i, j, k, _minusRange,
                _x, _x0, _info,
                _series, _value, _index,
                _stack = 0, _labelWait,
                _ratio
                ;
        _items_count = _st_xAxis[_k._items].length;
        _minusRange = _axis._getMinusRange(_kd._horizontal);
        _x0 = _pl._padLeft + _minusRange;
        _item_width = _axisHeight / _items_count;
        _total_columns = 1;
        for (i = 1; i < _listSeries.length; i++)
          if (_listSeries[i][_kc._type] === _kc._bar)
            _total_columns += 1;
        if (!K._isEmpty(_st["CategoryGapRatio"])) {
          _ratio = _st["CategoryGapRatio"];
          _externalGapRatio = _ratio / (_ratio + 1);
        }
        else {
          _externalGapRatio = 1.4 / (1 + 1.4 * _total_columns);
        }
        _internalGapRatio = _st["BarGapRatio"];
        _column_width = _item_width * (1 - _externalGapRatio) /
                (_total_columns + (_total_columns - 1) * _internalGapRatio);
        _start = _item_width * _externalGapRatio / 2;
        _group_visible_columns = _newContainer(_pp)
                ._id("group_visible_columns")
                ;
        _columns = [];
        for (i = 0; i < _listSeries.length; i++)
        {
          if (_listSeries[i][_kc._type] === _kc._bar)
          {
            _columns[i] = _newContainer(_pp)
                    ._id(this._id + "_plotarea_series" + i)
                    ;
            for (j = 0; j < _listSeries[i][_k._items].length; j++)
            {
              _labelWait = 0;
              _item = _listSeries[i][_k._items][j];
              _index = _listSeries[i]._indices[0];
              _base_y = _pl._h - _pl._padBottom - _item_width * j;
              if (_defined(_item[_ko._yValue]))
              {
                _x = 0;
                _info = K._newObject({
                  id: "column_" + (_index) + "_" + j,
                  gradientDirection: _kd._vertical,
                  height: -_column_width,
                  x: _x0,
                  y: _base_y - _start,
                  _class: 'column'
                });
                if (_listSeries[i]['Stacked']) {
                  for (k = 0; k < _listSeries[i]._set.length; k++) {
                    _series = _listSeries[i]._set[k];
                    _index = _listSeries[i]._indices[k];
                    _value = (k === 0) ? _series._clone[j][_ko._yValue]
                            : _series[_k._items][j][_ko._yValue];
                    _series[_k._items][j]["tipValue"] = _value;
                    _col_height = _axis._getColHeight(_value, _kd._horizontal);
                    _bgcolorSeries = _series[_k._appearance][_k._bgcolor];
                    _info._merge({
                      color: _bgcolorSeries,
                      width: _col_height,
                      x: _x0 + _x,
                      animWait: k
                    });
                    _column = this._drawColumn(_info);
                    _columns[i]._addElements(_column);
                    _info._merge({
                      id: "visible_column_" + (_index) + "_" + j,
                      width: _col_height,
                      x: _x0 + _x
                    });
                    _visible_column = this._drawColumn(_info)
                            ._stroke("#fff", 0, 0, "#fff", 0);
                    _group_visible_columns._addElements(_visible_column);
                    _visible_column._dblclick(this._getDblClickHandler());
                    _visible_column._click(this._getClickHandler());
                    _visible_column._mouseover(this._getMouseOverHandler());
                    _x += _col_height;
                    _labelWait += 1;
                  }
                }
                else {
                  _value = _item[_ko._yValue];
                  _col_height = _axis._getColHeight(_value, _kd._horizontal);
                  _bgcolorSeries = _listSeries[i][_k._appearance][_k._bgcolor];
                  _index = _listSeries[i]._indices[0];
                  _info._merge({
                    color: _bgcolorSeries,
                    width: _col_height
                  });
                  _column = this._drawColumn(_info);
                  _columns[i]._addElements(_column);
                  _info._merge({
                    id: "visible_column_" + (_index) + "_" + j,
                    width: _col_height,
                    x: _x0
                  });
                  _visible_column = this._drawColumn(_info)
                          ._stroke("#fff", 0, 0, "#fff", 0);
                  _group_visible_columns._addElements(_visible_column);
                  _visible_column._dblclick(this._getDblClickHandler());
                  _visible_column._click(this._getClickHandler());
                  _visible_column._mouseover(this._getMouseOverHandler());
                  _x += _col_height;
                  _labelWait += 1;
                }
                _col_height = _x;
                _lapp = _listSeries[i][_ka._label];
                if (_lapp[_k._visible])
                {
                  _values = [_item[_ko._yValue]];
                  _label_text = this._getFormatedString(
                          _lapp[_kfo._data], _values, _st);
                  _label_text = _label_text.replace("{1}",
                          _st_xAxis[_k._items][j][_k._text]);
                  _direction = _kd._horizontal;
                  _position = (_col_height >= 0) ? _lapp[_k._position]
                          : _inversePosition(_lapp[_k._position])
                          ;
                  _info = {
                    _class: 'label',
                    text: _label_text,
                    direction: _direction,
                    position: _position,
                    appearance: _lapp
                  };
                  _label = this._drawChartLabel(_info, _column);
                  if (_st["Transitions"])
                    _label._setTransitionOpaque(700 * _labelWait);
                }
              }
            }
            if (_st["Transitions"])
              _columns[i]._setTransitionWidth();
            _start += _column_width * (1 + _internalGapRatio);
            if (_listSeries[i]['Stacked'])
              _stack += _listSeries[i]._set.length - 1;
          }
        }
        _highlight = _newRect(_pp)
                ._id('highlight')
                ._stroke("none", 0, 0, "#fff", 0.2)
                ;
        var _separator = _pp.rect(_pl._padLeft, _pl._padTop,
                _axisWidth, _axisHeight);
        _stroke(_separator, "#fff", 1, 0, "#fff", 0);
        _separator.mouseout(function (e) {
          var _chart = _get_chart(this.node);
          _chart._tooltip_timeout_id = setTimeout(
                  "KoolChartJS.off_tooltip('" + _chart._id + "')", 100);
        });
        _group_visible_columns._toFront();
        var _tooltip_rec = _addRec(0, 0, 0, 0, _pp);
        _tooltip_rec.id = "tooltip_rec";
        var _tooltip_text = _addText("", _pp, null);
        _tooltip_text.id = "tooltip_text";
        _tooltip_rec.attr("opacity", 0);
        _tooltip_text.attr("opacity", 0);
        _tooltip_rec.mouseover(function () {
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
        });
        _tooltip_text.mouseover(function () {
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
        });
      },
      _draw_pie_series: function (_pp)
      {
        var _series = _settings["PlotArea"]["ListOfSeries"][0];
        var _center_x = _pl._padLeft + (_pl._w - _pl._padLeft - _pl._padRight) / 2;
        var _center_y = _pl._padTop + (_pl._h - _pl._padTop - _pl._padBottom) / 2;
        var _total = 0;
        for (var i = 0; i < _series["Items"].length; i++)
        {
          _total += _series["Items"][i]["YValue"];
        }
        var _available_width = _pl._w - _pl._padLeft - _pl._padRight;
        var _available_height = _pl._h - _pl._padTop - _pl._padBottom;
        var _exploded = false;
        for (var i = 0; i < _series["Items"].length; i++)
          if (_series["Items"][i]["Exploded"]) {
            _exploded = true;
            break
          }
        var _explodedRatio = _series["ExplodedRatio"];
        var _pila = _series["PieLabel"];
        var _innerTickSize = _pila["ArcTickSize"];
        var _outerTickSize = _pila["LabelTickSize"];
        var _arcGap = _pila["ArcTickGap"] + _innerTickSize;
        var _tickGap = _pila["LabelTickGap"];
        var _radius = ((_available_width < _available_height) ? (_available_width - _arcGap * 2) : (_available_height - _arcGap * 2)) / 2 / (_exploded ? _explodedRatio : 1);
        if (this._is_svg)
        {
          var _this = K._domObj(this._id);
          var _svg = _this.firstChild;
          var _defs = (_svg.getElementsByTagName("defs"))[0];
          var _radialGradient = _newSVGNode("radialGradient", _defs);
          _radialGradient.id = this._id + "_unexploded";
          _radialGradient.setAttribute("gradientUnits", "userSpaceOnUse");
          _radialGradient.setAttribute("r", _radius);
          _radialGradient.setAttribute("cx", _center_x);
          _radialGradient.setAttribute("cy", _center_y);
          _radialGradient.setAttribute("fx", _center_x);
          _radialGradient.setAttribute("fy", _center_y);
          var _stop = _newSVGNode("stop", _radialGradient);
          _stop.setAttribute("style", "stop-color:#fff;stop-opacity:0.06");
          _stop.setAttribute("offset", "0%");
          var _stop = _newSVGNode("stop", _radialGradient);
          _stop.setAttribute("style", "stop-color:#fff;stop-opacity:0.2");
          _stop.setAttribute("offset", "83%");
          var _stop = _newSVGNode("stop", _radialGradient);
          _stop.setAttribute("style", "stop-color:#fff;stop-opacity:0");
          _stop.setAttribute("offset", "95%");
        }
        else
        {
          _settings["Transitions"] = false;
        }
        var _group_pie = _addGroup(_pp);
        _group_pie.id = this._id + "_plotarea_pie";
        var _group_visible_pies = _addGroup(_pp);
        var _start = -_series["StartAngle"];
        for (var i = 0; i < _series["Items"].length; i++)
        {
          var _angle = (_series["Items"][i]["YValue"] / _total) * 360;
          var _exploded_x = 0;
          var _exploded_y = 0;
          if (_series["Items"][i]["Exploded"])
          {
            _exploded_x = _radius * (_explodedRatio - 1) * Math.cos((_start + _angle / 2) * Math.PI / 180);
            _exploded_y = _radius * (_explodedRatio - 1) * Math.sin((_start + _angle / 2) * Math.PI / 180);
          }
          var _arc = _addArc(_center_x + _exploded_x, _center_y + _exploded_y, _radius, _start, _angle, _pp, _group_pie);
          _stroke(_arc, "black", 0, 0, _series["Items"][i]["BackgroundColor"], 1);
          if (this._is_svg)
          {
            if (_series["Items"][i]["Exploded"])
            {
              var _radialExploded = _radialGradient.cloneNode(true);
              _radialExploded.id = this._id + "_exploded" + i;
              _radialExploded.setAttribute("cx", _center_x + _exploded_x);
              _radialExploded.setAttribute("cy", _center_y + _exploded_y);
              _radialExploded.setAttribute("fx", _center_x + _exploded_x);
              _radialExploded.setAttribute("fy", _center_y + _exploded_y);
              _defs.appendChild(_radialExploded);
              var _arc = _addArc(_center_x + _exploded_x, _center_y + _exploded_y, _radius, _start, _angle, _pp, _group_pie);
              _stroke(_arc, "white", 0, 0, "white", 1);
              _arc.node.setAttribute("fill", "url(#" + this._id + "_exploded" + i + ")");
            }
            else
            {
              var _arc = _addArc(_center_x + _exploded_x, _center_y + _exploded_y, _radius, _start, _angle, _pp, _group_pie);
              _stroke(_arc, "white", 0, 0, "white", 1);
              _arc.node.setAttribute("fill", "url(#" + this._id + "_unexploded)");
            }
          }
          else
          {
            var _focus_x = ((_center_x + _exploded_x) - _arc.getBBox().x) / _arc.getBBox().width;
            var _focus_y = ((_center_y + _exploded_y) - _arc.getBBox().y) / _arc.getBBox().height;
            _arc.node.removeChild(_arc.node.lastChild);
            var _fill_node = document.createElement("rvml:fill");
            _fill_node["className"] = "rvml";
            _fill_node["type"] = "gradientTitle";
            _fill_node["focusposition"] = _focus_x + "," + _focus_y;
            _fill_node["focussize"] = "0,0";
            _fill_node["angle"] = "0";
            _fill_node["color"] = _series["Items"][i]["BackgroundColor"];
            _fill_node["color2"] = _series["Items"][i]["BackgroundColor"];
            _fill_node["colors"] = "0 " + _series["Items"][i]["BackgroundColor"] + ";.83 " + _blendwhite(_series["Items"][i]["BackgroundColor"], 0.2) + ";.95 " + _series["Items"][i]["BackgroundColor"];
            _fill_node["focus"] = "100%";
            _fill_node["rotate"] = false;
            _fill_node["method"] = "none";
            _arc.node.appendChild(_fill_node);
          }
          _visble_arc = _addArc(_center_x + _exploded_x, _center_y + _exploded_y, _radius, _start, _angle, _pp, _group_visible_pies);
          _stroke(_visble_arc, "#fff", 0, 0, "#fff", 0);
          _visble_arc.id = "visible_pie_" + i + "_" + (_start + _angle / 2);
          _visble_arc.mouseover(function () {
            var _chart = _get_chart(this.node);
            if (_chart._animating)
              return;
            var _idx = this.id.replace("visible_pie_", "").split("_");
            var _pos = _idx[0];
            var _angle = _idx[1];
            var _pp = this.paper;
            var _series = _chart._settings["PlotArea"]["ListOfSeries"][0];
            clearTimeout(_chart._tooltip_timeout_id);
            if (_series["TooltipsAppearance"]["Visible"]) {
              var _total = 0;
              for (var i = 0; i < _series["Items"].length; i++) {
                _total += _series["Items"][i]["YValue"];
              }
              if (_series["ShowRealValue"]) {
                var _text = (KCJS.decodeURI(_series["TooltipsAppearance"]["DataFormatString"])).replace("{0}", _number_format(_settings["NumberFormat"], _settings["DecimalNumber"], _settings["DecimalSeparator"], _settings["ThousandSeparator"], _series["Items"][_pos]["YValue"]));
                _text = _text.replace("{1}", KCJS.decodeURI(_series["Items"][_pos]["Name"]));
              }
              else {
                var _v = Math.round((_series["Items"][_pos]["YValue"] / _total) * 100 * Math.pow(10, _series["DecimalNumber"])) / Math.pow(10, _series["DecimalNumber"]);
                var _text = (KCJS.decodeURI(_series["TooltipsAppearance"]["DataFormatString"])).replace("{0}", _number_format(_settings["NumberFormat"], _settings["DecimalNumber"], _settings["DecimalSeparator"], _settings["ThousandSeparator"], _v));
                _text = _text.replace("{1}", KCJS.decodeURI(_series["Items"][_pos]["Name"]));
              }
              var _tooltip_rec = _pp.getById("tooltip_rec");
              var _tooltip_text = _pp.getById("tooltip_text");
              _tooltip_text.attr("text", _text);
              _style(_tooltip_text, _series["TooltipsAppearance"]["FontSize"], _series["TooltipsAppearance"]["FontFamily"], _series["TooltipsAppearance"]["FontColor"], _series["TooltipsAppearance"]["FontStyle"], _series["TooltipsAppearance"]["FontWeight"]);
              _stroke(_tooltip_rec, _series["Items"][_pos]["BackgroundColor"], 1, 1, _series["TooltipsAppearance"]["BackgroundColor"], 1);
              _tooltip_rec.attr("width", _tooltip_text.getBBox().width + 6);
              _tooltip_rec.attr("height", _tooltip_text.getBBox().height + 4);
              var _center_x = _chart._plot._padLeft + (_pl._w - _chart._plot._padLeft - _chart._plot._padRight) / 2;
              var _center_y = _chart._plot._padTop + (_pl._h - _chart._plot._padTop - _chart._plot._padBottom) / 2;
              var _label_radius = _radius * ((_series["Items"][_pos]["Exploded"]) ? _explodedRatio : 1) + _arcGap;
              var _x = _center_x + (_label_radius) * Math.cos(_angle * Math.PI / 180) - _tooltip_rec.getBBox().width / 2;
              var _y = _center_y + (_label_radius) * Math.sin(_angle * Math.PI / 180) - _tooltip_rec.getBBox().height / 2;
              if (_tooltip_rec.attr("opacity") > 0) {
                _tooltip_rec.animate({
                  "x": _x,
                  "y": _y
                }, 200, "cubic-bezier(0,0.3,0,1)");
                _tooltip_text.animate({
                  "x": _x + _tooltip_rec.getBBox().width / 2,
                  "y": _y + _tooltip_rec.getBBox().height / 2
                }, 200, "cubic-bezier(0,0.3,0,1)");
              }
              else {
                _tooltip_rec.attr("x", _x);
                _tooltip_rec.attr("y", _y);
                _tooltip_text.attr("x", _tooltip_rec.getBBox().x + _tooltip_rec.getBBox().width / 2);
                _tooltip_text.attr("y", _tooltip_rec.getBBox().y + _tooltip_rec.getBBox().height / 2);
                _tooltip_rec.animate({
                  "opacity": 1
                }, 200);
                _tooltip_text.animate({
                  "opacity": 1
                }, 200);
              }
            }
            var _path = this.attr("path");
            var _pp = this.paper;
            var _highlight = _pp.getById("highlight");
            _highlight.attr("path", _path);
            _highlight.attr("fill-opacity", 0.2);
            _chart._handleEvent("OnItemOver", {"Item": _series["Items"][_pos], "SeriesItems": _series["Items"]});
          });
          _visble_arc.click(function () {
            var _chart = _get_chart(this.node);
            if (_chart._animating)
              return;
            var _idx = this.id.replace("visible_pie_", "").split("_");
            var _pos = _idx[0];
            var _series = _chart._settings["PlotArea"]["ListOfSeries"][0];
            _chart._handleEvent("OnItemClick", {"Item": _series["Items"][_pos], "SeriesItems": _series["Items"]});
          });
          _visble_arc.dblclick(function () {
            var _chart = _get_chart(this.node);
            if (_chart._animating)
              return;
            var _idx = this.id.replace("visible_pie_", "").split("_");
            var _pos = _idx[0];
            var _series = _chart._settings["PlotArea"]["ListOfSeries"][0];
            _chart._handleEvent("OnItemDblClick", {"Item": _series["Items"][_pos], "SeriesItems": _series["Items"]});
          });
          _lapp = _series["LabelsAppearance"];
          if (_lapp[_k._visible])
          {
            if (_series["ShowRealValue"])
            {
              var _label_text = (KCJS.decodeURI(_series["LabelsAppearance"]["DataFormatString"])).replace("{0}", _number_format(_settings["NumberFormat"], _settings["DecimalNumber"], _settings["DecimalSeparator"], _settings["ThousandSeparator"], _series["Items"][i]["YValue"]));
              _label_text = _label_text.replace("{1}", KCJS.decodeURI(_series["Items"][i]["Name"]));
            }
            else
            {
              var _v = Math.round((_series["Items"][i]["YValue"] / _total) * 100 * Math.pow(10, _series["DecimalNumber"])) / Math.pow(10, _series["DecimalNumber"]);
              var _label_text = (KCJS.decodeURI(_series["LabelsAppearance"]["DataFormatString"])).replace("{0}", _number_format(_settings["NumberFormat"], _settings["DecimalNumber"], _settings["DecimalSeparator"], _settings["ThousandSeparator"], _v));
              _label_text = _label_text.replace("{1}", KCJS.decodeURI(_series["Items"][i]["Name"]));
            }
            var _label = _addText(_label_text, _pp, _group_pie);
            _style(_label, _series["LabelsAppearance"]["FontSize"], _series["LabelsAppearance"]["FontFamily"], _series["LabelsAppearance"]["FontColor"], _series["LabelsAppearance"]["FontStyle"], _series["LabelsAppearance"]["FontWeight"]);
            switch (_series["LabelsAppearance"]["Position"])
            {
              case "column":
                break;
              case "circle":
              default:
                var _label_radius = _radius * ((_series["Items"][i]["Exploded"]) ? _explodedRatio : 1) + _arcGap;
                var _label_x = _center_x + (_label_radius) * Math.cos((_start + _angle / 2) * Math.PI / 180);
                var _label_y = _center_y + (_label_radius) * Math.sin((_start + _angle / 2) * Math.PI / 180);
                var _line_x = _center_x + (_label_radius - _innerTickSize) * Math.cos((_start + _angle / 2) * Math.PI / 180);
                var _line_y = _center_y + (_label_radius - _innerTickSize) * Math.sin((_start + _angle / 2) * Math.PI / 180);
                var _line1 = _addLine(_line_x + 0.5, _line_y + 0.5, _label_x + 0.5, _label_y + 0.5, _pp, _group_pie);
                if (Math.cos((_start + _angle / 2) * Math.PI / 180) < 0)
                {
                  var _line2 = _addLine(_label_x + 0.5, _label_y + 0.5, _label_x + 0.5 - _outerTickSize, _label_y + 0.5, _pp, _group_pie);
                  _setPosition(_label, _label_x - _label.getBBox().width - _outerTickSize - _tickGap - _label.getBBox().x, _label_y - _label.getBBox().height / 2 - _label.getBBox().y);
                }
                else
                {
                  var _line2 = _addLine(_label_x + 0.5, _label_y + 0.5, _label_x + _outerTickSize + 0.5, _label_y + 0.5, _pp, _group_pie);
                  _setPosition(_label, _label_x + _outerTickSize + _tickGap - _label.getBBox().x, _label_y - _label.getBBox().height / 2 - _label.getBBox().y);
                }
                _stroke(_line1, _series["LabelsAppearance"]["FontColor"]);
                _stroke(_line2, _series["LabelsAppearance"]["FontColor"]);
                break;
            }
            if (_settings["Transitions"])
            {
              _label.attr("opacity", 0);
              _line1.attr("opacity", 0);
              _line2.attr("opacity", 0);
              var _anim = Raphael.animation({"opacity": 1}, 500, "linear");
              _label.animate(_anim.delay(2500 + 100 * i));
              var _anim = Raphael.animation({"opacity": 1}, 500, "linear");
              _line1.animate(_anim.delay(2500 + 100 * i));
              var _anim = Raphael.animation({"opacity": 1}, 500, "linear");
              _line2.animate(_anim.delay(2500 + 100 * i));
            }
          }
          _start += _angle;
        }
        this._end_animation = false;
        if (_settings["Transitions"])
        {
          _group_pie.animate({"transform": "R-360," + _center_x + "," + _center_y}, 2500, "cubic-bezier(0,0.3,0,1)", function () {
            var _chart = _get_chart(this.items[0].node);
            _chart._animating = false;
          });
          this._animating = true;
        }
        var _highlight = _pp.path("M0,0,0,0");
        _highlight.id = "highlight";
        _stroke(_highlight, "none", 0, 0, "#fff", 0.2);
        var _separator = _pp.rect(_pl._padLeft, _pl._padTop, _pl._w - _pl._padLeft - _pl._padRight, _pl._h - _pl._padTop - _pl._padBottom);
        _stroke(_separator, "#fff", 1, 0, "#fff", 0);
        _separator.mouseout(function (e) {
          var _chart = _get_chart(this.node);
          _chart._tooltip_timeout_id = setTimeout("KoolChartJS.off_tooltip('" + _chart._id + "')", 100);
        });
        _group_visible_pies.toFront();
        var _tooltip_rec = _addRec(0, 0, 0, 0, _pp);
        _tooltip_rec.id = "tooltip_rec";
        var _tooltip_text = _addText("", _pp);
        _tooltip_text.id = "tooltip_text";
        _tooltip_rec.attr("opacity", 0);
        _tooltip_text.attr("opacity", 0);
        _tooltip_rec.mouseover(function () {
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
        });
        _tooltip_text.mouseover(function () {
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
        });
      },
      _drawTitle: function (_info) {
        var _text, _title;
        _text = _newText(this._pp)
                ._set({defaultAngle: _info.defaultAngle})
                ._setText(_info.text)
                ._format(_info.format)
                ;
        _title = _newContainer(this._pp)
                ._id(_info.id)
                ._toBack()
                ._set(_info.properties)
                ._addElements(_text)
                ._projectChildElemens()
                ;
        _title._setPlotArea(_info.plotArea)
                ;
        return _title;
      },
      _drawLabels: function (_info) {
        var _text, _labels, i, _p, _w, _h,
                _kp = _key._positions;
        _labels = _newContainer(this._pp)
                ._id(_info.id)
                ;
        for (i = 0; i < _info.items.length; i += 1) {
          _text = _newText(this._pp)
                  ._set(_info.items[i].properties)
                  ._setText(_info.items[i].text)
                  ._format(_info.items[i].format)
                  ;
          _labels._addElements(_text);
          if (_defined(_info.items[i]._resize)) {
            _p = _info.items[i]._resize;
            if (_p === _kp._left || _p === _kp._right) {
              _w = _text._getWidth();
              _info.properties.fixedWidth -= _w / 2;
              _info.plotArea._addPad(_p, _w / 2);
            }
            else if (_p === _kp._top || _p === _kp._bottom) {
              _h = _text._getHeight();
              _info.properties.fixedHeight -= _h / 2;
              _info.plotArea._addPad(_p, _h / 2);
            }
          }
        }
        _labels
                ._set(_info.properties)
                ._projectChildElemens()
                ._setPlotArea(_info.plotArea)
                ;
        return _labels;
      },
      _parallelLines: function (_info, _items) {
        var _pp = this._pp;
        var _line, i,
                _lines = _newContainer(_pp),
                _infoX = _info.x,
                _infoY = _info.y
                ;
        for (i = 0; i < _items; i += 1) {
          _line = _newLine(_pp)
                  ._set(_info)
                  ._createLine()
                  ;
          _lines._addElements(_line);
          if (_info.direction === _kd._vertical)
            _info.x += _info.step;
          else if (_info.direction === _kd._horizontal)
            _info.y += _info.step;
        }
        _info.x = _infoX;
        _info.y = _infoY;
        return _lines;
      },
      _drawCooridnateGrid: function (_axis) {
        var
                _C = {_major: {}, _minor: {}},
        _i = {
          x: _axis.x,
          y: _axis.y,
          direction: _axis.direction,
          length: _axis.sign * _axis.length,
          step: _axis.sign * _axis.major.step,
          color: _axis.major.color
        };
        if (_axis.major.show.line === true)
          _C._major._lines = this._parallelLines(_i, _axis.major.items);
        _i.step = _axis.sign * _axis.minor.step;
        _i.color = _axis.minor.color;
        if (_axis.minor.show.line === true)
          _C._minor._lines = this._parallelLines(_i, _axis.minor.items);
        _i.color = _axis.color;
        _i.length = -_axis.sign * _axis.minor.tickSize;
        if (_axis.direction === _kd._horizontal)
          _i.x += _axis.sign * _axis.minusRange;
        else if (_axis.direction === _kd._vertical)
          _i.y += _axis.sign * _axis.minusRange;
        if (_axis.minor.show.tick === true)
          _C._minor._ticks = this._parallelLines(_i, _axis.minor.items);
        _i.step = _axis.sign * _axis.major.step;
        _i.length = -_axis.sign * _axis.major.tickSize;
        if (_axis.major.show.tick === true)
          _C._major._ticks = this._parallelLines(_i, _axis.major.items);
        return _C;
      },
      _drawAxes: function (_axes) {
        var _pp = this._pp;
        var
                _C = {};
        _C._y = _newLine(_pp)
                ._set({
                  x: _axes.x + _axes.minusRange.x
                  , y: _axes.y
                  , direction: _kd._vertical
                  , length: _axes.height
                  , format: _axes.format.y
                })
                ._createLine()
                ;
        _C._x = _newLine(_pp)
                ._set({
                  x: _axes.x
                  , y: _axes.y + _axes.minusRange.y
                  , direction: _kd._horizontal
                  , length: _axes.width
                  , format: _axes.format.x
                })
                ._createLine()
                ;
      },
      _drawColumn: function (_info) {
        var _pp = this._pp;
        var
                _column = _newRect(_pp),
                _gradient = _info.color + "-" +
                _blendwhite(_info.color, 0.3) + ":30-" + _info.color;
        if (_info.gradientDirection === _kd._horizontal)
          _gradient = "0-" + _gradient;
        else if (_info.gradientDirection === _kd._vertical)
          _gradient = "270-" + _gradient;
        _column
                ._stroke(_brightness(_info.color, 0.85), 1, 1, _gradient)
                ._setSize(_info.width, _info.height)
                ._move(_info.x, _info.y)
                ._id(_info.id)
                ._set({
                  _class: _defined(_info._class) ?
                          _info._class : '',
                  animWait: _defined(_info.animWait) ?
                          _info.animWait : 0
                })
                ;
        return _column;
      },
      _getFormatedString: function (_str, _values, _settings) {
        var
                _forStr = KCJS.decodeURI(_str);
        for (var i = 0; i < _values.length; i += 1)
          _forStr = _forStr.replace("{" + i + "}",
            _number_format(
              _settings[_kfo._number],
              _settings[_kfo._decimal],
              _settings[_kfo._decSepa],
              _settings[_kfo._thousandSepa],
              _values[i]))
            ;
        return _forStr;
      },
      _drawChartLabel: function (_info, _column) {
        var _pp = this._pp;
        var
                _label = _newText(_pp)
                ._set({
                  _class: _defined(_info._class) ?
                          _info._class : ''
                })
                ._setText(_info.text)
                ._stalk(_column,
                        _info.direction, _info.position)
                ._format(_info.appearance)
                ;
        return _label;
      },
      _drawMarker: function (_info) {
        var
                _shape,
                _x = _info.x,
                _y = _info.y,
                _y0 = _pl._getY(0, _info.minusRange),
                _color = _info.color,
                _animWait = _info.animWait
                ;
        if (_notDefined(_x))
          _x = _pl._getX(_info.width);
        if (_notDefined(_y))
          _y = _pl._getY(_info.height, _info.minusRange);
        switch (_info.marker)
        {
          case "square":
            _shape = _newRect(this._pp)
                    ._setSize(8, 8)
                    ._move(_x - 4, _y - 4)
                    ._stroke(_color, 2, 1, _color)
                    ;
            if (_st["Transitions"])
            {
              _shape._setTransition({
                type: _info.transitionType,
                dimension: 'y',
                slide: [_y0 - 4, _y - 4],
                delay: _animWait * 500
              });
            }
            break;
          case "triangle":
            _shape = _newTriangle(this._pp)
                    ._addTriangles(_x, _y - 5, 5 * Math.sqrt(3))
                    ._stroke(_color, 1, 1, _color)
                    ;
            if (_st["Transitions"])
            {
              var _triStr = _shape._get_triagles_d(
                      _x, _y0 - 5, 5 * Math.sqrt(3));
              _shape._setTransition({
                type: _info.transitionType,
                dimension: 'path',
                slide: [_triStr, _triStr],
                delay: _animWait * 500
              });
            }
            break;
          case "circle":
          default:
            _shape = _newCircle(this._pp)
                    ._addCircle(_x, _y, 4)
                    ._stroke(_color, 2, 1, _bgcolor)
                    ;
            if (_st["Transitions"])
            {
              _shape._setTransition({
                type: _info.transitionType,
                dimension: 'cy',
                slide: [_y0, _y],
                delay: _animWait * 500
              });
            }
            break;
        }
        _shape
                ._id(_info.id)
                ._set({
                  _class: 'point',
                  stalkedPoint: [_x, _y]
                })
                ;
        return _shape;
      },
      _getDblClickHandler: function () {
        return function () {
          var _chart = _get_chart(this.node);
          var _st = _chart._settings,
                  _st_pl = _st[_k._plotArea];
          var _idx = (this.id.replace("visible_column_", "")).split("_");
          var _series = _st_pl[_k._listSeries][_idx[0]];
          _chart._handleEvent("OnItemDblClick", {"Item": _series[_k._items][_idx[1]], "Category": _st_pl[_ko._xAxis][_k._items][_idx[1]], "SeriesItems": _series[_k._items]});
        };
      },
      _getClickHandler: function () {
        return function () {
          var _chart = _get_chart(this.node);
          var _st = _chart._settings,
                  _st_pl = _st[_k._plotArea];
          var _idx = (this.id.replace("visible_column_", "")).split("_");
          var _series = _st_pl[_k._listSeries][_idx[0]];
          _chart._handleEvent("OnItemClick", {"Item": _series[_k._items][_idx[1]], "Category": _st_pl[_ko._xAxis][_k._items][_idx[1]], "SeriesItems": _series[_k._items]});
        };
      },
      _getMouseOverHandler: function (_idPrefix) {
        return function () {
          if (_notDefined(_idPrefix))
            _idPrefix = "visible_column_";
          var _idx = (this.id.replace(_idPrefix, "")).split("_");
          var _pp = this.paper;
          var _chart = _get_chart(this.node);
          var _st = _chart._settings,
                  _st_pl = _st[_k._plotArea];
          var _st_xAxis = _st_pl[_ko._xAxis];
          var _series = _st_pl[_k._listSeries][_idx[0]];
          var _seriesTip = _series[_ka._tip];
          var _value = 0;
          clearTimeout(_chart._tooltip_timeout_id);
          if (_series[_ka._tip][_k._visible]) {
            if (_defined(_series[_k._items][_idx[1]]["tipValue"]))
              _value = _series[_k._items][_idx[1]]["tipValue"];
            else
              _value = _series[_k._items][_idx[1]][_ko._yValue];
            var _text = (KCJS.decodeURI(_seriesTip[_kfo._data])).replace("{0}", _number_format(_settings[_kfo._number], _settings[_kfo._decimal], _settings[_kfo._decSepa], _settings[_kfo._thousandSepa], _value));
            _text = _text.replace("{1}", _st_xAxis[_k._items][_idx[1]][_k._text]);
            var _tooltip_rec = _pp.getById("tooltip_rec");
            var _tooltip_text = _pp.getById("tooltip_text");
            _tooltip_text.attr("text", _text);
            _style(_tooltip_text, _seriesTip[_kf._size], _seriesTip[_kf._fam], _seriesTip[_kf._color], _seriesTip[_kf._style], _seriesTip[_kf._weight]);
            _stroke(_tooltip_rec, _series[_k._appearance][_k._bgcolor], 1, 1, _seriesTip[_k._bgcolor], 1);
            _tooltip_rec.attr("width", _tooltip_text.getBBox().width + 4);
            _tooltip_rec.attr("height", _tooltip_text.getBBox().height + 4);
            if (_series[_k._items][_idx[1]][_ko._yValue] > 0) {
              var _x = parseFloat(this.attr("x")) + parseFloat(this.attr("width")) + 5;
              var _y = parseFloat(this.attr("y"));
            }
            else {
              var _x = parseFloat(this.attr("x")) + parseFloat(this.attr("width")) + 5;
              var _y = parseFloat(this.attr("y")) + parseFloat(this.attr("height") - _tooltip_rec.getBBox().height);
            }
            if (_tooltip_rec.attr("opacity") > 0) {
              _tooltip_rec.animate({
                "x": _x,
                "y": _y
              }, 100);
              _tooltip_text.animate({
                "x": _x + _tooltip_rec.getBBox().width / 2,
                "y": _y + _tooltip_rec.getBBox().height / 2
              }, 100);
            }
            else {
              _tooltip_rec.attr("x", _x);
              _tooltip_rec.attr("y", _y);
              _tooltip_text.attr("x", _tooltip_rec.getBBox().x + _tooltip_rec.getBBox().width / 2);
              _tooltip_text.attr("y", _tooltip_rec.getBBox().y + _tooltip_rec.getBBox().height / 2);
              _tooltip_rec.animate({
                "opacity": 1
              }, 200);
              _tooltip_text.animate({
                "opacity": 1
              }, 200);
            }
          }
          var _x = this.attr("x");
          var _y = this.attr("y");
          var _width = this.attr("width");
          var _height = this.attr("height");
          var _pp = this.paper;
          var _highlight = _pp.getById("highlight");
          _highlight.attr({"x": _x, "y": _y, "width": _width, "height": _height});
          _highlight.attr("fill-opacity", 0.2);
          _chart._handleEvent("OnItemOver", {"Item": _series[_k._items][_idx[1]], "Category": _st_pl[_ko._xAxis][_k._items][_idx[1]], "SeriesItems": _series[_k._items]});
        };
      },
      _getMouseOverPointHandler: function () {
        return function () {
          var _idx = (this.id.replace("point_", "")).split("_");
          var _pp = this.paper;
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
          var _series = _chart._settings["PlotArea"]["ListOfSeries"][_idx[0]];
          var _st_xAxis = _chart._settings["PlotArea"][_ko._xAxis];
          if (_series["TooltipsAppearance"]["Visible"])
          {
            var _x = this.getBBox().x + this.getBBox().width / 2;
            var _y = this.getBBox().y + this.getBBox().height / 2;
            var _text = (KCJS.decodeURI(_series["TooltipsAppearance"]["DataFormatString"])).replace("{0}", _number_format(_settings["NumberFormat"], _settings["DecimalNumber"], _settings["DecimalSeparator"], _settings["ThousandSeparator"], _series["Items"][_idx[1]]["YValue"]));
            _text = _text.replace("{1}", _st_xAxis[_k._items][_idx[1]][_k._text]);
            var _tooltip_rec = _pp.getById("tooltip_rec");
            var _tooltip_text = _pp.getById("tooltip_text");
            _tooltip_text.attr("text", _text);
            _style(_tooltip_text, _series["TooltipsAppearance"]["FontSize"], _series["TooltipsAppearance"]["FontFamily"], _series["TooltipsAppearance"]["FontColor"], _series["TooltipsAppearance"]["FontStyle"], _series["TooltipsAppearance"]["FontWeight"]);
            _stroke(_tooltip_rec, _series["Appearance"]["BackgroundColor"], 1, 1, _series["TooltipsAppearance"]["BackgroundColor"], 1);
            _tooltip_rec.attr("width", _tooltip_text.getBBox().width + 2);
            _tooltip_rec.attr("height", _tooltip_text.getBBox().height + 4);
            if (_tooltip_rec.attr("opacity") > 0)
            {
              _tooltip_rec.animate({"x": _x + 10, "y": _y - _tooltip_rec.getBBox().height / 2}, 100);
              _tooltip_text.animate({"x": _x + 10 + _tooltip_rec.getBBox().width / 2, "y": _y - _tooltip_rec.getBBox().height / 2 + _tooltip_rec.getBBox().height / 2}, 100);
            }
            else
            {
              _tooltip_rec.attr("x", _x + 10);
              _tooltip_rec.attr("y", _y - _tooltip_rec.getBBox().height / 2);
              _tooltip_text.attr("x", _tooltip_rec.getBBox().x + _tooltip_rec.getBBox().width / 2);
              _tooltip_text.attr("y", _tooltip_rec.getBBox().y + _tooltip_rec.getBBox().height / 2);
              _tooltip_rec.animate({"opacity": 1}, 200);
              _tooltip_text.animate({"opacity": 1}, 200);
            }
          }
          _chart._handleEvent("OnItemOver", {"Item": _series["Items"][_idx[1]], "Category": _chart._settings["PlotArea"]["XAxis"]["Items"][_idx[1]], "SeriesItems": _series["Items"]});
        };
      },
      _getMouseOverScatterHandler: function () {
        return function () {
          var _idx = (this.id.replace("point_", "")).split("_");
          var _pp = this.paper;
          var _chart = _get_chart(this.node);
          clearTimeout(_chart._tooltip_timeout_id);
          var _series = _chart._settings["PlotArea"]["ListOfSeries"][_idx[0]];
          if (_series["TooltipsAppearance"]["Visible"])
          {
            var _x = this.getBBox().x + this.getBBox().width / 2;
            var _y = this.getBBox().y + this.getBBox().height / 2;
            var _text = (KCJS.decodeURI(_series["TooltipsAppearance"]["DataFormatString"])).replace("{0}", _number_format(_settings["NumberFormat"], _settings["DecimalNumber"], _settings["DecimalSeparator"], _settings["ThousandSeparator"], _series["Items"][_idx[1]]["XValue"])).replace("{1}", _number_format(_settings["NumberFormat"], _settings["DecimalNumber"], _settings["DecimalSeparator"], _settings["ThousandSeparator"], _series["Items"][_idx[1]]["YValue"]));
            var _tooltip_rec = _pp.getById("tooltip_rec");
            var _tooltip_text = _pp.getById("tooltip_text");
            _tooltip_text.attr("text", _text);
            _style(_tooltip_text, _series["TooltipsAppearance"]["FontSize"], _series["TooltipsAppearance"]["FontFamily"], _series["TooltipsAppearance"]["FontColor"], _series["TooltipsAppearance"]["FontStyle"], _series["TooltipsAppearance"]["FontWeight"]);
            _stroke(_tooltip_rec, _series["Appearance"]["BackgroundColor"], 1, 1, _series["TooltipsAppearance"]["BackgroundColor"], 1);
            _tooltip_rec.attr("width", _tooltip_text.getBBox().width + 2);
            _tooltip_rec.attr("height", _tooltip_text.getBBox().height + 4);
            if (_tooltip_rec.attr("opacity") > 0)
            {
              _tooltip_rec.animate({"x": _x + 10, "y": _y - _tooltip_rec.getBBox().height / 2}, 100);
              _tooltip_text.animate({"x": _x + 10 + _tooltip_rec.getBBox().width / 2, "y": _y - _tooltip_rec.getBBox().height / 2 + _tooltip_rec.getBBox().height / 2}, 100);
            }
            else
            {
              _tooltip_rec.attr("x", _x + 10);
              _tooltip_rec.attr("y", _y - _tooltip_rec.getBBox().height / 2);
              _tooltip_text.attr("x", _tooltip_rec.getBBox().x + _tooltip_rec.getBBox().width / 2);
              _tooltip_text.attr("y", _tooltip_rec.getBBox().y + _tooltip_rec.getBBox().height / 2);
              _tooltip_rec.animate({"opacity": 1}, 200);
              _tooltip_text.animate({"opacity": 1}, 200);
            }
          }
          _chart._handleEvent("OnItemOver", {"Item": _series["Items"][_idx[1]], "SeriesItems": _series["Items"]});
        };
      },
      _off_tooltip: function ()
      {
        var _tooltip_rec = this._pp.getById("tooltip_rec");
        var _tooltip_text = this._pp.getById("tooltip_text");
        _tooltip_rec.animate({"opacity": 0}, 200, "linear");
        _tooltip_text.animate({"opacity": 0}, 200, "linear");
        var _highlight = this._pp.getById("highlight");
        if (_highlight != null)
          _highlight.attr("fill-opacity", 0);
      },
      _handleEvent: function (_name, _arg, _sender)
      {
        if (_exist(this._settings["ClientEvents"]) && _exist(this._settings["ClientEvents"][_name]))
        {
          var _func = _getFunctionByName(this._settings["ClientEvents"][_name], window);
          return _func(_defined(_sender) ? _sender : this, _arg);
        }
        else
        {
          return true;
        }
      },
      getSetting: function () {
        return K._cloneObject(this._settings);
      },
      setSetting: function (s) {
        this._settings = s;
        return this;
      },
      fillSetting: function(_settings) {
        var _st = _settings;
        _st = KCJS.propertiesMerge(_st, K._cloneObject(KCJS.defaultChart));
        KCJS.propertiesToLowerCase(_st);
        var copyFont = function() {
          var arr = ['FontColor', 'FontFamily', 'FontSize', 'FontStyle', 'FontWeight'];
          for (var i=0; i<arguments.length; i+=1) 
            for (var j = 0; j < arr.length; j += 1)
              if (K._isEmpty(arguments[i][arr[j]])) 
                arguments[i][arr[j]] = _st[arr[j]];
        };
        copyFont(_st.Title.Appearance, _st.Legend.Appearance, 
          _st.PlotArea.XAxis.LabelsAppearance, _st.PlotArea.XAxis.TitleAppearance, 
          _st.PlotArea.YAxis.LabelsAppearance, _st.PlotArea.YAxis.TitleAppearance
        );
        var ls = _st.PlotArea.ListOfSeries;
        if (K.isArray(ls)) {
          for (var i=0, len=ls.length; i<len; i+=1) {
            var s = ls[i];
            s = KCJS.propertiesMerge(
                    s, K._cloneObject(KCJS.defaultSeries));
            s = KCJS.propertiesMerge(
                    s, K._cloneObject(KCJS.defaultSeriesType[s.ChartType]));
          }
        }
      }
    };
    return kc;
  })();
  var generateChart = function (_id) {
    var _settings = (function _loadSettings() {
      var _input = K._domObj(_id + "_settings");
      return JSON.parse(_base64_decode(_input.value));
    }());
    _settings._id = _id;
    var chart = KCJS._new(koolchart);
    chart.setSetting(_settings);
    return chart;
  };
  global.KoolChartJS = KoolChartJS;
  if (typeof (global.__KCHInits) !== 'undefined' && _exist(global.__KCHInits))
  {
    for (var i = 0; i < global.__KCHInits.length; i++)
    {
      global.__KCHInits[i]();
    }
  }
}(typeof window !== 'undefined' ? window : this));
!function(e){function f(e){this.ok=!1,"#"==e.charAt(0)&&(e=e.substr(1,6)),e=e.replace(/ /g,""),e=e.toLowerCase();var a={aliceblue:"f0f8ff",antiquewhite:"faebd7",aqua:"00ffff",aquamarine:"7fffd4",azure:"f0ffff",beige:"f5f5dc",bisque:"ffe4c4",black:"000000",blanchedalmond:"ffebcd",blue:"0000ff",blueviolet:"8a2be2",brown:"a52a2a",burlywood:"deb887",cadetblue:"5f9ea0",chartreuse:"7fff00",chocolate:"d2691e",coral:"ff7f50",cornflowerblue:"6495ed",cornsilk:"fff8dc",crimson:"dc143c",cyan:"00ffff",darkblue:"00008b",darkcyan:"008b8b",darkgoldenrod:"b8860b",darkgray:"a9a9a9",darkgreen:"006400",darkkhaki:"bdb76b",darkmagenta:"8b008b",darkolivegreen:"556b2f",darkorange:"ff8c00",darkorchid:"9932cc",darkred:"8b0000",darksalmon:"e9967a",darkseagreen:"8fbc8f",darkslateblue:"483d8b",darkslategray:"2f4f4f",darkturquoise:"00ced1",darkviolet:"9400d3",deeppink:"ff1493",deepskyblue:"00bfff",dimgray:"696969",dodgerblue:"1e90ff",feldspar:"d19275",firebrick:"b22222",floralwhite:"fffaf0",forestgreen:"228b22",fuchsia:"ff00ff",gainsboro:"dcdcdc",ghostwhite:"f8f8ff",gold:"ffd700",goldenrod:"daa520",gray:"808080",green:"008000",greenyellow:"adff2f",honeydew:"f0fff0",hotpink:"ff69b4",indianred:"cd5c5c",indigo:"4b0082",ivory:"fffff0",khaki:"f0e68c",lavender:"e6e6fa",lavenderblush:"fff0f5",lawngreen:"7cfc00",lemonchiffon:"fffacd",lightblue:"add8e6",lightcoral:"f08080",lightcyan:"e0ffff",lightgoldenrodyellow:"fafad2",lightgrey:"d3d3d3",lightgreen:"90ee90",lightpink:"ffb6c1",lightsalmon:"ffa07a",lightseagreen:"20b2aa",lightskyblue:"87cefa",lightslateblue:"8470ff",lightslategray:"778899",lightsteelblue:"b0c4de",lightyellow:"ffffe0",lime:"00ff00",limegreen:"32cd32",linen:"faf0e6",magenta:"ff00ff",maroon:"800000",mediumaquamarine:"66cdaa",mediumblue:"0000cd",mediumorchid:"ba55d3",mediumpurple:"9370d8",mediumseagreen:"3cb371",mediumslateblue:"7b68ee",mediumspringgreen:"00fa9a",mediumturquoise:"48d1cc",mediumvioletred:"c71585",midnightblue:"191970",mintcream:"f5fffa",mistyrose:"ffe4e1",moccasin:"ffe4b5",navajowhite:"ffdead",navy:"000080",oldlace:"fdf5e6",olive:"808000",olivedrab:"6b8e23",orange:"ffa500",orangered:"ff4500",orchid:"da70d6",palegoldenrod:"eee8aa",palegreen:"98fb98",paleturquoise:"afeeee",palevioletred:"d87093",papayawhip:"ffefd5",peachpuff:"ffdab9",peru:"cd853f",pink:"ffc0cb",plum:"dda0dd",powderblue:"b0e0e6",purple:"800080",red:"ff0000",rosybrown:"bc8f8f",royalblue:"4169e1",saddlebrown:"8b4513",salmon:"fa8072",sandybrown:"f4a460",seagreen:"2e8b57",seashell:"fff5ee",sienna:"a0522d",silver:"c0c0c0",skyblue:"87ceeb",slateblue:"6a5acd",slategray:"708090",snow:"fffafa",springgreen:"00ff7f",steelblue:"4682b4",tan:"d2b48c",teal:"008080",thistle:"d8bfd8",tomato:"ff6347",turquoise:"40e0d0",violet:"ee82ee",violetred:"d02090",wheat:"f5deb3",white:"ffffff",whitesmoke:"f5f5f5",yellow:"ffff00",yellowgreen:"9acd32"};for(var r in a)e==r&&(e=a[r]);for(var t=[{re:/^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/,example:["rgb(123, 234, 45)","rgb(255,234,245)"],process:function(e){return[parseInt(e[1]),parseInt(e[2]),parseInt(e[3])]}},{re:/^(\w{2})(\w{2})(\w{2})$/,example:["#00ff00","336699"],process:function(e){return[parseInt(e[1],16),parseInt(e[2],16),parseInt(e[3],16)]}},{re:/^(\w{1})(\w{1})(\w{1})$/,example:["#fb0","f0f"],process:function(e){return[parseInt(e[1]+e[1],16),parseInt(e[2]+e[2],16),parseInt(e[3]+e[3],16)]}}],d=0;d<t.length;d++){var n=t[d].re,i=t[d].process,l=n.exec(e);l&&(channels=i(l),this.r=channels[0],this.g=channels[1],this.b=channels[2],this.ok=!0)}this.r=this.r<0||isNaN(this.r)?0:this.r>255?255:this.r,this.g=this.g<0||isNaN(this.g)?0:this.g>255?255:this.g,this.b=this.b<0||isNaN(this.b)?0:this.b>255?255:this.b,this.toRGB=function(){return"rgb("+this.r+", "+this.g+", "+this.b+")"},this.toHex=function(){var e=this.r.toString(16),f=this.g.toString(16),a=this.b.toString(16);return 1==e.length&&(e="0"+e),1==f.length&&(f="0"+f),1==a.length&&(a="0"+a),"#"+e+f+a},this.getHelpXML=function(){for(var e=new Array,r=0;r<t.length;r++)for(var d=t[r].example,n=0;n<d.length;n++)e[e.length]=d[n];for(var i in a)e[e.length]=i;var l=document.createElement("ul");l.setAttribute("id","rgbcolor-examples");for(var r=0;r<e.length;r++)try{var o=document.createElement("li"),s=new f(e[r]),c=document.createElement("div");c.style.cssText="margin: 3px; border: 1px solid black; background:"+s.toHex()+"; color:"+s.toHex(),c.appendChild(document.createTextNode("test"));var b=document.createTextNode(" "+e[r]+" -> "+s.toRGB()+" -> "+s.toHex());o.appendChild(c),o.appendChild(b),l.appendChild(o)}catch(h){}return l}}"undefined"!=typeof define&&define.amd?define(function(){return f}):"undefined"!=typeof module&&module.exports&&(module.exports=f),e.RGBColor=f}("undefined"!=typeof window?window:this);
!function(e){function t(e){for(var t=e.data,a=e.width*e.height*4,r=0;a>r;r+=4){var n=t[r+3]/255;t[r]*=n,t[r+1]*=n,t[r+2]*=n}}function a(e){for(var t=e.data,a=e.width*e.height*4,r=0;a>r;r+=4){var n=t[r+3];0!=n&&(n=255/n,t[r]*=n,t[r+1]*=n,t[r+2]*=n)}}function r(e,t,a,r){var i=document.getElementById(e),g=i.naturalWidth,c=i.naturalHeight,l=document.getElementById(t);l.style.width=g+"px",l.style.height=c+"px",l.width=g,l.height=c;var d=l.getContext("2d");d.clearRect(0,0,g,c),d.drawImage(i,0,0),isNaN(a)||1>a||(r?n(t,0,0,g,c,a):o(t,0,0,g,c,a))}function n(e,r,n,o,l,d){if(!(isNaN(d)||1>d)){d|=0;var f,s=document.getElementById(e),u=s.getContext("2d");try{try{f=u.getImageData(r,n,o,l)}catch(h){try{netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead"),f=u.getImageData(r,n,o,l)}catch(h){throw alert("Cannot access local image"),new Error("unable to access local image data: "+h)}}}catch(h){throw alert("Cannot access image"),new Error("unable to access image data: "+h)}t(f);var m,x,b,v,w,y,p,I,B,E,C,D,N,R,P,G,M,U,k,A,H,W,j,q,z=f.data,F=d+d+1,J=o-1,K=l-1,L=d+1,O=L*(L+1)/2,Q=new i,S=Q;for(b=1;F>b;b++)if(S=S.next=new i,b==L)var T=S;S.next=Q;var V=null,X=null;p=y=0;var Y=g[d],Z=c[d];for(x=0;l>x;x++){for(G=M=U=k=I=B=E=C=0,D=L*(A=z[y]),N=L*(H=z[y+1]),R=L*(W=z[y+2]),P=L*(j=z[y+3]),I+=O*A,B+=O*H,E+=O*W,C+=O*j,S=Q,b=0;L>b;b++)S.r=A,S.g=H,S.b=W,S.a=j,S=S.next;for(b=1;L>b;b++)v=y+((b>J?J:b)<<2),I+=(S.r=A=z[v])*(q=L-b),B+=(S.g=H=z[v+1])*q,E+=(S.b=W=z[v+2])*q,C+=(S.a=j=z[v+3])*q,G+=A,M+=H,U+=W,k+=j,S=S.next;for(V=Q,X=T,m=0;o>m;m++)z[y]=I*Y>>Z,z[y+1]=B*Y>>Z,z[y+2]=E*Y>>Z,z[y+3]=C*Y>>Z,I-=D,B-=N,E-=R,C-=P,D-=V.r,N-=V.g,R-=V.b,P-=V.a,v=p+((v=m+d+1)<J?v:J)<<2,G+=V.r=z[v],M+=V.g=z[v+1],U+=V.b=z[v+2],k+=V.a=z[v+3],I+=G,B+=M,E+=U,C+=k,V=V.next,D+=A=X.r,N+=H=X.g,R+=W=X.b,P+=j=X.a,G-=A,M-=H,U-=W,k-=j,X=X.next,y+=4;p+=o}for(m=0;o>m;m++){for(M=U=k=G=B=E=C=I=0,y=m<<2,D=L*(A=z[y]),N=L*(H=z[y+1]),R=L*(W=z[y+2]),P=L*(j=z[y+3]),I+=O*A,B+=O*H,E+=O*W,C+=O*j,S=Q,b=0;L>b;b++)S.r=A,S.g=H,S.b=W,S.a=j,S=S.next;for(w=o,b=1;d>=b;b++)y=w+m<<2,I+=(S.r=A=z[y])*(q=L-b),B+=(S.g=H=z[y+1])*q,E+=(S.b=W=z[y+2])*q,C+=(S.a=j=z[y+3])*q,G+=A,M+=H,U+=W,k+=j,S=S.next,K>b&&(w+=o);for(y=m,V=Q,X=T,x=0;l>x;x++)v=y<<2,z[v]=I*Y>>Z,z[v+1]=B*Y>>Z,z[v+2]=E*Y>>Z,z[v+3]=C*Y>>Z,I-=D,B-=N,E-=R,C-=P,D-=V.r,N-=V.g,R-=V.b,P-=V.a,v=m+((v=x+L)<K?v:K)*o<<2,I+=G+=V.r=z[v],B+=M+=V.g=z[v+1],E+=U+=V.b=z[v+2],C+=k+=V.a=z[v+3],V=V.next,D+=A=X.r,N+=H=X.g,R+=W=X.b,P+=j=X.a,G-=A,M-=H,U-=W,k-=j,X=X.next,y+=o}a(f),u.putImageData(f,r,n)}}function o(e,t,a,r,n,o){if(!(isNaN(o)||1>o)){o|=0;var l,d=document.getElementById(e),f=d.getContext("2d");try{try{l=f.getImageData(t,a,r,n)}catch(s){try{netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead"),l=f.getImageData(t,a,r,n)}catch(s){throw alert("Cannot access local image"),new Error("unable to access local image data: "+s)}}}catch(s){throw alert("Cannot access image"),new Error("unable to access image data: "+s)}var u,h,m,x,b,v,w,y,p,I,B,E,C,D,N,R,P,G,M,U,k=l.data,A=o+o+1,H=r-1,W=n-1,j=o+1,q=j*(j+1)/2,z=new i,F=z;for(m=1;A>m;m++)if(F=F.next=new i,m==j)var J=F;F.next=z;var K=null,L=null;w=v=0;var O=g[o],Q=c[o];for(h=0;n>h;h++){for(D=N=R=y=p=I=0,B=j*(P=k[v]),E=j*(G=k[v+1]),C=j*(M=k[v+2]),y+=q*P,p+=q*G,I+=q*M,F=z,m=0;j>m;m++)F.r=P,F.g=G,F.b=M,F=F.next;for(m=1;j>m;m++)x=v+((m>H?H:m)<<2),y+=(F.r=P=k[x])*(U=j-m),p+=(F.g=G=k[x+1])*U,I+=(F.b=M=k[x+2])*U,D+=P,N+=G,R+=M,F=F.next;for(K=z,L=J,u=0;r>u;u++)k[v]=y*O>>Q,k[v+1]=p*O>>Q,k[v+2]=I*O>>Q,y-=B,p-=E,I-=C,B-=K.r,E-=K.g,C-=K.b,x=w+((x=u+o+1)<H?x:H)<<2,D+=K.r=k[x],N+=K.g=k[x+1],R+=K.b=k[x+2],y+=D,p+=N,I+=R,K=K.next,B+=P=L.r,E+=G=L.g,C+=M=L.b,D-=P,N-=G,R-=M,L=L.next,v+=4;w+=r}for(u=0;r>u;u++){for(N=R=D=p=I=y=0,v=u<<2,B=j*(P=k[v]),E=j*(G=k[v+1]),C=j*(M=k[v+2]),y+=q*P,p+=q*G,I+=q*M,F=z,m=0;j>m;m++)F.r=P,F.g=G,F.b=M,F=F.next;for(b=r,m=1;o>=m;m++)v=b+u<<2,y+=(F.r=P=k[v])*(U=j-m),p+=(F.g=G=k[v+1])*U,I+=(F.b=M=k[v+2])*U,D+=P,N+=G,R+=M,F=F.next,W>m&&(b+=r);for(v=u,K=z,L=J,h=0;n>h;h++)x=v<<2,k[x]=y*O>>Q,k[x+1]=p*O>>Q,k[x+2]=I*O>>Q,y-=B,p-=E,I-=C,B-=K.r,E-=K.g,C-=K.b,x=u+((x=h+j)<W?x:W)*r<<2,y+=D+=K.r=k[x],p+=N+=K.g=k[x+1],I+=R+=K.b=k[x+2],K=K.next,B+=P=L.r,E+=G=L.g,C+=M=L.b,D-=P,N-=G,R-=M,L=L.next,v+=r}f.putImageData(l,t,a)}}function i(){this.r=0,this.g=0,this.b=0,this.a=0,this.next=null}var g=[512,512,456,512,328,456,335,512,405,328,271,456,388,335,292,512,454,405,364,328,298,271,496,456,420,388,360,335,312,292,273,512,482,454,428,405,383,364,345,328,312,298,284,271,259,496,475,456,437,420,404,388,374,360,347,335,323,312,302,292,282,273,265,512,497,482,468,454,441,428,417,405,394,383,373,364,354,345,337,328,320,312,305,298,291,284,278,271,265,259,507,496,485,475,465,456,446,437,428,420,412,404,396,388,381,374,367,360,354,347,341,335,329,323,318,312,307,302,297,292,287,282,278,273,269,265,261,512,505,497,489,482,475,468,461,454,447,441,435,428,422,417,411,405,399,394,389,383,378,373,368,364,359,354,350,345,341,337,332,328,324,320,316,312,309,305,301,298,294,291,287,284,281,278,274,271,268,265,262,259,257,507,501,496,491,485,480,475,470,465,460,456,451,446,442,437,433,428,424,420,416,412,408,404,400,396,392,388,385,381,377,374,370,367,363,360,357,354,350,347,344,341,338,335,332,329,326,323,320,318,315,312,310,307,304,302,299,297,294,292,289,287,285,282,280,278,275,273,271,269,267,265,263,261,259],c=[9,11,12,13,13,14,14,15,15,15,15,16,16,16,16,17,17,17,17,17,17,17,18,18,18,18,18,18,18,18,18,19,19,19,19,19,19,19,19,19,19,19,19,19,19,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,21,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,22,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,23,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24,24],l={image:r,canvasRGBA:n,canvasRGB:o};"undefined"!=typeof define&&define.amd?define(function(){return l}):"undefined"!=typeof module&&module.exports&&(module.exports=l),e.stackBlur=l}("undefined"!=typeof window?window:this);
!function(t,e){"use strict";"undefined"!=typeof define&&define.amd?define("canvgModule",["rgbcolor","stackblur"],e):"undefined"!=typeof module&&module.exports&&(module.exports=e(require("rgbcolor"),require("stackblur"))),t.canvg=e(t.RGBColor,t.stackBlur)}("undefined"!=typeof window?window:this,function(t,e){function n(t){var e=[0,0,0],i=function(i,n){var s=t.match(i);null!=s&&(e[n]+=s.length,t=t.replace(i," "))};return t=t.replace(/:not\(([^\)]*)\)/g,"     $1 "),t=t.replace(/{[^]*/gm," "),i(o,1),i(l,0),i(h,1),i(u,2),i(c,1),i(f,1),t=t.replace(/[\*\s\+>~]/g," "),t=t.replace(/[#\.]/g," "),i(p,2),e.join("")}function s(s){var r={opts:s};r.FRAMERATE=30,r.MAX_VIRTUAL_PIXELS=3e4,r.log=function(){},1==r.opts.log&&"undefined"!=typeof console&&(r.log=function(t){console.log(t)}),r.init=function(t){var e=0;r.UniqueId=function(){return e++,"canvg"+e},r.Definitions={},r.Styles={},r.StylesSpecificity={},r.Animations=[],r.Images=[],r.ctx=t,r.ViewPort=new function(){this.viewPorts=[],this.Clear=function(){this.viewPorts=[]},this.SetCurrent=function(t,e){this.viewPorts.push({width:t,height:e})},this.RemoveCurrent=function(){this.viewPorts.pop()},this.Current=function(){return this.viewPorts[this.viewPorts.length-1]},this.width=function(){return this.Current().width},this.height=function(){return this.Current().height},this.ComputeSize=function(t){return null!=t&&"number"==typeof t?t:"x"==t?this.width():"y"==t?this.height():Math.sqrt(Math.pow(this.width(),2)+Math.pow(this.height(),2))/Math.sqrt(2)}}},r.init(),r.ImagesLoaded=function(){for(var t=0;t<r.Images.length;t++)if(!r.Images[t].loaded)return!1;return!0},r.trim=function(t){return t.replace(/^\s+|\s+$/g,"")},r.compressSpaces=function(t){return t.replace(/[\s\r\t\n]+/gm," ")},r.ajax=function(t){var e;return e=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP"),e?(e.open("GET",t,!1),e.send(null),e.responseText):null},r.parseXml=function(t){if("undefined"!=typeof Windows&&"undefined"!=typeof Windows.Data&&"undefined"!=typeof Windows.Data.Xml){var e=new Windows.Data.Xml.Dom.XmlDocument,i=new Windows.Data.Xml.Dom.XmlLoadSettings;return i.prohibitDtd=!1,e.loadXml(t,i),e}if(window.DOMParser){var n=new DOMParser;return n.parseFromString(t,"text/xml")}t=t.replace(/<!DOCTYPE svg[^>]*>/,"");var e=new ActiveXObject("Microsoft.XMLDOM");return e.async="false",e.loadXML(t),e},r.Property=function(t,e){this.name=t,this.value=e},r.Property.prototype.getValue=function(){return this.value},r.Property.prototype.hasValue=function(){return null!=this.value&&""!==this.value},r.Property.prototype.numValue=function(){if(!this.hasValue())return 0;var t=parseFloat(this.value);return(this.value+"").match(/%$/)&&(t/=100),t},r.Property.prototype.valueOrDefault=function(t){return this.hasValue()?this.value:t},r.Property.prototype.numValueOrDefault=function(t){return this.hasValue()?this.numValue():t},r.Property.prototype.addOpacity=function(e){var i=this.value;if(null!=e.value&&""!=e.value&&"string"==typeof this.value){var n=new t(this.value);n.ok&&(i="rgba("+n.r+", "+n.g+", "+n.b+", "+e.numValue()+")")}return new r.Property(this.name,i)},r.Property.prototype.getDefinition=function(){var t=this.value.match(/#([^\)'"]+)/);return t&&(t=t[1]),t||(t=this.value),r.Definitions[t]},r.Property.prototype.isUrlDefinition=function(){return 0==this.value.indexOf("url(")},r.Property.prototype.getFillStyleDefinition=function(t,e){var i=this.getDefinition();if(null!=i&&i.createGradient)return i.createGradient(r.ctx,t,e);if(null!=i&&i.createPattern){if(i.getHrefAttribute().hasValue()){var n=i.attribute("patternTransform");i=i.getHrefAttribute().getDefinition(),n.hasValue()&&(i.attribute("patternTransform",!0).value=n.value)}return i.createPattern(r.ctx,t)}return null},r.Property.prototype.getDPI=function(){return 96},r.Property.prototype.getEM=function(t){var e=12,i=new r.Property("fontSize",r.Font.Parse(r.ctx.font).fontSize);return i.hasValue()&&(e=i.toPixels(t)),e},r.Property.prototype.getUnits=function(){var t=this.value+"";return t.replace(/[0-9\.\-]/g,"")},r.Property.prototype.toPixels=function(t,e){if(!this.hasValue())return 0;var i=this.value+"";if(i.match(/em$/))return this.numValue()*this.getEM(t);if(i.match(/ex$/))return this.numValue()*this.getEM(t)/2;if(i.match(/px$/))return this.numValue();if(i.match(/pt$/))return this.numValue()*this.getDPI(t)*(1/72);if(i.match(/pc$/))return 15*this.numValue();if(i.match(/cm$/))return this.numValue()*this.getDPI(t)/2.54;if(i.match(/mm$/))return this.numValue()*this.getDPI(t)/25.4;if(i.match(/in$/))return this.numValue()*this.getDPI(t);if(i.match(/%$/))return this.numValue()*r.ViewPort.ComputeSize(t);var n=this.numValue();return e&&1>n?n*r.ViewPort.ComputeSize(t):n},r.Property.prototype.toMilliseconds=function(){if(!this.hasValue())return 0;var t=this.value+"";return t.match(/s$/)?1e3*this.numValue():(t.match(/ms$/),this.numValue())},r.Property.prototype.toRadians=function(){if(!this.hasValue())return 0;var t=this.value+"";return t.match(/deg$/)?this.numValue()*(Math.PI/180):t.match(/grad$/)?this.numValue()*(Math.PI/200):t.match(/rad$/)?this.numValue():this.numValue()*(Math.PI/180)};var o={baseline:"alphabetic","before-edge":"top","text-before-edge":"top",middle:"middle",central:"middle","after-edge":"bottom","text-after-edge":"bottom",ideographic:"ideographic",alphabetic:"alphabetic",hanging:"hanging",mathematical:"alphabetic"};return r.Property.prototype.toTextBaseline=function(){return this.hasValue()?o[this.value]:null},r.Font=new function(){this.Styles="normal|italic|oblique|inherit",this.Variants="normal|small-caps|inherit",this.Weights="normal|bold|bolder|lighter|100|200|300|400|500|600|700|800|900|inherit",this.CreateFont=function(t,e,i,n,s,a){var o=null!=a?this.Parse(a):this.CreateFont("","","","","",r.ctx.font);return{fontFamily:s||o.fontFamily,fontSize:n||o.fontSize,fontStyle:t||o.fontStyle,fontWeight:i||o.fontWeight,fontVariant:e||o.fontVariant,toString:function(){return[this.fontStyle,this.fontVariant,this.fontWeight,this.fontSize,this.fontFamily].join(" ")}}};var t=this;this.Parse=function(e){for(var i={},n=r.trim(r.compressSpaces(e||"")).split(" "),s={fontSize:!1,fontStyle:!1,fontWeight:!1,fontVariant:!1},a="",o=0;o<n.length;o++)s.fontStyle||-1==t.Styles.indexOf(n[o])?s.fontVariant||-1==t.Variants.indexOf(n[o])?s.fontWeight||-1==t.Weights.indexOf(n[o])?s.fontSize?"inherit"!=n[o]&&(a+=n[o]):("inherit"!=n[o]&&(i.fontSize=n[o].split("/")[0]),s.fontStyle=s.fontVariant=s.fontWeight=s.fontSize=!0):("inherit"!=n[o]&&(i.fontWeight=n[o]),s.fontStyle=s.fontVariant=s.fontWeight=!0):("inherit"!=n[o]&&(i.fontVariant=n[o]),s.fontStyle=s.fontVariant=!0):("inherit"!=n[o]&&(i.fontStyle=n[o]),s.fontStyle=!0);return""!=a&&(i.fontFamily=a),i}},r.ToNumberArray=function(t){for(var e=r.trim(r.compressSpaces((t||"").replace(/,/g," "))).split(" "),i=0;i<e.length;i++)e[i]=parseFloat(e[i]);return e},r.Point=function(t,e){this.x=t,this.y=e},r.Point.prototype.angleTo=function(t){return Math.atan2(t.y-this.y,t.x-this.x)},r.Point.prototype.applyTransform=function(t){var e=this.x*t[0]+this.y*t[2]+t[4],i=this.x*t[1]+this.y*t[3]+t[5];this.x=e,this.y=i},r.CreatePoint=function(t){var e=r.ToNumberArray(t);return new r.Point(e[0],e[1])},r.CreatePath=function(t){for(var e=r.ToNumberArray(t),i=[],n=0;n<e.length;n+=2)i.push(new r.Point(e[n],e[n+1]));return i},r.BoundingBox=function(t,e,n,s){this.x1=Number.NaN,this.y1=Number.NaN,this.x2=Number.NaN,this.y2=Number.NaN,this.x=function(){return this.x1},this.y=function(){return this.y1},this.width=function(){return this.x2-this.x1},this.height=function(){return this.y2-this.y1},this.addPoint=function(t,e){null!=t&&((isNaN(this.x1)||isNaN(this.x2))&&(this.x1=t,this.x2=t),t<this.x1&&(this.x1=t),t>this.x2&&(this.x2=t)),null!=e&&((isNaN(this.y1)||isNaN(this.y2))&&(this.y1=e,this.y2=e),e<this.y1&&(this.y1=e),e>this.y2&&(this.y2=e))},this.addX=function(t){this.addPoint(t,null)},this.addY=function(t){this.addPoint(null,t)},this.addBoundingBox=function(t){this.addPoint(t.x1,t.y1),this.addPoint(t.x2,t.y2)},this.addQuadraticCurve=function(t,e,i,n,s,a){var r=t+2/3*(i-t),o=e+2/3*(n-e),l=r+1/3*(s-t),h=o+1/3*(a-e);this.addBezierCurve(t,e,r,l,o,h,s,a)},this.addBezierCurve=function(t,e,n,s,a,r,o,l){var h=[t,e],u=[n,s],c=[a,r],f=[o,l];for(this.addPoint(h[0],h[1]),this.addPoint(f[0],f[1]),i=0;i<=1;i++){var p=function(t){return Math.pow(1-t,3)*h[i]+3*Math.pow(1-t,2)*t*u[i]+3*(1-t)*Math.pow(t,2)*c[i]+Math.pow(t,3)*f[i]},m=6*h[i]-12*u[i]+6*c[i],d=-3*h[i]+9*u[i]-9*c[i]+3*f[i],y=3*u[i]-3*h[i];if(0!=d){var v=Math.pow(m,2)-4*y*d;if(!(0>v)){var g=(-m+Math.sqrt(v))/(2*d);g>0&&1>g&&(0==i&&this.addX(p(g)),1==i&&this.addY(p(g)));var x=(-m-Math.sqrt(v))/(2*d);x>0&&1>x&&(0==i&&this.addX(p(x)),1==i&&this.addY(p(x)))}}else{if(0==m)continue;var b=-y/m;b>0&&1>b&&(0==i&&this.addX(p(b)),1==i&&this.addY(p(b)))}}},this.isPointInBox=function(t,e){return this.x1<=t&&t<=this.x2&&this.y1<=e&&e<=this.y2},this.addPoint(t,e),this.addPoint(n,s)},r.Transform=function(t){var e=this;this.Type={},this.Type.translate=function(t){this.p=r.CreatePoint(t),this.apply=function(t){t.translate(this.p.x||0,this.p.y||0)},this.unapply=function(t){t.translate(-1*this.p.x||0,-1*this.p.y||0)},this.applyToPoint=function(t){t.applyTransform([1,0,0,1,this.p.x||0,this.p.y||0])}},this.Type.rotate=function(t){var e=r.ToNumberArray(t);this.angle=new r.Property("angle",e[0]),this.cx=e[1]||0,this.cy=e[2]||0,this.apply=function(t){t.translate(this.cx,this.cy),t.rotate(this.angle.toRadians()),t.translate(-this.cx,-this.cy)},this.unapply=function(t){t.translate(this.cx,this.cy),t.rotate(-1*this.angle.toRadians()),t.translate(-this.cx,-this.cy)},this.applyToPoint=function(t){var e=this.angle.toRadians();t.applyTransform([1,0,0,1,this.p.x||0,this.p.y||0]),t.applyTransform([Math.cos(e),Math.sin(e),-Math.sin(e),Math.cos(e),0,0]),t.applyTransform([1,0,0,1,-this.p.x||0,-this.p.y||0])}},this.Type.scale=function(t){this.p=r.CreatePoint(t),this.apply=function(t){t.scale(this.p.x||1,this.p.y||this.p.x||1)},this.unapply=function(t){t.scale(1/this.p.x||1,1/this.p.y||this.p.x||1)},this.applyToPoint=function(t){t.applyTransform([this.p.x||0,0,0,this.p.y||0,0,0])}},this.Type.matrix=function(t){this.m=r.ToNumberArray(t),this.apply=function(t){t.transform(this.m[0],this.m[1],this.m[2],this.m[3],this.m[4],this.m[5])},this.unapply=function(t){var e=this.m[0],i=this.m[2],n=this.m[4],s=this.m[1],a=this.m[3],r=this.m[5],o=0,l=0,h=1,u=1/(e*(a*h-r*l)-i*(s*h-r*o)+n*(s*l-a*o));t.transform(u*(a*h-r*l),u*(r*o-s*h),u*(n*l-i*h),u*(e*h-n*o),u*(i*r-n*a),u*(n*s-e*r))},this.applyToPoint=function(t){t.applyTransform(this.m)}},this.Type.SkewBase=function(t){this.base=e.Type.matrix,this.base(t),this.angle=new r.Property("angle",t)},this.Type.SkewBase.prototype=new this.Type.matrix,this.Type.skewX=function(t){this.base=e.Type.SkewBase,this.base(t),this.m=[1,0,Math.tan(this.angle.toRadians()),1,0,0]},this.Type.skewX.prototype=new this.Type.SkewBase,this.Type.skewY=function(t){this.base=e.Type.SkewBase,this.base(t),this.m=[1,Math.tan(this.angle.toRadians()),0,1,0,0]},this.Type.skewY.prototype=new this.Type.SkewBase,this.transforms=[],this.apply=function(t){for(var e=0;e<this.transforms.length;e++)this.transforms[e].apply(t)},this.unapply=function(t){for(var e=this.transforms.length-1;e>=0;e--)this.transforms[e].unapply(t)},this.applyToPoint=function(t){for(var e=0;e<this.transforms.length;e++)this.transforms[e].applyToPoint(t)};for(var i=r.trim(r.compressSpaces(t)).replace(/\)([a-zA-Z])/g,") $1").replace(/\)(\s?,\s?)/g,") ").split(/\s(?=[a-z])/),n=0;n<i.length;n++){var s=r.trim(i[n].split("(")[0]),a=i[n].split("(")[1].replace(")",""),o=new this.Type[s](a);o.type=s,this.transforms.push(o)}},r.AspectRatio=function(t,e,i,n,s,a,o,l,h,u){e=r.compressSpaces(e),e=e.replace(/^defer\s/,"");var c=e.split(" ")[0]||"xMidYMid",f=e.split(" ")[1]||"meet",p=i/n,m=s/a,d=Math.min(p,m),y=Math.max(p,m);"meet"==f&&(n*=d,a*=d),"slice"==f&&(n*=y,a*=y),h=new r.Property("refX",h),u=new r.Property("refY",u),h.hasValue()&&u.hasValue()?t.translate(-d*h.toPixels("x"),-d*u.toPixels("y")):(c.match(/^xMid/)&&("meet"==f&&d==m||"slice"==f&&y==m)&&t.translate(i/2-n/2,0),c.match(/YMid$/)&&("meet"==f&&d==p||"slice"==f&&y==p)&&t.translate(0,s/2-a/2),c.match(/^xMax/)&&("meet"==f&&d==m||"slice"==f&&y==m)&&t.translate(i-n,0),c.match(/YMax$/)&&("meet"==f&&d==p||"slice"==f&&y==p)&&t.translate(0,s-a)),"none"==c?t.scale(p,m):"meet"==f?t.scale(d,d):"slice"==f&&t.scale(y,y),t.translate(null==o?0:-o,null==l?0:-l)},r.Element={},r.EmptyProperty=new r.Property("EMPTY",""),r.Element.ElementBase=function(t){if(this.attributes={},this.styles={},this.stylesSpecificity={},this.children=[],this.attribute=function(t,e){var i=this.attributes[t];return null!=i?i:(1==e&&(i=new r.Property(t,""),this.attributes[t]=i),i||r.EmptyProperty)},this.getHrefAttribute=function(){for(var t in this.attributes)if("href"==t||t.match(/:href$/))return this.attributes[t];return r.EmptyProperty},this.style=function(t,e,i){var n=this.styles[t];if(null!=n)return n;var s=this.attribute(t);if(null!=s&&s.hasValue())return this.styles[t]=s,s;if(1!=i){var a=this.parent;if(null!=a){var o=a.style(t);if(null!=o&&o.hasValue())return o}}return 1==e&&(n=new r.Property(t,""),this.styles[t]=n),n||r.EmptyProperty},this.render=function(t){if("none"!=this.style("display").value&&"hidden"!=this.style("visibility").value){if(t.save(),this.style("mask").hasValue()){var e=this.style("mask").getDefinition();null!=e&&e.apply(t,this)}else if(this.style("filter").hasValue()){var i=this.style("filter").getDefinition();null!=i&&i.apply(t,this)}else this.setContext(t),this.renderChildren(t),this.clearContext(t);t.restore()}},this.setContext=function(){},this.clearContext=function(){},this.renderChildren=function(t){for(var e=0;e<this.children.length;e++)this.children[e].render(t)},this.addChild=function(t,e){var i=t;e&&(i=r.CreateElement(t)),i.parent=this,"title"!=i.type&&this.children.push(i)},this.addStylesFromStyleDefinition=function(){for(var e in r.Styles)if("@"!=e[0]&&a(t,e)){var i=r.Styles[e],n=r.StylesSpecificity[e];if(null!=i)for(var s in i){var o=this.stylesSpecificity[s];"undefined"==typeof o&&(o="000"),n>o&&(this.styles[s]=i[s],this.stylesSpecificity[s]=n)}}},null!=t&&1==t.nodeType){for(var e=0;e<t.attributes.length;e++){var i=t.attributes[e];this.attributes[i.nodeName]=new r.Property(i.nodeName,i.value)}if(this.addStylesFromStyleDefinition(),this.attribute("style").hasValue())for(var n=this.attribute("style").value.split(";"),e=0;e<n.length;e++)if(""!=r.trim(n[e])){var s=n[e].split(":"),o=r.trim(s[0]),l=r.trim(s[1]);this.styles[o]=new r.Property(o,l)}this.attribute("id").hasValue()&&null==r.Definitions[this.attribute("id").value]&&(r.Definitions[this.attribute("id").value]=this);for(var e=0;e<t.childNodes.length;e++){var h=t.childNodes[e];if(1==h.nodeType&&this.addChild(h,!0),this.captureTextNodes&&(3==h.nodeType||4==h.nodeType)){var u=h.value||h.text||h.textContent||"";""!=r.compressSpaces(u)&&this.addChild(new r.Element.tspan(h),!1)}}}},r.Element.RenderedElementBase=function(t){this.base=r.Element.ElementBase,this.base(t),this.setContext=function(t){if(this.style("fill").isUrlDefinition()){var e=this.style("fill").getFillStyleDefinition(this,this.style("fill-opacity"));null!=e&&(t.fillStyle=e)}else if(this.style("fill").hasValue()){var i=this.style("fill");"currentColor"==i.value&&(i.value=this.style("color").value),"inherit"!=i.value&&(t.fillStyle="none"==i.value?"rgba(0,0,0,0)":i.value)}if(this.style("fill-opacity").hasValue()){var i=new r.Property("fill",t.fillStyle);i=i.addOpacity(this.style("fill-opacity")),t.fillStyle=i.value}if(this.style("stroke").isUrlDefinition()){var e=this.style("stroke").getFillStyleDefinition(this,this.style("stroke-opacity"));null!=e&&(t.strokeStyle=e)}else if(this.style("stroke").hasValue()){var n=this.style("stroke");"currentColor"==n.value&&(n.value=this.style("color").value),"inherit"!=n.value&&(t.strokeStyle="none"==n.value?"rgba(0,0,0,0)":n.value)}if(this.style("stroke-opacity").hasValue()){var n=new r.Property("stroke",t.strokeStyle);n=n.addOpacity(this.style("stroke-opacity")),t.strokeStyle=n.value}if(this.style("stroke-width").hasValue()){var s=this.style("stroke-width").toPixels();t.lineWidth=0==s?.001:s}if(this.style("stroke-linecap").hasValue()&&(t.lineCap=this.style("stroke-linecap").value),this.style("stroke-linejoin").hasValue()&&(t.lineJoin=this.style("stroke-linejoin").value),this.style("stroke-miterlimit").hasValue()&&(t.miterLimit=this.style("stroke-miterlimit").value),this.style("stroke-dasharray").hasValue()&&"none"!=this.style("stroke-dasharray").value){var a=r.ToNumberArray(this.style("stroke-dasharray").value);"undefined"!=typeof t.setLineDash?t.setLineDash(a):"undefined"!=typeof t.webkitLineDash?t.webkitLineDash=a:"undefined"==typeof t.mozDash||1==a.length&&0==a[0]||(t.mozDash=a);var o=this.style("stroke-dashoffset").numValueOrDefault(1);"undefined"!=typeof t.lineDashOffset?t.lineDashOffset=o:"undefined"!=typeof t.webkitLineDashOffset?t.webkitLineDashOffset=o:"undefined"!=typeof t.mozDashOffset&&(t.mozDashOffset=o)}if("undefined"!=typeof t.font&&(t.font=r.Font.CreateFont(this.style("font-style").value,this.style("font-variant").value,this.style("font-weight").value,this.style("font-size").hasValue()?this.style("font-size").toPixels()+"px":"",this.style("font-family").value).toString()),this.style("transform",!1,!0).hasValue()){var l=new r.Transform(this.style("transform",!1,!0).value);l.apply(t)}if(this.style("clip-path",!1,!0).hasValue()){var h=this.style("clip-path",!1,!0).getDefinition();null!=h&&h.apply(t)}this.style("opacity").hasValue()&&(t.globalAlpha=this.style("opacity").numValue())}},r.Element.RenderedElementBase.prototype=new r.Element.ElementBase,r.Element.PathElementBase=function(t){this.base=r.Element.RenderedElementBase,this.base(t),this.path=function(t){return null!=t&&t.beginPath(),new r.BoundingBox},this.renderChildren=function(t){this.path(t),r.Mouse.checkPath(this,t),""!=t.fillStyle&&("inherit"!=this.style("fill-rule").valueOrDefault("inherit")?t.fill(this.style("fill-rule").value):t.fill()),""!=t.strokeStyle&&t.stroke();var e=this.getMarkers();if(null!=e){if(this.style("marker-start").isUrlDefinition()){var i=this.style("marker-start").getDefinition();i.render(t,e[0][0],e[0][1])}if(this.style("marker-mid").isUrlDefinition())for(var i=this.style("marker-mid").getDefinition(),n=1;n<e.length-1;n++)i.render(t,e[n][0],e[n][1]);if(this.style("marker-end").isUrlDefinition()){var i=this.style("marker-end").getDefinition();i.render(t,e[e.length-1][0],e[e.length-1][1])}}},this.getBoundingBox=function(){return this.path()},this.getMarkers=function(){return null}},r.Element.PathElementBase.prototype=new r.Element.RenderedElementBase,r.Element.svg=function(t){this.base=r.Element.RenderedElementBase,this.base(t),this.baseClearContext=this.clearContext,this.clearContext=function(t){this.baseClearContext(t),r.ViewPort.RemoveCurrent()},this.baseSetContext=this.setContext,this.setContext=function(t){t.strokeStyle="rgba(0,0,0,0)",t.lineCap="butt",t.lineJoin="miter",t.miterLimit=4,"undefined"!=typeof t.font&&"undefined"!=typeof window.getComputedStyle&&(t.font=window.getComputedStyle(t.canvas).getPropertyValue("font")),this.baseSetContext(t),this.attribute("x").hasValue()||(this.attribute("x",!0).value=0),this.attribute("y").hasValue()||(this.attribute("y",!0).value=0),t.translate(this.attribute("x").toPixels("x"),this.attribute("y").toPixels("y"));var e=r.ViewPort.width(),i=r.ViewPort.height();if(this.attribute("width").hasValue()||(this.attribute("width",!0).value="100%"),this.attribute("height").hasValue()||(this.attribute("height",!0).value="100%"),"undefined"==typeof this.root){e=this.attribute("width").toPixels("x"),i=this.attribute("height").toPixels("y");var n=0,s=0;this.attribute("refX").hasValue()&&this.attribute("refY").hasValue()&&(n=-this.attribute("refX").toPixels("x"),s=-this.attribute("refY").toPixels("y")),"visible"!=this.attribute("overflow").valueOrDefault("hidden")&&(t.beginPath(),t.moveTo(n,s),t.lineTo(e,s),t.lineTo(e,i),t.lineTo(n,i),t.closePath(),t.clip())}if(r.ViewPort.SetCurrent(e,i),this.attribute("viewBox").hasValue()){var a=r.ToNumberArray(this.attribute("viewBox").value),o=a[0],l=a[1];e=a[2],i=a[3],r.AspectRatio(t,this.attribute("preserveAspectRatio").value,r.ViewPort.width(),e,r.ViewPort.height(),i,o,l,this.attribute("refX").value,this.attribute("refY").value),r.ViewPort.RemoveCurrent(),r.ViewPort.SetCurrent(a[2],a[3])}}},r.Element.svg.prototype=new r.Element.RenderedElementBase,r.Element.rect=function(t){this.base=r.Element.PathElementBase,this.base(t),this.path=function(t){var e=this.attribute("x").toPixels("x"),i=this.attribute("y").toPixels("y"),n=this.attribute("width").toPixels("x"),s=this.attribute("height").toPixels("y"),a=this.attribute("rx").toPixels("x"),o=this.attribute("ry").toPixels("y");return this.attribute("rx").hasValue()&&!this.attribute("ry").hasValue()&&(o=a),this.attribute("ry").hasValue()&&!this.attribute("rx").hasValue()&&(a=o),a=Math.min(a,n/2),o=Math.min(o,s/2),null!=t&&(t.beginPath(),t.moveTo(e+a,i),t.lineTo(e+n-a,i),t.quadraticCurveTo(e+n,i,e+n,i+o),t.lineTo(e+n,i+s-o),t.quadraticCurveTo(e+n,i+s,e+n-a,i+s),t.lineTo(e+a,i+s),t.quadraticCurveTo(e,i+s,e,i+s-o),t.lineTo(e,i+o),t.quadraticCurveTo(e,i,e+a,i),t.closePath()),new r.BoundingBox(e,i,e+n,i+s)}},r.Element.rect.prototype=new r.Element.PathElementBase,r.Element.circle=function(t){this.base=r.Element.PathElementBase,this.base(t),this.path=function(t){var e=this.attribute("cx").toPixels("x"),i=this.attribute("cy").toPixels("y"),n=this.attribute("r").toPixels();return null!=t&&(t.beginPath(),t.arc(e,i,n,0,2*Math.PI,!0),t.closePath()),new r.BoundingBox(e-n,i-n,e+n,i+n)}},r.Element.circle.prototype=new r.Element.PathElementBase,r.Element.ellipse=function(t){this.base=r.Element.PathElementBase,this.base(t),this.path=function(t){var e=4*((Math.sqrt(2)-1)/3),i=this.attribute("rx").toPixels("x"),n=this.attribute("ry").toPixels("y"),s=this.attribute("cx").toPixels("x"),a=this.attribute("cy").toPixels("y");return null!=t&&(t.beginPath(),t.moveTo(s,a-n),t.bezierCurveTo(s+e*i,a-n,s+i,a-e*n,s+i,a),t.bezierCurveTo(s+i,a+e*n,s+e*i,a+n,s,a+n),t.bezierCurveTo(s-e*i,a+n,s-i,a+e*n,s-i,a),t.bezierCurveTo(s-i,a-e*n,s-e*i,a-n,s,a-n),t.closePath()),new r.BoundingBox(s-i,a-n,s+i,a+n)}},r.Element.ellipse.prototype=new r.Element.PathElementBase,r.Element.line=function(t){this.base=r.Element.PathElementBase,this.base(t),this.getPoints=function(){return[new r.Point(this.attribute("x1").toPixels("x"),this.attribute("y1").toPixels("y")),new r.Point(this.attribute("x2").toPixels("x"),this.attribute("y2").toPixels("y"))]},this.path=function(t){var e=this.getPoints();return null!=t&&(t.beginPath(),t.moveTo(e[0].x,e[0].y),t.lineTo(e[1].x,e[1].y)),new r.BoundingBox(e[0].x,e[0].y,e[1].x,e[1].y)},this.getMarkers=function(){var t=this.getPoints(),e=t[0].angleTo(t[1]);return[[t[0],e],[t[1],e]]}},r.Element.line.prototype=new r.Element.PathElementBase,r.Element.polyline=function(t){this.base=r.Element.PathElementBase,this.base(t),this.points=r.CreatePath(this.attribute("points").value),this.path=function(t){var e=new r.BoundingBox(this.points[0].x,this.points[0].y);null!=t&&(t.beginPath(),t.moveTo(this.points[0].x,this.points[0].y));for(var i=1;i<this.points.length;i++)e.addPoint(this.points[i].x,this.points[i].y),null!=t&&t.lineTo(this.points[i].x,this.points[i].y);return e},this.getMarkers=function(){for(var t=[],e=0;e<this.points.length-1;e++)t.push([this.points[e],this.points[e].angleTo(this.points[e+1])]);return t.push([this.points[this.points.length-1],t[t.length-1][1]]),t}},r.Element.polyline.prototype=new r.Element.PathElementBase,r.Element.polygon=function(t){this.base=r.Element.polyline,this.base(t),this.basePath=this.path,this.path=function(t){var e=this.basePath(t);return null!=t&&(t.lineTo(this.points[0].x,this.points[0].y),t.closePath()),e}},r.Element.polygon.prototype=new r.Element.polyline,r.Element.path=function(t){this.base=r.Element.PathElementBase,this.base(t);var e=this.attribute("d").value;e=e.replace(/,/gm," ");for(var i=0;2>i;i++)e=e.replace(/([MmZzLlHhVvCcSsQqTtAa])([^\s])/gm,"$1 $2");e=e.replace(/([^\s])([MmZzLlHhVvCcSsQqTtAa])/gm,"$1 $2"),e=e.replace(/([0-9])([+\-])/gm,"$1 $2");for(var i=0;2>i;i++)e=e.replace(/(\.[0-9]*)(\.)/gm,"$1 $2");e=e.replace(/([Aa](\s+[0-9]+){3})\s+([01])\s*([01])/gm,"$1 $3 $4 "),e=r.compressSpaces(e),e=r.trim(e),this.PathParser=new function(t){this.tokens=t.split(" "),this.reset=function(){this.i=-1,this.command="",this.previousCommand="",this.start=new r.Point(0,0),this.control=new r.Point(0,0),this.current=new r.Point(0,0),this.points=[],this.angles=[]},this.isEnd=function(){return this.i>=this.tokens.length-1},this.isCommandOrEnd=function(){return this.isEnd()?!0:null!=this.tokens[this.i+1].match(/^[A-Za-z]$/)},this.isRelativeCommand=function(){switch(this.command){case"m":case"l":case"h":case"v":case"c":case"s":case"q":case"t":case"a":case"z":return!0}return!1},this.getToken=function(){return this.i++,this.tokens[this.i]},this.getScalar=function(){return parseFloat(this.getToken())},this.nextCommand=function(){this.previousCommand=this.command,this.command=this.getToken()},this.getPoint=function(){var t=new r.Point(this.getScalar(),this.getScalar());return this.makeAbsolute(t)},this.getAsControlPoint=function(){var t=this.getPoint();return this.control=t,t},this.getAsCurrentPoint=function(){var t=this.getPoint();return this.current=t,t},this.getReflectedControlPoint=function(){if("c"!=this.previousCommand.toLowerCase()&&"s"!=this.previousCommand.toLowerCase()&&"q"!=this.previousCommand.toLowerCase()&&"t"!=this.previousCommand.toLowerCase())return this.current;var t=new r.Point(2*this.current.x-this.control.x,2*this.current.y-this.control.y);return t},this.makeAbsolute=function(t){return this.isRelativeCommand()&&(t.x+=this.current.x,t.y+=this.current.y),t},this.addMarker=function(t,e,i){null!=i&&this.angles.length>0&&null==this.angles[this.angles.length-1]&&(this.angles[this.angles.length-1]=this.points[this.points.length-1].angleTo(i)),this.addMarkerAngle(t,null==e?null:e.angleTo(t))},this.addMarkerAngle=function(t,e){this.points.push(t),this.angles.push(e)},this.getMarkerPoints=function(){return this.points},this.getMarkerAngles=function(){for(var t=0;t<this.angles.length;t++)if(null==this.angles[t])for(var e=t+1;e<this.angles.length;e++)if(null!=this.angles[e]){this.angles[t]=this.angles[e];break}return this.angles}}(e),this.path=function(t){var e=this.PathParser;e.reset();var i=new r.BoundingBox;for(null!=t&&t.beginPath();!e.isEnd();)switch(e.nextCommand(),e.command){case"M":case"m":var n=e.getAsCurrentPoint();for(e.addMarker(n),i.addPoint(n.x,n.y),null!=t&&t.moveTo(n.x,n.y),e.start=e.current;!e.isCommandOrEnd();){var n=e.getAsCurrentPoint();e.addMarker(n,e.start),i.addPoint(n.x,n.y),null!=t&&t.lineTo(n.x,n.y)}break;case"L":case"l":for(;!e.isCommandOrEnd();){var s=e.current,n=e.getAsCurrentPoint();e.addMarker(n,s),i.addPoint(n.x,n.y),null!=t&&t.lineTo(n.x,n.y)}break;case"H":case"h":for(;!e.isCommandOrEnd();){var a=new r.Point((e.isRelativeCommand()?e.current.x:0)+e.getScalar(),e.current.y);e.addMarker(a,e.current),e.current=a,i.addPoint(e.current.x,e.current.y),null!=t&&t.lineTo(e.current.x,e.current.y)}break;case"V":case"v":for(;!e.isCommandOrEnd();){var a=new r.Point(e.current.x,(e.isRelativeCommand()?e.current.y:0)+e.getScalar());e.addMarker(a,e.current),e.current=a,i.addPoint(e.current.x,e.current.y),null!=t&&t.lineTo(e.current.x,e.current.y)}break;case"C":case"c":for(;!e.isCommandOrEnd();){var o=e.current,l=e.getPoint(),h=e.getAsControlPoint(),u=e.getAsCurrentPoint();e.addMarker(u,h,l),i.addBezierCurve(o.x,o.y,l.x,l.y,h.x,h.y,u.x,u.y),null!=t&&t.bezierCurveTo(l.x,l.y,h.x,h.y,u.x,u.y)}break;case"S":case"s":for(;!e.isCommandOrEnd();){var o=e.current,l=e.getReflectedControlPoint(),h=e.getAsControlPoint(),u=e.getAsCurrentPoint();e.addMarker(u,h,l),i.addBezierCurve(o.x,o.y,l.x,l.y,h.x,h.y,u.x,u.y),null!=t&&t.bezierCurveTo(l.x,l.y,h.x,h.y,u.x,u.y)}break;case"Q":case"q":for(;!e.isCommandOrEnd();){var o=e.current,h=e.getAsControlPoint(),u=e.getAsCurrentPoint();e.addMarker(u,h,h),i.addQuadraticCurve(o.x,o.y,h.x,h.y,u.x,u.y),null!=t&&t.quadraticCurveTo(h.x,h.y,u.x,u.y)}break;case"T":case"t":for(;!e.isCommandOrEnd();){var o=e.current,h=e.getReflectedControlPoint();e.control=h;var u=e.getAsCurrentPoint();e.addMarker(u,h,h),i.addQuadraticCurve(o.x,o.y,h.x,h.y,u.x,u.y),null!=t&&t.quadraticCurveTo(h.x,h.y,u.x,u.y)}break;case"A":case"a":for(;!e.isCommandOrEnd();){var o=e.current,c=e.getScalar(),f=e.getScalar(),p=e.getScalar()*(Math.PI/180),m=e.getScalar(),d=e.getScalar(),u=e.getAsCurrentPoint(),y=new r.Point(Math.cos(p)*(o.x-u.x)/2+Math.sin(p)*(o.y-u.y)/2,-Math.sin(p)*(o.x-u.x)/2+Math.cos(p)*(o.y-u.y)/2),v=Math.pow(y.x,2)/Math.pow(c,2)+Math.pow(y.y,2)/Math.pow(f,2);v>1&&(c*=Math.sqrt(v),f*=Math.sqrt(v));var g=(m==d?-1:1)*Math.sqrt((Math.pow(c,2)*Math.pow(f,2)-Math.pow(c,2)*Math.pow(y.y,2)-Math.pow(f,2)*Math.pow(y.x,2))/(Math.pow(c,2)*Math.pow(y.y,2)+Math.pow(f,2)*Math.pow(y.x,2)));isNaN(g)&&(g=0);var x=new r.Point(g*c*y.y/f,g*-f*y.x/c),b=new r.Point((o.x+u.x)/2+Math.cos(p)*x.x-Math.sin(p)*x.y,(o.y+u.y)/2+Math.sin(p)*x.x+Math.cos(p)*x.y),E=function(t){return Math.sqrt(Math.pow(t[0],2)+Math.pow(t[1],2))},P=function(t,e){return(t[0]*e[0]+t[1]*e[1])/(E(t)*E(e))},w=function(t,e){return(t[0]*e[1]<t[1]*e[0]?-1:1)*Math.acos(P(t,e))},B=w([1,0],[(y.x-x.x)/c,(y.y-x.y)/f]),C=[(y.x-x.x)/c,(y.y-x.y)/f],T=[(-y.x-x.x)/c,(-y.y-x.y)/f],V=w(C,T);P(C,T)<=-1&&(V=Math.PI),P(C,T)>=1&&(V=0);var M=1-d?1:-1,S=B+M*(V/2),k=new r.Point(b.x+c*Math.cos(S),b.y+f*Math.sin(S));if(e.addMarkerAngle(k,S-M*Math.PI/2),e.addMarkerAngle(u,S-M*Math.PI),i.addPoint(u.x,u.y),null!=t){var P=c>f?c:f,D=c>f?1:c/f,A=c>f?f/c:1;t.translate(b.x,b.y),t.rotate(p),t.scale(D,A),t.arc(0,0,P,B,B+V,1-d),t.scale(1/D,1/A),t.rotate(-p),t.translate(-b.x,-b.y)}}break;case"Z":case"z":null!=t&&t.closePath(),e.current=e.start}return i},this.getMarkers=function(){for(var t=this.PathParser.getMarkerPoints(),e=this.PathParser.getMarkerAngles(),i=[],n=0;n<t.length;n++)i.push([t[n],e[n]]);return i}},r.Element.path.prototype=new r.Element.PathElementBase,r.Element.pattern=function(t){this.base=r.Element.ElementBase,this.base(t),this.createPattern=function(t){var e=this.attribute("width").toPixels("x",!0),i=this.attribute("height").toPixels("y",!0),n=new r.Element.svg;n.attributes.viewBox=new r.Property("viewBox",this.attribute("viewBox").value),n.attributes.width=new r.Property("width",e+"px"),n.attributes.height=new r.Property("height",i+"px"),n.attributes.transform=new r.Property("transform",this.attribute("patternTransform").value),n.children=this.children;var s=document.createElement("canvas");s.width=e,s.height=i;var a=s.getContext("2d");this.attribute("x").hasValue()&&this.attribute("y").hasValue()&&a.translate(this.attribute("x").toPixels("x",!0),this.attribute("y").toPixels("y",!0));for(var o=-1;1>=o;o++)for(var l=-1;1>=l;l++)a.save(),n.attributes.x=new r.Property("x",o*s.width),n.attributes.y=new r.Property("y",l*s.height),n.render(a),a.restore();var h=t.createPattern(s,"repeat");return h}},r.Element.pattern.prototype=new r.Element.ElementBase,r.Element.marker=function(t){this.base=r.Element.ElementBase,this.base(t),this.baseRender=this.render,this.render=function(t,e,i){t.translate(e.x,e.y),"auto"==this.attribute("orient").valueOrDefault("auto")&&t.rotate(i),"strokeWidth"==this.attribute("markerUnits").valueOrDefault("strokeWidth")&&t.scale(t.lineWidth,t.lineWidth),t.save();var n=new r.Element.svg;n.attributes.viewBox=new r.Property("viewBox",this.attribute("viewBox").value),n.attributes.refX=new r.Property("refX",this.attribute("refX").value),n.attributes.refY=new r.Property("refY",this.attribute("refY").value),n.attributes.width=new r.Property("width",this.attribute("markerWidth").value),n.attributes.height=new r.Property("height",this.attribute("markerHeight").value),n.attributes.fill=new r.Property("fill",this.attribute("fill").valueOrDefault("black")),n.attributes.stroke=new r.Property("stroke",this.attribute("stroke").valueOrDefault("none")),n.children=this.children,n.render(t),t.restore(),"strokeWidth"==this.attribute("markerUnits").valueOrDefault("strokeWidth")&&t.scale(1/t.lineWidth,1/t.lineWidth),"auto"==this.attribute("orient").valueOrDefault("auto")&&t.rotate(-i),t.translate(-e.x,-e.y)
}},r.Element.marker.prototype=new r.Element.ElementBase,r.Element.defs=function(t){this.base=r.Element.ElementBase,this.base(t),this.render=function(){}},r.Element.defs.prototype=new r.Element.ElementBase,r.Element.GradientBase=function(t){this.base=r.Element.ElementBase,this.base(t),this.stops=[];for(var e=0;e<this.children.length;e++){var i=this.children[e];"stop"==i.type&&this.stops.push(i)}this.getGradient=function(){},this.gradientUnits=function(){return this.attribute("gradientUnits").valueOrDefault("objectBoundingBox")},this.attributesToInherit=["gradientUnits"],this.inheritStopContainer=function(t){for(var e=0;e<this.attributesToInherit.length;e++){var i=this.attributesToInherit[e];!this.attribute(i).hasValue()&&t.attribute(i).hasValue()&&(this.attribute(i,!0).value=t.attribute(i).value)}},this.createGradient=function(t,e,i){var n=this;this.getHrefAttribute().hasValue()&&(n=this.getHrefAttribute().getDefinition(),this.inheritStopContainer(n));var s=function(t){if(i.hasValue()){var e=new r.Property("color",t);return e.addOpacity(i).value}return t},a=this.getGradient(t,e);if(null==a)return s(n.stops[n.stops.length-1].color);for(var o=0;o<n.stops.length;o++)a.addColorStop(n.stops[o].offset,s(n.stops[o].color));if(this.attribute("gradientTransform").hasValue()){var l=r.ViewPort.viewPorts[0],h=new r.Element.rect;h.attributes.x=new r.Property("x",-r.MAX_VIRTUAL_PIXELS/3),h.attributes.y=new r.Property("y",-r.MAX_VIRTUAL_PIXELS/3),h.attributes.width=new r.Property("width",r.MAX_VIRTUAL_PIXELS),h.attributes.height=new r.Property("height",r.MAX_VIRTUAL_PIXELS);var u=new r.Element.g;u.attributes.transform=new r.Property("transform",this.attribute("gradientTransform").value),u.children=[h];var c=new r.Element.svg;c.attributes.x=new r.Property("x",0),c.attributes.y=new r.Property("y",0),c.attributes.width=new r.Property("width",l.width),c.attributes.height=new r.Property("height",l.height),c.children=[u];var f=document.createElement("canvas");f.width=l.width,f.height=l.height;var p=f.getContext("2d");return p.fillStyle=a,c.render(p),p.createPattern(f,"no-repeat")}return a}},r.Element.GradientBase.prototype=new r.Element.ElementBase,r.Element.linearGradient=function(t){this.base=r.Element.GradientBase,this.base(t),this.attributesToInherit.push("x1"),this.attributesToInherit.push("y1"),this.attributesToInherit.push("x2"),this.attributesToInherit.push("y2"),this.getGradient=function(t,e){var i="objectBoundingBox"==this.gradientUnits()?e.getBoundingBox():null;this.attribute("x1").hasValue()||this.attribute("y1").hasValue()||this.attribute("x2").hasValue()||this.attribute("y2").hasValue()||(this.attribute("x1",!0).value=0,this.attribute("y1",!0).value=0,this.attribute("x2",!0).value=1,this.attribute("y2",!0).value=0);var n="objectBoundingBox"==this.gradientUnits()?i.x()+i.width()*this.attribute("x1").numValue():this.attribute("x1").toPixels("x"),s="objectBoundingBox"==this.gradientUnits()?i.y()+i.height()*this.attribute("y1").numValue():this.attribute("y1").toPixels("y"),a="objectBoundingBox"==this.gradientUnits()?i.x()+i.width()*this.attribute("x2").numValue():this.attribute("x2").toPixels("x"),r="objectBoundingBox"==this.gradientUnits()?i.y()+i.height()*this.attribute("y2").numValue():this.attribute("y2").toPixels("y");return n==a&&s==r?null:t.createLinearGradient(n,s,a,r)}},r.Element.linearGradient.prototype=new r.Element.GradientBase,r.Element.radialGradient=function(t){this.base=r.Element.GradientBase,this.base(t),this.attributesToInherit.push("cx"),this.attributesToInherit.push("cy"),this.attributesToInherit.push("r"),this.attributesToInherit.push("fx"),this.attributesToInherit.push("fy"),this.getGradient=function(t,e){var i=e.getBoundingBox();this.attribute("cx").hasValue()||(this.attribute("cx",!0).value="50%"),this.attribute("cy").hasValue()||(this.attribute("cy",!0).value="50%"),this.attribute("r").hasValue()||(this.attribute("r",!0).value="50%");var n="objectBoundingBox"==this.gradientUnits()?i.x()+i.width()*this.attribute("cx").numValue():this.attribute("cx").toPixels("x"),s="objectBoundingBox"==this.gradientUnits()?i.y()+i.height()*this.attribute("cy").numValue():this.attribute("cy").toPixels("y"),a=n,r=s;this.attribute("fx").hasValue()&&(a="objectBoundingBox"==this.gradientUnits()?i.x()+i.width()*this.attribute("fx").numValue():this.attribute("fx").toPixels("x")),this.attribute("fy").hasValue()&&(r="objectBoundingBox"==this.gradientUnits()?i.y()+i.height()*this.attribute("fy").numValue():this.attribute("fy").toPixels("y"));var o="objectBoundingBox"==this.gradientUnits()?(i.width()+i.height())/2*this.attribute("r").numValue():this.attribute("r").toPixels();return t.createRadialGradient(a,r,0,n,s,o)}},r.Element.radialGradient.prototype=new r.Element.GradientBase,r.Element.stop=function(t){this.base=r.Element.ElementBase,this.base(t),this.offset=this.attribute("offset").numValue(),this.offset<0&&(this.offset=0),this.offset>1&&(this.offset=1);var e=this.style("stop-color",!0);""===e.value&&(e.value="#000"),this.style("stop-opacity").hasValue()&&(e=e.addOpacity(this.style("stop-opacity"))),this.color=e.value},r.Element.stop.prototype=new r.Element.ElementBase,r.Element.AnimateBase=function(t){this.base=r.Element.ElementBase,this.base(t),r.Animations.push(this),this.duration=0,this.begin=this.attribute("begin").toMilliseconds(),this.maxDuration=this.begin+this.attribute("dur").toMilliseconds(),this.getProperty=function(){var t=this.attribute("attributeType").value,e=this.attribute("attributeName").value;return"CSS"==t?this.parent.style(e,!0):this.parent.attribute(e,!0)},this.initialValue=null,this.initialUnits="",this.removed=!1,this.calcValue=function(){return""},this.update=function(t){if(null==this.initialValue&&(this.initialValue=this.getProperty().value,this.initialUnits=this.getProperty().getUnits()),this.duration>this.maxDuration){if("indefinite"==this.attribute("repeatCount").value||"indefinite"==this.attribute("repeatDur").value)this.duration=0;else if("freeze"!=this.attribute("fill").valueOrDefault("remove")||this.frozen){if("remove"==this.attribute("fill").valueOrDefault("remove")&&!this.removed)return this.removed=!0,this.getProperty().value=this.parent.animationFrozen?this.parent.animationFrozenValue:this.initialValue,!0}else this.frozen=!0,this.parent.animationFrozen=!0,this.parent.animationFrozenValue=this.getProperty().value;return!1}this.duration=this.duration+t;var e=!1;if(this.begin<this.duration){var i=this.calcValue();if(this.attribute("type").hasValue()){var n=this.attribute("type").value;i=n+"("+i+")"}this.getProperty().value=i,e=!0}return e},this.from=this.attribute("from"),this.to=this.attribute("to"),this.values=this.attribute("values"),this.values.hasValue()&&(this.values.value=this.values.value.split(";")),this.progress=function(){var t={progress:(this.duration-this.begin)/(this.maxDuration-this.begin)};if(this.values.hasValue()){var e=t.progress*(this.values.value.length-1),i=Math.floor(e),n=Math.ceil(e);t.from=new r.Property("from",parseFloat(this.values.value[i])),t.to=new r.Property("to",parseFloat(this.values.value[n])),t.progress=(e-i)/(n-i)}else t.from=this.from,t.to=this.to;return t}},r.Element.AnimateBase.prototype=new r.Element.ElementBase,r.Element.animate=function(t){this.base=r.Element.AnimateBase,this.base(t),this.calcValue=function(){var t=this.progress(),e=t.from.numValue()+(t.to.numValue()-t.from.numValue())*t.progress;return e+this.initialUnits}},r.Element.animate.prototype=new r.Element.AnimateBase,r.Element.animateColor=function(e){this.base=r.Element.AnimateBase,this.base(e),this.calcValue=function(){var e=this.progress(),i=new t(e.from.value),n=new t(e.to.value);if(i.ok&&n.ok){var s=i.r+(n.r-i.r)*e.progress,a=i.g+(n.g-i.g)*e.progress,r=i.b+(n.b-i.b)*e.progress;return"rgb("+parseInt(s,10)+","+parseInt(a,10)+","+parseInt(r,10)+")"}return this.attribute("from").value}},r.Element.animateColor.prototype=new r.Element.AnimateBase,r.Element.animateTransform=function(t){this.base=r.Element.AnimateBase,this.base(t),this.calcValue=function(){for(var t=this.progress(),e=r.ToNumberArray(t.from.value),i=r.ToNumberArray(t.to.value),n="",s=0;s<e.length;s++)n+=e[s]+(i[s]-e[s])*t.progress+" ";return n}},r.Element.animateTransform.prototype=new r.Element.animate,r.Element.font=function(t){this.base=r.Element.ElementBase,this.base(t),this.horizAdvX=this.attribute("horiz-adv-x").numValue(),this.isRTL=!1,this.isArabic=!1,this.fontFace=null,this.missingGlyph=null,this.glyphs=[];for(var e=0;e<this.children.length;e++){var i=this.children[e];"font-face"==i.type?(this.fontFace=i,i.style("font-family").hasValue()&&(r.Definitions[i.style("font-family").value]=this)):"missing-glyph"==i.type?this.missingGlyph=i:"glyph"==i.type&&(""!=i.arabicForm?(this.isRTL=!0,this.isArabic=!0,"undefined"==typeof this.glyphs[i.unicode]&&(this.glyphs[i.unicode]=[]),this.glyphs[i.unicode][i.arabicForm]=i):this.glyphs[i.unicode]=i)}},r.Element.font.prototype=new r.Element.ElementBase,r.Element.fontface=function(t){this.base=r.Element.ElementBase,this.base(t),this.ascent=this.attribute("ascent").value,this.descent=this.attribute("descent").value,this.unitsPerEm=this.attribute("units-per-em").numValue()},r.Element.fontface.prototype=new r.Element.ElementBase,r.Element.missingglyph=function(t){this.base=r.Element.path,this.base(t),this.horizAdvX=0},r.Element.missingglyph.prototype=new r.Element.path,r.Element.glyph=function(t){this.base=r.Element.path,this.base(t),this.horizAdvX=this.attribute("horiz-adv-x").numValue(),this.unicode=this.attribute("unicode").value,this.arabicForm=this.attribute("arabic-form").value},r.Element.glyph.prototype=new r.Element.path,r.Element.text=function(t){this.captureTextNodes=!0,this.base=r.Element.RenderedElementBase,this.base(t),this.baseSetContext=this.setContext,this.setContext=function(t){this.baseSetContext(t);var e=this.style("dominant-baseline").toTextBaseline();null==e&&(e=this.style("alignment-baseline").toTextBaseline()),null!=e&&(t.textBaseline=e)},this.getBoundingBox=function(){var t=this.attribute("x").toPixels("x"),e=this.attribute("y").toPixels("y"),i=this.parent.style("font-size").numValueOrDefault(r.Font.Parse(r.ctx.font).fontSize);return new r.BoundingBox(t,e-i,t+Math.floor(2*i/3)*this.children[0].getText().length,e)},this.renderChildren=function(t){this.x=this.attribute("x").toPixels("x"),this.y=this.attribute("y").toPixels("y"),this.attribute("dx").hasValue()&&(this.x+=this.attribute("dx").toPixels("x")),this.attribute("dy").hasValue()&&(this.y+=this.attribute("dy").toPixels("y")),this.x+=this.getAnchorDelta(t,this,0);for(var e=0;e<this.children.length;e++)this.renderChild(t,this,e)},this.getAnchorDelta=function(t,e,i){var n=this.style("text-anchor").valueOrDefault("start");if("start"!=n){for(var s=0,a=i;a<e.children.length;a++){var r=e.children[a];if(a>i&&r.attribute("x").hasValue())break;s+=r.measureTextRecursive(t)}return-1*("end"==n?s:s/2)}return 0},this.renderChild=function(t,e,i){var n=e.children[i];n.attribute("x").hasValue()?(n.x=n.attribute("x").toPixels("x")+e.getAnchorDelta(t,e,i),n.attribute("dx").hasValue()&&(n.x+=n.attribute("dx").toPixels("x"))):(n.attribute("dx").hasValue()&&(e.x+=n.attribute("dx").toPixels("x")),n.x=e.x),e.x=n.x+n.measureText(t),n.attribute("y").hasValue()?(n.y=n.attribute("y").toPixels("y"),n.attribute("dy").hasValue()&&(n.y+=n.attribute("dy").toPixels("y"))):(n.attribute("dy").hasValue()&&(e.y+=n.attribute("dy").toPixels("y")),n.y=e.y),e.y=n.y,n.render(t);for(var i=0;i<n.children.length;i++)e.renderChild(t,n,i)}},r.Element.text.prototype=new r.Element.RenderedElementBase,r.Element.TextElementBase=function(t){this.base=r.Element.RenderedElementBase,this.base(t),this.getGlyph=function(t,e,i){var n=e[i],s=null;if(t.isArabic){var a="isolated";(0==i||" "==e[i-1])&&i<e.length-2&&" "!=e[i+1]&&(a="terminal"),i>0&&" "!=e[i-1]&&i<e.length-2&&" "!=e[i+1]&&(a="medial"),i>0&&" "!=e[i-1]&&(i==e.length-1||" "==e[i+1])&&(a="initial"),"undefined"!=typeof t.glyphs[n]&&(s=t.glyphs[n][a],null==s&&"glyph"==t.glyphs[n].type&&(s=t.glyphs[n]))}else s=t.glyphs[n];return null==s&&(s=t.missingGlyph),s},this.renderChildren=function(t){var e=this.parent.style("font-family").getDefinition();if(null==e)""!=t.fillStyle&&t.fillText(r.compressSpaces(this.getText()),this.x,this.y),""!=t.strokeStyle&&t.strokeText(r.compressSpaces(this.getText()),this.x,this.y);else{var i=this.parent.style("font-size").numValueOrDefault(r.Font.Parse(r.ctx.font).fontSize),n=this.parent.style("font-style").valueOrDefault(r.Font.Parse(r.ctx.font).fontStyle),s=this.getText();e.isRTL&&(s=s.split("").reverse().join(""));for(var a=r.ToNumberArray(this.parent.attribute("dx").value),o=0;o<s.length;o++){var l=this.getGlyph(e,s,o),h=i/e.fontFace.unitsPerEm;t.translate(this.x,this.y),t.scale(h,-h);var u=t.lineWidth;t.lineWidth=t.lineWidth*e.fontFace.unitsPerEm/i,"italic"==n&&t.transform(1,0,.4,1,0,0),l.render(t),"italic"==n&&t.transform(1,0,-.4,1,0,0),t.lineWidth=u,t.scale(1/h,-1/h),t.translate(-this.x,-this.y),this.x+=i*(l.horizAdvX||e.horizAdvX)/e.fontFace.unitsPerEm,"undefined"==typeof a[o]||isNaN(a[o])||(this.x+=a[o])}}},this.getText=function(){},this.measureTextRecursive=function(t){for(var e=this.measureText(t),i=0;i<this.children.length;i++)e+=this.children[i].measureTextRecursive(t);return e},this.measureText=function(t){var e=this.parent.style("font-family").getDefinition();if(null!=e){var i=this.parent.style("font-size").numValueOrDefault(r.Font.Parse(r.ctx.font).fontSize),n=0,s=this.getText();e.isRTL&&(s=s.split("").reverse().join(""));for(var a=r.ToNumberArray(this.parent.attribute("dx").value),o=0;o<s.length;o++){var l=this.getGlyph(e,s,o);n+=(l.horizAdvX||e.horizAdvX)*i/e.fontFace.unitsPerEm,"undefined"==typeof a[o]||isNaN(a[o])||(n+=a[o])}return n}var h=r.compressSpaces(this.getText());if(!t.measureText)return 10*h.length;t.save(),this.setContext(t);var u=t.measureText(h).width;return t.restore(),u}},r.Element.TextElementBase.prototype=new r.Element.RenderedElementBase,r.Element.tspan=function(t){this.captureTextNodes=!0,this.base=r.Element.TextElementBase,this.base(t),this.text=r.compressSpaces(t.value||t.text||t.textContent||""),this.getText=function(){return this.children.length>0?"":this.text}},r.Element.tspan.prototype=new r.Element.TextElementBase,r.Element.tref=function(t){this.base=r.Element.TextElementBase,this.base(t),this.getText=function(){var t=this.getHrefAttribute().getDefinition();return null!=t?t.children[0].getText():void 0}},r.Element.tref.prototype=new r.Element.TextElementBase,r.Element.a=function(t){this.base=r.Element.TextElementBase,this.base(t),this.hasText=t.childNodes.length>0;for(var e=0;e<t.childNodes.length;e++)3!=t.childNodes[e].nodeType&&(this.hasText=!1);this.text=this.hasText?t.childNodes[0].value:"",this.getText=function(){return this.text},this.baseRenderChildren=this.renderChildren,this.renderChildren=function(t){if(this.hasText){this.baseRenderChildren(t);var e=new r.Property("fontSize",r.Font.Parse(r.ctx.font).fontSize);r.Mouse.checkBoundingBox(this,new r.BoundingBox(this.x,this.y-e.toPixels("y"),this.x+this.measureText(t),this.y))}else if(this.children.length>0){var i=new r.Element.g;i.children=this.children,i.parent=this,i.render(t)}},this.onclick=function(){window.open(this.getHrefAttribute().value)},this.onmousemove=function(){r.ctx.canvas.style.cursor="pointer"}},r.Element.a.prototype=new r.Element.TextElementBase,r.Element.image=function(t){this.base=r.Element.RenderedElementBase,this.base(t);var e=this.getHrefAttribute().value;if(""!=e){var i=e.match(/\.svg$/);if(r.Images.push(this),this.loaded=!1,i)this.img=r.ajax(e),this.loaded=!0;else{this.img=document.createElement("img"),1==r.opts.useCORS&&(this.img.crossOrigin="Anonymous");var n=this;this.img.onload=function(){n.loaded=!0},this.img.onerror=function(){r.log('ERROR: image "'+e+'" not found'),n.loaded=!0},this.img.src=e}this.renderChildren=function(t){var e=this.attribute("x").toPixels("x"),n=this.attribute("y").toPixels("y"),s=this.attribute("width").toPixels("x"),a=this.attribute("height").toPixels("y");0!=s&&0!=a&&(t.save(),i?t.drawSvg(this.img,e,n,s,a):(t.translate(e,n),r.AspectRatio(t,this.attribute("preserveAspectRatio").value,s,this.img.width,a,this.img.height,0,0),t.drawImage(this.img,0,0)),t.restore())},this.getBoundingBox=function(){var t=this.attribute("x").toPixels("x"),e=this.attribute("y").toPixels("y"),i=this.attribute("width").toPixels("x"),n=this.attribute("height").toPixels("y");return new r.BoundingBox(t,e,t+i,e+n)}}},r.Element.image.prototype=new r.Element.RenderedElementBase,r.Element.g=function(t){this.base=r.Element.RenderedElementBase,this.base(t),this.getBoundingBox=function(){for(var t=new r.BoundingBox,e=0;e<this.children.length;e++)t.addBoundingBox(this.children[e].getBoundingBox());return t}},r.Element.g.prototype=new r.Element.RenderedElementBase,r.Element.symbol=function(t){this.base=r.Element.RenderedElementBase,this.base(t),this.render=function(){}},r.Element.symbol.prototype=new r.Element.RenderedElementBase,r.Element.style=function(t){this.base=r.Element.ElementBase,this.base(t);for(var e="",i=0;i<t.childNodes.length;i++)e+=t.childNodes[i].data;e=e.replace(/(\/\*([^*]|[\r\n]|(\*+([^*\/]|[\r\n])))*\*+\/)|(^[\s]*\/\/.*)/gm,""),e=r.compressSpaces(e);for(var s=e.split("}"),i=0;i<s.length;i++)if(""!=r.trim(s[i]))for(var a=s[i].split("{"),o=a[0].split(","),l=a[1].split(";"),h=0;h<o.length;h++){var u=r.trim(o[h]);if(""!=u){for(var c=r.Styles[u]||{},f=0;f<l.length;f++){var p=l[f].indexOf(":"),m=l[f].substr(0,p),d=l[f].substr(p+1,l[f].length-p);null!=m&&null!=d&&(c[r.trim(m)]=new r.Property(r.trim(m),r.trim(d)))}if(r.Styles[u]=c,r.StylesSpecificity[u]=n(u),"@font-face"==u)for(var y=c["font-family"].value.replace(/"/g,""),v=c.src.value.split(","),g=0;g<v.length;g++)if(v[g].indexOf('format("svg")')>0)for(var x=v[g].indexOf("url"),b=v[g].indexOf(")",x),E=v[g].substr(x+5,b-x-6),P=r.parseXml(r.ajax(E)),w=P.getElementsByTagName("font"),B=0;B<w.length;B++){var C=r.CreateElement(w[B]);r.Definitions[y]=C}}}},r.Element.style.prototype=new r.Element.ElementBase,r.Element.use=function(t){this.base=r.Element.RenderedElementBase,this.base(t),this.baseSetContext=this.setContext,this.setContext=function(t){this.baseSetContext(t),this.attribute("x").hasValue()&&t.translate(this.attribute("x").toPixels("x"),0),this.attribute("y").hasValue()&&t.translate(0,this.attribute("y").toPixels("y"))};var e=this.getHrefAttribute().getDefinition();this.path=function(t){null!=e&&e.path(t)},this.getBoundingBox=function(){return null!=e?e.getBoundingBox():void 0},this.renderChildren=function(t){if(null!=e){var i=e;"symbol"==e.type&&(i=new r.Element.svg,i.type="svg",i.attributes.viewBox=new r.Property("viewBox",e.attribute("viewBox").value),i.attributes.preserveAspectRatio=new r.Property("preserveAspectRatio",e.attribute("preserveAspectRatio").value),i.attributes.overflow=new r.Property("overflow",e.attribute("overflow").value),i.children=e.children),"svg"==i.type&&(this.attribute("width").hasValue()&&(i.attributes.width=new r.Property("width",this.attribute("width").value)),this.attribute("height").hasValue()&&(i.attributes.height=new r.Property("height",this.attribute("height").value)));var n=i.parent;i.parent=null,i.render(t),i.parent=n}}},r.Element.use.prototype=new r.Element.RenderedElementBase,r.Element.mask=function(t){this.base=r.Element.ElementBase,this.base(t),this.apply=function(t,e){var i=this.attribute("x").toPixels("x"),n=this.attribute("y").toPixels("y"),s=this.attribute("width").toPixels("x"),a=this.attribute("height").toPixels("y");if(0==s&&0==a){for(var o=new r.BoundingBox,l=0;l<this.children.length;l++)o.addBoundingBox(this.children[l].getBoundingBox());var i=Math.floor(o.x1),n=Math.floor(o.y1),s=Math.floor(o.width()),a=Math.floor(o.height())}var h=e.attribute("mask").value;e.attribute("mask").value="";var u=document.createElement("canvas");u.width=i+s,u.height=n+a;var c=u.getContext("2d");this.renderChildren(c);var f=document.createElement("canvas");f.width=i+s,f.height=n+a;var p=f.getContext("2d");e.render(p),p.globalCompositeOperation="destination-in",p.fillStyle=c.createPattern(u,"no-repeat"),p.fillRect(0,0,i+s,n+a),t.fillStyle=p.createPattern(f,"no-repeat"),t.fillRect(0,0,i+s,n+a),e.attribute("mask").value=h},this.render=function(){}},r.Element.mask.prototype=new r.Element.ElementBase,r.Element.clipPath=function(t){this.base=r.Element.ElementBase,this.base(t),this.apply=function(t){var e=CanvasRenderingContext2D.prototype.beginPath;CanvasRenderingContext2D.prototype.beginPath=function(){};var i=CanvasRenderingContext2D.prototype.closePath;CanvasRenderingContext2D.prototype.closePath=function(){},e.call(t);for(var n=0;n<this.children.length;n++){var s=this.children[n];if("undefined"!=typeof s.path){var a=null;s.style("transform",!1,!0).hasValue()&&(a=new r.Transform(s.style("transform",!1,!0).value),a.apply(t)),s.path(t),CanvasRenderingContext2D.prototype.closePath=i,a&&a.unapply(t)}}i.call(t),t.clip(),CanvasRenderingContext2D.prototype.beginPath=e,CanvasRenderingContext2D.prototype.closePath=i},this.render=function(){}},r.Element.clipPath.prototype=new r.Element.ElementBase,r.Element.filter=function(t){this.base=r.Element.ElementBase,this.base(t),this.apply=function(t,e){var i=e.getBoundingBox(),n=Math.floor(i.x1),s=Math.floor(i.y1),a=Math.floor(i.width()),r=Math.floor(i.height()),o=e.style("filter").value;e.style("filter").value="";for(var l=0,h=0,u=0;u<this.children.length;u++){var c=this.children[u].extraFilterDistance||0;l=Math.max(l,c),h=Math.max(h,c)}var f=document.createElement("canvas");f.width=a+2*l,f.height=r+2*h;var p=f.getContext("2d");p.translate(-n+l,-s+h),e.render(p);for(var u=0;u<this.children.length;u++)"function"==typeof this.children[u].apply&&this.children[u].apply(p,0,0,a+2*l,r+2*h);t.drawImage(f,0,0,a+2*l,r+2*h,n-l,s-h,a+2*l,r+2*h),e.style("filter",!0).value=o},this.render=function(){}},r.Element.filter.prototype=new r.Element.ElementBase,r.Element.feMorphology=function(t){this.base=r.Element.ElementBase,this.base(t),this.apply=function(){}},r.Element.feMorphology.prototype=new r.Element.ElementBase,r.Element.feComposite=function(t){this.base=r.Element.ElementBase,this.base(t),this.apply=function(){}},r.Element.feComposite.prototype=new r.Element.ElementBase,r.Element.feColorMatrix=function(t){function e(t,e,i,n,s,a){return t[i*n*4+4*e+a]}function i(t,e,i,n,s,a,r){t[i*n*4+4*e+a]=r}function n(t,e){var i=s[t];return i*(0>i?e-255:e)}this.base=r.Element.ElementBase,this.base(t);var s=r.ToNumberArray(this.attribute("values").value);switch(this.attribute("type").valueOrDefault("matrix")){case"saturate":var a=s[0];s=[.213+.787*a,.715-.715*a,.072-.072*a,0,0,.213-.213*a,.715+.285*a,.072-.072*a,0,0,.213-.213*a,.715-.715*a,.072+.928*a,0,0,0,0,0,1,0,0,0,0,0,1];break;case"hueRotate":var o=s[0]*Math.PI/180,l=function(t,e,i){return t+Math.cos(o)*e+Math.sin(o)*i};s=[l(.213,.787,-.213),l(.715,-.715,-.715),l(.072,-.072,.928),0,0,l(.213,-.213,.143),l(.715,.285,.14),l(.072,-.072,-.283),0,0,l(.213,-.213,-.787),l(.715,-.715,.715),l(.072,.928,.072),0,0,0,0,0,1,0,0,0,0,0,1];break;case"luminanceToAlpha":s=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,.2125,.7154,.0721,0,0,0,0,0,0,1]}this.apply=function(t,s,a,r,o){for(var l=t.getImageData(0,0,r,o),a=0;o>a;a++)for(var s=0;r>s;s++){var h=e(l.data,s,a,r,o,0),u=e(l.data,s,a,r,o,1),c=e(l.data,s,a,r,o,2),f=e(l.data,s,a,r,o,3);i(l.data,s,a,r,o,0,n(0,h)+n(1,u)+n(2,c)+n(3,f)+n(4,1)),i(l.data,s,a,r,o,1,n(5,h)+n(6,u)+n(7,c)+n(8,f)+n(9,1)),i(l.data,s,a,r,o,2,n(10,h)+n(11,u)+n(12,c)+n(13,f)+n(14,1)),i(l.data,s,a,r,o,3,n(15,h)+n(16,u)+n(17,c)+n(18,f)+n(19,1))}t.clearRect(0,0,r,o),t.putImageData(l,0,0)}},r.Element.feColorMatrix.prototype=new r.Element.ElementBase,r.Element.feGaussianBlur=function(t){this.base=r.Element.ElementBase,this.base(t),this.blurRadius=Math.floor(this.attribute("stdDeviation").numValue()),this.extraFilterDistance=this.blurRadius,this.apply=function(t,i,n,s,a){return"undefined"==typeof e.canvasRGBA?void r.log("ERROR: StackBlur.js must be included for blur to work"):(t.canvas.id=r.UniqueId(),t.canvas.style.display="none",document.body.appendChild(t.canvas),e.canvasRGBA(t.canvas.id,i,n,s,a,this.blurRadius),void document.body.removeChild(t.canvas))}},r.Element.feGaussianBlur.prototype=new r.Element.ElementBase,r.Element.title=function(){},r.Element.title.prototype=new r.Element.ElementBase,r.Element.desc=function(){},r.Element.desc.prototype=new r.Element.ElementBase,r.Element.MISSING=function(t){r.log("ERROR: Element '"+t.nodeName+"' not yet implemented.")},r.Element.MISSING.prototype=new r.Element.ElementBase,r.CreateElement=function(t){var e=t.nodeName.replace(/^[^:]+:/,"");e=e.replace(/\-/g,"");var i=null;return i="undefined"!=typeof r.Element[e]?new r.Element[e](t):new r.Element.MISSING(t),i.type=t.nodeName,i},r.load=function(t,e){r.loadXml(t,r.ajax(e))},r.loadXml=function(t,e){r.loadXmlDoc(t,r.parseXml(e))},r.loadXmlDoc=function(t,e){r.init(t);var i=function(e){for(var i=t.canvas;i;)e.x-=i.offsetLeft,e.y-=i.offsetTop,i=i.offsetParent;return window.scrollX&&(e.x+=window.scrollX),window.scrollY&&(e.y+=window.scrollY),e};1!=r.opts.ignoreMouse&&(t.canvas.onclick=function(t){var e=i(new r.Point(null!=t?t.clientX:event.clientX,null!=t?t.clientY:event.clientY));r.Mouse.onclick(e.x,e.y)},t.canvas.onmousemove=function(t){var e=i(new r.Point(null!=t?t.clientX:event.clientX,null!=t?t.clientY:event.clientY));r.Mouse.onmousemove(e.x,e.y)});var n=r.CreateElement(e.documentElement);n.root=!0,n.addStylesFromStyleDefinition();var s=!0,a=function(){r.ViewPort.Clear(),t.canvas.parentNode&&r.ViewPort.SetCurrent(t.canvas.parentNode.clientWidth,t.canvas.parentNode.clientHeight),1!=r.opts.ignoreDimensions&&(n.style("width").hasValue()&&(t.canvas.width=n.style("width").toPixels("x"),t.canvas.style.width=t.canvas.width+"px"),n.style("height").hasValue()&&(t.canvas.height=n.style("height").toPixels("y"),t.canvas.style.height=t.canvas.height+"px"));var i=t.canvas.clientWidth||t.canvas.width,a=t.canvas.clientHeight||t.canvas.height;if(1==r.opts.ignoreDimensions&&n.style("width").hasValue()&&n.style("height").hasValue()&&(i=n.style("width").toPixels("x"),a=n.style("height").toPixels("y")),r.ViewPort.SetCurrent(i,a),null!=r.opts.offsetX&&(n.attribute("x",!0).value=r.opts.offsetX),null!=r.opts.offsetY&&(n.attribute("y",!0).value=r.opts.offsetY),null!=r.opts.scaleWidth||null!=r.opts.scaleHeight){var o=null,l=null,h=r.ToNumberArray(n.attribute("viewBox").value);null!=r.opts.scaleWidth&&(n.attribute("width").hasValue()?o=n.attribute("width").toPixels("x")/r.opts.scaleWidth:isNaN(h[2])||(o=h[2]/r.opts.scaleWidth)),null!=r.opts.scaleHeight&&(n.attribute("height").hasValue()?l=n.attribute("height").toPixels("y")/r.opts.scaleHeight:isNaN(h[3])||(l=h[3]/r.opts.scaleHeight)),null==o&&(o=l),null==l&&(l=o),n.attribute("width",!0).value=r.opts.scaleWidth,n.attribute("height",!0).value=r.opts.scaleHeight,n.style("transform",!0,!0).value+=" scale("+1/o+","+1/l+")"}1!=r.opts.ignoreClear&&t.clearRect(0,0,i,a),n.render(t),s&&(s=!1,"function"==typeof r.opts.renderCallback&&r.opts.renderCallback(e))},o=!0;r.ImagesLoaded()&&(o=!1,a()),r.intervalID=setInterval(function(){var t=!1;if(o&&r.ImagesLoaded()&&(o=!1,t=!0),1!=r.opts.ignoreMouse&&(t|=r.Mouse.hasEvents()),1!=r.opts.ignoreAnimation)for(var e=0;e<r.Animations.length;e++)t|=r.Animations[e].update(1e3/r.FRAMERATE);"function"==typeof r.opts.forceRedraw&&1==r.opts.forceRedraw()&&(t=!0),t&&(a(),r.Mouse.runEvents())},1e3/r.FRAMERATE)},r.stop=function(){r.intervalID&&clearInterval(r.intervalID)},r.Mouse=new function(){this.events=[],this.hasEvents=function(){return 0!=this.events.length},this.onclick=function(t,e){this.events.push({type:"onclick",x:t,y:e,run:function(t){t.onclick&&t.onclick()}})},this.onmousemove=function(t,e){this.events.push({type:"onmousemove",x:t,y:e,run:function(t){t.onmousemove&&t.onmousemove()}})},this.eventElements=[],this.checkPath=function(t,e){for(var i=0;i<this.events.length;i++){var n=this.events[i];e.isPointInPath&&e.isPointInPath(n.x,n.y)&&(this.eventElements[i]=t)}},this.checkBoundingBox=function(t,e){for(var i=0;i<this.events.length;i++){var n=this.events[i];e.isPointInBox(n.x,n.y)&&(this.eventElements[i]=t)}},this.runEvents=function(){r.ctx.canvas.style.cursor="";for(var t=0;t<this.events.length;t++)for(var e=this.events[t],i=this.eventElements[t];i;)e.run(i),i=i.parent;this.events=[],this.eventElements=[]}},r}var a,r=function(t,e,i){if(null!=t||null!=e||null!=i){"string"==typeof t&&(t=document.getElementById(t)),null!=t.svg&&t.svg.stop();var n=s(i||{});(1!=t.childNodes.length||"OBJECT"!=t.childNodes[0].nodeName)&&(t.svg=n);var a=t.getContext("2d");"undefined"!=typeof e.documentElement?n.loadXmlDoc(a,e):"<"==e.substr(0,1)?n.loadXml(a,e):n.load(a,e)}else for(var o=document.querySelectorAll("svg"),l=0;l<o.length;l++){var h=o[l],u=document.createElement("canvas");u.width=h.clientWidth,u.height=h.clientHeight,h.parentNode.insertBefore(u,h),h.parentNode.removeChild(h);var c=document.createElement("div");c.appendChild(h),r(u,c.innerHTML)}};"undefined"!=typeof Element.prototype.matches?a=function(t,e){return t.matches(e)}:"undefined"!=typeof Element.prototype.webkitMatchesSelector?a=function(t,e){return t.webkitMatchesSelector(e)}:"undefined"!=typeof Element.prototype.mozMatchesSelector?a=function(t,e){return t.mozMatchesSelector(e)}:"undefined"!=typeof Element.prototype.msMatchesSelector?a=function(t,e){return t.msMatchesSelector(e)}:"undefined"!=typeof Element.prototype.oMatchesSelector?a=function(t,e){return t.oMatchesSelector(e)}:(("function"==typeof jQuery||"function"==typeof Zepto)&&(a=function(t,e){return $(t).is(e)}),"undefined"==typeof a&&(a=Sizzle.matchesSelector));var o=/(\[[^\]]+\])/g,l=/(#[^\s\+>~\.\[:]+)/g,h=/(\.[^\s\+>~\.\[:]+)/g,u=/(::[^\s\+>~\.\[:]+|:first-line|:first-letter|:before|:after)/gi,c=/(:[\w-]+\([^\)]*\))/gi,f=/(:[^\s\+>~\.\[:]+)/g,p=/([^\s\+>~\.\[:]+)/g;return"undefined"!=typeof CanvasRenderingContext2D&&(CanvasRenderingContext2D.prototype.drawSvg=function(t,e,i,n,s){r(this.canvas,t,{ignoreMouse:!0,ignoreAnimation:!0,ignoreDimensions:!0,ignoreClear:!0,offsetX:e,offsetY:i,scaleWidth:n,scaleHeight:s})}),r});
!function t(e,r){"object"==typeof exports&&"object"==typeof module?module.exports=r():"function"==typeof define&&define.amd?define([],r):"object"==typeof exports?exports.Raphael=r():e.Raphael=r()}(this,function(){return function(t){function e(i){if(r[i])return r[i].exports;var n=r[i]={exports:{},id:i,loaded:!1};return t[i].call(n.exports,n,n.exports,e),n.loaded=!0,n.exports}var r={};return e.m=t,e.c=r,e.p="",e(0)}([function(t,e,r){var i,n;i=[r(1),r(3),r(4)],n=function(t){return t}.apply(e,i),!(void 0!==n&&(t.exports=n))},function(t,e,r){var i,n;i=[r(2)],n=function(t){function e(r){if(e.is(r,"function"))return w?r():t.on("raphael.DOMload",r);if(e.is(r,Q))return e._engine.create[z](e,r.splice(0,3+e.is(r[0],$))).add(r);var i=Array.prototype.slice.call(arguments,0);if(e.is(i[i.length-1],"function")){var n=i.pop();return w?n.call(e._engine.create[z](e,i)):t.on("raphael.DOMload",function(){n.call(e._engine.create[z](e,i))})}return e._engine.create[z](e,arguments)}function r(t){if("function"==typeof t||Object(t)!==t)return t;var e=new t.constructor;for(var i in t)t[A](i)&&(e[i]=r(t[i]));return e}function i(t,e){for(var r=0,i=t.length;r<i;r++)if(t[r]===e)return t.push(t.splice(r,1)[0])}function n(t,e,r){function n(){var a=Array.prototype.slice.call(arguments,0),s=a.join("␀"),o=n.cache=n.cache||{},l=n.count=n.count||[];return o[A](s)?(i(l,s),r?r(o[s]):o[s]):(l.length>=1e3&&delete o[l.shift()],l.push(s),o[s]=t[z](e,a),r?r(o[s]):o[s])}return n}function a(){return this.hex}function s(t,e){for(var r=[],i=0,n=t.length;n-2*!e>i;i+=2){var a=[{x:+t[i-2],y:+t[i-1]},{x:+t[i],y:+t[i+1]},{x:+t[i+2],y:+t[i+3]},{x:+t[i+4],y:+t[i+5]}];e?i?n-4==i?a[3]={x:+t[0],y:+t[1]}:n-2==i&&(a[2]={x:+t[0],y:+t[1]},a[3]={x:+t[2],y:+t[3]}):a[0]={x:+t[n-2],y:+t[n-1]}:n-4==i?a[3]=a[2]:i||(a[0]={x:+t[i],y:+t[i+1]}),r.push(["C",(-a[0].x+6*a[1].x+a[2].x)/6,(-a[0].y+6*a[1].y+a[2].y)/6,(a[1].x+6*a[2].x-a[3].x)/6,(a[1].y+6*a[2].y-a[3].y)/6,a[2].x,a[2].y])}return r}function o(t,e,r,i,n){var a=-3*e+9*r-9*i+3*n,s=t*a+6*e-12*r+6*i;return t*s-3*e+3*r}function l(t,e,r,i,n,a,s,l,h){null==h&&(h=1),h=h>1?1:h<0?0:h;for(var u=h/2,c=12,f=[-.1252,.1252,-.3678,.3678,-.5873,.5873,-.7699,.7699,-.9041,.9041,-.9816,.9816],p=[.2491,.2491,.2335,.2335,.2032,.2032,.1601,.1601,.1069,.1069,.0472,.0472],d=0,g=0;g<c;g++){var v=u*f[g]+u,x=o(v,t,r,n,s),y=o(v,e,i,a,l),m=x*x+y*y;d+=p[g]*Y.sqrt(m)}return u*d}function h(t,e,r,i,n,a,s,o,h){if(!(h<0||l(t,e,r,i,n,a,s,o)<h)){var u=1,c=u/2,f=u-c,p,d=.01;for(p=l(t,e,r,i,n,a,s,o,f);H(p-h)>d;)c/=2,f+=(p<h?1:-1)*c,p=l(t,e,r,i,n,a,s,o,f);return f}}function u(t,e,r,i,n,a,s,o){if(!(W(t,r)<G(n,s)||G(t,r)>W(n,s)||W(e,i)<G(a,o)||G(e,i)>W(a,o))){var l=(t*i-e*r)*(n-s)-(t-r)*(n*o-a*s),h=(t*i-e*r)*(a-o)-(e-i)*(n*o-a*s),u=(t-r)*(a-o)-(e-i)*(n-s);if(u){var c=l/u,f=h/u,p=+c.toFixed(2),d=+f.toFixed(2);if(!(p<+G(t,r).toFixed(2)||p>+W(t,r).toFixed(2)||p<+G(n,s).toFixed(2)||p>+W(n,s).toFixed(2)||d<+G(e,i).toFixed(2)||d>+W(e,i).toFixed(2)||d<+G(a,o).toFixed(2)||d>+W(a,o).toFixed(2)))return{x:c,y:f}}}}function c(t,e){return p(t,e)}function f(t,e){return p(t,e,1)}function p(t,r,i){var n=e.bezierBBox(t),a=e.bezierBBox(r);if(!e.isBBoxIntersect(n,a))return i?0:[];for(var s=l.apply(0,t),o=l.apply(0,r),h=W(~~(s/5),1),c=W(~~(o/5),1),f=[],p=[],d={},g=i?0:[],v=0;v<h+1;v++){var x=e.findDotsAtSegment.apply(e,t.concat(v/h));f.push({x:x.x,y:x.y,t:v/h})}for(v=0;v<c+1;v++)x=e.findDotsAtSegment.apply(e,r.concat(v/c)),p.push({x:x.x,y:x.y,t:v/c});for(v=0;v<h;v++)for(var y=0;y<c;y++){var m=f[v],b=f[v+1],_=p[y],w=p[y+1],k=H(b.x-m.x)<.001?"y":"x",B=H(w.x-_.x)<.001?"y":"x",C=u(m.x,m.y,b.x,b.y,_.x,_.y,w.x,w.y);if(C){if(d[C.x.toFixed(4)]==C.y.toFixed(4))continue;d[C.x.toFixed(4)]=C.y.toFixed(4);var S=m.t+H((C[k]-m[k])/(b[k]-m[k]))*(b.t-m.t),A=_.t+H((C[B]-_[B])/(w[B]-_[B]))*(w.t-_.t);S>=0&&S<=1.001&&A>=0&&A<=1.001&&(i?g++:g.push({x:C.x,y:C.y,t1:G(S,1),t2:G(A,1)}))}}return g}function d(t,r,i){t=e._path2curve(t),r=e._path2curve(r);for(var n,a,s,o,l,h,u,c,f,d,g=i?0:[],v=0,x=t.length;v<x;v++){var y=t[v];if("M"==y[0])n=l=y[1],a=h=y[2];else{"C"==y[0]?(f=[n,a].concat(y.slice(1)),n=f[6],a=f[7]):(f=[n,a,n,a,l,h,l,h],n=l,a=h);for(var m=0,b=r.length;m<b;m++){var _=r[m];if("M"==_[0])s=u=_[1],o=c=_[2];else{"C"==_[0]?(d=[s,o].concat(_.slice(1)),s=d[6],o=d[7]):(d=[s,o,s,o,u,c,u,c],s=u,o=c);var w=p(f,d,i);if(i)g+=w;else{for(var k=0,B=w.length;k<B;k++)w[k].segment1=v,w[k].segment2=m,w[k].bez1=f,w[k].bez2=d;g=g.concat(w)}}}}}return g}function g(t,e,r,i,n,a){null!=t?(this.a=+t,this.b=+e,this.c=+r,this.d=+i,this.e=+n,this.f=+a):(this.a=1,this.b=0,this.c=0,this.d=1,this.e=0,this.f=0)}function v(){return this.x+j+this.y}function x(){return this.x+j+this.y+j+this.width+" × "+this.height}function y(t,e,r,i,n,a){function s(t){return((c*t+u)*t+h)*t}function o(t,e){var r=l(t,e);return((d*r+p)*r+f)*r}function l(t,e){var r,i,n,a,o,l;for(n=t,l=0;l<8;l++){if(a=s(n)-t,H(a)<e)return n;if(o=(3*c*n+2*u)*n+h,H(o)<1e-6)break;n-=a/o}if(r=0,i=1,n=t,n<r)return r;if(n>i)return i;for(;r<i;){if(a=s(n),H(a-t)<e)return n;t>a?r=n:i=n,n=(i-r)/2+r}return n}var h=3*e,u=3*(i-e)-h,c=1-h-u,f=3*r,p=3*(n-r)-f,d=1-f-p;return o(t,1/(200*a))}function m(t,e){var r=[],i={};if(this.ms=e,this.times=1,t){for(var n in t)t[A](n)&&(i[ht(n)]=t[n],r.push(ht(n)));r.sort(Bt)}this.anim=i,this.top=r[r.length-1],this.percents=r}function b(r,i,n,a,s,o){n=ht(n);var l,h,u,c=[],f,p,d,v=r.ms,x={},m={},b={};if(a)for(w=0,B=Ee.length;w<B;w++){var _=Ee[w];if(_.el.id==i.id&&_.anim==r){_.percent!=n?(Ee.splice(w,1),u=1):h=_,i.attr(_.totalOrigin);break}}else a=+m;for(var w=0,B=r.percents.length;w<B;w++){if(r.percents[w]==n||r.percents[w]>a*r.top){n=r.percents[w],p=r.percents[w-1]||0,v=v/r.top*(n-p),f=r.percents[w+1],l=r.anim[n];break}a&&i.attr(r.anim[r.percents[w]])}if(l){if(h)h.initstatus=a,h.start=new Date-h.ms*a;else{for(var C in l)if(l[A](C)&&(pt[A](C)||i.paper.customAttributes[A](C)))switch(x[C]=i.attr(C),null==x[C]&&(x[C]=ft[C]),m[C]=l[C],pt[C]){case $:b[C]=(m[C]-x[C])/v;break;case"colour":x[C]=e.getRGB(x[C]);var S=e.getRGB(m[C]);b[C]={r:(S.r-x[C].r)/v,g:(S.g-x[C].g)/v,b:(S.b-x[C].b)/v};break;case"path":var T=Qt(x[C],m[C]),E=T[1];for(x[C]=T[0],b[C]=[],w=0,B=x[C].length;w<B;w++){b[C][w]=[0];for(var M=1,N=x[C][w].length;M<N;M++)b[C][w][M]=(E[w][M]-x[C][w][M])/v}break;case"transform":var L=i._,z=le(L[C],m[C]);if(z)for(x[C]=z.from,m[C]=z.to,b[C]=[],b[C].real=!0,w=0,B=x[C].length;w<B;w++)for(b[C][w]=[x[C][w][0]],M=1,N=x[C][w].length;M<N;M++)b[C][w][M]=(m[C][w][M]-x[C][w][M])/v;else{var F=i.matrix||new g,R={_:{transform:L.transform},getBBox:function(){return i.getBBox(1)}};x[C]=[F.a,F.b,F.c,F.d,F.e,F.f],se(R,m[C]),m[C]=R._.transform,b[C]=[(R.matrix.a-F.a)/v,(R.matrix.b-F.b)/v,(R.matrix.c-F.c)/v,(R.matrix.d-F.d)/v,(R.matrix.e-F.e)/v,(R.matrix.f-F.f)/v]}break;case"csv":var j=I(l[C])[q](k),D=I(x[C])[q](k);if("clip-rect"==C)for(x[C]=D,b[C]=[],w=D.length;w--;)b[C][w]=(j[w]-x[C][w])/v;m[C]=j;break;default:for(j=[][P](l[C]),D=[][P](x[C]),b[C]=[],w=i.paper.customAttributes[C].length;w--;)b[C][w]=((j[w]||0)-(D[w]||0))/v}var V=l.easing,O=e.easing_formulas[V];if(!O)if(O=I(V).match(st),O&&5==O.length){var Y=O;O=function(t){return y(t,+Y[1],+Y[2],+Y[3],+Y[4],v)}}else O=St;if(d=l.start||r.start||+new Date,_={anim:r,percent:n,timestamp:d,start:d+(r.del||0),status:0,initstatus:a||0,stop:!1,ms:v,easing:O,from:x,diff:b,to:m,el:i,callback:l.callback,prev:p,next:f,repeat:o||r.times,origin:i.attr(),totalOrigin:s},Ee.push(_),a&&!h&&!u&&(_.stop=!0,_.start=new Date-v*a,1==Ee.length))return Ne();u&&(_.start=new Date-_.ms*a),1==Ee.length&&Me(Ne)}t("raphael.anim.start."+i.id,i,r)}}function _(t){for(var e=0;e<Ee.length;e++)Ee[e].el.paper==t&&Ee.splice(e--,1)}e.version="2.2.0",e.eve=t;var w,k=/[, ]+/,B={circle:1,rect:1,path:1,ellipse:1,text:1,image:1},C=/\{(\d+)\}/g,S="prototype",A="hasOwnProperty",T={doc:document,win:window},E={was:Object.prototype[A].call(T.win,"Raphael"),is:T.win.Raphael},M=function(){this.ca=this.customAttributes={}},N,L="appendChild",z="apply",P="concat",F="ontouchstart"in T.win||T.win.DocumentTouch&&T.doc instanceof DocumentTouch,R="",j=" ",I=String,q="split",D="click dblclick mousedown mousemove mouseout mouseover mouseup touchstart touchmove touchend touchcancel"[q](j),V={mousedown:"touchstart",mousemove:"touchmove",mouseup:"touchend"},O=I.prototype.toLowerCase,Y=Math,W=Y.max,G=Y.min,H=Y.abs,X=Y.pow,U=Y.PI,$="number",Z="string",Q="array",J="toString",K="fill",tt=Object.prototype.toString,et={},rt="push",it=e._ISURL=/^url\(['"]?(.+?)['"]?\)$/i,nt=/^\s*((#[a-f\d]{6})|(#[a-f\d]{3})|rgba?\(\s*([\d\.]+%?\s*,\s*[\d\.]+%?\s*,\s*[\d\.]+%?(?:\s*,\s*[\d\.]+%?)?)\s*\)|hsba?\(\s*([\d\.]+(?:deg|\xb0|%)?\s*,\s*[\d\.]+%?\s*,\s*[\d\.]+(?:%?\s*,\s*[\d\.]+)?)%?\s*\)|hsla?\(\s*([\d\.]+(?:deg|\xb0|%)?\s*,\s*[\d\.]+%?\s*,\s*[\d\.]+(?:%?\s*,\s*[\d\.]+)?)%?\s*\))\s*$/i,at={NaN:1,Infinity:1,"-Infinity":1},st=/^(?:cubic-)?bezier\(([^,]+),([^,]+),([^,]+),([^\)]+)\)/,ot=Y.round,lt="setAttribute",ht=parseFloat,ut=parseInt,ct=I.prototype.toUpperCase,ft=e._availableAttrs={"arrow-end":"none","arrow-start":"none",blur:0,"clip-rect":"0 0 1e9 1e9",cursor:"default",cx:0,cy:0,fill:"#fff","fill-opacity":1,font:'10px "Arial"',"font-family":'"Arial"',"font-size":"10","font-style":"normal","font-weight":400,gradient:0,height:0,href:"http://raphaeljs.com/","letter-spacing":0,opacity:1,path:"M0,0",r:0,rx:0,ry:0,src:"",stroke:"#000","stroke-dasharray":"","stroke-linecap":"butt","stroke-linejoin":"butt","stroke-miterlimit":0,"stroke-opacity":1,"stroke-width":1,target:"_blank","text-anchor":"middle",title:"Raphael",transform:"",width:0,x:0,y:0,"class":""},pt=e._availableAnimAttrs={blur:$,"clip-rect":"csv",cx:$,cy:$,fill:"colour","fill-opacity":$,"font-size":$,height:$,opacity:$,path:"path",r:$,rx:$,ry:$,stroke:"colour","stroke-opacity":$,"stroke-width":$,transform:"transform",width:$,x:$,y:$},dt=/[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]/g,gt=/[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*/,vt={hs:1,rg:1},xt=/,?([achlmqrstvxz]),?/gi,yt=/([achlmrqstvz])[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029,]*((-?\d*\.?\d*(?:e[\-+]?\d+)?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*)+)/gi,mt=/([rstm])[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029,]*((-?\d*\.?\d*(?:e[\-+]?\d+)?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*)+)/gi,bt=/(-?\d*\.?\d*(?:e[\-+]?\d+)?)[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*/gi,_t=e._radial_gradient=/^r(?:\(([^,]+?)[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*([^\)]+?)\))?/,wt={},kt=function(t,e){return t.key-e.key},Bt=function(t,e){return ht(t)-ht(e)},Ct=function(){},St=function(t){return t},At=e._rectPath=function(t,e,r,i,n){return n?[["M",t+n,e],["l",r-2*n,0],["a",n,n,0,0,1,n,n],["l",0,i-2*n],["a",n,n,0,0,1,-n,n],["l",2*n-r,0],["a",n,n,0,0,1,-n,-n],["l",0,2*n-i],["a",n,n,0,0,1,n,-n],["z"]]:[["M",t,e],["l",r,0],["l",0,i],["l",-r,0],["z"]]},Tt=function(t,e,r,i){return null==i&&(i=r),[["M",t,e],["m",0,-i],["a",r,i,0,1,1,0,2*i],["a",r,i,0,1,1,0,-2*i],["z"]]},Et=e._getPath={path:function(t){return t.attr("path")},circle:function(t){var e=t.attrs;return Tt(e.cx,e.cy,e.r)},ellipse:function(t){var e=t.attrs;return Tt(e.cx,e.cy,e.rx,e.ry)},rect:function(t){var e=t.attrs;return At(e.x,e.y,e.width,e.height,e.r)},image:function(t){var e=t.attrs;return At(e.x,e.y,e.width,e.height)},text:function(t){var e=t._getBBox();return At(e.x,e.y,e.width,e.height)},set:function(t){var e=t._getBBox();return At(e.x,e.y,e.width,e.height)}},Mt=e.mapPath=function(t,e){if(!e)return t;var r,i,n,a,s,o,l;for(t=Qt(t),n=0,s=t.length;n<s;n++)for(l=t[n],a=1,o=l.length;a<o;a+=2)r=e.x(l[a],l[a+1]),i=e.y(l[a],l[a+1]),l[a]=r,l[a+1]=i;return t};if(e._g=T,e.type=T.win.SVGAngle||T.doc.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure","1.1")?"SVG":"VML","VML"==e.type){var Nt=T.doc.createElement("div"),Lt;if(Nt.innerHTML='<v:shape adj="1"/>',Lt=Nt.firstChild,Lt.style.behavior="url(#default#VML)",!Lt||"object"!=typeof Lt.adj)return e.type=R;Nt=null}e.svg=!(e.vml="VML"==e.type),e._Paper=M,e.fn=N=M.prototype=e.prototype,e._id=0,e.is=function(t,e){return e=O.call(e),"finite"==e?!at[A](+t):"array"==e?t instanceof Array:"null"==e&&null===t||e==typeof t&&null!==t||"object"==e&&t===Object(t)||"array"==e&&Array.isArray&&Array.isArray(t)||tt.call(t).slice(8,-1).toLowerCase()==e},e.angle=function(t,r,i,n,a,s){if(null==a){var o=t-i,l=r-n;return o||l?(180+180*Y.atan2(-l,-o)/U+360)%360:0}return e.angle(t,r,a,s)-e.angle(i,n,a,s)},e.rad=function(t){return t%360*U/180},e.deg=function(t){return Math.round(180*t/U%360*1e3)/1e3},e.snapTo=function(t,r,i){if(i=e.is(i,"finite")?i:10,e.is(t,Q)){for(var n=t.length;n--;)if(H(t[n]-r)<=i)return t[n]}else{t=+t;var a=r%t;if(a<i)return r-a;if(a>t-i)return r-a+t}return r};var zt=e.createUUID=function(t,e){return function(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(t,e).toUpperCase()}}(/[xy]/g,function(t){var e=16*Y.random()|0,r="x"==t?e:3&e|8;return r.toString(16)});e.setWindow=function(r){t("raphael.setWindow",e,T.win,r),T.win=r,T.doc=T.win.document,e._engine.initWin&&e._engine.initWin(T.win)};var Pt=function(t){if(e.vml){var r=/^\s+|\s+$/g,i;try{var a=new ActiveXObject("htmlfile");a.write("<body>"),a.close(),i=a.body}catch(s){i=createPopup().document.body}var o=i.createTextRange();Pt=n(function(t){try{i.style.color=I(t).replace(r,R);var e=o.queryCommandValue("ForeColor");return e=(255&e)<<16|65280&e|(16711680&e)>>>16,"#"+("000000"+e.toString(16)).slice(-6)}catch(n){return"none"}})}else{var l=T.doc.createElement("i");l.title="Raphaël Colour Picker",l.style.display="none",T.doc.body.appendChild(l),Pt=n(function(t){return l.style.color=t,T.doc.defaultView.getComputedStyle(l,R).getPropertyValue("color")})}return Pt(t)},Ft=function(){return"hsb("+[this.h,this.s,this.b]+")"},Rt=function(){return"hsl("+[this.h,this.s,this.l]+")"},jt=function(){return this.hex},It=function(t,r,i){if(null==r&&e.is(t,"object")&&"r"in t&&"g"in t&&"b"in t&&(i=t.b,r=t.g,t=t.r),null==r&&e.is(t,Z)){var n=e.getRGB(t);t=n.r,r=n.g,i=n.b}return(t>1||r>1||i>1)&&(t/=255,r/=255,i/=255),[t,r,i]},qt=function(t,r,i,n){t*=255,r*=255,i*=255;var a={r:t,g:r,b:i,hex:e.rgb(t,r,i),toString:jt};return e.is(n,"finite")&&(a.opacity=n),a};e.color=function(t){var r;return e.is(t,"object")&&"h"in t&&"s"in t&&"b"in t?(r=e.hsb2rgb(t),t.r=r.r,t.g=r.g,t.b=r.b,t.hex=r.hex):e.is(t,"object")&&"h"in t&&"s"in t&&"l"in t?(r=e.hsl2rgb(t),t.r=r.r,t.g=r.g,t.b=r.b,t.hex=r.hex):(e.is(t,"string")&&(t=e.getRGB(t)),e.is(t,"object")&&"r"in t&&"g"in t&&"b"in t?(r=e.rgb2hsl(t),t.h=r.h,t.s=r.s,t.l=r.l,r=e.rgb2hsb(t),t.v=r.b):(t={hex:"none"},t.r=t.g=t.b=t.h=t.s=t.v=t.l=-1)),t.toString=jt,t},e.hsb2rgb=function(t,e,r,i){this.is(t,"object")&&"h"in t&&"s"in t&&"b"in t&&(r=t.b,e=t.s,i=t.o,t=t.h),t*=360;var n,a,s,o,l;return t=t%360/60,l=r*e,o=l*(1-H(t%2-1)),n=a=s=r-l,t=~~t,n+=[l,o,0,0,o,l][t],a+=[o,l,l,o,0,0][t],s+=[0,0,o,l,l,o][t],qt(n,a,s,i)},e.hsl2rgb=function(t,e,r,i){this.is(t,"object")&&"h"in t&&"s"in t&&"l"in t&&(r=t.l,e=t.s,t=t.h),(t>1||e>1||r>1)&&(t/=360,e/=100,r/=100),t*=360;var n,a,s,o,l;return t=t%360/60,l=2*e*(r<.5?r:1-r),o=l*(1-H(t%2-1)),n=a=s=r-l/2,t=~~t,n+=[l,o,0,0,o,l][t],a+=[o,l,l,o,0,0][t],s+=[0,0,o,l,l,o][t],qt(n,a,s,i)},e.rgb2hsb=function(t,e,r){r=It(t,e,r),t=r[0],e=r[1],r=r[2];var i,n,a,s;return a=W(t,e,r),s=a-G(t,e,r),i=0==s?null:a==t?(e-r)/s:a==e?(r-t)/s+2:(t-e)/s+4,i=(i+360)%6*60/360,n=0==s?0:s/a,{h:i,s:n,b:a,toString:Ft}},e.rgb2hsl=function(t,e,r){r=It(t,e,r),t=r[0],e=r[1],r=r[2];var i,n,a,s,o,l;return s=W(t,e,r),o=G(t,e,r),l=s-o,i=0==l?null:s==t?(e-r)/l:s==e?(r-t)/l+2:(t-e)/l+4,i=(i+360)%6*60/360,a=(s+o)/2,n=0==l?0:a<.5?l/(2*a):l/(2-2*a),{h:i,s:n,l:a,toString:Rt}},e._path2string=function(){return this.join(",").replace(xt,"$1")};var Dt=e._preload=function(t,e){var r=T.doc.createElement("img");r.style.cssText="position:absolute;left:-9999em;top:-9999em",r.onload=function(){e.call(this),this.onload=null,T.doc.body.removeChild(this)},r.onerror=function(){T.doc.body.removeChild(this)},T.doc.body.appendChild(r),r.src=t};e.getRGB=n(function(t){if(!t||(t=I(t)).indexOf("-")+1)return{r:-1,g:-1,b:-1,hex:"none",error:1,toString:a};if("none"==t)return{r:-1,g:-1,b:-1,hex:"none",toString:a};!(vt[A](t.toLowerCase().substring(0,2))||"#"==t.charAt())&&(t=Pt(t));var r,i,n,s,o,l,h,u=t.match(nt);return u?(u[2]&&(s=ut(u[2].substring(5),16),n=ut(u[2].substring(3,5),16),i=ut(u[2].substring(1,3),16)),u[3]&&(s=ut((l=u[3].charAt(3))+l,16),n=ut((l=u[3].charAt(2))+l,16),i=ut((l=u[3].charAt(1))+l,16)),u[4]&&(h=u[4][q](gt),i=ht(h[0]),"%"==h[0].slice(-1)&&(i*=2.55),n=ht(h[1]),"%"==h[1].slice(-1)&&(n*=2.55),s=ht(h[2]),"%"==h[2].slice(-1)&&(s*=2.55),"rgba"==u[1].toLowerCase().slice(0,4)&&(o=ht(h[3])),h[3]&&"%"==h[3].slice(-1)&&(o/=100)),u[5]?(h=u[5][q](gt),i=ht(h[0]),"%"==h[0].slice(-1)&&(i*=2.55),n=ht(h[1]),"%"==h[1].slice(-1)&&(n*=2.55),s=ht(h[2]),"%"==h[2].slice(-1)&&(s*=2.55),("deg"==h[0].slice(-3)||"°"==h[0].slice(-1))&&(i/=360),"hsba"==u[1].toLowerCase().slice(0,4)&&(o=ht(h[3])),h[3]&&"%"==h[3].slice(-1)&&(o/=100),e.hsb2rgb(i,n,s,o)):u[6]?(h=u[6][q](gt),i=ht(h[0]),"%"==h[0].slice(-1)&&(i*=2.55),n=ht(h[1]),"%"==h[1].slice(-1)&&(n*=2.55),s=ht(h[2]),"%"==h[2].slice(-1)&&(s*=2.55),("deg"==h[0].slice(-3)||"°"==h[0].slice(-1))&&(i/=360),"hsla"==u[1].toLowerCase().slice(0,4)&&(o=ht(h[3])),h[3]&&"%"==h[3].slice(-1)&&(o/=100),e.hsl2rgb(i,n,s,o)):(u={r:i,g:n,b:s,toString:a},u.hex="#"+(16777216|s|n<<8|i<<16).toString(16).slice(1),e.is(o,"finite")&&(u.opacity=o),u)):{r:-1,g:-1,b:-1,hex:"none",error:1,toString:a}},e),e.hsb=n(function(t,r,i){return e.hsb2rgb(t,r,i).hex}),e.hsl=n(function(t,r,i){return e.hsl2rgb(t,r,i).hex}),e.rgb=n(function(t,e,r){function i(t){return t+.5|0}return"#"+(16777216|i(r)|i(e)<<8|i(t)<<16).toString(16).slice(1)}),e.getColor=function(t){var e=this.getColor.start=this.getColor.start||{h:0,s:1,b:t||.75},r=this.hsb2rgb(e.h,e.s,e.b);return e.h+=.075,e.h>1&&(e.h=0,e.s-=.2,e.s<=0&&(this.getColor.start={h:0,s:1,b:e.b})),r.hex},e.getColor.reset=function(){delete this.start},e.parsePathString=function(t){if(!t)return null;var r=Vt(t);if(r.arr)return Yt(r.arr);var i={a:7,c:6,h:1,l:2,m:2,r:4,q:4,s:4,t:2,v:1,z:0},n=[];return e.is(t,Q)&&e.is(t[0],Q)&&(n=Yt(t)),n.length||I(t).replace(yt,function(t,e,r){var a=[],s=e.toLowerCase();if(r.replace(bt,function(t,e){e&&a.push(+e)}),"m"==s&&a.length>2&&(n.push([e][P](a.splice(0,2))),s="l",e="m"==e?"l":"L"),"r"==s)n.push([e][P](a));else for(;a.length>=i[s]&&(n.push([e][P](a.splice(0,i[s]))),i[s]););}),n.toString=e._path2string,r.arr=Yt(n),n},e.parseTransformString=n(function(t){if(!t)return null;var r={r:3,s:4,t:2,m:6},i=[];return e.is(t,Q)&&e.is(t[0],Q)&&(i=Yt(t)),i.length||I(t).replace(mt,function(t,e,r){var n=[],a=O.call(e);r.replace(bt,function(t,e){e&&n.push(+e)}),i.push([e][P](n))}),i.toString=e._path2string,i});var Vt=function(t){var e=Vt.ps=Vt.ps||{};return e[t]?e[t].sleep=100:e[t]={sleep:100},setTimeout(function(){for(var r in e)e[A](r)&&r!=t&&(e[r].sleep--,!e[r].sleep&&delete e[r])}),e[t]};e.findDotsAtSegment=function(t,e,r,i,n,a,s,o,l){var h=1-l,u=X(h,3),c=X(h,2),f=l*l,p=f*l,d=u*t+3*c*l*r+3*h*l*l*n+p*s,g=u*e+3*c*l*i+3*h*l*l*a+p*o,v=t+2*l*(r-t)+f*(n-2*r+t),x=e+2*l*(i-e)+f*(a-2*i+e),y=r+2*l*(n-r)+f*(s-2*n+r),m=i+2*l*(a-i)+f*(o-2*a+i),b=h*t+l*r,_=h*e+l*i,w=h*n+l*s,k=h*a+l*o,B=90-180*Y.atan2(v-y,x-m)/U;return(v>y||x<m)&&(B+=180),{x:d,y:g,m:{x:v,y:x},n:{x:y,y:m},start:{x:b,y:_},end:{x:w,y:k},alpha:B}},e.bezierBBox=function(t,r,i,n,a,s,o,l){e.is(t,"array")||(t=[t,r,i,n,a,s,o,l]);var h=Zt.apply(null,t);return{x:h.min.x,y:h.min.y,x2:h.max.x,y2:h.max.y,width:h.max.x-h.min.x,height:h.max.y-h.min.y}},e.isPointInsideBBox=function(t,e,r){return e>=t.x&&e<=t.x2&&r>=t.y&&r<=t.y2},e.isBBoxIntersect=function(t,r){var i=e.isPointInsideBBox;return i(r,t.x,t.y)||i(r,t.x2,t.y)||i(r,t.x,t.y2)||i(r,t.x2,t.y2)||i(t,r.x,r.y)||i(t,r.x2,r.y)||i(t,r.x,r.y2)||i(t,r.x2,r.y2)||(t.x<r.x2&&t.x>r.x||r.x<t.x2&&r.x>t.x)&&(t.y<r.y2&&t.y>r.y||r.y<t.y2&&r.y>t.y)},e.pathIntersection=function(t,e){return d(t,e)},e.pathIntersectionNumber=function(t,e){return d(t,e,1)},e.isPointInsidePath=function(t,r,i){var n=e.pathBBox(t);return e.isPointInsideBBox(n,r,i)&&d(t,[["M",r,i],["H",n.x2+10]],1)%2==1},e._removedFactory=function(e){return function(){t("raphael.log",null,"Raphaël: you are calling to method “"+e+"” of removed object",e)}};var Ot=e.pathBBox=function(t){var e=Vt(t);if(e.bbox)return r(e.bbox);if(!t)return{x:0,y:0,width:0,height:0,x2:0,y2:0};t=Qt(t);for(var i=0,n=0,a=[],s=[],o,l=0,h=t.length;l<h;l++)if(o=t[l],"M"==o[0])i=o[1],n=o[2],a.push(i),s.push(n);else{var u=Zt(i,n,o[1],o[2],o[3],o[4],o[5],o[6]);a=a[P](u.min.x,u.max.x),s=s[P](u.min.y,u.max.y),i=o[5],n=o[6]}var c=G[z](0,a),f=G[z](0,s),p=W[z](0,a),d=W[z](0,s),g=p-c,v=d-f,x={x:c,y:f,x2:p,y2:d,width:g,height:v,cx:c+g/2,cy:f+v/2};return e.bbox=r(x),x},Yt=function(t){var i=r(t);return i.toString=e._path2string,i},Wt=e._pathToRelative=function(t){var r=Vt(t);if(r.rel)return Yt(r.rel);e.is(t,Q)&&e.is(t&&t[0],Q)||(t=e.parsePathString(t));var i=[],n=0,a=0,s=0,o=0,l=0;"M"==t[0][0]&&(n=t[0][1],a=t[0][2],s=n,o=a,l++,i.push(["M",n,a]));for(var h=l,u=t.length;h<u;h++){var c=i[h]=[],f=t[h];if(f[0]!=O.call(f[0]))switch(c[0]=O.call(f[0]),c[0]){case"a":c[1]=f[1],c[2]=f[2],c[3]=f[3],c[4]=f[4],c[5]=f[5],c[6]=+(f[6]-n).toFixed(3),c[7]=+(f[7]-a).toFixed(3);break;case"v":c[1]=+(f[1]-a).toFixed(3);break;case"m":s=f[1],o=f[2];default:for(var p=1,d=f.length;p<d;p++)c[p]=+(f[p]-(p%2?n:a)).toFixed(3)}else{c=i[h]=[],"m"==f[0]&&(s=f[1]+n,o=f[2]+a);for(var g=0,v=f.length;g<v;g++)i[h][g]=f[g]}var x=i[h].length;switch(i[h][0]){case"z":n=s,a=o;break;case"h":n+=+i[h][x-1];break;case"v":a+=+i[h][x-1];break;default:n+=+i[h][x-2],a+=+i[h][x-1]}}return i.toString=e._path2string,r.rel=Yt(i),i},Gt=e._pathToAbsolute=function(t){var r=Vt(t);if(r.abs)return Yt(r.abs);if(e.is(t,Q)&&e.is(t&&t[0],Q)||(t=e.parsePathString(t)),!t||!t.length)return[["M",0,0]];var i=[],n=0,a=0,o=0,l=0,h=0;"M"==t[0][0]&&(n=+t[0][1],a=+t[0][2],o=n,l=a,h++,i[0]=["M",n,a]);for(var u=3==t.length&&"M"==t[0][0]&&"R"==t[1][0].toUpperCase()&&"Z"==t[2][0].toUpperCase(),c,f,p=h,d=t.length;p<d;p++){if(i.push(c=[]),f=t[p],f[0]!=ct.call(f[0]))switch(c[0]=ct.call(f[0]),c[0]){case"A":c[1]=f[1],c[2]=f[2],c[3]=f[3],c[4]=f[4],c[5]=f[5],c[6]=+(f[6]+n),c[7]=+(f[7]+a);break;case"V":c[1]=+f[1]+a;break;case"H":c[1]=+f[1]+n;break;case"R":for(var g=[n,a][P](f.slice(1)),v=2,x=g.length;v<x;v++)g[v]=+g[v]+n,g[++v]=+g[v]+a;i.pop(),i=i[P](s(g,u));break;case"M":o=+f[1]+n,l=+f[2]+a;default:for(v=1,x=f.length;v<x;v++)c[v]=+f[v]+(v%2?n:a)}else if("R"==f[0])g=[n,a][P](f.slice(1)),i.pop(),i=i[P](s(g,u)),c=["R"][P](f.slice(-2));else for(var y=0,m=f.length;y<m;y++)c[y]=f[y];switch(c[0]){case"Z":n=o,a=l;break;case"H":n=c[1];break;case"V":a=c[1];break;case"M":o=c[c.length-2],l=c[c.length-1];default:n=c[c.length-2],a=c[c.length-1]}}return i.toString=e._path2string,r.abs=Yt(i),i},Ht=function(t,e,r,i){return[t,e,r,i,r,i]},Xt=function(t,e,r,i,n,a){var s=1/3,o=2/3;return[s*t+o*r,s*e+o*i,s*n+o*r,s*a+o*i,n,a]},Ut=function(t,e,r,i,a,s,o,l,h,u){var c=120*U/180,f=U/180*(+a||0),p=[],d,g=n(function(t,e,r){var i=t*Y.cos(r)-e*Y.sin(r),n=t*Y.sin(r)+e*Y.cos(r);return{x:i,y:n}});if(u)S=u[0],A=u[1],B=u[2],C=u[3];else{d=g(t,e,-f),t=d.x,e=d.y,d=g(l,h,-f),l=d.x,h=d.y;var v=Y.cos(U/180*a),x=Y.sin(U/180*a),y=(t-l)/2,m=(e-h)/2,b=y*y/(r*r)+m*m/(i*i);b>1&&(b=Y.sqrt(b),r=b*r,i=b*i);var _=r*r,w=i*i,k=(s==o?-1:1)*Y.sqrt(H((_*w-_*m*m-w*y*y)/(_*m*m+w*y*y))),B=k*r*m/i+(t+l)/2,C=k*-i*y/r+(e+h)/2,S=Y.asin(((e-C)/i).toFixed(9)),A=Y.asin(((h-C)/i).toFixed(9));S=t<B?U-S:S,A=l<B?U-A:A,S<0&&(S=2*U+S),A<0&&(A=2*U+A),o&&S>A&&(S-=2*U),!o&&A>S&&(A-=2*U)}var T=A-S;if(H(T)>c){var E=A,M=l,N=h;A=S+c*(o&&A>S?1:-1),l=B+r*Y.cos(A),h=C+i*Y.sin(A),p=Ut(l,h,r,i,a,0,o,M,N,[A,E,B,C])}T=A-S;var L=Y.cos(S),z=Y.sin(S),F=Y.cos(A),R=Y.sin(A),j=Y.tan(T/4),I=4/3*r*j,D=4/3*i*j,V=[t,e],O=[t+I*z,e-D*L],W=[l+I*R,h-D*F],G=[l,h];if(O[0]=2*V[0]-O[0],O[1]=2*V[1]-O[1],u)return[O,W,G][P](p);p=[O,W,G][P](p).join()[q](",");for(var X=[],$=0,Z=p.length;$<Z;$++)X[$]=$%2?g(p[$-1],p[$],f).y:g(p[$],p[$+1],f).x;return X},$t=function(t,e,r,i,n,a,s,o,l){var h=1-l;return{x:X(h,3)*t+3*X(h,2)*l*r+3*h*l*l*n+X(l,3)*s,y:X(h,3)*e+3*X(h,2)*l*i+3*h*l*l*a+X(l,3)*o}},Zt=n(function(t,e,r,i,n,a,s,o){var l=n-2*r+t-(s-2*n+r),h=2*(r-t)-2*(n-r),u=t-r,c=(-h+Y.sqrt(h*h-4*l*u))/2/l,f=(-h-Y.sqrt(h*h-4*l*u))/2/l,p=[e,o],d=[t,s],g;return H(c)>"1e12"&&(c=.5),H(f)>"1e12"&&(f=.5),c>0&&c<1&&(g=$t(t,e,r,i,n,a,s,o,c),d.push(g.x),p.push(g.y)),f>0&&f<1&&(g=$t(t,e,r,i,n,a,s,o,f),d.push(g.x),p.push(g.y)),l=a-2*i+e-(o-2*a+i),h=2*(i-e)-2*(a-i),u=e-i,c=(-h+Y.sqrt(h*h-4*l*u))/2/l,f=(-h-Y.sqrt(h*h-4*l*u))/2/l,H(c)>"1e12"&&(c=.5),H(f)>"1e12"&&(f=.5),c>0&&c<1&&(g=$t(t,e,r,i,n,a,s,o,c),d.push(g.x),p.push(g.y)),f>0&&f<1&&(g=$t(t,e,r,i,n,a,s,o,f),d.push(g.x),p.push(g.y)),{min:{x:G[z](0,d),y:G[z](0,p)},max:{x:W[z](0,d),y:W[z](0,p)}}}),Qt=e._path2curve=n(function(t,e){var r=!e&&Vt(t);if(!e&&r.curve)return Yt(r.curve);for(var i=Gt(t),n=e&&Gt(e),a={x:0,y:0,bx:0,by:0,X:0,Y:0,qx:null,qy:null},s={x:0,y:0,bx:0,by:0,X:0,Y:0,qx:null,qy:null},o=(function(t,e,r){var i,n,a={T:1,Q:1};if(!t)return["C",e.x,e.y,e.x,e.y,e.x,e.y];switch(!(t[0]in a)&&(e.qx=e.qy=null),t[0]){case"M":e.X=t[1],e.Y=t[2];break;case"A":t=["C"][P](Ut[z](0,[e.x,e.y][P](t.slice(1))));break;case"S":"C"==r||"S"==r?(i=2*e.x-e.bx,n=2*e.y-e.by):(i=e.x,n=e.y),t=["C",i,n][P](t.slice(1));break;case"T":"Q"==r||"T"==r?(e.qx=2*e.x-e.qx,e.qy=2*e.y-e.qy):(e.qx=e.x,e.qy=e.y),t=["C"][P](Xt(e.x,e.y,e.qx,e.qy,t[1],t[2]));break;case"Q":e.qx=t[1],e.qy=t[2],t=["C"][P](Xt(e.x,e.y,t[1],t[2],t[3],t[4]));break;case"L":t=["C"][P](Ht(e.x,e.y,t[1],t[2]));break;case"H":t=["C"][P](Ht(e.x,e.y,t[1],e.y));break;case"V":t=["C"][P](Ht(e.x,e.y,e.x,t[1]));break;case"Z":t=["C"][P](Ht(e.x,e.y,e.X,e.Y))}return t}),l=function(t,e){if(t[e].length>7){t[e].shift();for(var r=t[e];r.length;)u[e]="A",n&&(c[e]="A"),t.splice(e++,0,["C"][P](r.splice(0,6)));t.splice(e,1),g=W(i.length,n&&n.length||0)}},h=function(t,e,r,a,s){t&&e&&"M"==t[s][0]&&"M"!=e[s][0]&&(e.splice(s,0,["M",a.x,a.y]),r.bx=0,r.by=0,r.x=t[s][1],r.y=t[s][2],g=W(i.length,n&&n.length||0))},u=[],c=[],f="",p="",d=0,g=W(i.length,n&&n.length||0);d<g;d++){i[d]&&(f=i[d][0]),"C"!=f&&(u[d]=f,d&&(p=u[d-1])),i[d]=o(i[d],a,p),"A"!=u[d]&&"C"==f&&(u[d]="C"),l(i,d),n&&(n[d]&&(f=n[d][0]),"C"!=f&&(c[d]=f,d&&(p=c[d-1])),n[d]=o(n[d],s,p),"A"!=c[d]&&"C"==f&&(c[d]="C"),l(n,d)),h(i,n,a,s,d),h(n,i,s,a,d);var v=i[d],x=n&&n[d],y=v.length,m=n&&x.length;a.x=v[y-2],a.y=v[y-1],a.bx=ht(v[y-4])||a.x,a.by=ht(v[y-3])||a.y,s.bx=n&&(ht(x[m-4])||s.x),s.by=n&&(ht(x[m-3])||s.y),s.x=n&&x[m-2],s.y=n&&x[m-1]}return n||(r.curve=Yt(i)),n?[i,n]:i},null,Yt),Jt=e._parseDots=n(function(t){for(var r=[],i=0,n=t.length;i<n;i++){var a={},s=t[i].match(/^([^:]*):?([\d\.]*)/);if(a.color=e.getRGB(s[1]),a.color.error)return null;a.opacity=a.color.opacity,a.color=a.color.hex,s[2]&&(a.offset=s[2]+"%"),r.push(a)}for(i=1,n=r.length-1;i<n;i++)if(!r[i].offset){for(var o=ht(r[i-1].offset||0),l=0,h=i+1;h<n;h++)if(r[h].offset){l=r[h].offset;break}l||(l=100,h=n),l=ht(l);for(var u=(l-o)/(h-i+1);i<h;i++)o+=u,r[i].offset=o+"%"}return r}),Kt=e._tear=function(t,e){t==e.top&&(e.top=t.prev),t==e.bottom&&(e.bottom=t.next),t.next&&(t.next.prev=t.prev),t.prev&&(t.prev.next=t.next)},te=e._tofront=function(t,e){e.top!==t&&(Kt(t,e),t.next=null,t.prev=e.top,e.top.next=t,e.top=t)},ee=e._toback=function(t,e){e.bottom!==t&&(Kt(t,e),t.next=e.bottom,t.prev=null,e.bottom.prev=t,e.bottom=t)},re=e._insertafter=function(t,e,r){Kt(t,r),e==r.top&&(r.top=t),e.next&&(e.next.prev=t),t.next=e.next,t.prev=e,e.next=t},ie=e._insertbefore=function(t,e,r){Kt(t,r),e==r.bottom&&(r.bottom=t),e.prev&&(e.prev.next=t),t.prev=e.prev,e.prev=t,t.next=e},ne=e.toMatrix=function(t,e){var r=Ot(t),i={_:{transform:R},getBBox:function(){return r}};return se(i,e),i.matrix},ae=e.transformPath=function(t,e){return Mt(t,ne(t,e))},se=e._extractTransform=function(t,r){if(null==r)return t._.transform;r=I(r).replace(/\.{3}|\u2026/g,t._.transform||R);var i=e.parseTransformString(r),n=0,a=0,s=0,o=1,l=1,h=t._,u=new g;if(h.transform=i||[],i)for(var c=0,f=i.length;c<f;c++){var p=i[c],d=p.length,v=I(p[0]).toLowerCase(),x=p[0]!=v,y=x?u.invert():0,m,b,_,w,k;"t"==v&&3==d?x?(m=y.x(0,0),b=y.y(0,0),_=y.x(p[1],p[2]),w=y.y(p[1],p[2]),u.translate(_-m,w-b)):u.translate(p[1],p[2]):"r"==v?2==d?(k=k||t.getBBox(1),u.rotate(p[1],k.x+k.width/2,k.y+k.height/2),n+=p[1]):4==d&&(x?(_=y.x(p[2],p[3]),w=y.y(p[2],p[3]),u.rotate(p[1],_,w)):u.rotate(p[1],p[2],p[3]),n+=p[1]):"s"==v?2==d||3==d?(k=k||t.getBBox(1),u.scale(p[1],p[d-1],k.x+k.width/2,k.y+k.height/2),o*=p[1],l*=p[d-1]):5==d&&(x?(_=y.x(p[3],p[4]),w=y.y(p[3],p[4]),u.scale(p[1],p[2],_,w)):u.scale(p[1],p[2],p[3],p[4]),o*=p[1],l*=p[2]):"m"==v&&7==d&&u.add(p[1],p[2],p[3],p[4],p[5],p[6]),h.dirtyT=1,t.matrix=u}t.matrix=u,h.sx=o,h.sy=l,h.deg=n,h.dx=a=u.e,h.dy=s=u.f,1==o&&1==l&&!n&&h.bbox?(h.bbox.x+=+a,h.bbox.y+=+s):h.dirtyT=1},oe=function(t){var e=t[0];switch(e.toLowerCase()){case"t":return[e,0,0];case"m":return[e,1,0,0,1,0,0];case"r":return 4==t.length?[e,0,t[2],t[3]]:[e,0];case"s":return 5==t.length?[e,1,1,t[3],t[4]]:3==t.length?[e,1,1]:[e,1]}},le=e._equaliseTransform=function(t,r){r=I(r).replace(/\.{3}|\u2026/g,t),t=e.parseTransformString(t)||[],r=e.parseTransformString(r)||[];for(var i=W(t.length,r.length),n=[],a=[],s=0,o,l,h,u;s<i;s++){if(h=t[s]||oe(r[s]),u=r[s]||oe(h),h[0]!=u[0]||"r"==h[0].toLowerCase()&&(h[2]!=u[2]||h[3]!=u[3])||"s"==h[0].toLowerCase()&&(h[3]!=u[3]||h[4]!=u[4]))return;for(n[s]=[],a[s]=[],o=0,l=W(h.length,u.length);o<l;o++)o in h&&(n[s][o]=h[o]),o in u&&(a[s][o]=u[o])}return{from:n,to:a}};e._getContainer=function(t,r,i,n){var a;if(a=null!=n||e.is(t,"object")?t:T.doc.getElementById(t),null!=a)return a.tagName?null==r?{container:a,width:a.style.pixelWidth||a.offsetWidth,height:a.style.pixelHeight||a.offsetHeight}:{container:a,width:r,height:i}:{container:1,x:t,y:r,width:i,height:n}},e.pathToRelative=Wt,e._engine={},e.path2curve=Qt,e.matrix=function(t,e,r,i,n,a){return new g(t,e,r,i,n,a)},function(t){function r(t){return t[0]*t[0]+t[1]*t[1]}function i(t){var e=Y.sqrt(r(t));t[0]&&(t[0]/=e),t[1]&&(t[1]/=e)}t.add=function(t,e,r,i,n,a){var s=[[],[],[]],o=[[this.a,this.c,this.e],[this.b,this.d,this.f],[0,0,1]],l=[[t,r,n],[e,i,a],[0,0,1]],h,u,c,f;for(t&&t instanceof g&&(l=[[t.a,t.c,t.e],[t.b,t.d,t.f],[0,0,1]]),h=0;h<3;h++)for(u=0;u<3;u++){for(f=0,c=0;c<3;c++)f+=o[h][c]*l[c][u];s[h][u]=f}this.a=s[0][0],this.b=s[1][0],this.c=s[0][1],this.d=s[1][1],this.e=s[0][2],this.f=s[1][2]},t.invert=function(){var t=this,e=t.a*t.d-t.b*t.c;return new g(t.d/e,-t.b/e,-t.c/e,t.a/e,(t.c*t.f-t.d*t.e)/e,(t.b*t.e-t.a*t.f)/e)},t.clone=function(){return new g(this.a,this.b,this.c,this.d,this.e,this.f)},t.translate=function(t,e){
this.add(1,0,0,1,t,e)},t.scale=function(t,e,r,i){null==e&&(e=t),(r||i)&&this.add(1,0,0,1,r,i),this.add(t,0,0,e,0,0),(r||i)&&this.add(1,0,0,1,-r,-i)},t.rotate=function(t,r,i){t=e.rad(t),r=r||0,i=i||0;var n=+Y.cos(t).toFixed(9),a=+Y.sin(t).toFixed(9);this.add(n,a,-a,n,r,i),this.add(1,0,0,1,-r,-i)},t.x=function(t,e){return t*this.a+e*this.c+this.e},t.y=function(t,e){return t*this.b+e*this.d+this.f},t.get=function(t){return+this[I.fromCharCode(97+t)].toFixed(4)},t.toString=function(){return e.svg?"matrix("+[this.get(0),this.get(1),this.get(2),this.get(3),this.get(4),this.get(5)].join()+")":[this.get(0),this.get(2),this.get(1),this.get(3),0,0].join()},t.toFilter=function(){return"progid:DXImageTransform.Microsoft.Matrix(M11="+this.get(0)+", M12="+this.get(2)+", M21="+this.get(1)+", M22="+this.get(3)+", Dx="+this.get(4)+", Dy="+this.get(5)+", sizingmethod='auto expand')"},t.offset=function(){return[this.e.toFixed(4),this.f.toFixed(4)]},t.split=function(){var t={};t.dx=this.e,t.dy=this.f;var n=[[this.a,this.c],[this.b,this.d]];t.scalex=Y.sqrt(r(n[0])),i(n[0]),t.shear=n[0][0]*n[1][0]+n[0][1]*n[1][1],n[1]=[n[1][0]-n[0][0]*t.shear,n[1][1]-n[0][1]*t.shear],t.scaley=Y.sqrt(r(n[1])),i(n[1]),t.shear/=t.scaley;var a=-n[0][1],s=n[1][1];return s<0?(t.rotate=e.deg(Y.acos(s)),a<0&&(t.rotate=360-t.rotate)):t.rotate=e.deg(Y.asin(a)),t.isSimple=!(+t.shear.toFixed(9)||t.scalex.toFixed(9)!=t.scaley.toFixed(9)&&t.rotate),t.isSuperSimple=!+t.shear.toFixed(9)&&t.scalex.toFixed(9)==t.scaley.toFixed(9)&&!t.rotate,t.noRotation=!+t.shear.toFixed(9)&&!t.rotate,t},t.toTransformString=function(t){var e=t||this[q]();return e.isSimple?(e.scalex=+e.scalex.toFixed(4),e.scaley=+e.scaley.toFixed(4),e.rotate=+e.rotate.toFixed(4),(e.dx||e.dy?"t"+[e.dx,e.dy]:R)+(1!=e.scalex||1!=e.scaley?"s"+[e.scalex,e.scaley,0,0]:R)+(e.rotate?"r"+[e.rotate,0,0]:R)):"m"+[this.get(0),this.get(1),this.get(2),this.get(3),this.get(4),this.get(5)]}}(g.prototype);for(var he=function(){this.returnValue=!1},ue=function(){return this.originalEvent.preventDefault()},ce=function(){this.cancelBubble=!0},fe=function(){return this.originalEvent.stopPropagation()},pe=function(t){var e=T.doc.documentElement.scrollTop||T.doc.body.scrollTop,r=T.doc.documentElement.scrollLeft||T.doc.body.scrollLeft;return{x:t.clientX+r,y:t.clientY+e}},de=function(){return T.doc.addEventListener?function(t,e,r,i){var n=function(t){var e=pe(t);return r.call(i,t,e.x,e.y)};if(t.addEventListener(e,n,!1),F&&V[e]){var a=function(e){for(var n=pe(e),a=e,s=0,o=e.targetTouches&&e.targetTouches.length;s<o;s++)if(e.targetTouches[s].target==t){e=e.targetTouches[s],e.originalEvent=a,e.preventDefault=ue,e.stopPropagation=fe;break}return r.call(i,e,n.x,n.y)};t.addEventListener(V[e],a,!1)}return function(){return t.removeEventListener(e,n,!1),F&&V[e]&&t.removeEventListener(V[e],a,!1),!0}}:T.doc.attachEvent?function(t,e,r,i){var n=function(t){t=t||T.win.event;var e=T.doc.documentElement.scrollTop||T.doc.body.scrollTop,n=T.doc.documentElement.scrollLeft||T.doc.body.scrollLeft,a=t.clientX+n,s=t.clientY+e;return t.preventDefault=t.preventDefault||he,t.stopPropagation=t.stopPropagation||ce,r.call(i,t,a,s)};t.attachEvent("on"+e,n);var a=function(){return t.detachEvent("on"+e,n),!0};return a}:void 0}(),ge=[],ve=function(e){for(var r=e.clientX,i=e.clientY,n=T.doc.documentElement.scrollTop||T.doc.body.scrollTop,a=T.doc.documentElement.scrollLeft||T.doc.body.scrollLeft,s,o=ge.length;o--;){if(s=ge[o],F&&e.touches){for(var l=e.touches.length,h;l--;)if(h=e.touches[l],h.identifier==s.el._drag.id){r=h.clientX,i=h.clientY,(e.originalEvent?e.originalEvent:e).preventDefault();break}}else e.preventDefault();var u=s.el.node,c,f=u.nextSibling,p=u.parentNode,d=u.style.display;T.win.opera&&p.removeChild(u),u.style.display="none",c=s.el.paper.getElementByPoint(r,i),u.style.display=d,T.win.opera&&(f?p.insertBefore(u,f):p.appendChild(u)),c&&t("raphael.drag.over."+s.el.id,s.el,c),r+=a,i+=n,t("raphael.drag.move."+s.el.id,s.move_scope||s.el,r-s.el._drag.x,i-s.el._drag.y,r,i,e)}},xe=function(r){e.unmousemove(ve).unmouseup(xe);for(var i=ge.length,n;i--;)n=ge[i],n.el._drag={},t("raphael.drag.end."+n.el.id,n.end_scope||n.start_scope||n.move_scope||n.el,r);ge=[]},ye=e.el={},me=D.length;me--;)!function(t){e[t]=ye[t]=function(r,i){return e.is(r,"function")&&(this.events=this.events||[],this.events.push({name:t,f:r,unbind:de(this.shape||this.node||T.doc,t,r,i||this)})),this},e["un"+t]=ye["un"+t]=function(r){for(var i=this.events||[],n=i.length;n--;)i[n].name!=t||!e.is(r,"undefined")&&i[n].f!=r||(i[n].unbind(),i.splice(n,1),!i.length&&delete this.events);return this}}(D[me]);ye.data=function(r,i){var n=wt[this.id]=wt[this.id]||{};if(0==arguments.length)return n;if(1==arguments.length){if(e.is(r,"object")){for(var a in r)r[A](a)&&this.data(a,r[a]);return this}return t("raphael.data.get."+this.id,this,n[r],r),n[r]}return n[r]=i,t("raphael.data.set."+this.id,this,i,r),this},ye.removeData=function(t){return null==t?wt[this.id]={}:wt[this.id]&&delete wt[this.id][t],this},ye.getData=function(){return r(wt[this.id]||{})},ye.hover=function(t,e,r,i){return this.mouseover(t,r).mouseout(e,i||r)},ye.unhover=function(t,e){return this.unmouseover(t).unmouseout(e)};var be=[];ye.drag=function(r,i,n,a,s,o){function l(l){(l.originalEvent||l).preventDefault();var h=l.clientX,u=l.clientY,c=T.doc.documentElement.scrollTop||T.doc.body.scrollTop,f=T.doc.documentElement.scrollLeft||T.doc.body.scrollLeft;if(this._drag.id=l.identifier,F&&l.touches)for(var p=l.touches.length,d;p--;)if(d=l.touches[p],this._drag.id=d.identifier,d.identifier==this._drag.id){h=d.clientX,u=d.clientY;break}this._drag.x=h+f,this._drag.y=u+c,!ge.length&&e.mousemove(ve).mouseup(xe),ge.push({el:this,move_scope:a,start_scope:s,end_scope:o}),i&&t.on("raphael.drag.start."+this.id,i),r&&t.on("raphael.drag.move."+this.id,r),n&&t.on("raphael.drag.end."+this.id,n),t("raphael.drag.start."+this.id,s||a||this,l.clientX+f,l.clientY+c,l)}return this._drag={},be.push({el:this,start:l}),this.mousedown(l),this},ye.onDragOver=function(e){e?t.on("raphael.drag.over."+this.id,e):t.unbind("raphael.drag.over."+this.id)},ye.undrag=function(){for(var r=be.length;r--;)be[r].el==this&&(this.unmousedown(be[r].start),be.splice(r,1),t.unbind("raphael.drag.*."+this.id));!be.length&&e.unmousemove(ve).unmouseup(xe),ge=[]},N.circle=function(t,r,i){var n=e._engine.circle(this,t||0,r||0,i||0);return this.__set__&&this.__set__.push(n),n},N.rect=function(t,r,i,n,a){var s=e._engine.rect(this,t||0,r||0,i||0,n||0,a||0);return this.__set__&&this.__set__.push(s),s},N.ellipse=function(t,r,i,n){var a=e._engine.ellipse(this,t||0,r||0,i||0,n||0);return this.__set__&&this.__set__.push(a),a},N.path=function(t){t&&!e.is(t,Z)&&!e.is(t[0],Q)&&(t+=R);var r=e._engine.path(e.format[z](e,arguments),this);return this.__set__&&this.__set__.push(r),r},N.image=function(t,r,i,n,a){var s=e._engine.image(this,t||"about:blank",r||0,i||0,n||0,a||0);return this.__set__&&this.__set__.push(s),s},N.text=function(t,r,i){var n=e._engine.text(this,t||0,r||0,I(i));return this.__set__&&this.__set__.push(n),n},N.set=function(t){!e.is(t,"array")&&(t=Array.prototype.splice.call(arguments,0,arguments.length));var r=new ze(t);return this.__set__&&this.__set__.push(r),r.paper=this,r.type="set",r},N.setStart=function(t){this.__set__=t||this.set()},N.setFinish=function(t){var e=this.__set__;return delete this.__set__,e},N.getSize=function(){var t=this.canvas.parentNode;return{width:t.offsetWidth,height:t.offsetHeight}},N.setSize=function(t,r){return e._engine.setSize.call(this,t,r)},N.setViewBox=function(t,r,i,n,a){return e._engine.setViewBox.call(this,t,r,i,n,a)},N.top=N.bottom=null,N.raphael=e;var _e=function(t){var e=t.getBoundingClientRect(),r=t.ownerDocument,i=r.body,n=r.documentElement,a=n.clientTop||i.clientTop||0,s=n.clientLeft||i.clientLeft||0,o=e.top+(T.win.pageYOffset||n.scrollTop||i.scrollTop)-a,l=e.left+(T.win.pageXOffset||n.scrollLeft||i.scrollLeft)-s;return{y:o,x:l}};N.getElementByPoint=function(t,e){var r=this,i=r.canvas,n=T.doc.elementFromPoint(t,e);if(T.win.opera&&"svg"==n.tagName){var a=_e(i),s=i.createSVGRect();s.x=t-a.x,s.y=e-a.y,s.width=s.height=1;var o=i.getIntersectionList(s,null);o.length&&(n=o[o.length-1])}if(!n)return null;for(;n.parentNode&&n!=i.parentNode&&!n.raphael;)n=n.parentNode;return n==r.canvas.parentNode&&(n=i),n=n&&n.raphael?r.getById(n.raphaelid):null},N.getElementsByBBox=function(t){var r=this.set();return this.forEach(function(i){e.isBBoxIntersect(i.getBBox(),t)&&r.push(i)}),r},N.getById=function(t){for(var e=this.bottom;e;){if(e.id==t)return e;e=e.next}return null},N.forEach=function(t,e){for(var r=this.bottom;r;){if(t.call(e,r)===!1)return this;r=r.next}return this},N.getElementsByPoint=function(t,e){var r=this.set();return this.forEach(function(i){i.isPointInside(t,e)&&r.push(i)}),r},ye.isPointInside=function(t,r){var i=this.realPath=Et[this.type](this);return this.attr("transform")&&this.attr("transform").length&&(i=e.transformPath(i,this.attr("transform"))),e.isPointInsidePath(i,t,r)},ye.getBBox=function(t){if(this.removed)return{};var e=this._;return t?(!e.dirty&&e.bboxwt||(this.realPath=Et[this.type](this),e.bboxwt=Ot(this.realPath),e.bboxwt.toString=x,e.dirty=0),e.bboxwt):((e.dirty||e.dirtyT||!e.bbox)&&(!e.dirty&&this.realPath||(e.bboxwt=0,this.realPath=Et[this.type](this)),e.bbox=Ot(Mt(this.realPath,this.matrix)),e.bbox.toString=x,e.dirty=e.dirtyT=0),e.bbox)},ye.clone=function(){if(this.removed)return null;var t=this.paper[this.type]().attr(this.attr());return this.__set__&&this.__set__.push(t),t},ye.glow=function(t){if("text"==this.type)return null;t=t||{};var e={width:(t.width||10)+(+this.attr("stroke-width")||1),fill:t.fill||!1,opacity:null==t.opacity?.5:t.opacity,offsetx:t.offsetx||0,offsety:t.offsety||0,color:t.color||"#000"},r=e.width/2,i=this.paper,n=i.set(),a=this.realPath||Et[this.type](this);a=this.matrix?Mt(a,this.matrix):a;for(var s=1;s<r+1;s++)n.push(i.path(a).attr({stroke:e.color,fill:e.fill?e.color:"none","stroke-linejoin":"round","stroke-linecap":"round","stroke-width":+(e.width/r*s).toFixed(3),opacity:+(e.opacity/r).toFixed(3)}));return n.insertBefore(this).translate(e.offsetx,e.offsety)};var we={},ke=function(t,r,i,n,a,s,o,u,c){return null==c?l(t,r,i,n,a,s,o,u):e.findDotsAtSegment(t,r,i,n,a,s,o,u,h(t,r,i,n,a,s,o,u,c))},Be=function(t,r){return function(i,n,a){i=Qt(i);for(var s,o,l,h,u="",c={},f,p=0,d=0,g=i.length;d<g;d++){if(l=i[d],"M"==l[0])s=+l[1],o=+l[2];else{if(h=ke(s,o,l[1],l[2],l[3],l[4],l[5],l[6]),p+h>n){if(r&&!c.start){if(f=ke(s,o,l[1],l[2],l[3],l[4],l[5],l[6],n-p),u+=["C"+f.start.x,f.start.y,f.m.x,f.m.y,f.x,f.y],a)return u;c.start=u,u=["M"+f.x,f.y+"C"+f.n.x,f.n.y,f.end.x,f.end.y,l[5],l[6]].join(),p+=h,s=+l[5],o=+l[6];continue}if(!t&&!r)return f=ke(s,o,l[1],l[2],l[3],l[4],l[5],l[6],n-p),{x:f.x,y:f.y,alpha:f.alpha}}p+=h,s=+l[5],o=+l[6]}u+=l.shift()+l}return c.end=u,f=t?p:r?c:e.findDotsAtSegment(s,o,l[0],l[1],l[2],l[3],l[4],l[5],1),f.alpha&&(f={x:f.x,y:f.y,alpha:f.alpha}),f}},Ce=Be(1),Se=Be(),Ae=Be(0,1);e.getTotalLength=Ce,e.getPointAtLength=Se,e.getSubpath=function(t,e,r){if(this.getTotalLength(t)-r<1e-6)return Ae(t,e).end;var i=Ae(t,r,1);return e?Ae(i,e).end:i},ye.getTotalLength=function(){var t=this.getPath();if(t)return this.node.getTotalLength?this.node.getTotalLength():Ce(t)},ye.getPointAtLength=function(t){var e=this.getPath();if(e)return Se(e,t)},ye.getPath=function(){var t,r=e._getPath[this.type];if("text"!=this.type&&"set"!=this.type)return r&&(t=r(this)),t},ye.getSubpath=function(t,r){var i=this.getPath();if(i)return e.getSubpath(i,t,r)};var Te=e.easing_formulas={linear:function(t){return t},"<":function(t){return X(t,1.7)},">":function(t){return X(t,.48)},"<>":function(t){var e=.48-t/1.04,r=Y.sqrt(.1734+e*e),i=r-e,n=X(H(i),1/3)*(i<0?-1:1),a=-r-e,s=X(H(a),1/3)*(a<0?-1:1),o=n+s+.5;return 3*(1-o)*o*o+o*o*o},backIn:function(t){var e=1.70158;return t*t*((e+1)*t-e)},backOut:function(t){t-=1;var e=1.70158;return t*t*((e+1)*t+e)+1},elastic:function(t){return t==!!t?t:X(2,-10*t)*Y.sin((t-.075)*(2*U)/.3)+1},bounce:function(t){var e=7.5625,r=2.75,i;return t<1/r?i=e*t*t:t<2/r?(t-=1.5/r,i=e*t*t+.75):t<2.5/r?(t-=2.25/r,i=e*t*t+.9375):(t-=2.625/r,i=e*t*t+.984375),i}};Te.easeIn=Te["ease-in"]=Te["<"],Te.easeOut=Te["ease-out"]=Te[">"],Te.easeInOut=Te["ease-in-out"]=Te["<>"],Te["back-in"]=Te.backIn,Te["back-out"]=Te.backOut;var Ee=[],Me=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||function(t){setTimeout(t,16)},Ne=function(){for(var r=+new Date,i=0;i<Ee.length;i++){var n=Ee[i];if(!n.el.removed&&!n.paused){var a=r-n.start,s=n.ms,o=n.easing,l=n.from,h=n.diff,u=n.to,c=n.t,f=n.el,p={},d,g={},v;if(n.initstatus?(a=(n.initstatus*n.anim.top-n.prev)/(n.percent-n.prev)*s,n.status=n.initstatus,delete n.initstatus,n.stop&&Ee.splice(i--,1)):n.status=(n.prev+(n.percent-n.prev)*(a/s))/n.anim.top,!(a<0))if(a<s){var x=o(a/s);for(var y in l)if(l[A](y)){switch(pt[y]){case $:d=+l[y]+x*s*h[y];break;case"colour":d="rgb("+[Le(ot(l[y].r+x*s*h[y].r)),Le(ot(l[y].g+x*s*h[y].g)),Le(ot(l[y].b+x*s*h[y].b))].join(",")+")";break;case"path":d=[];for(var m=0,_=l[y].length;m<_;m++){d[m]=[l[y][m][0]];for(var w=1,k=l[y][m].length;w<k;w++)d[m][w]=+l[y][m][w]+x*s*h[y][m][w];d[m]=d[m].join(j)}d=d.join(j);break;case"transform":if(h[y].real)for(d=[],m=0,_=l[y].length;m<_;m++)for(d[m]=[l[y][m][0]],w=1,k=l[y][m].length;w<k;w++)d[m][w]=l[y][m][w]+x*s*h[y][m][w];else{var B=function(t){return+l[y][t]+x*s*h[y][t]};d=[["m",B(0),B(1),B(2),B(3),B(4),B(5)]]}break;case"csv":if("clip-rect"==y)for(d=[],m=4;m--;)d[m]=+l[y][m]+x*s*h[y][m];break;default:var C=[][P](l[y]);for(d=[],m=f.paper.customAttributes[y].length;m--;)d[m]=+C[m]+x*s*h[y][m]}p[y]=d}f.attr(p),function(e,r,i){setTimeout(function(){t("raphael.anim.frame."+e,r,i)})}(f.id,f,n.anim)}else{if(function(r,i,n){setTimeout(function(){t("raphael.anim.frame."+i.id,i,n),t("raphael.anim.finish."+i.id,i,n),e.is(r,"function")&&r.call(i)})}(n.callback,f,n.anim),f.attr(u),Ee.splice(i--,1),n.repeat>1&&!n.next){for(v in u)u[A](v)&&(g[v]=n.totalOrigin[v]);n.el.attr(g),b(n.anim,n.el,n.anim.percents[0],null,n.totalOrigin,n.repeat-1)}n.next&&!n.stop&&b(n.anim,n.el,n.next,null,n.totalOrigin,n.repeat)}}}Ee.length&&Me(Ne)},Le=function(t){return t>255?255:t<0?0:t};ye.animateWith=function(t,r,i,n,a,s){var o=this;if(o.removed)return s&&s.call(o),o;var l=i instanceof m?i:e.animation(i,n,a,s),h,u;b(l,o,l.percents[0],null,o.attr());for(var c=0,f=Ee.length;c<f;c++)if(Ee[c].anim==r&&Ee[c].el==t){Ee[f-1].start=Ee[c].start;break}return o},ye.onAnimation=function(e){return e?t.on("raphael.anim.frame."+this.id,e):t.unbind("raphael.anim.frame."+this.id),this},m.prototype.delay=function(t){var e=new m(this.anim,this.ms);return e.times=this.times,e.del=+t||0,e},m.prototype.repeat=function(t){var e=new m(this.anim,this.ms);return e.del=this.del,e.times=Y.floor(W(t,0))||1,e},e.animation=function(t,r,i,n){if(t instanceof m)return t;!e.is(i,"function")&&i||(n=n||i||null,i=null),t=Object(t),r=+r||0;var a={},s,o;for(o in t)t[A](o)&&ht(o)!=o&&ht(o)+"%"!=o&&(s=!0,a[o]=t[o]);if(s)return i&&(a.easing=i),n&&(a.callback=n),new m({100:a},r);if(n){var l=0;for(var h in t){var u=ut(h);t[A](h)&&u>l&&(l=u)}l+="%",!t[l].callback&&(t[l].callback=n)}return new m(t,r)},ye.animate=function(t,r,i,n){var a=this;if(a.removed)return n&&n.call(a),a;var s=t instanceof m?t:e.animation(t,r,i,n);return b(s,a,s.percents[0],null,a.attr()),a},ye.setTime=function(t,e){return t&&null!=e&&this.status(t,G(e,t.ms)/t.ms),this},ye.status=function(t,e){var r=[],i=0,n,a;if(null!=e)return b(t,this,-1,G(e,1)),this;for(n=Ee.length;i<n;i++)if(a=Ee[i],a.el.id==this.id&&(!t||a.anim==t)){if(t)return a.status;r.push({anim:a.anim,status:a.status})}return t?0:r},ye.pause=function(e){for(var r=0;r<Ee.length;r++)Ee[r].el.id!=this.id||e&&Ee[r].anim!=e||t("raphael.anim.pause."+this.id,this,Ee[r].anim)!==!1&&(Ee[r].paused=!0);return this},ye.resume=function(e){for(var r=0;r<Ee.length;r++)if(Ee[r].el.id==this.id&&(!e||Ee[r].anim==e)){var i=Ee[r];t("raphael.anim.resume."+this.id,this,i.anim)!==!1&&(delete i.paused,this.status(i.anim,i.status))}return this},ye.stop=function(e){for(var r=0;r<Ee.length;r++)Ee[r].el.id!=this.id||e&&Ee[r].anim!=e||t("raphael.anim.stop."+this.id,this,Ee[r].anim)!==!1&&Ee.splice(r--,1);return this},t.on("raphael.remove",_),t.on("raphael.clear",_),ye.toString=function(){return"Raphaël’s object"};var ze=function(t){if(this.items=[],this.length=0,this.type="set",t)for(var e=0,r=t.length;e<r;e++)!t[e]||t[e].constructor!=ye.constructor&&t[e].constructor!=ze||(this[this.items.length]=this.items[this.items.length]=t[e],this.length++)},Pe=ze.prototype;Pe.push=function(){for(var t,e,r=0,i=arguments.length;r<i;r++)t=arguments[r],!t||t.constructor!=ye.constructor&&t.constructor!=ze||(e=this.items.length,this[e]=this.items[e]=t,this.length++);return this},Pe.pop=function(){return this.length&&delete this[this.length--],this.items.pop()},Pe.forEach=function(t,e){for(var r=0,i=this.items.length;r<i;r++)if(t.call(e,this.items[r],r)===!1)return this;return this};for(var Fe in ye)ye[A](Fe)&&(Pe[Fe]=function(t){return function(){var e=arguments;return this.forEach(function(r){r[t][z](r,e)})}}(Fe));return Pe.attr=function(t,r){if(t&&e.is(t,Q)&&e.is(t[0],"object"))for(var i=0,n=t.length;i<n;i++)this.items[i].attr(t[i]);else for(var a=0,s=this.items.length;a<s;a++)this.items[a].attr(t,r);return this},Pe.clear=function(){for(;this.length;)this.pop()},Pe.splice=function(t,e,r){t=t<0?W(this.length+t,0):t,e=W(0,G(this.length-t,e));var i=[],n=[],a=[],s;for(s=2;s<arguments.length;s++)a.push(arguments[s]);for(s=0;s<e;s++)n.push(this[t+s]);for(;s<this.length-t;s++)i.push(this[t+s]);var o=a.length;for(s=0;s<o+i.length;s++)this.items[t+s]=this[t+s]=s<o?a[s]:i[s-o];for(s=this.items.length=this.length-=e-o;this[s];)delete this[s++];return new ze(n)},Pe.exclude=function(t){for(var e=0,r=this.length;e<r;e++)if(this[e]==t)return this.splice(e,1),!0},Pe.animate=function(t,r,i,n){(e.is(i,"function")||!i)&&(n=i||null);var a=this.items.length,s=a,o,l=this,h;if(!a)return this;n&&(h=function(){!--a&&n.call(l)}),i=e.is(i,Z)?i:h;var u=e.animation(t,r,i,h);for(o=this.items[--s].animate(u);s--;)this.items[s]&&!this.items[s].removed&&this.items[s].animateWith(o,u,u),this.items[s]&&!this.items[s].removed||a--;return this},Pe.insertAfter=function(t){for(var e=this.items.length;e--;)this.items[e].insertAfter(t);return this},Pe.getBBox=function(){for(var t=[],e=[],r=[],i=[],n=this.items.length;n--;)if(!this.items[n].removed){var a=this.items[n].getBBox();t.push(a.x),e.push(a.y),r.push(a.x+a.width),i.push(a.y+a.height)}return t=G[z](0,t),e=G[z](0,e),r=W[z](0,r),i=W[z](0,i),{x:t,y:e,x2:r,y2:i,width:r-t,height:i-e}},Pe.clone=function(t){t=this.paper.set();for(var e=0,r=this.items.length;e<r;e++)t.push(this.items[e].clone());return t},Pe.toString=function(){return"Raphaël‘s set"},Pe.glow=function(t){var e=this.paper.set();return this.forEach(function(r,i){var n=r.glow(t);null!=n&&n.forEach(function(t,r){e.push(t)})}),e},Pe.isPointInside=function(t,e){var r=!1;return this.forEach(function(i){if(i.isPointInside(t,e))return r=!0,!1}),r},e.registerFont=function(t){if(!t.face)return t;this.fonts=this.fonts||{};var e={w:t.w,face:{},glyphs:{}},r=t.face["font-family"];for(var i in t.face)t.face[A](i)&&(e.face[i]=t.face[i]);if(this.fonts[r]?this.fonts[r].push(e):this.fonts[r]=[e],!t.svg){e.face["units-per-em"]=ut(t.face["units-per-em"],10);for(var n in t.glyphs)if(t.glyphs[A](n)){var a=t.glyphs[n];if(e.glyphs[n]={w:a.w,k:{},d:a.d&&"M"+a.d.replace(/[mlcxtrv]/g,function(t){return{l:"L",c:"C",x:"z",t:"m",r:"l",v:"c"}[t]||"M"})+"z"},a.k)for(var s in a.k)a[A](s)&&(e.glyphs[n].k[s]=a.k[s])}}return t},N.getFont=function(t,r,i,n){if(n=n||"normal",i=i||"normal",r=+r||{normal:400,bold:700,lighter:300,bolder:800}[r]||400,e.fonts){var a=e.fonts[t];if(!a){var s=new RegExp("(^|\\s)"+t.replace(/[^\w\d\s+!~.:_-]/g,R)+"(\\s|$)","i");for(var o in e.fonts)if(e.fonts[A](o)&&s.test(o)){a=e.fonts[o];break}}var l;if(a)for(var h=0,u=a.length;h<u&&(l=a[h],l.face["font-weight"]!=r||l.face["font-style"]!=i&&l.face["font-style"]||l.face["font-stretch"]!=n);h++);return l}},N.print=function(t,r,i,n,a,s,o,l){s=s||"middle",o=W(G(o||0,1),-1),l=W(G(l||1,3),1);var h=I(i)[q](R),u=0,c=0,f=R,p;if(e.is(n,"string")&&(n=this.getFont(n)),n){p=(a||16)/n.face["units-per-em"];for(var d=n.face.bbox[q](k),g=+d[0],v=d[3]-d[1],x=0,y=+d[1]+("baseline"==s?v+ +n.face.descent:v/2),m=0,b=h.length;m<b;m++){if("\n"==h[m])u=0,w=0,c=0,x+=v*l;else{var _=c&&n.glyphs[h[m-1]]||{},w=n.glyphs[h[m]];u+=c?(_.w||n.w)+(_.k&&_.k[h[m]]||0)+n.w*o:0,c=1}w&&w.d&&(f+=e.transformPath(w.d,["t",u*p,x*p,"s",p,p,g,y,"t",(t-g)/p,(r-y)/p]))}}return this.path(f).attr({fill:"#000",stroke:"none"})},N.add=function(t){if(e.is(t,"array"))for(var r=this.set(),i=0,n=t.length,a;i<n;i++)a=t[i]||{},B[A](a.type)&&r.push(this[a.type]().attr(a));return r},e.format=function(t,r){var i=e.is(r,Q)?[0][P](r):arguments;return t&&e.is(t,Z)&&i.length-1&&(t=t.replace(C,function(t,e){return null==i[++e]?R:i[e]})),t||R},e.fullfill=function(){var t=/\{([^\}]+)\}/g,e=/(?:(?:^|\.)(.+?)(?=\[|\.|$|\()|\[('|")(.+?)\2\])(\(\))?/g,r=function(t,r,i){var n=i;return r.replace(e,function(t,e,r,i,a){e=e||i,n&&(e in n&&(n=n[e]),"function"==typeof n&&a&&(n=n()))}),n=(null==n||n==i?t:n)+""};return function(e,i){return String(e).replace(t,function(t,e){return r(t,e,i)})}}(),e.ninja=function(){if(E.was)T.win.Raphael=E.is;else{window.Raphael=void 0;try{delete window.Raphael}catch(t){}}return e},e.st=Pe,t.on("raphael.DOMload",function(){w=!0}),function(t,r,i){function n(){/in/.test(t.readyState)?setTimeout(n,9):e.eve("raphael.DOMload")}null==t.readyState&&t.addEventListener&&(t.addEventListener(r,i=function(){t.removeEventListener(r,i,!1),t.readyState="complete"},!1),t.readyState="loading"),n()}(document,"DOMContentLoaded"),e}.apply(e,i),!(void 0!==n&&(t.exports=n))},function(t,e,r){var i,n;!function(r){var a="0.5.0",s="hasOwnProperty",o=/[\.\/]/,l=/\s*,\s*/,h="*",u=function(){},c=function(t,e){return t-e},f,p,d={n:{}},g=function(){for(var t=0,e=this.length;t<e;t++)if("undefined"!=typeof this[t])return this[t]},v=function(){for(var t=this.length;--t;)if("undefined"!=typeof this[t])return this[t]},x=Object.prototype.toString,y=String,m=Array.isArray||function(t){return t instanceof Array||"[object Array]"==x.call(t)};eve=function(t,e){var r=d,i=p,n=Array.prototype.slice.call(arguments,2),a=eve.listeners(t),s=0,o=!1,l,h=[],u={},x=[],y=f,m=[];x.firstDefined=g,x.lastDefined=v,f=t,p=0;for(var b=0,_=a.length;b<_;b++)"zIndex"in a[b]&&(h.push(a[b].zIndex),a[b].zIndex<0&&(u[a[b].zIndex]=a[b]));for(h.sort(c);h[s]<0;)if(l=u[h[s++]],x.push(l.apply(e,n)),p)return p=i,x;for(b=0;b<_;b++)if(l=a[b],"zIndex"in l)if(l.zIndex==h[s]){if(x.push(l.apply(e,n)),p)break;do if(s++,l=u[h[s]],l&&x.push(l.apply(e,n)),p)break;while(l)}else u[l.zIndex]=l;else if(x.push(l.apply(e,n)),p)break;return p=i,f=y,x},eve._events=d,eve.listeners=function(t){var e=m(t)?t:t.split(o),r=d,i,n,a,s,l,u,c,f,p=[r],g=[];for(s=0,l=e.length;s<l;s++){for(f=[],u=0,c=p.length;u<c;u++)for(r=p[u].n,n=[r[e[s]],r[h]],a=2;a--;)i=n[a],i&&(f.push(i),g=g.concat(i.f||[]));p=f}return g},eve.separator=function(t){t?(t=y(t).replace(/(?=[\.\^\]\[\-])/g,"\\"),t="["+t+"]",o=new RegExp(t)):o=/[\.\/]/},eve.on=function(t,e){if("function"!=typeof e)return function(){};for(var r=m(t)?m(t[0])?t:[t]:y(t).split(l),i=0,n=r.length;i<n;i++)!function(t){for(var r=m(t)?t:y(t).split(o),i=d,n,a=0,s=r.length;a<s;a++)i=i.n,i=i.hasOwnProperty(r[a])&&i[r[a]]||(i[r[a]]={n:{}});for(i.f=i.f||[],a=0,s=i.f.length;a<s;a++)if(i.f[a]==e){n=!0;break}!n&&i.f.push(e)}(r[i]);return function(t){+t==+t&&(e.zIndex=+t)}},eve.f=function(t){var e=[].slice.call(arguments,1);return function(){eve.apply(null,[t,null].concat(e).concat([].slice.call(arguments,0)))}},eve.stop=function(){p=1},eve.nt=function(t){var e=m(f)?f.join("."):f;return t?new RegExp("(?:\\.|\\/|^)"+t+"(?:\\.|\\/|$)").test(e):e},eve.nts=function(){return m(f)?f:f.split(o)},eve.off=eve.unbind=function(t,e){if(!t)return void(eve._events=d={n:{}});var r=m(t)?m(t[0])?t:[t]:y(t).split(l);if(r.length>1)for(var i=0,n=r.length;i<n;i++)eve.off(r[i],e);else{r=m(t)?t:y(t).split(o);var a,u,c,i,n,f,p,g=[d];for(i=0,n=r.length;i<n;i++)for(f=0;f<g.length;f+=c.length-2){if(c=[f,1],a=g[f].n,r[i]!=h)a[r[i]]&&c.push(a[r[i]]);else for(u in a)a[s](u)&&c.push(a[u]);g.splice.apply(g,c)}for(i=0,n=g.length;i<n;i++)for(a=g[i];a.n;){if(e){if(a.f){for(f=0,p=a.f.length;f<p;f++)if(a.f[f]==e){a.f.splice(f,1);break}!a.f.length&&delete a.f}for(u in a.n)if(a.n[s](u)&&a.n[u].f){var v=a.n[u].f;for(f=0,p=v.length;f<p;f++)if(v[f]==e){v.splice(f,1);break}!v.length&&delete a.n[u].f}}else{delete a.f;for(u in a.n)a.n[s](u)&&a.n[u].f&&delete a.n[u].f}a=a.n}}},eve.once=function(t,e){var r=function(){return eve.off(t,r),e.apply(this,arguments)};return eve.on(t,r)},eve.version=a,eve.toString=function(){return"You are running Eve "+a},"undefined"!=typeof t&&t.exports?t.exports=eve:(i=[],n=function(){return eve}.apply(e,i),!(void 0!==n&&(t.exports=n)))}(this)},function(t,e,r){var i,n;i=[r(1)],n=function(t){if(!t||t.svg){var e="hasOwnProperty",r=String,i=parseFloat,n=parseInt,a=Math,s=a.max,o=a.abs,l=a.pow,h=/[, ]+/,u=t.eve,c="",f=" ",p="http://www.w3.org/1999/xlink",d={block:"M5,0 0,2.5 5,5z",classic:"M5,0 0,2.5 5,5 3.5,3 3.5,2z",diamond:"M2.5,0 5,2.5 2.5,5 0,2.5z",open:"M6,1 1,3.5 6,6",oval:"M2.5,0A2.5,2.5,0,0,1,2.5,5 2.5,2.5,0,0,1,2.5,0z"},g={};t.toString=function(){return"Your browser supports SVG.\nYou are running Raphaël "+this.version};var v=function(i,n){if(n){"string"==typeof i&&(i=v(i));for(var a in n)n[e](a)&&("xlink:"==a.substring(0,6)?i.setAttributeNS(p,a.substring(6),r(n[a])):i.setAttribute(a,r(n[a])))}else i=t._g.doc.createElementNS("http://www.w3.org/2000/svg",i),i.style&&(i.style.webkitTapHighlightColor="rgba(0,0,0,0)");return i},x=function(e,n){var h="linear",u=e.id+n,f=.5,p=.5,d=e.node,g=e.paper,x=d.style,y=t._g.doc.getElementById(u);if(!y){if(n=r(n).replace(t._radial_gradient,function(t,e,r){if(h="radial",e&&r){f=i(e),p=i(r);var n=2*(p>.5)-1;l(f-.5,2)+l(p-.5,2)>.25&&(p=a.sqrt(.25-l(f-.5,2))*n+.5)&&.5!=p&&(p=p.toFixed(5)-1e-5*n)}return c}),n=n.split(/\s*\-\s*/),"linear"==h){var b=n.shift();if(b=-i(b),isNaN(b))return null;var _=[0,0,a.cos(t.rad(b)),a.sin(t.rad(b))],w=1/(s(o(_[2]),o(_[3]))||1);_[2]*=w,_[3]*=w,_[2]<0&&(_[0]=-_[2],_[2]=0),_[3]<0&&(_[1]=-_[3],_[3]=0)}var k=t._parseDots(n);if(!k)return null;if(u=u.replace(/[\(\)\s,\xb0#]/g,"_"),e.gradient&&u!=e.gradient.id&&(g.defs.removeChild(e.gradient),delete e.gradient),!e.gradient){y=v(h+"Gradient",{id:u}),e.gradient=y,v(y,"radial"==h?{fx:f,fy:p}:{x1:_[0],y1:_[1],x2:_[2],y2:_[3],gradientTransform:e.matrix.invert()}),g.defs.appendChild(y);for(var B=0,C=k.length;B<C;B++)y.appendChild(v("stop",{offset:k[B].offset?k[B].offset:B?"100%":"0%","stop-color":k[B].color||"#fff","stop-opacity":isFinite(k[B].opacity)?k[B].opacity:1}))}}return v(d,{fill:m(u),opacity:1,"fill-opacity":1}),x.fill=c,x.opacity=1,x.fillOpacity=1,1},y=function(){var t=document.documentMode;return t&&(9===t||10===t)},m=function(t){if(y())return"url('#"+t+"')";var e=document.location,r=e.protocol+"//"+e.host+e.pathname+e.search;return"url('"+r+"#"+t+"')"},b=function(t){var e=t.getBBox(1);v(t.pattern,{patternTransform:t.matrix.invert()+" translate("+e.x+","+e.y+")"})},_=function(i,n,a){if("path"==i.type){for(var s=r(n).toLowerCase().split("-"),o=i.paper,l=a?"end":"start",h=i.node,u=i.attrs,f=u["stroke-width"],p=s.length,x="classic",y,m,b,_,w,k=3,B=3,C=5;p--;)switch(s[p]){case"block":case"classic":case"oval":case"diamond":case"open":case"none":x=s[p];break;case"wide":B=5;break;case"narrow":B=2;break;case"long":k=5;break;case"short":k=2}if("open"==x?(k+=2,B+=2,C+=2,b=1,_=a?4:1,w={fill:"none",stroke:u.stroke}):(_=b=k/2,w={fill:u.stroke,stroke:"none"}),i._.arrows?a?(i._.arrows.endPath&&g[i._.arrows.endPath]--,i._.arrows.endMarker&&g[i._.arrows.endMarker]--):(i._.arrows.startPath&&g[i._.arrows.startPath]--,i._.arrows.startMarker&&g[i._.arrows.startMarker]--):i._.arrows={},"none"!=x){var S="raphael-marker-"+x,A="raphael-marker-"+l+x+k+B+"-obj"+i.id;t._g.doc.getElementById(S)?g[S]++:(o.defs.appendChild(v(v("path"),{"stroke-linecap":"round",d:d[x],id:S})),g[S]=1);var T=t._g.doc.getElementById(A),E;T?(g[A]++,E=T.getElementsByTagName("use")[0]):(T=v(v("marker"),{id:A,markerHeight:B,markerWidth:k,orient:"auto",refX:_,refY:B/2}),E=v(v("use"),{"xlink:href":"#"+S,transform:(a?"rotate(180 "+k/2+" "+B/2+") ":c)+"scale("+k/C+","+B/C+")","stroke-width":(1/((k/C+B/C)/2)).toFixed(4)}),T.appendChild(E),o.defs.appendChild(T),g[A]=1),v(E,w);var M=b*("diamond"!=x&&"oval"!=x);a?(y=i._.arrows.startdx*f||0,m=t.getTotalLength(u.path)-M*f):(y=M*f,m=t.getTotalLength(u.path)-(i._.arrows.enddx*f||0)),w={},w["marker-"+l]="url(#"+A+")",(m||y)&&(w.d=t.getSubpath(u.path,y,m)),v(h,w),i._.arrows[l+"Path"]=S,i._.arrows[l+"Marker"]=A,i._.arrows[l+"dx"]=M,i._.arrows[l+"Type"]=x,i._.arrows[l+"String"]=n}else a?(y=i._.arrows.startdx*f||0,m=t.getTotalLength(u.path)-y):(y=0,m=t.getTotalLength(u.path)-(i._.arrows.enddx*f||0)),i._.arrows[l+"Path"]&&v(h,{d:t.getSubpath(u.path,y,m)}),delete i._.arrows[l+"Path"],delete i._.arrows[l+"Marker"],delete i._.arrows[l+"dx"],delete i._.arrows[l+"Type"],delete i._.arrows[l+"String"];for(w in g)if(g[e](w)&&!g[w]){var N=t._g.doc.getElementById(w);N&&N.parentNode.removeChild(N)}}},w={"-":[3,1],".":[1,1],"-.":[3,1,1,1],"-..":[3,1,1,1,1,1],". ":[1,3],"- ":[4,3],"--":[8,3],"- .":[4,3,1,3],"--.":[8,3,1,3],"--..":[8,3,1,3,1,3]},k=function(t,e,i){if(e=w[r(e).toLowerCase()]){for(var n=t.attrs["stroke-width"]||"1",a={round:n,square:n,butt:0}[t.attrs["stroke-linecap"]||i["stroke-linecap"]]||0,s=[],o=e.length;o--;)s[o]=e[o]*n+(o%2?1:-1)*a;v(t.node,{"stroke-dasharray":s.join(",")})}else v(t.node,{"stroke-dasharray":"none"})},B=function(i,a){var l=i.node,u=i.attrs,f=l.style.visibility;l.style.visibility="hidden";for(var d in a)if(a[e](d)){if(!t._availableAttrs[e](d))continue;var g=a[d];switch(u[d]=g,d){case"blur":i.blur(g);break;case"title":var y=l.getElementsByTagName("title");if(y.length&&(y=y[0]))y.firstChild.nodeValue=g;else{y=v("title");var m=t._g.doc.createTextNode(g);y.appendChild(m),l.appendChild(y)}break;case"href":case"target":var w=l.parentNode;if("a"!=w.tagName.toLowerCase()){var B=v("a");w.insertBefore(B,l),B.appendChild(l),w=B}"target"==d?w.setAttributeNS(p,"show","blank"==g?"new":g):w.setAttributeNS(p,d,g);break;case"cursor":l.style.cursor=g;break;case"transform":i.transform(g);break;case"arrow-start":_(i,g);break;case"arrow-end":_(i,g,1);break;case"clip-rect":var C=r(g).split(h);if(4==C.length){i.clip&&i.clip.parentNode.parentNode.removeChild(i.clip.parentNode);var A=v("clipPath"),T=v("rect");A.id=t.createUUID(),v(T,{x:C[0],y:C[1],width:C[2],height:C[3]}),A.appendChild(T),i.paper.defs.appendChild(A),v(l,{"clip-path":"url(#"+A.id+")"}),i.clip=T}if(!g){var E=l.getAttribute("clip-path");if(E){var M=t._g.doc.getElementById(E.replace(/(^url\(#|\)$)/g,c));M&&M.parentNode.removeChild(M),v(l,{"clip-path":c}),delete i.clip}}break;case"path":"path"==i.type&&(v(l,{d:g?u.path=t._pathToAbsolute(g):"M0,0"}),i._.dirty=1,i._.arrows&&("startString"in i._.arrows&&_(i,i._.arrows.startString),"endString"in i._.arrows&&_(i,i._.arrows.endString,1)));break;case"width":if(l.setAttribute(d,g),i._.dirty=1,!u.fx)break;d="x",g=u.x;case"x":u.fx&&(g=-u.x-(u.width||0));case"rx":if("rx"==d&&"rect"==i.type)break;case"cx":l.setAttribute(d,g),i.pattern&&b(i),i._.dirty=1;break;case"height":if(l.setAttribute(d,g),i._.dirty=1,!u.fy)break;d="y",g=u.y;case"y":u.fy&&(g=-u.y-(u.height||0));case"ry":if("ry"==d&&"rect"==i.type)break;case"cy":l.setAttribute(d,g),i.pattern&&b(i),i._.dirty=1;break;case"r":"rect"==i.type?v(l,{rx:g,ry:g}):l.setAttribute(d,g),i._.dirty=1;break;case"src":"image"==i.type&&l.setAttributeNS(p,"href",g);break;case"stroke-width":1==i._.sx&&1==i._.sy||(g/=s(o(i._.sx),o(i._.sy))||1),l.setAttribute(d,g),u["stroke-dasharray"]&&k(i,u["stroke-dasharray"],a),
i._.arrows&&("startString"in i._.arrows&&_(i,i._.arrows.startString),"endString"in i._.arrows&&_(i,i._.arrows.endString,1));break;case"stroke-dasharray":k(i,g,a);break;case"fill":var N=r(g).match(t._ISURL);if(N){A=v("pattern");var L=v("image");A.id=t.createUUID(),v(A,{x:0,y:0,patternUnits:"userSpaceOnUse",height:1,width:1}),v(L,{x:0,y:0,"xlink:href":N[1]}),A.appendChild(L),function(e){t._preload(N[1],function(){var t=this.offsetWidth,r=this.offsetHeight;v(e,{width:t,height:r}),v(L,{width:t,height:r})})}(A),i.paper.defs.appendChild(A),v(l,{fill:"url(#"+A.id+")"}),i.pattern=A,i.pattern&&b(i);break}var z=t.getRGB(g);if(z.error){if(("circle"==i.type||"ellipse"==i.type||"r"!=r(g).charAt())&&x(i,g)){if("opacity"in u||"fill-opacity"in u){var P=t._g.doc.getElementById(l.getAttribute("fill").replace(/^url\(#|\)$/g,c));if(P){var F=P.getElementsByTagName("stop");v(F[F.length-1],{"stop-opacity":("opacity"in u?u.opacity:1)*("fill-opacity"in u?u["fill-opacity"]:1)})}}u.gradient=g,u.fill="none";break}}else delete a.gradient,delete u.gradient,!t.is(u.opacity,"undefined")&&t.is(a.opacity,"undefined")&&v(l,{opacity:u.opacity}),!t.is(u["fill-opacity"],"undefined")&&t.is(a["fill-opacity"],"undefined")&&v(l,{"fill-opacity":u["fill-opacity"]});z[e]("opacity")&&v(l,{"fill-opacity":z.opacity>1?z.opacity/100:z.opacity});case"stroke":z=t.getRGB(g),l.setAttribute(d,z.hex),"stroke"==d&&z[e]("opacity")&&v(l,{"stroke-opacity":z.opacity>1?z.opacity/100:z.opacity}),"stroke"==d&&i._.arrows&&("startString"in i._.arrows&&_(i,i._.arrows.startString),"endString"in i._.arrows&&_(i,i._.arrows.endString,1));break;case"gradient":("circle"==i.type||"ellipse"==i.type||"r"!=r(g).charAt())&&x(i,g);break;case"opacity":u.gradient&&!u[e]("stroke-opacity")&&v(l,{"stroke-opacity":g>1?g/100:g});case"fill-opacity":if(u.gradient){P=t._g.doc.getElementById(l.getAttribute("fill").replace(/^url\(#|\)$/g,c)),P&&(F=P.getElementsByTagName("stop"),v(F[F.length-1],{"stop-opacity":g}));break}default:"font-size"==d&&(g=n(g,10)+"px");var R=d.replace(/(\-.)/g,function(t){return t.substring(1).toUpperCase()});l.style[R]=g,i._.dirty=1,l.setAttribute(d,g)}}S(i,a),l.style.visibility=f},C=1.2,S=function(i,a){if("text"==i.type&&(a[e]("text")||a[e]("font")||a[e]("font-size")||a[e]("x")||a[e]("y"))){var s=i.attrs,o=i.node,l=o.firstChild?n(t._g.doc.defaultView.getComputedStyle(o.firstChild,c).getPropertyValue("font-size"),10):10;if(a[e]("text")){for(s.text=a.text;o.firstChild;)o.removeChild(o.firstChild);for(var h=r(a.text).split("\n"),u=[],f,p=0,d=h.length;p<d;p++)f=v("tspan"),p&&v(f,{dy:l*C,x:s.x}),f.appendChild(t._g.doc.createTextNode(h[p])),o.appendChild(f),u[p]=f}else for(u=o.getElementsByTagName("tspan"),p=0,d=u.length;p<d;p++)p?v(u[p],{dy:l*C,x:s.x}):v(u[0],{dy:0});v(o,{x:s.x,y:s.y}),i._.dirty=1;var g=i._getBBox(),x=s.y-(g.y+g.height/2);x&&t.is(x,"finite")&&v(u[0],{dy:x})}},A=function(t){return t.parentNode&&"a"===t.parentNode.tagName.toLowerCase()?t.parentNode:t},T=function(e,r){function i(){return("0000"+(Math.random()*Math.pow(36,5)<<0).toString(36)).slice(-5)}var n=0,a=0;this[0]=this.node=e,e.raphael=!0,this.id=i(),e.raphaelid=this.id,this.matrix=t.matrix(),this.realPath=null,this.paper=r,this.attrs=this.attrs||{},this._={transform:[],sx:1,sy:1,deg:0,dx:0,dy:0,dirty:1},!r.bottom&&(r.bottom=this),this.prev=r.top,r.top&&(r.top.next=this),r.top=this,this.next=null},E=t.el;T.prototype=E,E.constructor=T,t._engine.path=function(t,e){var r=v("path");e.canvas&&e.canvas.appendChild(r);var i=new T(r,e);return i.type="path",B(i,{fill:"none",stroke:"#000",path:t}),i},E.rotate=function(t,e,n){if(this.removed)return this;if(t=r(t).split(h),t.length-1&&(e=i(t[1]),n=i(t[2])),t=i(t[0]),null==n&&(e=n),null==e||null==n){var a=this.getBBox(1);e=a.x+a.width/2,n=a.y+a.height/2}return this.transform(this._.transform.concat([["r",t,e,n]])),this},E.scale=function(t,e,n,a){if(this.removed)return this;if(t=r(t).split(h),t.length-1&&(e=i(t[1]),n=i(t[2]),a=i(t[3])),t=i(t[0]),null==e&&(e=t),null==a&&(n=a),null==n||null==a)var s=this.getBBox(1);return n=null==n?s.x+s.width/2:n,a=null==a?s.y+s.height/2:a,this.transform(this._.transform.concat([["s",t,e,n,a]])),this},E.translate=function(t,e){return this.removed?this:(t=r(t).split(h),t.length-1&&(e=i(t[1])),t=i(t[0])||0,e=+e||0,this.transform(this._.transform.concat([["t",t,e]])),this)},E.transform=function(r){var i=this._;if(null==r)return i.transform;if(t._extractTransform(this,r),this.clip&&v(this.clip,{transform:this.matrix.invert()}),this.pattern&&b(this),this.node&&v(this.node,{transform:this.matrix}),1!=i.sx||1!=i.sy){var n=this.attrs[e]("stroke-width")?this.attrs["stroke-width"]:1;this.attr({"stroke-width":n})}return this},E.hide=function(){return this.removed||(this.node.style.display="none"),this},E.show=function(){return this.removed||(this.node.style.display=""),this},E.remove=function(){var e=A(this.node);if(!this.removed&&e.parentNode){var r=this.paper;r.__set__&&r.__set__.exclude(this),u.unbind("raphael.*.*."+this.id),this.gradient&&r.defs.removeChild(this.gradient),t._tear(this,r),e.parentNode.removeChild(e),this.removeData();for(var i in this)this[i]="function"==typeof this[i]?t._removedFactory(i):null;this.removed=!0}},E._getBBox=function(){if("none"==this.node.style.display){this.show();var t=!0}var e=!1,r;this.paper.canvas.parentElement?r=this.paper.canvas.parentElement.style:this.paper.canvas.parentNode&&(r=this.paper.canvas.parentNode.style),r&&"none"==r.display&&(e=!0,r.display="");var i={};try{i=this.node.getBBox()}catch(n){i={x:this.node.clientLeft,y:this.node.clientTop,width:this.node.clientWidth,height:this.node.clientHeight}}finally{i=i||{},e&&(r.display="none")}return t&&this.hide(),i},E.attr=function(r,i){if(this.removed)return this;if(null==r){var n={};for(var a in this.attrs)this.attrs[e](a)&&(n[a]=this.attrs[a]);return n.gradient&&"none"==n.fill&&(n.fill=n.gradient)&&delete n.gradient,n.transform=this._.transform,n}if(null==i&&t.is(r,"string")){if("fill"==r&&"none"==this.attrs.fill&&this.attrs.gradient)return this.attrs.gradient;if("transform"==r)return this._.transform;for(var s=r.split(h),o={},l=0,c=s.length;l<c;l++)r=s[l],r in this.attrs?o[r]=this.attrs[r]:t.is(this.paper.customAttributes[r],"function")?o[r]=this.paper.customAttributes[r].def:o[r]=t._availableAttrs[r];return c-1?o:o[s[0]]}if(null==i&&t.is(r,"array")){for(o={},l=0,c=r.length;l<c;l++)o[r[l]]=this.attr(r[l]);return o}if(null!=i){var f={};f[r]=i}else null!=r&&t.is(r,"object")&&(f=r);for(var p in f)u("raphael.attr."+p+"."+this.id,this,f[p]);for(p in this.paper.customAttributes)if(this.paper.customAttributes[e](p)&&f[e](p)&&t.is(this.paper.customAttributes[p],"function")){var d=this.paper.customAttributes[p].apply(this,[].concat(f[p]));this.attrs[p]=f[p];for(var g in d)d[e](g)&&(f[g]=d[g])}return B(this,f),this},E.toFront=function(){if(this.removed)return this;var e=A(this.node);e.parentNode.appendChild(e);var r=this.paper;return r.top!=this&&t._tofront(this,r),this},E.toBack=function(){if(this.removed)return this;var e=A(this.node),r=e.parentNode;r.insertBefore(e,r.firstChild),t._toback(this,this.paper);var i=this.paper;return this},E.insertAfter=function(e){if(this.removed||!e)return this;var r=A(this.node),i=A(e.node||e[e.length-1].node);return i.nextSibling?i.parentNode.insertBefore(r,i.nextSibling):i.parentNode.appendChild(r),t._insertafter(this,e,this.paper),this},E.insertBefore=function(e){if(this.removed||!e)return this;var r=A(this.node),i=A(e.node||e[0].node);return i.parentNode.insertBefore(r,i),t._insertbefore(this,e,this.paper),this},E.blur=function(e){var r=this;if(0!==+e){var i=v("filter"),n=v("feGaussianBlur");r.attrs.blur=e,i.id=t.createUUID(),v(n,{stdDeviation:+e||1.5}),i.appendChild(n),r.paper.defs.appendChild(i),r._blur=i,v(r.node,{filter:"url(#"+i.id+")"})}else r._blur&&(r._blur.parentNode.removeChild(r._blur),delete r._blur,delete r.attrs.blur),r.node.removeAttribute("filter");return r},t._engine.circle=function(t,e,r,i){var n=v("circle");t.canvas&&t.canvas.appendChild(n);var a=new T(n,t);return a.attrs={cx:e,cy:r,r:i,fill:"none",stroke:"#000"},a.type="circle",v(n,a.attrs),a},t._engine.rect=function(t,e,r,i,n,a){var s=v("rect");t.canvas&&t.canvas.appendChild(s);var o=new T(s,t);return o.attrs={x:e,y:r,width:i,height:n,rx:a||0,ry:a||0,fill:"none",stroke:"#000"},o.type="rect",v(s,o.attrs),o},t._engine.ellipse=function(t,e,r,i,n){var a=v("ellipse");t.canvas&&t.canvas.appendChild(a);var s=new T(a,t);return s.attrs={cx:e,cy:r,rx:i,ry:n,fill:"none",stroke:"#000"},s.type="ellipse",v(a,s.attrs),s},t._engine.image=function(t,e,r,i,n,a){var s=v("image");v(s,{x:r,y:i,width:n,height:a,preserveAspectRatio:"none"}),s.setAttributeNS(p,"href",e),t.canvas&&t.canvas.appendChild(s);var o=new T(s,t);return o.attrs={x:r,y:i,width:n,height:a,src:e},o.type="image",o},t._engine.text=function(e,r,i,n){var a=v("text");e.canvas&&e.canvas.appendChild(a);var s=new T(a,e);return s.attrs={x:r,y:i,"text-anchor":"middle",text:n,"font-family":t._availableAttrs["font-family"],"font-size":t._availableAttrs["font-size"],stroke:"none",fill:"#000"},s.type="text",B(s,s.attrs),s},t._engine.setSize=function(t,e){return this.width=t||this.width,this.height=e||this.height,this.canvas.setAttribute("width",this.width),this.canvas.setAttribute("height",this.height),this._viewBox&&this.setViewBox.apply(this,this._viewBox),this},t._engine.create=function(){var e=t._getContainer.apply(0,arguments),r=e&&e.container,i=e.x,n=e.y,a=e.width,s=e.height;if(!r)throw new Error("SVG container not found.");var o=v("svg"),l="overflow:hidden;",h;return i=i||0,n=n||0,a=a||512,s=s||342,v(o,{height:s,version:1.1,width:a,xmlns:"http://www.w3.org/2000/svg","xmlns:xlink":"http://www.w3.org/1999/xlink"}),1==r?(o.style.cssText=l+"position:absolute;left:"+i+"px;top:"+n+"px",t._g.doc.body.appendChild(o),h=1):(o.style.cssText=l+"position:relative",r.firstChild?r.insertBefore(o,r.firstChild):r.appendChild(o)),r=new t._Paper,r.width=a,r.height=s,r.canvas=o,r.clear(),r._left=r._top=0,h&&(r.renderfix=function(){}),r.renderfix(),r},t._engine.setViewBox=function(t,e,r,i,n){u("raphael.setViewBox",this,this._viewBox,[t,e,r,i,n]);var a=this.getSize(),o=s(r/a.width,i/a.height),l=this.top,h=n?"xMidYMid meet":"xMinYMin",c,p;for(null==t?(this._vbSize&&(o=1),delete this._vbSize,c="0 0 "+this.width+f+this.height):(this._vbSize=o,c=t+f+e+f+r+f+i),v(this.canvas,{viewBox:c,preserveAspectRatio:h});o&&l;)p="stroke-width"in l.attrs?l.attrs["stroke-width"]:1,l.attr({"stroke-width":p}),l._.dirty=1,l._.dirtyT=1,l=l.prev;return this._viewBox=[t,e,r,i,!!n],this},t.prototype.renderfix=function(){var t=this.canvas,e=t.style,r;try{r=t.getScreenCTM()||t.createSVGMatrix()}catch(i){r=t.createSVGMatrix()}var n=-r.e%1,a=-r.f%1;(n||a)&&(n&&(this._left=(this._left+n)%1,e.left=this._left+"px"),a&&(this._top=(this._top+a)%1,e.top=this._top+"px"))},t.prototype.clear=function(){t.eve("raphael.clear",this);for(var e=this.canvas;e.firstChild;)e.removeChild(e.firstChild);this.bottom=this.top=null,(this.desc=v("desc")).appendChild(t._g.doc.createTextNode("Created with Raphaël "+t.version)),e.appendChild(this.desc),e.appendChild(this.defs=v("defs"))},t.prototype.remove=function(){u("raphael.remove",this),this.canvas.parentNode&&this.canvas.parentNode.removeChild(this.canvas);for(var e in this)this[e]="function"==typeof this[e]?t._removedFactory(e):null};var M=t.st;for(var N in E)E[e](N)&&!M[e](N)&&(M[N]=function(t){return function(){var e=arguments;return this.forEach(function(r){r[t].apply(r,e)})}}(N))}}.apply(e,i),!(void 0!==n&&(t.exports=n))},function(t,e,r){var i,n;i=[r(1)],n=function(t){if(!t||t.vml){var e="hasOwnProperty",r=String,i=parseFloat,n=Math,a=n.round,s=n.max,o=n.min,l=n.abs,h="fill",u=/[, ]+/,c=t.eve,f=" progid:DXImageTransform.Microsoft",p=" ",d="",g={M:"m",L:"l",C:"c",Z:"x",m:"t",l:"r",c:"v",z:"x"},v=/([clmz]),?([^clmz]*)/gi,x=/ progid:\S+Blur\([^\)]+\)/g,y=/-?[^,\s-]+/g,m="position:absolute;left:0;top:0;width:1px;height:1px;behavior:url(#default#VML)",b=21600,_={path:1,rect:1,image:1},w={circle:1,ellipse:1},k=function(e){var i=/[ahqstv]/gi,n=t._pathToAbsolute;if(r(e).match(i)&&(n=t._path2curve),i=/[clmz]/g,n==t._pathToAbsolute&&!r(e).match(i)){var s=r(e).replace(v,function(t,e,r){var i=[],n="m"==e.toLowerCase(),s=g[e];return r.replace(y,function(t){n&&2==i.length&&(s+=i+g["m"==e?"l":"L"],i=[]),i.push(a(t*b))}),s+i});return s}var o=n(e),l,h;s=[];for(var u=0,c=o.length;u<c;u++){l=o[u],h=o[u][0].toLowerCase(),"z"==h&&(h="x");for(var f=1,x=l.length;f<x;f++)h+=a(l[f]*b)+(f!=x-1?",":d);s.push(h)}return s.join(p)},B=function(e,r,i){var n=t.matrix();return n.rotate(-e,.5,.5),{dx:n.x(r,i),dy:n.y(r,i)}},C=function(t,e,r,i,n,a){var s=t._,o=t.matrix,u=s.fillpos,c=t.node,f=c.style,d=1,g="",v,x=b/e,y=b/r;if(f.visibility="hidden",e&&r){if(c.coordsize=l(x)+p+l(y),f.rotation=a*(e*r<0?-1:1),a){var m=B(a,i,n);i=m.dx,n=m.dy}if(e<0&&(g+="x"),r<0&&(g+=" y")&&(d=-1),f.flip=g,c.coordorigin=i*-x+p+n*-y,u||s.fillsize){var _=c.getElementsByTagName(h);_=_&&_[0],c.removeChild(_),u&&(m=B(a,o.x(u[0],u[1]),o.y(u[0],u[1])),_.position=m.dx*d+p+m.dy*d),s.fillsize&&(_.size=s.fillsize[0]*l(e)+p+s.fillsize[1]*l(r)),c.appendChild(_)}f.visibility="visible"}};t.toString=function(){return"Your browser doesn’t support SVG. Falling down to VML.\nYou are running Raphaël "+this.version};var S=function(t,e,i){for(var n=r(e).toLowerCase().split("-"),a=i?"end":"start",s=n.length,o="classic",l="medium",h="medium";s--;)switch(n[s]){case"block":case"classic":case"oval":case"diamond":case"open":case"none":o=n[s];break;case"wide":case"narrow":h=n[s];break;case"long":case"short":l=n[s]}var u=t.node.getElementsByTagName("stroke")[0];u[a+"arrow"]=o,u[a+"arrowlength"]=l,u[a+"arrowwidth"]=h},A=function(n,l){n.attrs=n.attrs||{};var c=n.node,f=n.attrs,g=c.style,v,x=_[n.type]&&(l.x!=f.x||l.y!=f.y||l.width!=f.width||l.height!=f.height||l.cx!=f.cx||l.cy!=f.cy||l.rx!=f.rx||l.ry!=f.ry||l.r!=f.r),y=w[n.type]&&(f.cx!=l.cx||f.cy!=l.cy||f.r!=l.r||f.rx!=l.rx||f.ry!=l.ry),m=n;for(var B in l)l[e](B)&&(f[B]=l[B]);if(x&&(f.path=t._getPath[n.type](n),n._.dirty=1),l.href&&(c.href=l.href),l.title&&(c.title=l.title),l.target&&(c.target=l.target),l.cursor&&(g.cursor=l.cursor),"blur"in l&&n.blur(l.blur),(l.path&&"path"==n.type||x)&&(c.path=k(~r(f.path).toLowerCase().indexOf("r")?t._pathToAbsolute(f.path):f.path),n._.dirty=1,"image"==n.type&&(n._.fillpos=[f.x,f.y],n._.fillsize=[f.width,f.height],C(n,1,1,0,0,0))),"transform"in l&&n.transform(l.transform),y){var A=+f.cx,E=+f.cy,M=+f.rx||+f.r||0,L=+f.ry||+f.r||0;c.path=t.format("ar{0},{1},{2},{3},{4},{1},{4},{1}x",a((A-M)*b),a((E-L)*b),a((A+M)*b),a((E+L)*b),a(A*b)),n._.dirty=1}if("clip-rect"in l){var z=r(l["clip-rect"]).split(u);if(4==z.length){z[2]=+z[2]+ +z[0],z[3]=+z[3]+ +z[1];var P=c.clipRect||t._g.doc.createElement("div"),F=P.style;F.clip=t.format("rect({1}px {2}px {3}px {0}px)",z),c.clipRect||(F.position="absolute",F.top=0,F.left=0,F.width=n.paper.width+"px",F.height=n.paper.height+"px",c.parentNode.insertBefore(P,c),P.appendChild(c),c.clipRect=P)}l["clip-rect"]||c.clipRect&&(c.clipRect.style.clip="auto")}if(n.textpath){var R=n.textpath.style;l.font&&(R.font=l.font),l["font-family"]&&(R.fontFamily='"'+l["font-family"].split(",")[0].replace(/^['"]+|['"]+$/g,d)+'"'),l["font-size"]&&(R.fontSize=l["font-size"]),l["font-weight"]&&(R.fontWeight=l["font-weight"]),l["font-style"]&&(R.fontStyle=l["font-style"])}if("arrow-start"in l&&S(m,l["arrow-start"]),"arrow-end"in l&&S(m,l["arrow-end"],1),null!=l.opacity||null!=l.fill||null!=l.src||null!=l.stroke||null!=l["stroke-width"]||null!=l["stroke-opacity"]||null!=l["fill-opacity"]||null!=l["stroke-dasharray"]||null!=l["stroke-miterlimit"]||null!=l["stroke-linejoin"]||null!=l["stroke-linecap"]){var j=c.getElementsByTagName(h),I=!1;if(j=j&&j[0],!j&&(I=j=N(h)),"image"==n.type&&l.src&&(j.src=l.src),l.fill&&(j.on=!0),null!=j.on&&"none"!=l.fill&&null!==l.fill||(j.on=!1),j.on&&l.fill){var q=r(l.fill).match(t._ISURL);if(q){j.parentNode==c&&c.removeChild(j),j.rotate=!0,j.src=q[1],j.type="tile";var D=n.getBBox(1);j.position=D.x+p+D.y,n._.fillpos=[D.x,D.y],t._preload(q[1],function(){n._.fillsize=[this.offsetWidth,this.offsetHeight]})}else j.color=t.getRGB(l.fill).hex,j.src=d,j.type="solid",t.getRGB(l.fill).error&&(m.type in{circle:1,ellipse:1}||"r"!=r(l.fill).charAt())&&T(m,l.fill,j)&&(f.fill="none",f.gradient=l.fill,j.rotate=!1)}if("fill-opacity"in l||"opacity"in l){var V=((+f["fill-opacity"]+1||2)-1)*((+f.opacity+1||2)-1)*((+t.getRGB(l.fill).o+1||2)-1);V=o(s(V,0),1),j.opacity=V,j.src&&(j.color="none")}c.appendChild(j);var O=c.getElementsByTagName("stroke")&&c.getElementsByTagName("stroke")[0],Y=!1;!O&&(Y=O=N("stroke")),(l.stroke&&"none"!=l.stroke||l["stroke-width"]||null!=l["stroke-opacity"]||l["stroke-dasharray"]||l["stroke-miterlimit"]||l["stroke-linejoin"]||l["stroke-linecap"])&&(O.on=!0),("none"==l.stroke||null===l.stroke||null==O.on||0==l.stroke||0==l["stroke-width"])&&(O.on=!1);var W=t.getRGB(l.stroke);O.on&&l.stroke&&(O.color=W.hex),V=((+f["stroke-opacity"]+1||2)-1)*((+f.opacity+1||2)-1)*((+W.o+1||2)-1);var G=.75*(i(l["stroke-width"])||1);if(V=o(s(V,0),1),null==l["stroke-width"]&&(G=f["stroke-width"]),l["stroke-width"]&&(O.weight=G),G&&G<1&&(V*=G)&&(O.weight=1),O.opacity=V,l["stroke-linejoin"]&&(O.joinstyle=l["stroke-linejoin"]||"miter"),O.miterlimit=l["stroke-miterlimit"]||8,l["stroke-linecap"]&&(O.endcap="butt"==l["stroke-linecap"]?"flat":"square"==l["stroke-linecap"]?"square":"round"),"stroke-dasharray"in l){var H={"-":"shortdash",".":"shortdot","-.":"shortdashdot","-..":"shortdashdotdot",". ":"dot","- ":"dash","--":"longdash","- .":"dashdot","--.":"longdashdot","--..":"longdashdotdot"};O.dashstyle=H[e](l["stroke-dasharray"])?H[l["stroke-dasharray"]]:d}Y&&c.appendChild(O)}if("text"==m.type){m.paper.canvas.style.display=d;var X=m.paper.span,U=100,$=f.font&&f.font.match(/\d+(?:\.\d*)?(?=px)/);g=X.style,f.font&&(g.font=f.font),f["font-family"]&&(g.fontFamily=f["font-family"]),f["font-weight"]&&(g.fontWeight=f["font-weight"]),f["font-style"]&&(g.fontStyle=f["font-style"]),$=i(f["font-size"]||$&&$[0])||10,g.fontSize=$*U+"px",m.textpath.string&&(X.innerHTML=r(m.textpath.string).replace(/</g,"&#60;").replace(/&/g,"&#38;").replace(/\n/g,"<br>"));var Z=X.getBoundingClientRect();m.W=f.w=(Z.right-Z.left)/U,m.H=f.h=(Z.bottom-Z.top)/U,m.X=f.x,m.Y=f.y+m.H/2,("x"in l||"y"in l)&&(m.path.v=t.format("m{0},{1}l{2},{1}",a(f.x*b),a(f.y*b),a(f.x*b)+1));for(var Q=["x","y","text","font","font-family","font-weight","font-style","font-size"],J=0,K=Q.length;J<K;J++)if(Q[J]in l){m._.dirty=1;break}switch(f["text-anchor"]){case"start":m.textpath.style["v-text-align"]="left",m.bbx=m.W/2;break;case"end":m.textpath.style["v-text-align"]="right",m.bbx=-m.W/2;break;default:m.textpath.style["v-text-align"]="center",m.bbx=0}m.textpath.style["v-text-kern"]=!0}},T=function(e,a,s){e.attrs=e.attrs||{};var o=e.attrs,l=Math.pow,h,u,c="linear",f=".5 .5";if(e.attrs.gradient=a,a=r(a).replace(t._radial_gradient,function(t,e,r){return c="radial",e&&r&&(e=i(e),r=i(r),l(e-.5,2)+l(r-.5,2)>.25&&(r=n.sqrt(.25-l(e-.5,2))*(2*(r>.5)-1)+.5),f=e+p+r),d}),a=a.split(/\s*\-\s*/),"linear"==c){var g=a.shift();if(g=-i(g),isNaN(g))return null}var v=t._parseDots(a);if(!v)return null;if(e=e.shape||e.node,v.length){e.removeChild(s),s.on=!0,s.method="none",s.color=v[0].color,s.color2=v[v.length-1].color;for(var x=[],y=0,m=v.length;y<m;y++)v[y].offset&&x.push(v[y].offset+p+v[y].color);s.colors=x.length?x.join():"0% "+s.color,"radial"==c?(s.type="gradientTitle",s.focus="100%",s.focussize="0 0",s.focusposition=f,s.angle=0):(s.type="gradient",s.angle=(270-g)%360),e.appendChild(s)}return 1},E=function(e,r){this[0]=this.node=e,e.raphael=!0,this.id=t._oid++,e.raphaelid=this.id,this.X=0,this.Y=0,this.attrs={},this.paper=r,this.matrix=t.matrix(),this._={transform:[],sx:1,sy:1,dx:0,dy:0,deg:0,dirty:1,dirtyT:1},!r.bottom&&(r.bottom=this),this.prev=r.top,r.top&&(r.top.next=this),r.top=this,this.next=null},M=t.el;E.prototype=M,M.constructor=E,M.transform=function(e){if(null==e)return this._.transform;var i=this.paper._viewBoxShift,n=i?"s"+[i.scale,i.scale]+"-1-1t"+[i.dx,i.dy]:d,a;i&&(a=e=r(e).replace(/\.{3}|\u2026/g,this._.transform||d)),t._extractTransform(this,n+e);var s=this.matrix.clone(),o=this.skew,l=this.node,h,u=~r(this.attrs.fill).indexOf("-"),c=!r(this.attrs.fill).indexOf("url(");if(s.translate(1,1),c||u||"image"==this.type)if(o.matrix="1 0 0 1",o.offset="0 0",h=s.split(),u&&h.noRotation||!h.isSimple){l.style.filter=s.toFilter();var f=this.getBBox(),g=this.getBBox(1),v=f.x-g.x,x=f.y-g.y;l.coordorigin=v*-b+p+x*-b,C(this,1,1,v,x,0)}else l.style.filter=d,C(this,h.scalex,h.scaley,h.dx,h.dy,h.rotate);else l.style.filter=d,o.matrix=r(s),o.offset=s.offset();return null!==a&&(this._.transform=a,t._extractTransform(this,a)),this},M.rotate=function(t,e,n){if(this.removed)return this;if(null!=t){if(t=r(t).split(u),t.length-1&&(e=i(t[1]),n=i(t[2])),t=i(t[0]),null==n&&(e=n),null==e||null==n){var a=this.getBBox(1);e=a.x+a.width/2,n=a.y+a.height/2}return this._.dirtyT=1,this.transform(this._.transform.concat([["r",t,e,n]])),this}},M.translate=function(t,e){return this.removed?this:(t=r(t).split(u),t.length-1&&(e=i(t[1])),t=i(t[0])||0,e=+e||0,this._.bbox&&(this._.bbox.x+=t,this._.bbox.y+=e),this.transform(this._.transform.concat([["t",t,e]])),this)},M.scale=function(t,e,n,a){if(this.removed)return this;if(t=r(t).split(u),t.length-1&&(e=i(t[1]),n=i(t[2]),a=i(t[3]),isNaN(n)&&(n=null),isNaN(a)&&(a=null)),t=i(t[0]),null==e&&(e=t),null==a&&(n=a),null==n||null==a)var s=this.getBBox(1);return n=null==n?s.x+s.width/2:n,a=null==a?s.y+s.height/2:a,this.transform(this._.transform.concat([["s",t,e,n,a]])),this._.dirtyT=1,this},M.hide=function(){return!this.removed&&(this.node.style.display="none"),this},M.show=function(){return!this.removed&&(this.node.style.display=d),this},M.auxGetBBox=t.el.getBBox,M.getBBox=function(){var t=this.auxGetBBox();if(this.paper&&this.paper._viewBoxShift){var e={},r=1/this.paper._viewBoxShift.scale;return e.x=t.x-this.paper._viewBoxShift.dx,e.x*=r,e.y=t.y-this.paper._viewBoxShift.dy,e.y*=r,e.width=t.width*r,e.height=t.height*r,e.x2=e.x+e.width,e.y2=e.y+e.height,e}return t},M._getBBox=function(){return this.removed?{}:{x:this.X+(this.bbx||0)-this.W/2,y:this.Y-this.H,width:this.W,height:this.H}},M.remove=function(){if(!this.removed&&this.node.parentNode){this.paper.__set__&&this.paper.__set__.exclude(this),t.eve.unbind("raphael.*.*."+this.id),t._tear(this,this.paper),this.node.parentNode.removeChild(this.node),this.shape&&this.shape.parentNode.removeChild(this.shape);for(var e in this)this[e]="function"==typeof this[e]?t._removedFactory(e):null;this.removed=!0}},M.attr=function(r,i){if(this.removed)return this;if(null==r){var n={};for(var a in this.attrs)this.attrs[e](a)&&(n[a]=this.attrs[a]);return n.gradient&&"none"==n.fill&&(n.fill=n.gradient)&&delete n.gradient,n.transform=this._.transform,n}if(null==i&&t.is(r,"string")){if(r==h&&"none"==this.attrs.fill&&this.attrs.gradient)return this.attrs.gradient;for(var s=r.split(u),o={},l=0,f=s.length;l<f;l++)r=s[l],r in this.attrs?o[r]=this.attrs[r]:t.is(this.paper.customAttributes[r],"function")?o[r]=this.paper.customAttributes[r].def:o[r]=t._availableAttrs[r];return f-1?o:o[s[0]]}if(this.attrs&&null==i&&t.is(r,"array")){for(o={},l=0,f=r.length;l<f;l++)o[r[l]]=this.attr(r[l]);return o}var p;null!=i&&(p={},p[r]=i),null==i&&t.is(r,"object")&&(p=r);for(var d in p)c("raphael.attr."+d+"."+this.id,this,p[d]);if(p){for(d in this.paper.customAttributes)if(this.paper.customAttributes[e](d)&&p[e](d)&&t.is(this.paper.customAttributes[d],"function")){var g=this.paper.customAttributes[d].apply(this,[].concat(p[d]));this.attrs[d]=p[d];for(var v in g)g[e](v)&&(p[v]=g[v])}p.text&&"text"==this.type&&(this.textpath.string=p.text),A(this,p)}return this},M.toFront=function(){return!this.removed&&this.node.parentNode.appendChild(this.node),this.paper&&this.paper.top!=this&&t._tofront(this,this.paper),this},M.toBack=function(){return this.removed?this:(this.node.parentNode.firstChild!=this.node&&(this.node.parentNode.insertBefore(this.node,this.node.parentNode.firstChild),t._toback(this,this.paper)),this)},M.insertAfter=function(e){return this.removed?this:(e.constructor==t.st.constructor&&(e=e[e.length-1]),e.node.nextSibling?e.node.parentNode.insertBefore(this.node,e.node.nextSibling):e.node.parentNode.appendChild(this.node),t._insertafter(this,e,this.paper),this)},M.insertBefore=function(e){return this.removed?this:(e.constructor==t.st.constructor&&(e=e[0]),e.node.parentNode.insertBefore(this.node,e.node),t._insertbefore(this,e,this.paper),this)},M.blur=function(e){var r=this.node.runtimeStyle,i=r.filter;return i=i.replace(x,d),0!==+e?(this.attrs.blur=e,r.filter=i+p+f+".Blur(pixelradius="+(+e||1.5)+")",r.margin=t.format("-{0}px 0 0 -{0}px",a(+e||1.5))):(r.filter=i,r.margin=0,delete this.attrs.blur),this},t._engine.path=function(t,e){var r=N("shape");r.style.cssText=m,r.coordsize=b+p+b,r.coordorigin=e.coordorigin;var i=new E(r,e),n={fill:"none",stroke:"#000"};t&&(n.path=t),i.type="path",i.path=[],i.Path=d,A(i,n),e.canvas&&e.canvas.appendChild(r);var a=N("skew");return a.on=!0,r.appendChild(a),i.skew=a,i.transform(d),i},t._engine.rect=function(e,r,i,n,a,s){var o=t._rectPath(r,i,n,a,s),l=e.path(o),h=l.attrs;return l.X=h.x=r,l.Y=h.y=i,l.W=h.width=n,l.H=h.height=a,h.r=s,h.path=o,l.type="rect",l},t._engine.ellipse=function(t,e,r,i,n){var a=t.path(),s=a.attrs;return a.X=e-i,a.Y=r-n,a.W=2*i,a.H=2*n,a.type="ellipse",A(a,{cx:e,cy:r,rx:i,ry:n}),a},t._engine.circle=function(t,e,r,i){var n=t.path(),a=n.attrs;return n.X=e-i,n.Y=r-i,n.W=n.H=2*i,n.type="circle",A(n,{cx:e,cy:r,r:i}),n},t._engine.image=function(e,r,i,n,a,s){var o=t._rectPath(i,n,a,s),l=e.path(o).attr({stroke:"none"}),u=l.attrs,c=l.node,f=c.getElementsByTagName(h)[0];return u.src=r,l.X=u.x=i,l.Y=u.y=n,l.W=u.width=a,l.H=u.height=s,u.path=o,l.type="image",f.parentNode==c&&c.removeChild(f),f.rotate=!0,f.src=r,f.type="tile",l._.fillpos=[i,n],l._.fillsize=[a,s],c.appendChild(f),C(l,1,1,0,0,0),l},t._engine.text=function(e,i,n,s){var o=N("shape"),l=N("path"),h=N("textpath");i=i||0,n=n||0,s=s||"",l.v=t.format("m{0},{1}l{2},{1}",a(i*b),a(n*b),a(i*b)+1),l.textpathok=!0,h.string=r(s),h.on=!0,o.style.cssText=m,o.coordsize=b+p+b,o.coordorigin="0 0";var u=new E(o,e),c={fill:"#000",stroke:"none",font:t._availableAttrs.font,text:s};u.shape=o,u.path=l,u.textpath=h,u.type="text",u.attrs.text=r(s),u.attrs.x=i,u.attrs.y=n,u.attrs.w=1,u.attrs.h=1,A(u,c),o.appendChild(h),o.appendChild(l),e.canvas.appendChild(o);var f=N("skew");return f.on=!0,o.appendChild(f),u.skew=f,u.transform(d),u},t._engine.setSize=function(e,r){var i=this.canvas.style;return this.width=e,this.height=r,e==+e&&(e+="px"),r==+r&&(r+="px"),i.width=e,i.height=r,i.clip="rect(0 "+e+" "+r+" 0)",this._viewBox&&t._engine.setViewBox.apply(this,this._viewBox),this},t._engine.setViewBox=function(e,r,i,n,a){t.eve("raphael.setViewBox",this,this._viewBox,[e,r,i,n,a]);var s=this.getSize(),o=s.width,l=s.height,h,u;return a&&(h=l/n,u=o/i,i*h<o&&(e-=(o-i*h)/2/h),n*u<l&&(r-=(l-n*u)/2/u)),this._viewBox=[e,r,i,n,!!a],this._viewBoxShift={dx:-e,dy:-r,scale:s},this.forEach(function(t){t.transform("...")}),this};var N;t._engine.initWin=function(t){var e=t.document;e.styleSheets.length<31?e.createStyleSheet().addRule(".rvml","behavior:url(#default#VML)"):e.styleSheets[0].addRule(".rvml","behavior:url(#default#VML)");try{!e.namespaces.rvml&&e.namespaces.add("rvml","urn:schemas-microsoft-com:vml"),N=function(t){return e.createElement("<rvml:"+t+' class="rvml">')}}catch(r){N=function(t){return e.createElement("<"+t+' xmlns="urn:schemas-microsoft.com:vml" class="rvml">')}}},t._engine.initWin(t._g.win),t._engine.create=function(){var e=t._getContainer.apply(0,arguments),r=e.container,i=e.height,n,a=e.width,s=e.x,o=e.y;if(!r)throw new Error("VML container not found.");var l=new t._Paper,h=l.canvas=t._g.doc.createElement("div"),u=h.style;return s=s||0,o=o||0,a=a||512,i=i||342,l.width=a,l.height=i,a==+a&&(a+="px"),i==+i&&(i+="px"),l.coordsize=1e3*b+p+1e3*b,l.coordorigin="0 0",l.span=t._g.doc.createElement("span"),l.span.style.cssText="position:absolute;left:-9999em;top:-9999em;padding:0;margin:0;line-height:1;",h.appendChild(l.span),u.cssText=t.format("top:0;left:0;width:{0};height:{1};display:inline-block;position:relative;clip:rect(0 {0} {1} 0);overflow:hidden",a,i),1==r?(t._g.doc.body.appendChild(h),u.left=s+"px",u.top=o+"px",u.position="absolute"):r.firstChild?r.insertBefore(h,r.firstChild):r.appendChild(h),l.renderfix=function(){},l},t.prototype.clear=function(){t.eve("raphael.clear",this),this.canvas.innerHTML=d,this.span=t._g.doc.createElement("span"),this.span.style.cssText="position:absolute;left:-9999em;top:-9999em;padding:0;margin:0;line-height:1;display:inline;",this.canvas.appendChild(this.span),this.bottom=this.top=null},t.prototype.remove=function(){t.eve("raphael.remove",this),this.canvas.parentNode.removeChild(this.canvas);for(var e in this)this[e]="function"==typeof this[e]?t._removedFactory(e):null;return!0};var L=t.st;for(var z in M)M[e](z)&&!L[e](z)&&(L[z]=function(t){return function(){var e=arguments;return this.forEach(function(r){r[t].apply(r,e)})}}(z))}}.apply(e,i),!(void 0!==n&&(t.exports=n))}])});
