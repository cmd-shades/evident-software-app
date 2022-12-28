/**
 * @author Nghiem Anh Tuan
 */
function _exist(_theObj) {
  return (_theObj != null)
}
/* Comment out on 28/12/2008 due to unneccessary.
 if (!_exist(_identity))
 {
 var _identity = 0;	
 }
 function _getIdentity()
 {
 _identity++;
 return _identity;
 }
 */
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
function msieversion() {
   var ua = window.navigator.userAgent;
   var msie = ua.indexOf ( "MSIE " );
   if ( msie > 0 )      // If Internet Explorer, return version number
      return parseInt (ua.substring (msie+5, ua.indexOf (".", msie )));
   else                 // If another browser, return 0
      return 0;
}
function getElementsByClassName(node, classname) {
    var a = [];
    var re = new RegExp('(^| )'+classname+'( |$)');
    var els = node.getElementsByTagName("*");
    for(var i=0,j=els.length; i<j; i++)
        if(re.test(els[i].className))a.push(els[i]);
    return a;
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
    _replaceClass(_class, "", _theObj);
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
function _index(_search, _original) {
  return _original.indexOf(_search);
}
function _stopPropagation(_e) {
  if (_e.stopPropagation)
    _e.stopPropagation();
  else
    _e.cancelBubble = true;
}
function _uuid(len, radix) {
  var CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
  var chars = CHARS, uuid = [], rnd = Math.random;
  radix = radix || chars.length;
  if (len) {
    for (var i = 0; i < len; i++)
      uuid[i] = chars[0 | rnd() * radix];
  } else {
    var ri = 0, r;
    uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
    uuid[14] = '4';
    for (var i = 0; i < 36; i++) {
      if (!uuid[i]) {
        r = 0 | rnd() * 16;
        uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r & 0xf];
      }
    }
  }
  return uuid.join('');
}
function _getBrowser() {
  var _agent = navigator.userAgent.toLowerCase();
  if (_index("opera", _agent) != -1) {
    return "opera";
  } else if (_index("firefox", _agent) != -1) {
    return "firefox";
  } else if (_index("safari", _agent) != -1) {
    return "safari";
  } else if ((_index("msie", _agent) != -1) && (_index("opera", _agent) == -1)) {
    return "ie";
  } else {
    return "firefox";
  }
}
function _json2string(_o) {
  var _res = "{";
  for (var _name in _o) {
    switch (typeof (_o[_name])) {
      case "string":
        _res += "\"" + _name + "\":\"" + _o[_name] + "\",";
        break;
      case "number":
        _res += "\"" + _name + "\":" + _o[_name] + ",";
        break;
      case "object":
        _res += "\"" + _name + "\":" + _json2string(_o[_name]) + ",";
        break;
    }
  }
  if (_res.length > 1)
    _res = _res.substring(0, _res.length - 1);
  _res += "}";
  if (_res == "{}")
    _res = "null";
  return _res;
}
var KoolPHP = {
  isDate: function(obj) {
    return Object.prototype.toString.call(obj) === "[object Date]";
  },
  isArray: function(obj) {
    return Object.prototype.toString.call(obj) === "[object Array]";
  },
  isObject: function(obj) {
    return Object.prototype.toString.call(obj) === "[object Object]";
  },
  isString: function(obj) {
    return typeof obj === 'string';
  },
  isNumber: function(value) {
    return typeof value === 'number' && isFinite(value);
  },
  hasClasses: function(element, classNames, p) {
    var flag = true;
    if (typeof element === 'string') {
      var names = classNames;
      if (this.isString(names))
        names = names.split(' ');
      if (!this.isArray(names))
        return false;
      for (var i in names) {
        var nameRegExp = new RegExp(
                '(\\s|^)' + names[ i ] + '(\\s|$)', 'i');
        flag = flag && nameRegExp.test(element);
      }
      return flag;
    } else {
      if (!p)
        p = 'className';
      if (element && this.isString(element[ p ]))
        flag = this.hasClasses(element[ p ], classNames);
      else
        flag = false;
    }
    return flag;
  },
  hasEitherClasses: function(element, classNames, p) {
    var flag = false;
    if (typeof element === 'string') {
      var names = classNames;
      if (this.isString(names))
        names = names.split(' ');
      if (!this.isArray(names))
        return false;
      for (var i in names) {
        var nameRegExp = new RegExp(
                '(\\s|^)' + names[ i ] + '(\\s|$)', 'i');
        flag = flag || nameRegExp.test(element);
      }
      return flag;
    } else {
      if (!p)
        p = 'className';
      if (element && this.isString(element[ p ]))
        flag = this.hasEitherClasses(element[ p ], classNames);
      else
        flag = false;
    }
    return flag;
  },
  addClasses: function(element, classNames, p) {
    var flag = element;
    if (typeof element === 'string') {
      var names = classNames;
      if (this.isString(names))
        names = names.split(' ');
      if (!this.isArray(names))
        return false;
      for (var i in names)
        if (!this.hasClasses(element, names[ i ]))
          flag += ' ' + names[ i ];
    } else if (element) {
      if (!p)
        p = 'className';
      if (!element[ p ])
        element[ p ] = '';
      flag[ p ] = this.addClasses(element[ p ], classNames);
    }
    return flag;
  },
  removeClasses: function(element, classNames, p) {
    var flag = element;
    if (typeof element === 'string') {
      var names = classNames;
      if (this.isString(names))
        names = names.split(' ');
      if (!this.isArray(names))
        return false;
      for (var i in names) {
        var nameRegExp = new RegExp(
                '(\\s|^)' + names[ i ] + '(\\s|$)', 'ig');
        flag = flag.replace(nameRegExp, '');
      }
    } else if (element) {
      if (!p)
        p = 'className';
      if (element[ p ])
        flag[ p ] = this.removeClasses(element[ p ], classNames);
    }
    return flag;
  }
};
var KoolUploaderFiles = {};
function KoolUploaderItem(_id, files, fileIds) {
  this._id = _id;
  this.id = _id;
  this.files = files;
  this.fileIds = fileIds;
}
KoolUploaderItem.prototype = {
  _extToType: {
    'pdf': 'pdf_2-24',
    'zip': 'zip_2-24',
    'rar': 'zip_2-24',
    '7z': 'zip_2-24',
    'xls': 'excel_2-24',
    'xlsx': 'excel_2-24',
    'jpg': 'image_2-24',
    'png': 'image_2-24',
    'gif': 'image_2-24',
    'txt': 'text_2-24',
    'doc': 'word_2-24',
    'docx': 'word_2-24',
    'ppt': 'powerpoint_2-24',
    'pptx': 'powerpoint_2-24'
  },
  _init: function () {
    var _kul = this._getkul();
    var _filename = this.getFileName();
    var _item = _obj(this._id);
    if (!_kul._isAllowed(_filename)) {
      var _span_status = _getElements("span", "kulStatus", _item)[0];
      _span_status.innerHTML = _obj(_kul._id + ".message.file_not_allowed").innerHTML;
      _addClass(_item, "kulNotAllowed");
      _addClass(_item, "kulDone");
      _addClass(_span_status, "kulError");
      _kul._handleEvent("OnUploadDone", {"ItemId": this._id});
    }
    if (_filename.lastIndexOf(".") > 0) {
      var _ext = _filename.substr(_filename.lastIndexOf(".") + 1).toLowerCase();
      var _span_thumbnail = _getElements("span", "kulThumbnail", _obj(this._id))[0];
      _addClass(_span_thumbnail, "kul" + _ext);
    }
    this._setItemHeight( );
  },
  _initCurrentFile: function ( ) {
    var _filename = this.getFileName();
    if (_filename.lastIndexOf(".") > 0) {
      var _ext = _filename.substr(_filename.lastIndexOf(".") + 1).toLowerCase();
      var _extCss = this._extToType[ _ext ] ? this._extToType[ _ext ] : 'file_2-24';
      var _span_thumbnail = _getElements("span", "kulThumbnail", _obj(this._id))[0];
      _addClass(_span_thumbnail, "kul" + _ext);
    }
    var _item = _obj(this._id);
    _addClass(_item, "kulDone");
  },
  _setItemHeight: function ( ) {
    var _item = _obj(this._id);
    var _h = 0;
    for (var i = 0; i < _item.children.length; i += 1) {
      if (_h < _item.children[ i ].offsetHeight)
        _h = _item.children[ i ].offsetHeight;
    }
    _item.style.height = _h + 'px';
  },
  _setLeftMostWidth: function ( ) {
    var _item = _obj(this._id);
    var _w = 0;
    var ie = msieversion();
    if (ie !== 0 && ie < 9)
      var _leftMostItem = getElementsByClassName(_item, 'kulLeftMost')[ 0 ];
    else  
      var _leftMostItem = _item.getElementsByClassName('kulLeftMost')[ 0 ];
    for (var i = 0; i < _leftMostItem.children.length; i += 1) {
      var _child = _leftMostItem.children[ i ];
      if (_w < _child.offsetWidth)
        _w = _child.offsetWidth;
    }
    var _div = document.createElement('div');
    _div.innerHTML = '&nbsp;';
    _div.style.width = _w + 'px';
    _div.style.height = '1px';
    _leftMostItem.appendChild(_div);
  },
  _getkul: function () {
    return eval(this._id.substring(0, _index(".", this._id)));
  },
  _setProgressBar: function (_percent) {
    var _span_progress = _getElements("span", "kulProgress", _obj(this._id))[0];
    var _img_progress_bar = _goFirstChild(_span_progress);
    _img_progress_bar.title = _percent + "%";
    var _max_length = _img_progress_bar.offsetWidth;
    _img_progress_bar.style.backgroundPosition = "-" + (_max_length * (100 - _percent) / 100) + "px";
  },
  setProgressText: function (_text) {
    var _span_progress = _getElements("span", "kulProgress", _obj(this._id))[0];
    var _span_progress_text = _span_progress.lastChild;
    _span_progress_text.innerHTML = _text;
  },
  attachData: function (_name, _value) {
    var _input = _obj(this._id + ".data");
    var _item = _obj(this._id);
    if (!_exist(_input)) {
      _input = document.createElement("input");
      _input.type = "hidden";
      _input.id = this._id + ".data";
      _input.value = "{}";
      _item.appendChild(_input);
    }
    var _data = eval("__=" + _input.value);
    _data[_name] = _value;
    _input.value = _json2string(_data);
  },
  upload: function () {
    if (this.getStatus() != "ready")
      return;
    if (!this._getkul()._handleEvent("OnBeforeUpload", {"ItemId": this._id}))
      return;
    var _kul = this._getkul();
    var _item = _obj(this._id);
    var _input_data = _obj(this._id + ".data");
    var _data = new Array();
    if (_exist(_input_data)) {
      _data = eval("__=" + _input_data.value);
    }
    try {
      var _iframe = document.createElement("<iframe id='" + this._id + ".iframe' name='" + this._id + ".iframe' >");
      _item.appendChild(_iframe);
    } catch (_ex) {
      var _iframe = document.createElement("iframe");
      _iframe.name = this._id + ".iframe";
      _iframe.id = this._id + ".iframe";
      _item.appendChild(_iframe);
    }
    try {
      var _form = document.createElement("<form id='" + this._id + ".form' method='post' enctype='multipart/form-data' action='" + _kul._handlePage_upload + "' target='" + this._id + ".iframe'>");
      _item.appendChild(_form);
    } catch (_ex) {
      var _form = document.createElement("form");
      _form.id = this._id + ".form";
      _form.enctype = "multipart/form-data";
      _form.method = "post";
      _form.action = _kul._handlePage_upload;
      _form.target = this._id + ".iframe";
      _item.appendChild(_form);
    }
    var _hidden_ITEM_ID = document.createElement("input");
    _hidden_ITEM_ID.type = "hidden";
    _hidden_ITEM_ID.name = "UPLOAD_IDENTIFIER";
      _hidden_ITEM_ID.value = this._id;
    _form.appendChild(_hidden_ITEM_ID);
    var _hidden_files_ID = document.createElement("input");
    _hidden_files_ID.type = "hidden";
    _hidden_files_ID.name = "fileIds";
    var _input_file_ids = _obj(this._id + ".inputFileIds");
    if (_input_file_ids && _input_file_ids.value) {
      var _itemIds = _input_file_ids.value.split(',');
      var _toUploadIds = [];
      for (var i = 0; i < _itemIds.length; i += 1) {
        var _uploadItem = new KoolUploaderItem(_itemIds[ i ]);
        if (_uploadItem.getStatus( ) === 'ready' 
             || _uploadItem.getStatus( ) === 'multiupload item'
             )
          _toUploadIds.push(_itemIds[ i ]);
      }
      _hidden_files_ID.value = _toUploadIds.join(',');
    } else
      _hidden_files_ID.value = this._id;
    _form.appendChild(_hidden_files_ID);
    var _hidden_MAX_FILE_SIZE = document.createElement("input");
    _hidden_MAX_FILE_SIZE.type = "hidden";
    _hidden_MAX_FILE_SIZE.name = "MAX_FILE_SIZE";
    _hidden_MAX_FILE_SIZE.value = _kul._maxFileSize;
    _form.appendChild(_hidden_MAX_FILE_SIZE);
    var _hidden_upload_progress = document.createElement("input");
    _hidden_upload_progress.type = "hidden";
    _hidden_upload_progress.name = window['upload_progress_name'];
    _hidden_upload_progress.value = this._id;
    _form.appendChild(_hidden_upload_progress);
    if (_kul._targetFolder !== '') {
      var _hidden_input = document.createElement("input");
      _hidden_input.type = "hidden";
      _hidden_input.name = 'targetFolder';
      _hidden_input.value = _kul._targetFolder;
      _form.appendChild(_hidden_input);
    }
    var _input_file = _obj(this._id + ".input");
    var _span_input_file = _goParentNode(_input_file);    
    _purge(_input_file);
    var files = this.getFiles();
    if (! files) {
      _input_file.id = "KUL_FILE";
      _input_file.name = "KUL_FILE[]";
      _form.appendChild(_input_file);
    }
    for (var _name in _data)
      if (typeof _data[_name] != "function") //Mootools
      {
        var _hidden_data = document.createElement("input");
        _hidden_data.type = "hidden";
        _hidden_data.name = _name;
        _hidden_data.value = _data[_name];
        _form.appendChild(_hidden_data);
      }
    if (_span_input_file)
      _goParentNode(_span_input_file).removeChild(_span_input_file);
    if (! files) {
      _form.submit();
    }
    else {
      var that = this;
      function reqListener (e) {
        var results = JSON.parse(this.responseText);
        kuldonemultiple(results);
        if (this.readyState === 4)
          that.delFiles();
      }
      var formData = new FormData(_form);
      for (var fi = 0; fi< files.length; fi+=1)
        formData.append('KUL_FILE[]', files[fi]);
      formData.append('xhr', true);
      var request = new XMLHttpRequest();
      request.open("POST", _kul._handlePage_upload);
      _addEvent(request, 'load', reqListener);
      request.send(formData);
    }
    _addClass(_item, "kulUploading");
    _kul._add_monitored_item(this._id);
    this._setProgressBar(0);
    this._getkul()._handleEvent("OnUpload", {"ItemId": this._id});
    if (_kul._progressTracking) {
      if (!_kul._handleEvent("OnBeforeUpdateProgress", {"ItemId": this._id}))
        return;
      this.setProgressText("0%");
      _kul._handleEvent("OnUpdateProgress", {"ItemId": this._id});
    }
  },
  remove: function () {
    var _kul = this._getkul();
    if (!_kul._handleEvent("OnBeforeRemove", {"ItemId": this._id}))
      return;
    this.cancel();//Cancel if uploading
    var _item = _obj(this._id);
    _purge(_item);
    try {
      var _span_item_input = _goParentNode(_obj(_item.id + ".input"));
      _goParentNode(_span_item_input).removeChild(_span_item_input);
    } catch (_ex) {
    }
    _goParentNode(_item).removeChild(_item);
    _kul._handleEvent("OnRemove", {"ItemId": this._id});
  },
  cancel: function () {
    if (this.getStatus() == "uploading") {
      var _kul = this._getkul();
      if (!_kul._handleEvent("OnBeforeCancel", {"ItemId": this._id}))
        return;
      var _item = _obj(this._id);
      _removeClass(_item, "kulUploading");
      _addClass(_item, "kulDone");
      var _span_status = _getElements("span", "kulStatus", _item)[0];
      _span_status.innerHTML = _obj(_kul._id + ".message.upload_cancel").innerHTML;
      var _form = _obj(this._id + ".form");
      var _iframe = _obj(this._id + ".iframe");
      _item.removeChild(_form);
      _item.removeChild(_iframe);
      _kul._remove_monitored_item(this._id);
      _kul._handleEvent("OnCancel", {"ItemId": this._id});
    }
  },
  deleteItem: function ( ) {
    if (this.getStatus( ) === 'uploaded') {
      var _kul = this._getkul();
      if (!_kul._allowDelete)
        return;
      if (!_kul._handleEvent("OnBeforeDelete", {"ItemId": this._id}))
        return;
      var _kul = this._getkul();
      var _item = _obj(this._id);
      try {
        var _iframe = document.createElement("<iframe id='" + this._id + ".iframe' name='" + this._id + ".iframe' >");
        _item.appendChild(_iframe);
      } catch (_ex) {
        var _iframe = document.createElement("iframe");
        _iframe.name = this._id + ".iframe";
        _iframe.id = this._id + ".iframe";
        _item.appendChild(_iframe);
      }
      try {
        var _form = document.createElement("<form id='" + this._id + ".form' method='post' enctype='multipart/form-data' action='" + _kul._handlePage_delete + "' target='" + this._id + ".iframe'>");
        _item.appendChild(_form);
      } catch (_ex) {
        var _form = document.createElement("form");
        _form.id = this._id + ".form";
        _form.enctype = "multipart/form-data";
        _form.method = "post";
        _form.action = _kul._handlePage_delete;
        _form.target = this._id + ".iframe";
        _item.appendChild(_form);
      }
      var _hidden_ITEM_ID = document.createElement("input");
      _hidden_ITEM_ID.type = "hidden";
      _hidden_ITEM_ID.name = "UPLOAD_IDENTIFIER";
      _hidden_ITEM_ID.value = this._id;
      _form.appendChild(_hidden_ITEM_ID);
      var _hidden_ITEM_ID = document.createElement("input");
      _hidden_ITEM_ID.type = "hidden";
      _hidden_ITEM_ID.name = "DELETE_IDENTIFIER";
      var _filename = this.getFileName();
      _hidden_ITEM_ID.value = _filename;
      _form.appendChild(_hidden_ITEM_ID);
      if (_kul._targetFolder !== '') {
        var _hidden_input = document.createElement("input");
        _hidden_input.type = "hidden";
        _hidden_input.name = 'targetFolder';
        _hidden_input.value = _kul._targetFolder;
        _form.appendChild(_hidden_input);
      }
      _form.submit();
      _kul._handleEvent("OnDelete", {"ItemId": this._id});
    }
  },
  download: function ( ) {
    if (this.getStatus( ) === 'uploaded') {
      var _kul = this._getkul();
      if (_kul && !_kul._allowDownload)
        return;
      if (!_kul._handleEvent("OnBeforeDownload", {"ItemId": this._id}))
        return;
      var _item = _obj(this._id);
      try {
        var _iframe = document.createElement("<iframe id='" + this._id + ".iframe' name='" + this._id + ".iframe' >");
        _item.appendChild(_iframe);
      } catch (_ex) {
        var _iframe = document.createElement("iframe");
        _iframe.name = this._id + ".iframe";
        _iframe.id = this._id + ".iframe";
        _item.appendChild(_iframe);
      }
      try {
        var _form = document.createElement("<form id='" + this._id + ".form' method='post' enctype='multipart/form-data' action='" + _kul._handlePage_download + "' target='" + this._id + ".iframe'>");
        _item.appendChild(_form);
      } catch (_ex) {
        var _form = document.createElement("form");
        _form.id = this._id + ".form";
        _form.enctype = "multipart/form-data";
        _form.method = "post";
        _form.action = _kul._handlePage_download;
        _form.target = this._id + ".iframe";
        _item.appendChild(_form);
      }
      var _hidden_ITEM_ID = document.createElement("input");
      _hidden_ITEM_ID.type = "hidden";
      _hidden_ITEM_ID.name = "UPLOAD_IDENTIFIER";
      _hidden_ITEM_ID.value = this._id;
      _form.appendChild(_hidden_ITEM_ID);
      var _hidden_ITEM_ID = document.createElement("input");
      _hidden_ITEM_ID.type = "hidden";
      _hidden_ITEM_ID.name = "DOWNLOAD_IDENTIFIER";
      var _filename = this.getFileName();
      _hidden_ITEM_ID.value = _filename;
      _form.appendChild(_hidden_ITEM_ID);
      if (_kul._targetFolder !== '') {
        var _hidden_input = document.createElement("input");
        _hidden_input.type = "hidden";
        _hidden_input.name = 'targetFolder';
        _hidden_input.value = _kul._targetFolder;
        _form.appendChild(_hidden_input);
      }
      _form.submit();
      _kul._handleEvent("OnDownload", {"ItemId": this._id});
    }
  },
  getFiles: function() {
    return KoolUploaderFiles[this._id];
  },
  delFiles: function() {
    delete KoolUploaderFiles[this._id];
  },
  getStatus: function () {
    var _item = _obj(this._id);
    var _class = _getClass(_item);
    if (_index("kulDone", _class) > 0) {
      var _span_status = _getElements("span", "kulStatus", _item)[0];
      if (_index("kulError", _getClass(_span_status)) > 0) {
        return "failed";
      } else {
        return "uploaded";
      }
    }
    if (_index("kulDeleted", _class) > 0) {
      return "deleted";
    } else if (_index("kulUploading", _class) > 0) {
      return "uploading";
    } else {
      var _input_file = _obj(this._id + ".input");
      if (!_input_file && !this.getFiles())
        return 'multiupload item';
      else
        return "ready";
    }
  },
  getDescription: function ( ) {
    var _item = _obj(this._id);
    var ie = msieversion();
    if (ie !== 0 && ie < 9)
      var kulDes = getElementsByClassName(_item, 'kulDescription');
    else
      var kulDes = _item.getElementsByClassName('kulDescription');
    if (kulDes.length > 0) {
      var _description = kulDes[ 0 ].value;
      return _description;
    } else
      return '';
  },
  getFileName: function () {
    var _span_filename = _getElements("span", "kulFileName", _obj(this._id))[0];
    return _span_filename.innerHTML;
  },
  getTotalBytes: function () {
    var _input_info = _obj(this._id + ".info");
    var _info = eval("__=" + _input_info.value);
    return parseInt(_info["bytes_total"]);
  },
  getUploadedBytes: function () {
    var _input_info = _obj(this._id + ".info");
    var _info = eval("__=" + _input_info.value);
    return parseInt(_info["bytes_uploaded"]);
  },
  getEstimatedTime: function () {
    return Math.round(this.getTotalBytes() / this.getAverageSpeed());
  },
  getElapsedTime: function () {
    var _input_info = _obj(this._id + ".info");
    var _info = eval("__=" + _input_info.value);
    return parseInt(_info["time_last"]) - parseInt(_info["time_start"]);
  },
  getEstimatedTimeLeft: function () {
    var _input_info = _obj(this._id + ".info");
    var _info = eval("__=" + _input_info.value);
    return parseInt(_info["est_sec"]);
  },
  getLastSpeed: function () {
    var _input_info = _obj(this._id + ".info");
    var _info = eval("__=" + _input_info.value);
    return _info["speed_last"];
  },
  getAverageSpeed: function () {
    var _input_info = _obj(this._id + ".info");
    var _info = eval("__=" + _input_info.value);
    return _info["speed_average"];
  },
  getUploadedPercent: function () {
    return this.getUploadedBytes() * 100 / this.getTotalBytes();
  },
  _handle_btnUpload_click: function (_e) {
    this.upload();
  },
  _handle_btnRemove_click: function (_e) {
    this.remove();
  },
  _handle_btnCancel_click: function (_e) {
    this.cancel();
  },
  _handle_btnDelete_click: function (_e) {
    this.deleteItem();
  },
  _handle_upload_done: function (_result, _info) {
    var _item = _obj(this._id);
    _removeClass(_item, "kulUploading");
    _addClass(_item, "kulDone");
    var _kulid = this._getkul()._id;
    var _span_status = _getElements("span", "kulStatus", _item)[0];
    _span_status.innerHTML = _obj(_kulid + ".message." + _result).innerHTML;
    if (_index("successful", _result) > 0) {
      var _input = _obj(_kulid + "_uploadedFiles");
      var _pattern = "[|-" + _info["name"] + "-|-" + _info["type"] + "-|-" + _info["size"] + "-|]";
      if (_index(_pattern, _input.value) < 0)
        _input.value += _pattern;
    } else {
      _addClass(_span_status, "kulError");
    }
    var _input_info = _obj(this._id + ".info");
    var _info = eval("__=" + _input_info.value);
    _info["bytes_uploaded"] = _info["bytes_total"];
    _input_info.value = _json2string(_info);
    var ie = msieversion();
    if (ie !== 0 && ie < 9)
      var _filenameSpan = getElementsByClassName(_item, 'kulFileName')[ 0 ];
    else
      var _filenameSpan = _item.getElementsByClassName('kulFileName')[ 0 ];
    var _kul = this._getkul();
    if (_kul && _kul._allowDownload && _filenameSpan) {
      _filenameSpan.style.color = '#189fee';
      _filenameSpan.style.cursor = 'pointer';
      var that = this;
      _addEvent(_filenameSpan, 'click', function () {
        that.download();
      }, false);
    }
    var _form = _obj(this._id + ".form");
    var _iframe = _obj(this._id + ".iframe");
    if (_form)
      _item.removeChild(_form);
    if (_iframe)
      _item.removeChild(_iframe);
    this._getkul()._remove_monitored_item(this._id);
    this._getkul()._handleEvent("OnUploadDone", {"ItemId": this._id});
  },
  _handle_delete_done: function (_result, _info) {
    var _item = _obj(this._id);
    var ie = msieversion();
    if (ie !== 0 && ie < 9)
      var _filenameSpan = getElementsByClassName(_item, 'kulFileName')[ 0 ];
    else
      var _filenameSpan = _item.getElementsByClassName('kulFileName')[ 0 ];
    if (_filenameSpan) {
      _filenameSpan.style.color = '';
      _filenameSpan.style.cursor = 'auto';
      _filenameSpan.onclick = "javascript: void 0;";
    }
    _removeClass(_item, 'kulDone');
    _addClass(_item, "kulDeleted");
    var _kul = this._getkul();
    var _kulid = _kul._id;
    var _span_status = _getElements("span", "kulStatus", _item)[0];
    _span_status.innerHTML = _obj(_kulid + ".message.deleted").innerHTML;
  },
  _handle_receive_progress: function (_info) {
    if (!_exist(_obj(this._id)))
      return;
    if (this.getStatus() == "uploading" && _exist(_info)) {
      var _input_info = _obj(this._id + ".info");
      _input_info.value = _json2string(_info);
			var _upload_percent = _kul._peclUploadProgress ? 
        Math.round(this.getUploadedPercent()) : _info.progress;
      this._setProgressBar(_upload_percent);
      if (!this._getkul()._handleEvent("OnBeforeUpdateProgress", {"ItemId": this._id}))
        return;
      this.setProgressText(_upload_percent + "%");
      this._getkul()._handleEvent("OnUpdateProgress", {"ItemId": this._id});
    }
  }
};
function setGetParameter(url, paramName, paramValue) {
  var newUrl = url;
  if (newUrl.indexOf(paramName + "=") >= 0) {
    var prefix = newUrl.substring(0, newUrl.indexOf(paramName));
    var suffix = newUrl.substring(newUrl.indexOf(paramName));
    suffix = suffix.substring(suffix.indexOf("=") + 1);
    suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
    newUrl = prefix + paramName + "=" + paramValue + suffix;
  } else {
    if (newUrl.indexOf("?") < 0)
      newUrl += "?" + paramName + '=' + paramValue;
    else
      newUrl += "&" + paramName + '=' + paramValue;
  }
  return newUrl;
}
function styleInit(id)
{
    var _div = document.getElementById(id);
    var _tbody = _div.firstChild.firstChild;
    var _first_td = _tbody.firstChild.firstChild;
    var _last_td = _tbody.lastChild.firstChild;
    var isNumber = function isNumber(value) {
        return typeof value === 'number' && isFinite(value);
    };
    var _last_td_height = parseInt(_last_td.style.height);
    _last_td_height = isNumber(_last_td_height) ? _last_td_height : 0; 
    var _div_height = parseInt(_div.style.height);
    _div_height = isNumber(_div_height) ? _div_height : 0;
    _first_td.style.height = (_div_height - _last_td_height) + "px";
}
function KoolUploader(_info) {
  with (_info) {
    styleInit(id);
    this._id = id;
    this.id = id;
    if (handlePage !== '') {
      this._handlePage_upload = setGetParameter(handlePage, upload, 1);
      this._handlePage_status = setGetParameter(handlePage, status, 1);
      this._handlePage_progress = setGetParameter(handlePage, progress, 1);
      this._handlePage_delete = setGetParameter(handlePage, del, 1);
      this._handlePage_download = setGetParameter(handlePage, download, 1);
    } else {
      var url = document.URL;
      this._handlePage_upload = setGetParameter(url, upload, 1);
      this._handlePage_status = setGetParameter(url, status, 1);
      this._handlePage_progress = setGetParameter(url, progress, 1);
      this._handlePage_delete = setGetParameter(url, del, 1);
      this._handlePage_download = setGetParameter(url, download, 1);
    }
    this._updateProgressInterval = updateProgressInterval;
    this._peclUploadProgress = peclUploadProgress;
    this._progressTracking = progressTracking;
    this._allowedExtension = allowedExtension;
    this._targetFolder = targetFolder;
    this._currentFiles = currentFiles;
    this._mustHaveFiles = mustHaveFiles;
    this._allowDelete = allowDelete;
    this._allowDownload = allowDownload;
    this._autoUpload = autoUpload;
    this._multipleUpload = multipleUpload;
    this._maxFileSize = maxFileSize;
    this._errorMessage1 = errorMessage1;
    this._errorMessage2 = errorMessage2;
    this._dragAndDrop = dragAndDrop;
    this._eventhandles = new Array();
    this._monitor_items = new Array();
    this._monitoring = false;
    this._last_request_time = (new Date()).getTime();
    this._init();
  }
}
KoolUploader.prototype = {
  _init: function () {
    var _input = _obj(this._id + "_uploadedFiles");
    _input.value = "";
    this._add_new_fileinput();
    var _btn_uploadall = _obj(this._id + ".btn.uploadall");
    if (_exist(_btn_uploadall)) {
      _addEvent(_btn_uploadall, "click", _btn_uploadall_click, false);
    }
    var _btn_clearall = _obj(this._id + ".btn.clearall");
    if (_exist(_btn_clearall)) {
      _addEvent(_btn_clearall, "click", _btn_clearall_click, false);
    }
    var _div = document.getElementById(this._id);
    var _tbody = _div.firstChild.firstChild;
    var _first_tr = _tbody.firstChild;
    var _first_td = _first_tr.firstChild;
    var _second_tr = _first_tr.nextSibling;
    var _second_td = _second_tr.firstChild;
    _first_td.style.height = (parseInt(_div.style.height) - parseInt(_second_td.style.height)) + "px";
    for (var i = 0; i < this._currentFiles.length; i += 1) {
      var _f = this._currentFiles[ i ];
      this._add_current_file(_f);
    }
    if (this._dragAndDrop) {
      var dropZone = _obj(this._id + '.container');
      _addEvent(dropZone, 'dragover', fileDraggedOverListener, false);
      _addEvent(dropZone, 'dragleave', fileDraggedLeaveListener, false);
      _addEvent(dropZone, 'drop', fileDroppedListener, false);
    }
  },
  getItems: function () {
    var _div_items = _getElements("div", "kulItem", _obj(this._id + ".container"));
    var _items = new Array();
    for (i in _div_items)
      if (typeof _div_items[i] != "function") //Mootools
      {
        _items.push(new KoolUploaderItem(_div_items[i].id));
      }
    return _items;
  },
  getItem: function (_itemid) {
    return (new KoolUploaderItem(_itemid));
  },
  getItemsStatus: function ( ) {
    var _itemsStatus = [];
    var _items = this.getItems( );
    var _descriptions = this.getAllDescriptions( );
    for (var i = 0; i < _items.length; i += 1) {
      var _item = _items[ i ];
      var _status = _item.getStatus( );
      if (_status === 'uploaded') {
        var _desc = _item.getDescription( );
        for (var j = 0; j < _descriptions.length; j += 1)
          if (_descriptions[ j ] === _desc) {
            _descriptions.splice(j, 1);
            break;
          }
        var _itemStatus = {
          name: _item.getFileName( ),
          description: _desc
        };
        _itemsStatus.push(_itemStatus);
      }
    }
    if (_descriptions.length === 0)
      return JSON.stringify(_itemsStatus);
    else {
      var _thisObj = _obj(this._id);
      var _table = _thisObj.firstChild;
      var ie = msieversion();
      if (ie !== 0 && ie < 9)
        var _tr = getElementsByClassName(_table, 'kulMessage')[ 0 ];
      else
        var _tr = _table.getElementsByClassName('kulMessage')[ 0 ];
      var _errorMsg = this._errorMessage1;
      _errorMsg += '<ul style="list-style-position:inside; color:red">';
      for (var i = 0; i < _descriptions.length; i += 1)
        _errorMsg += '<li>' + _descriptions[ i ] + '</li>';
      _errorMsg += '</ul>';
      _errorMsg += this._errorMessage2;
      var _increaseHeight = false;
      if (_tr.style.display === 'none') {
        _increaseHeight = true;
      }
      _tr.firstChild.innerHTML = _errorMsg;
      _tr.style.display = '';
      if (_increaseHeight)
        _thisObj.style.height = parseFloat(_thisObj.style.height) + _tr.offsetHeight + 'px';
      return false;
    }
  },
  getAllDescriptions: function ( ) {
    var _templateItem = _obj(this._id + ".template.item");
    var ie = msieversion();
    if (ie !== 0 && ie < 9)
      var _select = getElementsByClassName(_templateItem, 'kulDescription')[0];
    else
      var _select = _templateItem.getElementsByClassName('kulDescription')[0];
    var _descriptions = [];
    if (_select && _select.options) {
      var _options = _select.options;
      for (var i = 0; i < _options.length; i += 1)
        if (_options[ i ].value !== '')
          _descriptions.push(_options[ i ].value);
    }
    return _descriptions;
  },
  uploadAll: function () {
    if (!this._handleEvent("OnBeforeUploadAll", null))
      return;
    var _items = this.getItems();
    for (i in _items)
      if (typeof _items[i] != "function") //Mootools
        if (_items[i].getStatus() == "ready") {
          _items[i].upload();
        }
    this._handleEvent("OnUploadAll", null);
  },
  clearAll: function () {
    if (!this._handleEvent("OnBeforeClearAll", null))
      return;
    var _items = this.getItems();
    for (var i in _items)
      if (typeof _items[i] != "function") //Mootools
      {
        _items[i].remove();
      }
    this._handleEvent("OnClearAll", null);
  },
  _isAllowed: function (_filename) {
    if (this._allowedExtension === '*')
      return true;
    var _allows = this._allowedExtension.toLowerCase().split(",");
    for (var i in _allows)
      if (typeof _allows[i] != "function") //Mootools
      {
        var _reg_allow = eval("/(\." + _allows[i] + ")$/");
        if (_filename.toLowerCase().match(_reg_allow))
          return true;
      }
    return false;
  },
  _add_monitored_item: function (_itemid) {
    if (this._progressTracking) {
      var _jstring = "[" + this._monitor_items.join("][") + "]";
      if (_index("[" + _itemid + "]", _jstring) < 0) {
        this._monitor_items.push(_itemid);
      }
      if (!this._monitoring) {
        this._send_monitor_request();
      }
    }
  },
  _remove_monitored_item: function (_itemid) {
    var _newlist = new Array();
    for (i in this._monitor_items)
      if (typeof this._monitor_items[i] != "function") //Mootools
        if (this._monitor_items[i] != _itemid) {
          _newlist.push(this._monitor_items[i]);
        }
    this._monitor_items = _newlist;
  },
  _send_monitor_request: function () {
    var _request = new KoolAjaxRequest({
      method: "get",
      url: _kul._peclUploadProgress ? 
        _kul._handlePage_status : _kul._handlePage_progress,
      onDone: _monitor_data_done,
      onError: _monitor_data_error,
      _kulid: this._id
    });
    for (var i in this._monitor_items)
      if (typeof this._monitor_items[i] != "function") //Mootools
      {
        _request.addArg("itemids[]", this._monitor_items[i]);
      }
    koolajax.sendRequest(_request);
    this._monitoring = true;
    this._last_request_time = (new Date()).getTime();
  },
  SMR: function () //Send monitor request
  {
    this._send_monitor_request();
  },
  _receive_monitor_data: function (_data) {
    try {
      _data = eval("__=" + _data);
    }
    catch (ex) {
      _data = [];
    }
    for (var _itemid in _data)
      if (typeof _data[_itemid] != "function") //Mootools
      {
        (new KoolUploaderItem(_itemid))._handle_receive_progress(_data[_itemid]);
      }
    if (this._monitoring && (this._monitor_items.length > 0)) {
      var _time_now = (new Date()).getTime();
      if (_time_now - this._last_request_time > this._updateProgressInterval) {
        this._send_monitor_request();
      } else {
        setTimeout(this._id + ".SMR()", this._updateProgressInterval - (_time_now - this._last_request_time));
      }
    } else {
      this._monitoring = false;
    }
  },
  _handle_monitor_request_error: function (_code) {
    this._handleEvent("OnError", {"Error": _code});
  },
  _add_current_file: function (_f) {
    var _thisid = this._id;
    var _iden = _uuid(13, 16);
    var _div_item = _goFirstChild(_obj(_thisid + ".template.item")).cloneNode(true);
    _div_item.id = _thisid + ".item" + _iden;
    var _div_container = _obj(_thisid + ".container");
    _div_container.appendChild(_div_item);
    var _span_filename = _getElements("span", "kulFileName", _div_item)[0];
    var _btn_upload = _getElements("span", "kulUpload", _div_item)[0];
    var _btn_remove = _getElements("span", "kulRemove", _div_item)[0];
    var _btn_cancel = _getElements("span", "kulCancel", _div_item)[0];
    var _btn_delete = _getElements("span", "kulDelete", _div_item)[0];
    _btn_upload.id = _div_item.id + ".btn.upload";
    _btn_remove.id = _div_item.id + ".btn.remove";
    _btn_cancel.id = _div_item.id + ".btn.cancel";
    if (_btn_delete)
      _btn_delete.id = _div_item.id + ".btn.delete";
    if (_btn_delete && !this._allowDelete) {
      _btn_delete.parentNode.removeChild(_btn_delete);
    }
    _span_filename.innerHTML = _f.name;
    _addEvent(_btn_delete, "click", _item_btn_delete_click, false);
    var ie = msieversion();
    if (ie !== 0 && ie < 9)
      var _description_input = getElementsByClassName(_div_item, 'kulDescription')[ 0 ];
    else
      var _description_input = _div_item.getElementsByClassName('kulDescription')[ 0 ];
    if (_description_input) {
      _description_input.value = _f.description;
    }
    var _uploadItem = new KoolUploaderItem(_div_item.id);
    _uploadItem._initCurrentFile( );
    if (this._allowDownload) {
      _span_filename.style.color = '#189fee';
      _span_filename.style.cursor = 'pointer';
    }
    var that = _uploadItem;
    _addEvent(_span_filename, 'click', function () {
      that.download();
    }, false);
  },
  _add_new_fileinput: function () {
    var _btnAdd = _obj(this._id + ".btn.add");
    var _spanFileInput = _newNode("span", _btnAdd);
    var _inputfile = document.createElement("input");
    _inputfile.type = "file";
    _inputfile.id = this._id + ".new.input";
    _inputfile.name = this._id + ".new.input";
    var extensions = this._allowedExtension.split(',');
    for (var i in extensions)
      extensions[i] = '.' + extensions[i];
    extensions = extensions.join(',');
    _inputfile.setAttribute('accept', extensions);
    if (this._multipleUpload)
      _inputfile.setAttribute('multiple', '');
    _spanFileInput.appendChild(_inputfile);
    _setClass(_spanFileInput, "kulFileInput");
    _addEvent(_inputfile, "mouseover", _fileinput_mouseover, false);
    _addEvent(_inputfile, "mouseout", _fileinput_mouseout, false);
    _addEvent(_inputfile, "change", _fileinput_change, false);
  },
  _handle_file_add: function (files) {
    var _thisid = this._id;
    var _newfileinput = _obj(_thisid + ".new.input");
    if (_newfileinput.value == "" && ! files)
      return;
    var _inputFileIds = [];
    var ie = msieversion();
    if (! files) {
      if (ie !==0 && ie < 10) {
        var _filename=_newfileinput.value.replace(/\\/g,"/");
		_filename = _filename.substring(_filename.lastIndexOf("/")+1,_filename.length);	
        _newfileinput.name = _filename;
        files = [_newfileinput];
      }
      else
        files = _newfileinput.files;
    }
    for (var i = 0; i < files.length; i += 1) {
      var _filename = files[ i ].name;
      if (!this._handleEvent("OnBeforeAddItem", {"FileName": _filename}))
        return;
      var _iden = _uuid(13, 16);
      _newfileinput.id = _thisid + ".item" + _iden + ".input";
      _newfileinput.name = _thisid + ".item" + _iden + ".input";
      var _div_item = _goFirstChild(_obj(_thisid + ".template.item")).cloneNode(true);
      _div_item.id = _thisid + ".item" + _iden;
      _inputFileIds.push(_div_item.id);
      var _div_container = _obj(_thisid + ".container");
      _div_container.appendChild(_div_item);
      var _span_filename = _getElements("span", "kulFileName", _div_item)[0];
      var _btn_upload = _getElements("span", "kulUpload", _div_item)[0];
      var _btn_remove = _getElements("span", "kulRemove", _div_item)[0];
      var _btn_cancel = _getElements("span", "kulCancel", _div_item)[0];
      var _btn_delete = _getElements("span", "kulDelete", _div_item)[0];
      _btn_upload.id = _div_item.id + ".btn.upload";
      _btn_remove.id = _div_item.id + ".btn.remove";
      _btn_cancel.id = _div_item.id + ".btn.cancel";
      _btn_delete.id = _div_item.id + ".btn.delete";
      if (!this._allowDelete)
        _btn_delete.parentNode.removeChild(_btn_delete);
      _span_filename.innerHTML = _filename;
      _addEvent(_div_item, "click", _item_click, false);
      _addEvent(_btn_upload, "click", _item_btn_upload_click, false);
      _addEvent(_btn_remove, "click", _item_btn_remove_click, false);
      _addEvent(_btn_cancel, "click", _item_btn_cancel_click, false);
      _addEvent(_btn_delete, "click", _item_btn_delete_click, false);
      if (i < files.length - 1) {
        _btn_upload.style.visibility = 'hidden';
        _btn_remove.style.visibility = 'hidden';
        _btn_cancel.style.visibility = 'hidden';
      }
      var _input_info = document.createElement("input");
      _input_info.type = "hidden";
      _input_info.id = _div_item.id + ".info";
      _div_item.appendChild(_input_info);
      _input_info.value = "{'time_start':'0','time_last':'1','speed_average':'1','speed_last':'1','bytes_uploaded' :'0','bytes_total':'1','files_uploaded':'1','est_sec':'0'}";
      if (i === files.length - 1) {
        var _input_file_ids = document.createElement("input");
        _input_file_ids.type = "hidden";
        _input_file_ids.id = _div_item.id + ".inputFileIds";
        _div_item.appendChild(_input_file_ids);
        _input_file_ids.value = _inputFileIds.join(',');
        if ((ie === 0 || ie > 9) && _newfileinput.files && _newfileinput.files.length === 0) {
          KoolUploaderFiles[_div_item.id] = files;
        }
      }
      var _item = new KoolUploaderItem(_div_item.id);
      _item._init();
      this._handleEvent("OnAddItem", {"ItemId": _div_item.id});
      this._moveToEnd( );
    }
    this._add_new_fileinput();
    if (this._autoUpload)
      _item.upload( );
  },
  _handle_uploadall_click: function (_e) {
    this.uploadAll();
  },
  _handle_clearall_click: function (_e) {
    this.clearAll();
  },
  registerEvent: function (_eventname, _handleFunction) {
    this._eventhandles[_eventname] = _handleFunction;
  },
  _handleEvent: function (_name, _arg) {
    return (_exist(this._eventhandles[_name])) ? this._eventhandles[_name](this, _arg) : true;
  },
  _moveToEnd: function ( ) {
    var _container = _obj(this._id + '.container');
    var _height = _container.scrollHeight, _step = 25, i = 0;
    var _scroll = function ( ) {
      _container.scrollTop += _height / _step;
      i += 1;
      if (i < _step && _container.scrollTop < _height)
        setTimeout(_scroll, 10);
    };
    setTimeout(_scroll, 10);
  }
};
function _getkul(_partid) {
  return eval(_partid.substring(0, _index(".", _partid)));
}
var kuldone = function (_id, _result, _info) {
  (new KoolUploaderItem(_id))._handle_upload_done(_result, _info);
};
var kuldonemultiple = function (_infos) {
  for (var i = 0; i < _infos.length; i += 1) {
    var _info = _infos[ i ];
    var _item = new KoolUploaderItem(_info.id);
    _item._handle_upload_done(_info.result, _info);
  }
};
var kuldeletedone = function (_id, _result, _info) {
  (new KoolUploaderItem(_id))._handle_delete_done(_result, _info);
};
function _fileinput_mouseover(_e) {
  var _btnAdd = _goParentNode(this, 2);
  _addClass(_btnAdd, "kulAddOver");
}
function _fileinput_mouseout(_e) {
  var _btnAdd = _goParentNode(this, 2);
  _removeClass(_btnAdd, "kulAddOver");
}
function _fileinput_change(_e) {
  _kul = _getkul(this.id);
  _kul._handle_file_add();
}
function _item_click(_e) {
  var _div_items = _getElements("div", "kulItem", _goParentNode(this));
  for (var i in _div_items)
    if (typeof _div_items[i] != "function") //Mootools
    {
      _removeClass(_div_items[i], "kulSelected");
    }
  _addClass(this, "kulSelected");
}
function _btn_clearall_click(_e) {
  _kul = _getkul(this.id);
  _kul._handle_clearall_click(_e);
}
function _btn_uploadall_click(_e) {
  _kul = _getkul(this.id);
  _kul._handle_uploadall_click(_e);
}
function _item_btn_upload_click(_e) {
  _kul_item = new KoolUploaderItem(this.id.replace(".btn.upload", ""));
  _kul_item._handle_btnUpload_click(_e);
}
function _item_btn_remove_click(_e) {
  var _kul_item = new KoolUploaderItem(this.id.replace(".btn.remove", ""));
  var _input_file_ids = _obj(_kul_item._id + ".inputFileIds");
  if (_input_file_ids && _input_file_ids.value) {
    var _file_ids = _input_file_ids.value.split(',');
    for (var i = 0; i < _file_ids.length; i += 1) {
      var _item = new KoolUploaderItem(_file_ids[ i ]);
      _item._handle_btnRemove_click(_e);
    }
  } else {
    _kul_item._handle_btnRemove_click(_e);
  }
  _stopPropagation(_e);
}
function _item_btn_cancel_click(_e) {
  _kul_item = new KoolUploaderItem(this.id.replace(".btn.cancel", ""));
  _kul_item._handle_btnCancel_click(_e);
}
function _item_btn_delete_click(_e) {
  _kul_item = new KoolUploaderItem(this.id.replace(".btn.delete", ""));
  _kul_item._handle_btnDelete_click(_e);
}
function fileDraggedOverListener(e) {
  e.stopPropagation();
  e.preventDefault();
  var kulContainer = e.currentTarget;
  KoolPHP.addClasses(kulContainer.parentNode, 'fileDragOver');
}
function fileDraggedLeaveListener(e) {
  var kulContainer = e.currentTarget;
  KoolPHP.removeClasses(kulContainer.parentNode, 'fileDragOver');
}
function fileDroppedListener(e) {
  e.stopPropagation();
  e.preventDefault();
  var kulContainer = e.currentTarget;
  KoolPHP.removeClasses(kulContainer.parentNode, 'fileDragOver');
  var kulId = kulContainer.id.replace('.container', '');
  var kul = window[kulId];
  var files = e.target.files || e.dataTransfer.files;
  kul._handle_file_add(files);
}
function _monitor_data_done(_data) {
  _kul = eval("__=" + this._kulid);
  _kul._receive_monitor_data(_data);
}
function _monitor_data_error(_code) {
  _kul = eval("__=" + this._kulid);
  _kul._handle_monitor_request_error(_code);
}
