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
function _index(_search,_original)
{
	return _original.indexOf(_search);
}
function _replace(_search,_rep,_str)
{
	return _str.replace(eval("/"+_search+"/g"),_rep);
}
function _setAttribute(_o,_name,_value)
{
	_o.setAttribute(_name,_value);
}

function _newNode(_sTag,_oParent)
{
    var _oNode = document.createElement(_sTag);
    _oParent.appendChild(_oNode);
    return _oNode;
}
function _newSVGNode(_sTag,_oParent)
{
    var _oNode = document.createElementNS("http://www.w3.org/2000/svg",_sTag);
    _oParent.appendChild(_oNode);
    return _oNode;
}
function _getClass(_theObj)
{
    return _theObj.className;
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
function _goParentNode(_theObj,_level)
{
	if(!_exist(_level)) _level=1;
    for(var i=0;i<_level;i++)
        _theObj = _theObj.parentNode;
    return _theObj;
}
//-----------------------------------------------------//
//-----------------------------------------------------//




function _addText(_content,_parent)
{
	var _text = _newSVGNode("text",_parent);
	var _textnode = document.createTextNode(_content);
	_text.appendChild(_textnode);
	return _text;
}

function _addRec(_x1,_y1,_x2,_y2,_parent)
{
	//var _path = _newSVGNode("path", _parent);
	//_setAttribute(_path,"d","M "+_x1+" "+_y1+" L "+_x2+" "+_y1+" L "+_x2+" "+_y2+" L "+_x1+" "+_y2+" z");
	//_setAttribute(_path,"stroke-linejoin","round");
	//_setAttribute(_path,"stroke-linecap","square");
	//return _path;
	var _width = Math.abs(_x2-_x1);
	var _height = Math.abs(_y2-_y1);
	var _x = (_x1<_x2)?_x1:_x2;
	var _y = (_y1<_y2)?_y1:_y2;	
	var _rec = _newSVGNode("rect", _parent);
	_setAttribute(_rec,"stroke-linejoin","round");
	_setAttribute(_rec,"stroke-linecap","square");
	_setAttribute(_rec,"x",_x);
	_setAttribute(_rec,"y",_y);
	_setAttribute(_rec,"width",_width);
	_setAttribute(_rec,"height",_height);
	return _rec;
}
function _setRec(_path,_x1,_y1,_x2,_y2)
{
	//_setAttribute(_path,"d","M "+_x1+" "+_y1+" L "+_x2+" "+_y1+" L "+_x2+" "+_y2+" L "+_x1+" "+_y2+" z");	
	var _width = Math.abs(_x2-_x1);
	var _height = Math.abs(_y2-_y1);
	var _x = (_x1<_x2)?_x1:_x2;
	var _y = (_y1<_y2)?_y1:_y2;	
	_setAttribute(_path,"x",_x);
	_setAttribute(_path,"y",_y);
	_setAttribute(_path,"width",_width);
	_setAttribute(_path,"height",_height);
}

function _animate(_object,_attribute,_from,_to,_dur,_begin,_onbegin,_onend)
{
	var _anim = _newSVGNode("animate",_object);
	//_anim = document.createElementNS("http://www.w3.org/2000/svg","animate");
	_setAttribute(_anim,"attributeName",_attribute);
	_setAttribute(_anim,"dur",_dur);
	_setAttribute(_anim,"from",_from);
	_setAttribute(_anim,"to",_to);
	if(_begin)_setAttribute(_anim,"begin",_begin);
	
	if(_onbegin) _addEvent(_anim,"begin",_onbegin,false);
	if(_onend) _addEvent(_anim,"end",_onend, false);
	return _anim;
}
function _animateTransform(_object,_type,_values,_splines,_dur,_begin,_onbegin,_onend)
{
	var _anim = _newSVGNode("animateTransform",_object);
	_setAttribute(_anim,"attributeName","transform");
	_setAttribute(_anim,"type",_type);
	_setAttribute(_anim,"dur",_dur);	
	_setAttribute(_anim,"calcMode","spline");
	_setAttribute(_anim,"values",_values);
	_setAttribute(_anim,"keySplines",_splines);
	if(_begin)_setAttribute(_anim,"begin",_begin);
	if(_onbegin) _addEvent(_anim,"begin",_onbegin,false);
	if(_onend) _addEvent(_anim,"end",_onend, false);
	
	return _anim;
}


function _addLine(_x1,_y1,_x2,_y2,_parent)
{
	/*var _path = _newSVGNode("path", _parent);
	_setAttribute(_path,"d","M"+_x1+" "+_y1+" "+_x2+" "+_y2);
	_setAttribute(_path,"stroke-linejoin","round");
	_setAttribute(_path,"stroke-linecap","square");
	return _path;
	*/
	
	var _line = _newSVGNode("line",_parent);
	_setAttribute(_line,"x1",_x1);
	_setAttribute(_line,"y1",_y1);
	_setAttribute(_line,"x2",_x2);
	_setAttribute(_line,"y2",_y2);
	_setAttribute(_line,"stroke-linejoin","round");
	_setAttribute(_line,"stroke-linecap","square");
	return _line;	
}
function _addCircle(_x,_y,_radius,_parent)
{
	var _circle = _newSVGNode("circle", _parent);
	_setAttribute(_circle,"cx",_x);
	_setAttribute(_circle,"cy",_y);
	_setAttribute(_circle,"r",_radius);
	return _circle;
}
function _get_triagles_d(_x,_y,_length)
{
	var _x1 = _x-_length/2;
	var _x2 = _x+_length/2;
	var _y1 = _y+_length*Math.sqrt(3)/2;
	var _y2=_y1;
	return "M"+_x+" "+_y+" L"+_x1+" "+_y1+" L"+_x2+" "+_y2+" z";
}
function _addTriangles(_x,_y,_length,_parent)
{
	var _path = _newSVGNode("path",_parent);
	_setAttribute(_path,"stroke-linejoin","round");
	_setAttribute(_path,"stroke-linecap","square");	
	_setAttribute(_path,"d",_get_triagles_d(_x,_y,_length));
	return _path;	
}



function _get_arc_d(_cx,_cy,_radius,_start_angle,_degree)
{
	var _start_angle_rad = (_start_angle*Math.PI/180);
	var _degree_rad = (_degree*Math.PI/180);
	
	var _start_point_x = _cx + _radius*Math.cos(_start_angle_rad);
	var _start_point_y = _cy + _radius*Math.sin(_start_angle_rad);
	
	var _end_point_x = _cx + _radius*Math.cos(_start_angle_rad+_degree_rad);
	var _end_point_y = _cy + _radius*Math.sin(_start_angle_rad+_degree_rad);

	return "M"+_cx+","+_cy+" L "+_start_point_x+","+_start_point_y+" A"+_radius+","+_radius+" 0 "+((_degree>180)?1:0)+",1 "+_end_point_x+","+_end_point_y+" z";	
}

function _addArc(_cx,_cy,_radius,_start_angle,_degree,_parent)
{
	var _path = _newSVGNode("path",_parent);
	_setAttribute(_path,"stroke-linejoin","round");
	_setAttribute(_path,"stroke-linecap","square");
	_setAttribute(_path,"d",_get_arc_d(_cx,_cy,_radius,_start_angle,_degree));
	return _path;
}

function _get_ourAngles_d(x1,y1,x2,y2,x3,y3,x4,y4)
{
	return "M"+x1+" "+y1+" L"+x2+" "+y2+" L"+x2+" "+y2+" L"+x3+" "+y3+" L"+x4+" "+y4+" z";
}

function _addFourAngles(x1,y1,x2,y2,x3,y3,x4,y4,_parent)
{
	var _path = _newSVGNode("path",_parent);
	_setAttribute(_path,"stroke-linejoin","round");
	_setAttribute(_path,"stroke-linecap","square");
	_setAttribute(_path,"d",_get_ourAngles_d(x1,y1,x2,y2,x3,y3,x4,y4));
	return _path;	
}


function _colorNameToHex(colour)
{
    var colours = {"aliceblue":"#f0f8ff","antiquewhite":"#faebd7","aqua":"#00ffff","aquamarine":"#7fffd4","azure":"#f0ffff",
    "beige":"#f5f5dc","bisque":"#ffe4c4","black":"#000000","blanchedalmond":"#ffebcd","blue":"#0000ff","blueviolet":"#8a2be2","brown":"#a52a2a","burlywood":"#deb887",
    "cadetblue":"#5f9ea0","chartreuse":"#7fff00","chocolate":"#d2691e","coral":"#ff7f50","cornflowerblue":"#6495ed","cornsilk":"#fff8dc","crimson":"#dc143c","cyan":"#00ffff",
    "darkblue":"#00008b","darkcyan":"#008b8b","darkgoldenrod":"#b8860b","darkgray":"#a9a9a9","darkgreen":"#006400","darkkhaki":"#bdb76b","darkmagenta":"#8b008b","darkolivegreen":"#556b2f",
    "darkorange":"#ff8c00","darkorchid":"#9932cc","darkred":"#8b0000","darksalmon":"#e9967a","darkseagreen":"#8fbc8f","darkslateblue":"#483d8b","darkslategray":"#2f4f4f","darkturquoise":"#00ced1",
    "darkviolet":"#9400d3","deeppink":"#ff1493","deepskyblue":"#00bfff","dimgray":"#696969","dodgerblue":"#1e90ff",
    "firebrick":"#b22222","floralwhite":"#fffaf0","forestgreen":"#228b22","fuchsia":"#ff00ff",
    "gainsboro":"#dcdcdc","ghostwhite":"#f8f8ff","gold":"#ffd700","goldenrod":"#daa520","gray":"#808080","green":"#008000","greenyellow":"#adff2f",
    "honeydew":"#f0fff0","hotpink":"#ff69b4",
    "indianred ":"#cd5c5c","indigo ":"#4b0082","ivory":"#fffff0","khaki":"#f0e68c",
    "lavender":"#e6e6fa","lavenderblush":"#fff0f5","lawngreen":"#7cfc00","lemonchiffon":"#fffacd","lightblue":"#add8e6","lightcoral":"#f08080","lightcyan":"#e0ffff","lightgoldenrodyellow":"#fafad2",
    "lightgrey":"#d3d3d3","lightgreen":"#90ee90","lightpink":"#ffb6c1","lightsalmon":"#ffa07a","lightseagreen":"#20b2aa","lightskyblue":"#87cefa","lightslategray":"#778899","lightsteelblue":"#b0c4de",
    "lightyellow":"#ffffe0","lime":"#00ff00","limegreen":"#32cd32","linen":"#faf0e6",
    "magenta":"#ff00ff","maroon":"#800000","mediumaquamarine":"#66cdaa","mediumblue":"#0000cd","mediumorchid":"#ba55d3","mediumpurple":"#9370d8","mediumseagreen":"#3cb371","mediumslateblue":"#7b68ee",
    "mediumspringgreen":"#00fa9a","mediumturquoise":"#48d1cc","mediumvioletred":"#c71585","midnightblue":"#191970","mintcream":"#f5fffa","mistyrose":"#ffe4e1","moccasin":"#ffe4b5",
    "navajowhite":"#ffdead","navy":"#000080",
    "oldlace":"#fdf5e6","olive":"#808000","olivedrab":"#6b8e23","orange":"#ffa500","orangered":"#ff4500","orchid":"#da70d6",
    "palegoldenrod":"#eee8aa","palegreen":"#98fb98","paleturquoise":"#afeeee","palevioletred":"#d87093","papayawhip":"#ffefd5","peachpuff":"#ffdab9","peru":"#cd853f","pink":"#ffc0cb","plum":"#dda0dd","powderblue":"#b0e0e6","purple":"#800080",
    "red":"#ff0000","rosybrown":"#bc8f8f","royalblue":"#4169e1",
    "saddlebrown":"#8b4513","salmon":"#fa8072","sandybrown":"#f4a460","seagreen":"#2e8b57","seashell":"#fff5ee","sienna":"#a0522d","silver":"#c0c0c0","skyblue":"#87ceeb","slateblue":"#6a5acd","slategray":"#708090","snow":"#fffafa","springgreen":"#00ff7f","steelblue":"#4682b4",
    "tan":"#d2b48c","teal":"#008080","thistle":"#d8bfd8","tomato":"#ff6347","turquoise":"#40e0d0",
    "violet":"#ee82ee",
    "wheat":"#f5deb3","white":"#ffffff","whitesmoke":"#f5f5f5",
    "yellow":"#ffff00","yellowgreen":"#9acd32"};

    if (typeof colours[colour.toLowerCase()] != 'undefined')
    	return colours[colour.toLowerCase()];

    return false;
}
function _hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? [
        parseInt(result[1], 16),
        parseInt(result[2], 16),
        parseInt(result[3], 16)
    ] : null;
}
function _rgbToHex(r, g, b) {
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}
function _brightness(_color,_change)
{
	if(_index("#",_color)<0)
	{
		_color = _colorNameToHex(_color);
		if(!_color) alert("Color is unknown");
	}
	var _rgb = _hexToRgb(_color);
	for(var i=0;i<3;i++)
	{
		_rgb[i] =  Math.round(_rgb[i]*_change);
	}
	return _rgbToHex(_rgb[0],_rgb[1],_rgb[2]); 
}

function _setPosition(_o,_x,_y)
{
	_setAttribute(_o,"x",_x);
	_setAttribute(_o,"y",_y);	
}
function _fill(_o,_color)
{
	_setAttribute(_o,"fill",_color);
}
function _stroke(_o,_color,_width,_opacity,_fill,_fill_opacity)
{
	if(_width==null) _width=1;
	if(_opacity==null) _opacity=1;
	if(_fill==null) _fill="none";
	if(_fill_opacity==null) _fill_opacity=1;
	
	
	_setAttribute(_o,"stroke",_color);
	_setAttribute(_o,"stroke-width",_width);
	_setAttribute(_o,"stroke-opacity",_opacity);
	_setAttribute(_o,"fill",_fill);
	_setAttribute(_o,"fill-opacity",_fill_opacity);
	
}
function _style(_o,_s)
{
	_setAttribute(_o,"style",_s);
}
function _translate(_o,_x,_y,_clear)
{
	var _transform = _o.getAttribute("transform");
	if(!_transform||_clear!=null) _transform="";
	_transform+=" translate("+_x+","+_y+")";
	_setAttribute(_o,"transform",_transform);
}
function _rotate(_o,_degree)
{
	var _transform = _o.getAttribute("transform");
	if(!_transform) _transform="";
	_transform+=" rotate("+_degree+")";
	_setAttribute(_o,"transform",_transform);
}


function pie_end_animtation(_chart)
{
	_chart._end_animation = true;
}

function _hidden(_object,_second)
{
	_setAttribute(_object,"visibility","hidden");
	_object.id = "chartobject_"+ _getIdentity();
	setTimeout("visible_back('"+_object.id+"')",(_second+0.1)*1000);
}
function visible_back(_object_name)
{
	_setAttribute(_obj(_object_name),"visibility","visible");
}

function _supportsSvg() {
    return (document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Shape", "1.0")||document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Shape", "1.1"));
}


function KoolChart(_id)
{
	this._id = _id;
	this._init();
}
KoolChart.prototype = 
{
	_init:function()
	{
		//alert(_supportsSvg());

		var _this = _obj(this._id);
		var _settings = this._loadSettings();
		var _svg = _newSVGNode("svg",_this);
		_setAttribute(_svg,"style","position: relative; display: block;");
		_setAttribute(_svg,"version","1.1");
		_setAttribute(_svg,"xmlns","http://www.w3.org/2000/svg");		
		_setAttribute(_svg,"width",_settings["Width"]);
		_setAttribute(_svg,"height",_settings["Height"]);
		_setAttribute(_svg,"id",this._id+"_canvas");


		//_addEvent(_this,"mouseout",_out,false);
		
		//fill gradient
		var _defs = _newSVGNode("defs",_svg);
		_defs.id=this._id+"_defs";

		
		//whole area
		var _area = _addRec(0,0,_settings["Width"],_settings["Height"],_svg);
		//_fill(_area,_settings["PlotArea"]["Appearance"]["BackgroundColor"]);
		_fill(_area,"#ffffff");

		
		
		this._padding_left=_settings["Padding"];
		this._padding_top=_settings["Padding"];
		this._padding_right=_settings["Padding"];
		this._padding_bottom=_settings["Padding"];

		//Title		
		this._create_title(_svg,_settings);
		this._create_legend(_svg,_settings);
		
		switch(_settings["PlotArea"]["ListOfSeries"][0]["ChartType"])
		{
			case "Bar":
				this._create_bar_plotarea(_svg,_settings);	
				break;
			case "Column":
			case "Line":
			case "Area":			
				this._create_column_line_area_plotarea(_svg,_settings);	
				break;
			case "Pie":
				this._create_pie_plotarea(_svg,_settings);	
				break;				
			case "Scatter":
				this._create_scatter_plotarea(_svg,_settings);	
				break;						
		}
	},
	_create_title:function(_svg,_settings)
	{
		if (_settings["Title"]["Appearance"]["Visible"])
		{
			var _g = _newSVGNode("g",_svg);
			_g.id=this._id+"_title";
			
			var _panel = _addRec(0,0,0,0,_g);
			_fill(_panel,_settings["Title"]["Appearance"]["BackgroundColor"]);
	
			var _text = _addText(_settings["Title"]["Text"],_g);
			_style(_text,"font-size: "+_settings["Title"]["Appearance"]["FontSize"]+";font-family:"+_settings["Title"]["Appearance"]["FontFamily"]+";");
			_fill(_text,_settings["Title"]["Appearance"]["FontColor"]);
	
			_setRec(_panel,_g.getBBox().x-5,_g.getBBox().y-5,_g.getBBox().x+_g.getBBox().width+5,_g.getBBox().y+_g.getBBox().height+5);		
	
			var _area_width = _settings["Width"];
			var _area_height = _settings["Height"];
			var _title_width = _g.getBBox().width;
			var _title_height = _g.getBBox().height;
			
			
			switch(_settings["Title"]["Appearance"]["Align"])
			{
				case "left":
						var _title_x = this._padding_left;
					break;
				case "right":
						var _title_x = _area_width - this._padding_right - _title_width;
					break;
				case "center":
				default:
						var _title_x = (_area_width-_title_width)/2;
					break;
			}	
			
			switch(_settings["Title"]["Appearance"]["Position"])
			{
				case "top":
					var _title_y = this._padding_top;
					this._padding_top+=_title_height+10;
					break;
				case "bottom":
				default:
					var _title_y = _area_height - this._padding_bottom - _title_height;
					this._padding_bottom+=_title_height+10;
					break;
			}
			_translate(_g,_title_x-_g.getBBox().x,_title_y-_g.getBBox().y);
			
		}
	},
	_create_legend:function(_svg,_settings)
	{
		if(_settings["Legend"]["Appearance"]["Visible"])
		{
			var _g = _newSVGNode("g",_svg);
			_g.id = this._id+"_legend";
			var _listofseries = _settings["PlotArea"]["ListOfSeries"];
			//Data preparation
			var _data=[];
			if(_listofseries[0]["ChartType"]=="Pie")
			{
				for(var i=0;i<_listofseries[0]["Items"].length;i++)
				{
					_data.push({"BackgroundColor":_listofseries[0]["Items"][i]["BackgroundColor"],"Name":_listofseries[0]["Items"][i]["Name"]});
				}
			}
			else if(_listofseries[0]["ChartType"]=="Bar")
			{
				for(var i=0;i<_listofseries.length;i++)
					if(_listofseries[i]["ChartType"]=="Bar")
					{
						_data.push({"BackgroundColor":_listofseries[i]["Appearance"]["BackgroundColor"],"Name":_listofseries[i]["Name"]});				
					}
			}
			else if(_listofseries[0]["ChartType"]=="Column"||_listofseries[0]["ChartType"]=="Line"||_listofseries[0]["ChartType"]=="Area")
			{
				for(var i=0;i<_listofseries.length;i++)
					if(_listofseries[i]["ChartType"]=="Column"||_listofseries[i]["ChartType"]=="Line"||_listofseries[0]["ChartType"]=="Area")
					{
						_data.push({"BackgroundColor":_listofseries[i]["Appearance"]["BackgroundColor"],"Name":_listofseries[i]["Name"]});				
					}			
			}
			else if(_listofseries[0]["ChartType"]=="Scatter")
			{
				for(var i=0;i<_listofseries.length;i++)
					if(_listofseries[i]["ChartType"]=="Scatter")
					{
						_data.push({"BackgroundColor":_listofseries[i]["Appearance"]["BackgroundColor"],"Name":_listofseries[i]["Name"]});				
					}			
			}
			
	
	
	
			var _panel = _addRec(0,0,0,0,_g);
			var _recs = [];
			var _texts = [];
			switch(_settings["Legend"]["Appearance"]["Position"])
			{
				case "top":
				case "bottom":
					var _pointer = 0;
					for(var i=0;i<_data.length;i++)
					{
						var _rec = _addRec(_pointer,0,_pointer+10,10,_g);
						_fill(_rec,_data[i]["BackgroundColor"]);
						_pointer+=15;
						var _g_text = _newSVGNode("g",_g);
						var _text = _addText(_data[i]["Name"],_g_text);
						_style(_text,"font-size: "+_settings["FontSize"]+";font-family:"+_settings["FontFamily"]+";");
						_fill(_text,_settings["FontColor"]);	
						_translate(_g_text,_pointer,(_g_text.getBBox().height+5)/2);
						_pointer+=_text.getBBox().width+10;
					}
					_setRec(_panel,-5,-5,_pointer-5,15);
					break;
				case "left":
				case "right":
					default:
					var _pointer = 0;
					for(var i=0;i<_data.length;i++)
					{
						var _rec = _addRec(0,_pointer,10,_pointer+10,_g);
						_fill(_rec,_data[i]["BackgroundColor"]);
	
						var _g_text = _newSVGNode("g",_g);
						var _text = _addText(_data[i]["Name"],_g_text);
						_style(_text,"font-size: "+_settings["FontSize"]+";font-family:"+_settings["FontFamily"]+";");
						_fill(_text,_settings["FontColor"]);		
	
						if(_settings["Legend"]["Appearance"]["Position"]=="right")
						{
							_translate(_g_text,15,_pointer+(_g_text.getBBox().height+5)/2);						
						}
						else
						{
							_translate(_g_text,-(_g_text.getBBox().width+5),_pointer+(_g_text.getBBox().height+5)/2);						
						}
	
						_pointer+=_g_text.getBBox().height+5;
					}
					_setRec(_panel,_g.getBBox().x-5,_g.getBBox().y-5,_g.getBBox().x+_g.getBBox().width+5,_g.getBBox().y+_g.getBBox().height+5);				
					break;				
			}
			
			_fill(_panel,_settings["Legend"]["Appearance"]["BackgroundColor"]);
			var _area_width = _settings["Width"];
			var _area_height = _settings["Height"];
	
			switch(_settings["Legend"]["Appearance"]["Position"])
			{
				case "top":
					var _g_x = (_area_width-_g.getBBox().width)/2;
					var _g_y= this._padding_top;
					this._padding_top+=_g.getBBox().height+10;
					break;
				case "bottom":
					var _g_x = (_area_width-_g.getBBox().width)/2;
					var _g_y= _area_height-this._padding_bottom-_g.getBBox().height;
					this._padding_bottom+=_g.getBBox().height+10;
					break;
				case "left":
					var _g_x = this._padding_left;
					var _g_y= (_area_height-_g.getBBox().height)/2;
					this._padding_left+=_g.getBBox().width+10;
					break;
				case "right":			
					var _g_x = _area_width-this._padding_right-_g.getBBox().width;
					var _g_y= (_area_height-_g.getBBox().height)/2;
					this._padding_right+=_g.getBBox().width+10;
					break;
			}
			_translate(_g,_g_x-_g.getBBox().x,_g_y-_g.getBBox().y);
		}


	},
	_yaxis_auto_assign_minvalue_minvalue_and_step:function(_settings)
	{
		//Data fixing
		var _listofseries = _settings["PlotArea"]["ListOfSeries"];
		var _max_number_of_items = 0;
		//Fix missing value
		for(var i=0;i<_listofseries.length;i++)
		{
			if(_listofseries[i]["ChartType"]=="Line"||_listofseries[i]["ChartType"]=="Area"||_listofseries[i]["ChartType"]=="Scatter")
			{
				for(var j=0;j<_listofseries[i]["Items"].length;j++)
				{
					if (_listofseries[i]["Items"][j]["YValue"]==null)
					{
						switch(_listofseries[i]["MissingValue"])
						{
							case "zero":
								_listofseries[i]["Items"][j]["YValue"]=0;
								break;
							case "interpolated":
								break;
							case "gap":
								break;														
						}
					}
				}
									
			}
		}		
		
		for(var i=0;i<_listofseries.length;i++)
			if(_listofseries[i]["ChartType"]=="Column"||_listofseries[i]["ChartType"]=="Bar"||_listofseries[i]["ChartType"]=="Area"||_listofseries[i]["ChartType"]=="Line")
				if(_max_number_of_items<_listofseries[i]["Items"].length)
				{
					_max_number_of_items = _listofseries[i]["Items"].length;
				}
		while (_settings["PlotArea"]["XAxis"]["Items"].length<_max_number_of_items)
		{
			_settings["PlotArea"]["XAxis"]["Items"].push({"Text":""});
		}
		//MinValue, MaxValue and MajorStep and MinorStep
		//MinorStep = MajorStep/5
		//MajorStep % 10
		var _min_value=9999999;
		var _max_value=-9999999;
		for(var i=0;i<_listofseries.length;i++)
			if(_listofseries[i]["ChartType"]=="Scatter"||_listofseries[i]["ChartType"]=="Column"||_listofseries[i]["ChartType"]=="Bar"||_listofseries[i]["ChartType"]=="Area"||_listofseries[i]["ChartType"]=="Line")
			{
				for(var j=0;j<_listofseries[i]["Items"].length;j++)
				{
					if(_min_value>_listofseries[i]["Items"][j]["YValue"]) _min_value=_listofseries[i]["Items"][j]["YValue"];
					if(_max_value<_listofseries[i]["Items"][j]["YValue"]) _max_value=_listofseries[i]["Items"][j]["YValue"];										
				}
			}
			
		if(_settings["PlotArea"]["YAxis"]["MinValue"]==null || _settings["PlotArea"]["YAxis"]["MaxValue"]==null)
		{
			var _step_options = [0.0001,0.00025,0.0005];
			
			if(_min_value>=0&&_max_value>=0)
			{

				if((_max_value-_min_value)/_max_value>0.3)
				{
					_settings["PlotArea"]["YAxis"]["MinValue"] = 0;
					var _step = _max_value/100;
					i=0;
					while(_max_value/_step>5)
					{
						_step = _step_options[i%3]*Math.floor(Math.pow(10,Math.floor(i/3)));
						i++;												
					}
					_settings["PlotArea"]["YAxis"]["MaxValue"] = Math.ceil(_max_value/_step + 1)*_step;
				}
				else
				{
					var _step = (_max_value-_min_value)/100;
					i=0;
					while( (_max_value-_min_value)/_step>5)
					{
						_step = _step_options[i%3]*Math.floor(Math.pow(10,Math.floor(i/3)));
						i++;												
					}
					_settings["PlotArea"]["YAxis"]["MinValue"] =  Math.floor(_min_value/_step - 1)*_step;					
					_settings["PlotArea"]["YAxis"]["MaxValue"] = Math.ceil(_max_value/_step + 1)*_step;
				}
			}
			else if(_min_value<0 && _max_value>=0)
			{
				var _step = (_max_value-_min_value)/100;
				i=0;
				while( (_max_value-_min_value)/_step>5)
				{
					_step = _step_options[i%3]*Math.floor(Math.pow(10,Math.floor(i/3)));
					i++;												
				}
				_settings["PlotArea"]["YAxis"]["MinValue"] =  Math.floor(_min_value/_step - 0.5)*_step;					
				_settings["PlotArea"]["YAxis"]["MaxValue"] = Math.ceil(_max_value/_step + 0.5)*_step;
			}
			else
			{
				_settings["PlotArea"]["YAxis"]["MaxValue"] = 0;
				var _step = _min_value/100;
				i=0;
				while(_min_value/_step>5)
				{
					_step = _step_options[i%3]*Math.floor(Math.pow(10,Math.floor(i/3)));
					i++;												
				}
				_settings["PlotArea"]["YAxis"]["MinValue"] = Math.floor(_min_value/_step + 1)*_step;
				
			}
			
			if(_settings["PlotArea"]["YAxis"]["MajorStep"]==null)
			{
				_settings["PlotArea"]["YAxis"]["MajorStep"]= _step;				
			}
			if(_settings["PlotArea"]["YAxis"]["MinorStep"]==null)
			{
				_settings["PlotArea"]["YAxis"]["MinorStep"]= _step/5;				
			}
		
		
		}
		return _settings;
	},

	_xaxis_auto_assign_minvalue_minvalue_and_step:function(_settings)
	{
		//Data fixing
		var _listofseries = _settings["PlotArea"]["ListOfSeries"];

		for(var i=0;i<_listofseries.length;i++)
		{
			if(_listofseries[i]["ChartType"]=="Scatter")
			{
				for(var j=0;j<_listofseries[i]["Items"].length;j++)
				{
					if (_listofseries[i]["Items"][j]["XValue"]==null)
					{
						switch(_listofseries[i]["MissingValue"])
						{
							case "zero":
								_listofseries[i]["Items"][j]["XValue"]=0;
								break;
							case "interpolated":
								break;
							case "gap":
								break;														
						}
					}
				}
									
			}
		}		



		var _min_value=9999999;
		var _max_value=-9999999;
		for(var i=0;i<_listofseries.length;i++)
			if(_listofseries[i]["ChartType"]=="Scatter")
			{
				for(var j=0;j<_listofseries[i]["Items"].length;j++)
				{
					if(_min_value>_listofseries[i]["Items"][j]["YValue"]) _min_value=_listofseries[i]["Items"][j]["YValue"];
					if(_max_value<_listofseries[i]["Items"][j]["YValue"]) _max_value=_listofseries[i]["Items"][j]["YValue"];										
				}
			}
			
		if(_settings["PlotArea"]["YAxis"]["MinValue"]==null || _settings["PlotArea"]["YAxis"]["MaxValue"]==null)
		{
			var _step_options = [0.0001,0.00025,0.0005];
			
			if(_min_value>=0&&_max_value>=0)
			{
				
				if((_max_value-_min_value)/_max_value>0.3)
				{
					_settings["PlotArea"]["YAxis"]["MinValue"] = 0;
					var _step = _max_value/100;
					i=0;
					while(_max_value/_step>5)
					{
						_step = _step_options[i%3]*Math.floor(Math.pow(10,Math.floor(i/3)));
						i++;												
					}
					_settings["PlotArea"]["YAxis"]["MaxValue"] = Math.ceil(_max_value/_step + 1)*_step;
				}
				else
				{
					var _step = (_max_value-_min_value)/100;
					i=0;
					while( (_max_value-_min_value)/_step>5)
					{
						_step = _step_options[i%3]*Math.floor(Math.pow(10,Math.floor(i/3)));
						i++;												
					}
					_settings["PlotArea"]["YAxis"]["MinValue"] =  Math.floor(_min_value/_step - 1)*_step;					
					_settings["PlotArea"]["YAxis"]["MaxValue"] = Math.ceil(_max_value/_step + 1)*_step;
				}
			}
			else if(_min_value<0 && _max_value>=0)
			{
				var _step = (_max_value-_min_value)/100;
				i=0;
				while( (_max_value-_min_value)/_step>5)
				{
					_step = _step_options[i%3]*Math.floor(Math.pow(10,Math.floor(i/3)));
					i++;												
				}
				_settings["PlotArea"]["YAxis"]["MinValue"] =  Math.floor(_min_value/_step - 0.5)*_step;					
				_settings["PlotArea"]["YAxis"]["MaxValue"] = Math.ceil(_max_value/_step + 0.5)*_step;
			}
			else
			{
				_settings["PlotArea"]["YAxis"]["MaxValue"] = 0;
				var _step = _min_value/100;
				i=0;
				while(_min_value/_step>5)
				{
					_step = _step_options[i%3]*Math.floor(Math.pow(10,Math.floor(i/3)));
					i++;												
				}
				_settings["PlotArea"]["YAxis"]["MinValue"] = Math.floor(_min_value/_step + 1)*_step;
				
			}
			
			if(_settings["PlotArea"]["YAxis"]["MajorStep"]==null)
			{
				_settings["PlotArea"]["YAxis"]["MajorStep"]= _step;				
			}
			if(_settings["PlotArea"]["YAxis"]["MinorStep"]==null)
			{
				_settings["PlotArea"]["YAxis"]["MinorStep"]= _step/5;				
			}
		}
		return _settings;
	},


	_create_pie_plotarea:function(_svg,_settings)
	{
		//Start drawing
		var _g = _newSVGNode("g",_svg);
		_g.id = this._id+"_plotarea";
		this._draw_pie_series(_g,_settings);		
	},
	_create_scatter_plotarea:function(_svg,_settings)
	{
		_settings = this._yaxis_auto_assign_minvalue_minvalue_and_step(_settings);
		_settings = this._xaxis_auto_assign_minvalue_minvalue_and_step(_settings);
		var _yaxis_settings = _settings["PlotArea"]["YAxis"];
		var _xaxis_settings = _settings["PlotArea"]["XAxis"];
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		
		//Start drawing
		var _g = _newSVGNode("g",_svg);
		_g.id = this._id+"_plotarea";
		
		
		//Create y-axis
		//title y-axis
		if(_yaxis_settings["Title"]!=null)
		{
			//Draw the title of yaxis
			var _yaxis_group_title = _newSVGNode("g",_g);
			_yaxis_group_title.id=this._id+"_plotarea_yaxis_title";
			var _title_text = _addText(_yaxis_settings["Title"],_yaxis_group_title);
			_style(_title_text,"font-size: "+_yaxis_settings["TitleAppearance"]["FontSize"]+";font-family:"+_yaxis_settings["TitleAppearance"]["FontFamily"]+";");
			_fill(_title_text,_yaxis_settings["TitleAppearance"]["FontColor"]);	
			_rotate(_title_text,-90+_yaxis_settings["TitleAppearance"]["RotationAngle"]);//By default the yaxis has vertical title.
			var _yaxis_group_title_x = this._padding_left;
			//Move padding left
			this._padding_left+=_yaxis_group_title.getBBox().width+10;				
		}
		
		var _min_to_zero_width = 0;
		var _min_to_zero_height = 0;
		
		//labels on YAxis
		if(_yaxis_settings["MajorStep"]!=null && _yaxis_settings["LabelsAppearance"]["Visible"])
		{
			var _yaxis_group_labels = _newSVGNode("g",_g);
			_yaxis_group_labels.id=this._id+"_plotarea_yaxis_labels";
			var _yaxis_label_texts=[];
			for(var i=0;i<(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MajorStep"]+1;i++)
			{
				var _group_text = _newSVGNode("g",_yaxis_group_labels);
				var _format = _yaxis_settings["LabelsAppearance"]["DataFormatString"];
				var _text = _addText( _format.replace("{0}",(_yaxis_settings["MinValue"]*10+(i*10)*_yaxis_settings["MajorStep"])/10 ),_group_text);
				
				_style(_text,"font-size: "+_yaxis_settings["LabelsAppearance"]["FontSize"]+";font-family:"+_yaxis_settings["LabelsAppearance"]["FontFamily"]+";");
				_fill(_text,_yaxis_settings["LabelsAppearance"]["FontColor"]);	
				if(_yaxis_settings["LabelsAppearance"]["RotationAngle"])
				{
					_rotate(_text,_yaxis_settings["LabelsAppearance"]["RotationAngle"]);				
				}
				_yaxis_label_texts.push(_group_text);
			}
			
			if(_xaxis_settings["MinValue"]>=0)
			{
				_translate(_yaxis_group_labels,this._padding_left-_yaxis_group_labels.getBBox().x,this._padding_top-_yaxis_group_labels.getBBox().y);
				//Move padding left
				this._padding_left+=_yaxis_group_labels.getBBox().width+10;				
			}
			else
			{
				_min_to_zero_width = (Math.abs(_xaxis_settings["MinValue"])/(_xaxis_settings["MaxValue"]-_xaxis_settings["MinValue"]))*(_area_width-this._padding_left - this._padding_right);
				_translate(_yaxis_group_labels,this._padding_left + _min_to_zero_width - _yaxis_group_labels.getBBox().width - 5 - _yaxis_group_labels.getBBox().x,this._padding_top-_yaxis_group_labels.getBBox().y);				
			}
		}
		
		
		
		//title for XAxis:
		if(_xaxis_settings["Title"]!=null)
		{
			//Draw the title of yaxis
			var _xaxis_group_title = _newSVGNode("g",_g);
			_xaxis_group_title.id=this._id+"_plotarea_xaxis_title";
			var _title_text = _addText(_xaxis_settings["Title"],_xaxis_group_title);
			_style(_title_text,"font-size: "+_xaxis_settings["TitleAppearance"]["FontSize"]+";font-family:"+_xaxis_settings["TitleAppearance"]["FontFamily"]+";");
			_fill(_title_text,_xaxis_settings["TitleAppearance"]["FontColor"]);	
			_rotate(_title_text,_xaxis_settings["TitleAppearance"]["RotationAngle"]);
			
			switch(_xaxis_settings["TitleAppearance"]["Position"])
			{
				case "left":
					var _xaxis_group_title_x=this._padding_left;					
					break;
				case "right":
					var _xaxis_group_title_x= _area_width - this._padding_right-_xaxis_group_title.getBBox().width;					
					break;
				case "center":
					default:
					var _xaxis_group_title_x=(_area_width- this._padding_left - this._padding_right -_xaxis_group_title.getBBox().width)/2 + this._padding_left;					
					break;					
			}
			
			var _xaxis_group_title_y = _area_height - this._padding_bottom - _xaxis_group_title.getBBox().height;
			_translate(_xaxis_group_title,_xaxis_group_title_x-_xaxis_group_title.getBBox().x,_xaxis_group_title_y-_xaxis_group_title.getBBox().y);
			//Move padding left
			this._padding_bottom+=_xaxis_group_title.getBBox().height+10;				
		}
		//Labels for XAxis
		if(_xaxis_settings["MajorStep"]!=null && _xaxis_settings["LabelsAppearance"]["Visible"])
		{
			var _xaxis_group_labels = _newSVGNode("g",_g);
			_xaxis_group_labels.id=this._id+"_plotarea_xaxis_labels";
			var _xaxis_label_texts=[];
			for(var i=0;i<(_xaxis_settings["MaxValue"]-_xaxis_settings["MinValue"])/_xaxis_settings["MajorStep"]+1;i++)
			{
				var _group_text = _newSVGNode("g",_xaxis_group_labels);
				var _format = _xaxis_settings["LabelsAppearance"]["DataFormatString"];
				var _text = _addText( _format.replace("{0}",(_xaxis_settings["MinValue"]*10+(i*10)*_xaxis_settings["MajorStep"])/10 ),_group_text);
				
				_style(_text,"font-size: "+_xaxis_settings["LabelsAppearance"]["FontSize"]+";font-family:"+_xaxis_settings["LabelsAppearance"]["FontFamily"]+";");
				_fill(_text,_xaxis_settings["LabelsAppearance"]["FontColor"]);	
				if(_xaxis_settings["LabelsAppearance"]["RotationAngle"])
				{
					_rotate(_text,_xaxis_settings["LabelsAppearance"]["RotationAngle"]);				
				}
				_xaxis_label_texts.push(_group_text);
			}

			if(_xaxis_settings["MinValue"]>=0)
			{
				_translate(_xaxis_group_labels,this._padding_left-_xaxis_group_labels.getBBox().x,_area_height-this._padding_bottom-_xaxis_group_labels.getBBox().height-_xaxis_group_labels.getBBox().y);			
				this._padding_bottom+=_xaxis_group_labels.getBBox().height+10;				
			}
			else
			{
				_min_to_zero_height = (Math.abs(_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*(_area_height-this._padding_top - this._padding_bottom);
				_translate(_xaxis_group_labels,this._padding_left-_xaxis_group_labels.getBBox().x,_area_height-this._padding_bottom - _min_to_zero_height + 5 - _xaxis_group_labels.getBBox().y);				
			}



		}
		
		
		
		var _group_grid = _newSVGNode("g",_g);
		_group_grid.id=this._id+"_plotarea_grid";
		
		
		//Line for YAxis:
		//var _yaxis_line = _addLine(this._padding_left+0.5,this._padding_top+0.5,this._padding_left+0.5,_area_height+0.5-this._padding_bottom,_group_grid);
		//_stroke(_yaxis_line,_yaxis_settings["Color"]);
		var _yaxis_line_height = _area_height-this._padding_bottom - this._padding_top;
		

		//minor ticks and minor grid line
		if(_yaxis_settings["MinorStep"]) //Only if MinorStep is defined
		{
			var _tmp_count = (_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MinorStep"];
			for(var i=_tmp_count;i>=0;i--)
			{
				//Minor ticks
				var _tmp_y = _area_height - this._padding_bottom - (_yaxis_line_height/_tmp_count)*i;
				
				if (_yaxis_settings["MinorTickType"] == "outside") 
				{
					var _major_tick = _addLine(this._padding_left + _min_to_zero_width -1-_yaxis_settings["MinorTickSize"]+0.5,_tmp_y+0.5,this._padding_left +_min_to_zero_width -1+0.5,_tmp_y +0.5,_group_grid);
					_stroke(_major_tick,_yaxis_settings["Color"]);
				}				
				//minor grid lines
				if(_yaxis_settings["MinorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(this._padding_left+1+0.5,_tmp_y+0.5,_area_width-this._padding_right+0.5,_tmp_y+0.5,_group_grid);
					_stroke(_grid_line,_yaxis_settings["MinorGridLines"]["Color"]);
				}
			}			
		}
		
		
		
		//major ticks and major grid line and labels
		if(_yaxis_settings["MajorStep"]) //Only if MajorStep is defined
		{
			var _tmp_count = (_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MajorStep"];
			for(var i=_tmp_count;i>=0;i--)
			{
				//Major ticks
				var _tmp_y = _area_height - this._padding_bottom - (_yaxis_line_height/_tmp_count)*i;
				
				if(_yaxis_settings["MajorTickType"]=="outside")
				{
					var _major_tick = _addLine(this._padding_left + _min_to_zero_width-1-_yaxis_settings["MajorTickSize"]+0.5,_tmp_y+0.5,this._padding_left + _min_to_zero_width-1+0.5,_tmp_y +0.5,_group_grid);
					_stroke(_major_tick,_yaxis_settings["Color"]);					
				}
				
				//Labels
				if(_yaxis_settings["LabelsAppearance"]["Visible"])
				{
					if(i==_tmp_count)
					{
						//_yaxis_label_texts
						_translate(_yaxis_group_labels,this._padding_left + _min_to_zero_width-10-_yaxis_group_labels.getBBox().width,_tmp_y-_yaxis_group_labels.getBBox().y-_yaxis_group_labels.getBBox().height/2,"clear");
					}
					else
					{
						_translate(_yaxis_label_texts[i],_yaxis_group_labels.getBBox().width - _yaxis_label_texts[i].getBBox().width - 1,(_yaxis_line_height/_tmp_count)*(_tmp_count-i));				
					}				
				}
				//Major grid lines
				if(_yaxis_settings["MajorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(this._padding_left+1+0.5,_tmp_y+0.5,_area_width-this._padding_right+0.5,_tmp_y+0.5,_group_grid);
					_stroke(_grid_line,_yaxis_settings["MajorGridLines"]["Color"]);
				}
			}			
		}
		
		
		
		//Line for horizontal axis:
		var _xaxis_line_width = _area_width - this._padding_left - this._padding_right;

		//minor ticks and minor grid line
		if(_xaxis_settings["MinorStep"]) //Only if MinorStep is defined
		{
			var _tmp_count = (_xaxis_settings["MaxValue"]-_xaxis_settings["MinValue"])/_xaxis_settings["MinorStep"];
			for(var i=_tmp_count;i>=0;i--)
			{
				//Minor ticks
				var _tmp_x = this._padding_left + (_xaxis_line_width/_tmp_count)*i;
				
				if (_xaxis_settings["MinorTickType"] == "outside") 
				{
					var _tick = _addLine(_tmp_x+0.5,_area_height - this._padding_bottom - _min_to_zero_height +1+_xaxis_settings["MinorTickSize"]+0.5,_tmp_x +0.5,_area_height - this._padding_bottom- _min_to_zero_height+1+0.5,_group_grid);
					_stroke(_tick,_xaxis_settings["Color"]);
				}				
				//minor grid lines
				if(_xaxis_settings["MinorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(_tmp_x+0.5,this._padding_top+0.5,_tmp_x+0.5,_area_height-this._padding_bottom - 1 +0.5,_group_grid);
					_stroke(_grid_line,_xaxis_settings["MinorGridLines"]["Color"]);
				}
			}			
		}
		
		

		//major ticks and major grid line and labels
		if(_xaxis_settings["MajorStep"]) //Only if MajorStep is defined
		{
			var _tmp_count = (_xaxis_settings["MaxValue"]-_xaxis_settings["MinValue"])/_xaxis_settings["MajorStep"];
			for(var i=0;i<_tmp_count+1;i++)
			{
				//Major ticks
				var _tmp_x = this._padding_left + (_xaxis_line_width/_tmp_count)*i;
				
				
				//Labels
				
				if(_xaxis_settings["LabelsAppearance"]["Visible"])
				{
					//_translate(_yaxis_group_labels,_tmp_x-_yaxis_label_texts[i].getBBox().width/2 -_yaxis_group_labels.getBBox().x,_area_height - this._padding_bottom-10-_yaxis_group_labels.getBBox().height,"clear");
				
					
				
					_translate(_xaxis_label_texts[i],(_xaxis_line_width/_tmp_count)*i - _xaxis_label_texts[i].getBBox().width/2,0);				
				}
				
				//Major grid lines
				if(_xaxis_settings["MajorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(_tmp_x+0.5,this._padding_top+0.5,_tmp_x+0.5,_area_height-this._padding_bottom -1 +0.5,_group_grid);
					_stroke(_grid_line,_xaxis_settings["MajorGridLines"]["Color"]);
				}

				if(_xaxis_settings["MajorTickType"]=="outside")
				{
					var _tick = _addLine(_tmp_x+0.5,_area_height - this._padding_bottom- _min_to_zero_height+1+_xaxis_settings["MajorTickSize"]+0.5,_tmp_x +0.5,_area_height - this._padding_bottom- _min_to_zero_height+1+0.5,_group_grid);
					_stroke(_tick,_xaxis_settings["Color"]);
				}


			}			
		}

		
		
		var _yaxis_line = _addLine(this._padding_left + _min_to_zero_width +0.5,this._padding_top+0.5,this._padding_left + _min_to_zero_width +0.5,_area_height-this._padding_bottom +0.5,_group_grid);
		_stroke(_yaxis_line,_xaxis_settings["Color"]);		
		
		var _xaxis_line = _addLine(this._padding_left+0.5,_area_height+0.5-this._padding_bottom - _min_to_zero_height,_area_width+0.5-this._padding_right,_area_height+0.5-this._padding_bottom - _min_to_zero_height,_group_grid);
		_stroke(_xaxis_line,_xaxis_settings["Color"]);
		
		
		this._draw_scatter_series(_g,_settings);
		
		_g.appendChild(_xaxis_group_labels);
		_g.appendChild(_yaxis_group_labels);
	},
	_create_bar_plotarea:function(_svg,_settings)
	{
		_settings = this._yaxis_auto_assign_minvalue_minvalue_and_step(_settings);
		var _yaxis_settings = _settings["PlotArea"]["YAxis"];
		var _xaxis_settings = _settings["PlotArea"]["XAxis"];
		//Start drawing
		var _g = _newSVGNode("g",_svg);
		_g.id = this._id+"_plotarea";
		
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		
		
		//Create vertical-axis
		//title vertical-axis
		
		var _min_to_zero_width = 0;				
		
		if(_xaxis_settings["Title"]!=null)
		{
			//Draw the title of yaxis
			var _xaxis_group_title = _newSVGNode("g",_g);
			_xaxis_group_title.id=this._id+"_plotarea_xaxis_title";
			var _title_text = _addText(_xaxis_settings["Title"],_xaxis_group_title);
			_style(_title_text,"font-size: "+_xaxis_settings["TitleAppearance"]["FontSize"]+";font-family:"+_xaxis_settings["TitleAppearance"]["FontFamily"]+";");
			_fill(_title_text,_xaxis_settings["TitleAppearance"]["FontColor"]);	
			_rotate(_title_text,-90+_xaxis_settings["TitleAppearance"]["RotationAngle"]);
			
			switch(_xaxis_settings["TitleAppearance"]["Position"])
			{
				case "top":
					break;
				case "bottom":
					break;
				case "middle":
					default:
					break;					
			}
			
			var _xaxis_group_title_x = this._padding_left;
			//Move padding left
			this._padding_left+=_xaxis_group_title.getBBox().width+10;				
		}
		//labels on vertical axis 
		if(_xaxis_settings["LabelsAppearance"]["Visible"])
		{
			var _xaxis_group_labels = _newSVGNode("g",_g);
			_xaxis_group_labels.id=this._id+"_plotarea_xaxis_labels";
			_fill(_addRec(0,0,1,1,_xaxis_group_labels),"none");
			
			var _xaxis_label_texts=[];
			for(var i=0;i<_xaxis_settings["Items"].length;i++)
			{
				var _group_text = _newSVGNode("g",_xaxis_group_labels);
				var _text = _addText(_xaxis_settings["Items"][i]["Text"],_group_text);
				_style(_text,"font-size: "+_xaxis_settings["LabelsAppearance"]["FontSize"]+";font-family:"+_xaxis_settings["LabelsAppearance"]["FontFamily"]+";");
				_fill(_text,_xaxis_settings["LabelsAppearance"]["FontColor"]);	
				if(_xaxis_settings["LabelsAppearance"]["RotationAngle"])
				{
					_rotate(_text,_xaxis_settings["LabelsAppearance"]["RotationAngle"]);				
				}
				_xaxis_label_texts.push(_group_text);
			}
			
			if(_yaxis_settings["MinValue"]>=0)
			{
				
				
				_translate(_xaxis_group_labels,this._padding_left-_xaxis_group_labels.getBBox().x,this._padding_top-_xaxis_group_labels.getBBox().y);
				//Move padding left
				this._padding_left+=_xaxis_group_labels.getBBox().width+10;				
				
			}
			else
			{
				_min_to_zero_width = (Math.abs(_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*(_area_width-this._padding_left - this._padding_right);
				_translate(_xaxis_group_labels,this._padding_left + _min_to_zero_width - _xaxis_group_labels.getBBox().width - 5 - _xaxis_group_labels.getBBox().x,this._padding_top-_xaxis_group_labels.getBBox().y);

			}	
		}

		
		
		//Create horizontal-axis
		//title horizontal-axis		
		
		
		if(_yaxis_settings["Title"]!=null)
		{
			//Draw the title of yaxis
			var _yaxis_group_title = _newSVGNode("g",_g);
			_yaxis_group_title.id=this._id+"_plotarea_yaxis_title";
			var _title_text = _addText(_yaxis_settings["Title"],_yaxis_group_title);
			_style(_title_text,"font-size: "+_yaxis_settings["TitleAppearance"]["FontSize"]+";font-family:"+_yaxis_settings["TitleAppearance"]["FontFamily"]+";");
			_fill(_title_text,_yaxis_settings["TitleAppearance"]["FontColor"]);	
			_rotate(_title_text,_yaxis_settings["TitleAppearance"]["RotationAngle"]);//By default the yaxis has vertical title.
			
			switch(_xaxis_settings["TitleAppearance"]["Position"])
			{
				case "left":
					var _yaxis_group_title_x=this._padding_left;					
					break;
				case "right":
					var _yaxis_group_title_x= _area_width - this._padding_right-_yaxis_group_title.getBBox().width;					
					break;
				case "center":
					default:
					var _yaxis_group_title_x=(_area_width- this._padding_left - this._padding_right -_yaxis_group_title.getBBox().width)/2 + this._padding_left;					
					break;					
			}			
		
			var _yaxis_group_title_y = _area_height - this._padding_bottom - _yaxis_group_title.getBBox().height;
			_translate(_yaxis_group_title,_yaxis_group_title_x-_yaxis_group_title.getBBox().x,_yaxis_group_title_y-_yaxis_group_title.getBBox().y);
			//Move padding left
			this._padding_bottom+=_yaxis_group_title.getBBox().height+10;				
		
		
		}
		//labels horizontal-axis		
		
		if(_yaxis_settings["MajorStep"]!=null && _yaxis_settings["LabelsAppearance"]["Visible"])
		{
			var _yaxis_group_labels = _newSVGNode("g",_g);
			_yaxis_group_labels.id=this._id+"_plotarea_yaxis_labels";
			var _yaxis_label_texts=[];
			for(var i=0;i<(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MajorStep"]+1;i++)
			{
				var _group_text = _newSVGNode("g",_yaxis_group_labels);
				var _format = _yaxis_settings["LabelsAppearance"]["DataFormatString"];
				var _text = _addText( _format.replace("{0}",(_yaxis_settings["MinValue"]*10+(i*10)*_yaxis_settings["MajorStep"])/10 ),_group_text);
				
				_style(_text,"font-size: "+_yaxis_settings["LabelsAppearance"]["FontSize"]+";font-family:"+_yaxis_settings["LabelsAppearance"]["FontFamily"]+";");
				_fill(_text,_yaxis_settings["LabelsAppearance"]["FontColor"]);	
				if(_yaxis_settings["LabelsAppearance"]["RotationAngle"])
				{
					_rotate(_text,_yaxis_settings["LabelsAppearance"]["RotationAngle"]);				
				}
				_yaxis_label_texts.push(_group_text);
			}

			_translate(_yaxis_group_labels,this._padding_left-_xaxis_group_labels.getBBox().x,_area_height-this._padding_bottom-_yaxis_group_labels.getBBox().height-_yaxis_group_labels.getBBox().y);			
			this._padding_bottom+=_yaxis_group_labels.getBBox().height+10;				

		}
		
		//Line for vertical axis: using _xaxis_settings
		var _group_grid = _newSVGNode("g",_g);
		_group_grid.id=this._id+"_plotarea_grid";
		var _yaxis_line_width = _area_width-this._padding_left - this._padding_right;

		
		
		var _xaxis_line_height = _area_height - this._padding_top - this._padding_bottom;
		
		var _items_count = _xaxis_settings["Items"].length;
		//Major ticks and major grid line
		for(var i=1;i<_items_count+1;i++)
		{
			var _tmp_y = _area_height - this._padding_bottom - (_xaxis_line_height/_items_count)*(i);
			//Grid lines
			if(i!=0)
			{
				
				//Major line
				if(_xaxis_settings["MajorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(this._padding_left+0.5,_tmp_y+0.5,_area_width-this._padding_right-1+0.5,_tmp_y+0.5,_group_grid);
					_stroke(_grid_line,_xaxis_settings["MajorGridLines"]["Color"]);				
				}						
				//Minor line
				if(_xaxis_settings["MinorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(this._padding_left+0.5,_tmp_y+(_xaxis_line_height/_items_count)/2+0.5,_area_width-this._padding_right+0.5,_tmp_y+(_xaxis_line_height/_items_count)/2+0.5,_group_grid);
					_stroke(_grid_line,_xaxis_settings["MinorGridLines"]["Color"]);
				}
				if(_xaxis_settings["LabelsAppearance"]["Visible"])
				{
					_translate(_xaxis_label_texts[i-1], _xaxis_group_labels.getBBox().width - _xaxis_label_texts[i-1].getBBox().width, _tmp_y - this._padding_top + (_xaxis_line_height/_items_count)/2 - _xaxis_label_texts[i-1].getBBox().height/2,"clear");
				
				}
				//Minor ticks
				if(_xaxis_settings["MinorTickType"]=="outside")
				{
					var _minor_tick = _addLine(this._padding_left + _min_to_zero_width -1+0.5,_tmp_y + (_xaxis_line_height/_items_count)/2 +0.5,this._padding_left + _min_to_zero_width -1 - _xaxis_settings["MinorTickSize"]+0.5,_tmp_y + (_xaxis_line_height/_items_count)/2 +0.5,_group_grid);
					_stroke(_minor_tick,_xaxis_settings["Color"]);
				}
			}
			//Major Ticks
			if(_xaxis_settings["MajorTickType"]=="outside")
			{
				var _major_tick = _addLine(this._padding_left + _min_to_zero_width -1+0.5,_tmp_y +0.5,this._padding_left + _min_to_zero_width -1 - _xaxis_settings["MajorTickSize"]+0.5,_tmp_y+0.5,_group_grid);
				//var _major_tick = _addLine(_tmp_x+0.5,_area_height-this._padding_bottom - _min_to_zero_height +1+0.5,_tmp_x+0.5,_area_height-this._padding_bottom -_min_to_zero_height +1+_xaxis_settings["MajorTickSize"]+0.5,_group_grid);
				_stroke(_major_tick,_xaxis_settings["Color"]);
			}

		}



		//Line for horizontal axis:
		var _yaxis_line = _addLine(this._padding_left+0.5,_area_height - this._padding_bottom+0.5,_area_width - this._padding_right+0.5,_area_height-this._padding_bottom+0.5,_group_grid);
		_stroke(_yaxis_line,_yaxis_settings["Color"]);
		

		//minor ticks and minor grid line
		if(_yaxis_settings["MinorStep"]) //Only if MinorStep is defined
		{
			var _tmp_count = (_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MinorStep"];
			for(var i=_tmp_count;i>=0;i--)
			{
				//Minor ticks
				var _tmp_x = this._padding_left + (_yaxis_line_width/_tmp_count)*i;
				
				if (_yaxis_settings["MinorTickType"] == "outside") 
				{
					var _tick = _addLine(_tmp_x+0.5,_area_height - this._padding_bottom+1+_yaxis_settings["MinorTickSize"]+0.5,_tmp_x +0.5,_area_height - this._padding_bottom+1+0.5,_group_grid);
					_stroke(_tick,_yaxis_settings["Color"]);
				}				
				//minor grid lines
				if(_yaxis_settings["MinorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(_tmp_x+0.5,this._padding_top+0.5,_tmp_x+0.5,_area_height-this._padding_bottom - 1 +0.5,_group_grid);
					_stroke(_grid_line,_yaxis_settings["MinorGridLines"]["Color"]);
				}
			}			
		}
		
		

		//major ticks and major grid line and labels
		if(_yaxis_settings["MajorStep"]) //Only if MajorStep is defined
		{
			var _tmp_count = (_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MajorStep"];
			for(var i=0;i<_tmp_count+1;i++)
			{
				//Major ticks
				var _tmp_x = this._padding_left + (_yaxis_line_width/_tmp_count)*i;
				
				if(_yaxis_settings["MajorTickType"]=="outside")
				{
					var _tick = _addLine(_tmp_x+0.5,_area_height - this._padding_bottom+1+_yaxis_settings["MajorTickSize"]+0.5,_tmp_x +0.5,_area_height - this._padding_bottom+1+0.5,_group_grid);
					_stroke(_tick,_yaxis_settings["Color"]);
				}
				
				//Labels
				
				if(_yaxis_settings["LabelsAppearance"]["Visible"])
				{
					//_translate(_yaxis_group_labels,_tmp_x-_yaxis_label_texts[i].getBBox().width/2 -_yaxis_group_labels.getBBox().x,_area_height - this._padding_bottom-10-_yaxis_group_labels.getBBox().height,"clear");
					_translate(_yaxis_label_texts[i],(_yaxis_line_width/_tmp_count)*i - _yaxis_label_texts[i].getBBox().width/2,0);				
				}
				
				//Major grid lines
				if(_yaxis_settings["MajorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(_tmp_x+0.5,this._padding_top+0.5,_tmp_x+0.5,_area_height-this._padding_bottom -1 +0.5,_group_grid);
					_stroke(_grid_line,_yaxis_settings["MajorGridLines"]["Color"]);
				}
			}			
		}

		
		
		
		
		
		var _xaxis_line = _addLine(this._padding_left + _min_to_zero_width +0.5,this._padding_top+0.5,this._padding_left + _min_to_zero_width +0.5,_area_height-this._padding_bottom +0.5,_group_grid);
		_stroke(_xaxis_line,_xaxis_settings["Color"]);		
		
		
		
		
		
		if(_xaxis_settings["Title"]!=null)
		{
			//Redraw the title.
			switch(_xaxis_settings["TitleAppearance"]["Position"])
			{
				case "top":
					var _xaxis_group_title_y=this._padding_top;
					break;
				case "bottom":
					var _xaxis_group_title_y=_area_height - this._padding_bottom - _yaxis_group_title.getBBox().height;
					break;
				case "middle":
				default:
					var _xaxis_group_title_y=(_area_height- this._padding_top - this._padding_bottom -_xaxis_group_title.getBBox().height)/2 + this._padding_top;				
					break;					
			}
			
			_translate(_xaxis_group_title,_xaxis_group_title_x -_xaxis_group_title.getBBox().x,_xaxis_group_title_y-_xaxis_group_title.getBBox().y);
		}
		
		
		this._draw_bar_series(_g,_settings);
		
		
		//Adjust the labels of xaxis group.
		if(_xaxis_settings["LabelsAppearance"]["Visible"])
		{
			if(_yaxis_settings["MinValue"]<0)
			{
				_g.appendChild(_xaxis_group_labels);	
			}				
		}
		
	},	
	_create_column_line_area_plotarea:function(_svg,_settings)
	{

		_settings = this._yaxis_auto_assign_minvalue_minvalue_and_step(_settings);
		//Start drawing
		var _g = _newSVGNode("g",_svg);
		_g.id = this._id+"_plotarea";
		
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		
		//Create y-axis
		//title y-axis
		var _yaxis_settings = _settings["PlotArea"]["YAxis"];
		if(_yaxis_settings["Title"]!=null)
		{
			//Draw the title of yaxis
			var _yaxis_group_title = _newSVGNode("g",_g);
			_yaxis_group_title.id=this._id+"_plotarea_yaxis_title";
			var _title_text = _addText(_yaxis_settings["Title"],_yaxis_group_title);
			_style(_title_text,"font-size: "+_yaxis_settings["TitleAppearance"]["FontSize"]+";font-family:"+_yaxis_settings["TitleAppearance"]["FontFamily"]+";");
			_fill(_title_text,_yaxis_settings["TitleAppearance"]["FontColor"]);	
			_rotate(_title_text,-90+_yaxis_settings["TitleAppearance"]["RotationAngle"]);//By default the yaxis has vertical title.
			var _yaxis_group_title_x = this._padding_left;
			//Move padding left
			this._padding_left+=_yaxis_group_title.getBBox().width+10;				
		}
		
		//labels on YAxis
		if(_yaxis_settings["MajorStep"]!=null && _yaxis_settings["LabelsAppearance"]["Visible"])
		{
			var _yaxis_group_labels = _newSVGNode("g",_g);
			_yaxis_group_labels.id=this._id+"_plotarea_yaxis_labels";
			var _yaxis_label_texts=[];
			for(var i=0;i<(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MajorStep"]+1;i++)
			{
				var _group_text = _newSVGNode("g",_yaxis_group_labels);
				var _format = _yaxis_settings["LabelsAppearance"]["DataFormatString"];
				var _text = _addText( _format.replace("{0}",(_yaxis_settings["MinValue"]*10+(i*10)*_yaxis_settings["MajorStep"])/10 ),_group_text);
				
				_style(_text,"font-size: "+_yaxis_settings["LabelsAppearance"]["FontSize"]+";font-family:"+_yaxis_settings["LabelsAppearance"]["FontFamily"]+";");
				_fill(_text,_yaxis_settings["LabelsAppearance"]["FontColor"]);	
				if(_yaxis_settings["LabelsAppearance"]["RotationAngle"])
				{
					_rotate(_text,_yaxis_settings["LabelsAppearance"]["RotationAngle"]);				
				}
				_yaxis_label_texts.push(_group_text);
			}
			_translate(_yaxis_group_labels,this._padding_left-_yaxis_group_labels.getBBox().x,this._padding_top-_yaxis_group_labels.getBBox().y);
			//Move padding left
			this._padding_left+=_yaxis_group_labels.getBBox().width+10;
		}




		//title for XAxis:
		var _xaxis_settings = _settings["PlotArea"]["XAxis"];
		if(_xaxis_settings["Title"]!=null)
		{
			//Draw the title of yaxis
			var _xaxis_group_title = _newSVGNode("g",_g);
			_xaxis_group_title.id=this._id+"_plotarea_xaxis_title";
			var _title_text = _addText(_xaxis_settings["Title"],_xaxis_group_title);
			_style(_title_text,"font-size: "+_xaxis_settings["TitleAppearance"]["FontSize"]+";font-family:"+_xaxis_settings["TitleAppearance"]["FontFamily"]+";");
			_fill(_title_text,_xaxis_settings["TitleAppearance"]["FontColor"]);	
			_rotate(_title_text,_xaxis_settings["TitleAppearance"]["RotationAngle"]);
			
			switch(_xaxis_settings["TitleAppearance"]["Position"])
			{
				case "left":
					var _xaxis_group_title_x=this._padding_left;					
					break;
				case "right":
					var _xaxis_group_title_x= _area_width - this._padding_right-_xaxis_group_title.getBBox().width;					
					break;
				case "center":
					default:
					var _xaxis_group_title_x=(_area_width- this._padding_left - this._padding_right -_xaxis_group_title.getBBox().width)/2 + this._padding_left;					
					break;					
			}
			
			var _xaxis_group_title_y = _area_height - this._padding_bottom - _xaxis_group_title.getBBox().height;
			_translate(_xaxis_group_title,_xaxis_group_title_x-_xaxis_group_title.getBBox().x,_xaxis_group_title_y-_xaxis_group_title.getBBox().y);
			//Move padding left
			this._padding_bottom+=_xaxis_group_title.getBBox().height+10;				
		}

		//Labels for XAxis:
		if(_xaxis_settings["LabelsAppearance"]["Visible"])
		{
			var _xaxis_group_labels = _newSVGNode("g",_g);
			_xaxis_group_labels.id=this._id+"_plotarea_xaxis_labels";
			_fill(_addRec(0,0,1,1,_xaxis_group_labels),"none");
			
			var _xaxis_label_texts=[];
			for(var i=0;i<_xaxis_settings["Items"].length;i++)
			{
				var _group_text = _newSVGNode("g",_xaxis_group_labels);
				var _text = _addText(_xaxis_settings["Items"][i]["Text"],_group_text);
				_style(_text,"font-size: "+_xaxis_settings["LabelsAppearance"]["FontSize"]+";font-family:"+_xaxis_settings["LabelsAppearance"]["FontFamily"]+";");
				_fill(_text,_xaxis_settings["LabelsAppearance"]["FontColor"]);	
				if(_xaxis_settings["LabelsAppearance"]["RotationAngle"])
				{
					_rotate(_text,_xaxis_settings["LabelsAppearance"]["RotationAngle"]);				
				}
				_xaxis_label_texts.push(_group_text);
			}
			if(_yaxis_settings["MinValue"]>=0)
			{
				_translate(_xaxis_group_labels,this._padding_left-_xaxis_group_labels.getBBox().x,_area_height-this._padding_bottom-_xaxis_group_labels.getBBox().height-_xaxis_group_labels.getBBox().y);			
				this._padding_bottom+=_xaxis_group_labels.getBBox().height+10;				
			}
		}

		
		
		var _group_grid = _newSVGNode("g",_g);
		_group_grid.id=this._id+"_plotarea_grid";
		//Line for YAxis:
		var _yaxis_line = _addLine(this._padding_left+0.5,this._padding_top+0.5,this._padding_left+0.5,_area_height+0.5-this._padding_bottom,_group_grid);
		_stroke(_yaxis_line,_yaxis_settings["Color"]);
		var _yaxis_line_height = _area_height-this._padding_bottom - this._padding_top;
		

		//minor ticks and minor grid line
		if(_yaxis_settings["MinorStep"]) //Only if MinorStep is defined
		{
			var _tmp_count = (_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MinorStep"];
			for(var i=_tmp_count;i>=0;i--)
			{
				//Minor ticks
				var _tmp_y = _area_height - this._padding_bottom - (_yaxis_line_height/_tmp_count)*i;
				
				if (_yaxis_settings["MinorTickType"] == "outside") 
				{
					var _major_tick = _addLine(this._padding_left-1-_yaxis_settings["MinorTickSize"]+0.5,_tmp_y+0.5,this._padding_left-1+0.5,_tmp_y +0.5,_group_grid);
					_stroke(_major_tick,_yaxis_settings["Color"]);
				}				
				//minor grid lines
				if(_yaxis_settings["MinorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(this._padding_left+1+0.5,_tmp_y+0.5,_area_width-this._padding_right+0.5,_tmp_y+0.5,_group_grid);
					_stroke(_grid_line,_yaxis_settings["MinorGridLines"]["Color"]);
				}
			}			
		}
		
		
		
		//major ticks and major grid line and labels
		if(_yaxis_settings["MajorStep"]) //Only if MajorStep is defined
		{
			var _tmp_count = (_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"])/_yaxis_settings["MajorStep"];
			for(var i=_tmp_count;i>=0;i--)
			{
				//Major ticks
				var _tmp_y = _area_height - this._padding_bottom - (_yaxis_line_height/_tmp_count)*i;
				
				if(_yaxis_settings["MajorTickType"]=="outside")
				{
					var _major_tick = _addLine(this._padding_left-1-_yaxis_settings["MajorTickSize"]+0.5,_tmp_y+0.5,this._padding_left-1+0.5,_tmp_y +0.5,_group_grid);
					_stroke(_major_tick,_yaxis_settings["Color"]);					
				}
				
				//Labels
				if(_yaxis_settings["LabelsAppearance"]["Visible"])
				{
					if(i==_tmp_count)
					{
						//_yaxis_label_texts
						_translate(_yaxis_group_labels,this._padding_left-10-_yaxis_group_labels.getBBox().width,_tmp_y-_yaxis_group_labels.getBBox().y-_yaxis_group_labels.getBBox().height/2,"clear");
					}
					else
					{
						_translate(_yaxis_label_texts[i],_yaxis_group_labels.getBBox().width - _yaxis_label_texts[i].getBBox().width - 1,(_yaxis_line_height/_tmp_count)*(_tmp_count-i));				
					}				
				}
				//Major grid lines
				if(_yaxis_settings["MajorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(this._padding_left+1+0.5,_tmp_y+0.5,_area_width-this._padding_right+0.5,_tmp_y+0.5,_group_grid);
					_stroke(_grid_line,_yaxis_settings["MajorGridLines"]["Color"]);
				}
			}			
		}

		//Create XAxis
		
		var _min_to_zero_height = 0;
		
		if(_yaxis_settings["MinValue"]<0)
		{
			_min_to_zero_height = (Math.abs(_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*_yaxis_line_height;
		}
		
		var _xaxis_line_width = _area_width - this._padding_right - this._padding_left;
		
		var _items_count = _xaxis_settings["Items"].length;
		//Major ticks and major grid line
		for(var i=0;i<_items_count+1;i++)
		{
			var _tmp_x = this._padding_left+(_xaxis_line_width/_items_count)*i;
			//Grid lines
			if(i!=0)
			{
				
				//Major line
				if(_xaxis_settings["MajorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(_tmp_x+0.5,this._padding_top+0.5,_tmp_x+0.5,_area_height-this._padding_bottom-1+0.5,_group_grid);
					_stroke(_grid_line,_xaxis_settings["MajorGridLines"]["Color"]);				
				}						
				//Minor line
				if(_xaxis_settings["MinorGridLines"]["Visible"])
				{
					var _grid_line = _addLine(_tmp_x-(_xaxis_line_width/_items_count)/2+0.5,this._padding_top+0.5,_tmp_x-(_xaxis_line_width/_items_count)/2+0.5,_area_height-this._padding_bottom-1+0.5,_group_grid);
					_stroke(_grid_line,_xaxis_settings["MinorGridLines"]["Color"]);
				}
				if(_xaxis_settings["LabelsAppearance"]["Visible"])
				{
					_translate(_xaxis_label_texts[i-1],(_xaxis_line_width/_items_count)*(i-0.5) - _xaxis_label_texts[i-1].getBBox().width/2,0,"clear");
				}				
				//Minor ticks
				if(_xaxis_settings["MinorTickType"]=="outside")
				{
					var _minor_tick = _addLine(_tmp_x - (_xaxis_line_width/_items_count)/2 +0.5,_area_height-this._padding_bottom - _min_to_zero_height +1+0.5,_tmp_x - (_xaxis_line_width/_items_count)/2+0.5,_area_height-this._padding_bottom - _min_to_zero_height +1+_xaxis_settings["MinorTickSize"]+0.5,_group_grid);
					_stroke(_minor_tick,_xaxis_settings["Color"]);
				}
			}
			//Major Ticks
			if(_xaxis_settings["MajorTickType"]=="outside")
			{
				var _major_tick = _addLine(_tmp_x+0.5,_area_height-this._padding_bottom - _min_to_zero_height +1+0.5,_tmp_x+0.5,_area_height-this._padding_bottom -_min_to_zero_height +1+_xaxis_settings["MajorTickSize"]+0.5,_group_grid);
				_stroke(_major_tick,_xaxis_settings["Color"]);
			}

		}
		var _xaxis_line = _addLine(this._padding_left+0.5,_area_height+0.5-this._padding_bottom - _min_to_zero_height,_area_width+0.5-this._padding_right,_area_height+0.5-this._padding_bottom - _min_to_zero_height,_group_grid);
		_stroke(_xaxis_line,_xaxis_settings["Color"]);
		
		
		
		if(_yaxis_settings["Title"]!=null)
		{
			//Redraw the title.
			switch(_yaxis_settings["TitleAppearance"]["Position"])
			{
				case "top":
					var _yaxis_group_title_y=this._padding_top;
					break;
				case "bottom":
					var _yaxis_group_title_y=_area_height - this._padding_bottom - _yaxis_group_title.getBBox().height;
					break;
				case "middle":
				default:
					var _yaxis_group_title_y=(_area_height- this._padding_top - this._padding_bottom -_yaxis_group_title.getBBox().height)/2 + this._padding_top;				
					break;					
			}
			
			_translate(_yaxis_group_title,_yaxis_group_title_x -_yaxis_group_title.getBBox().x,_yaxis_group_title_y-_yaxis_group_title.getBBox().y);
		}
	
	
		this._draw_column_series(_g,_settings);
		this._draw_line_series(_g,_settings);
		this._draw_area_series(_g,_settings);

		//Adjust the labels of xaxis group.
		if(_xaxis_settings["LabelsAppearance"]["Visible"])
		{
			if(_yaxis_settings["MinValue"]<0)
			{
				_translate(_xaxis_group_labels,this._padding_left-_xaxis_group_labels.getBBox().x+0.5,_area_height-this._padding_bottom- _min_to_zero_height + 5 - _xaxis_group_labels.getBBox().y+0.5,"clear");			
				_g.appendChild(_xaxis_group_labels);	
			}				
		}
		
		
	},
	_draw_area_series:function(_g,_settings,_series)
	{
		var _yaxis_settings = _settings["PlotArea"]["YAxis"];
		var _xaxis_settings = _settings["PlotArea"]["XAxis"];
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		var _xaxis_line_width = _area_width - this._padding_right - this._padding_left;
		var _yaxis_line_height = _area_height-this._padding_bottom - this._padding_top;
		var _min_to_zero_height = 0;
		
		if(_yaxis_settings["MinValue"]<0)
		{
			_min_to_zero_height = (Math.abs(_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*_yaxis_line_height;
		}
		
		
		//Draw the columns and labels:
		var _items_count = _xaxis_settings["Items"].length;
		var _item_width = _xaxis_line_width/_items_count;
		var _listofseries = _settings["PlotArea"]["ListOfSeries"]; 
		for(var i=0;i<_listofseries.length;i++)
		{
			if(_listofseries[i]["ChartType"]=="Area")
			{
				var _group_series = _newSVGNode("g",_g);
				_group_series.id = this._id+"_plotarea_series"+i;				
				var _old_point_height=null;
				for(var j=0;j<_items_count;j++)
				{
				
					if(_listofseries[i]["Items"][j]["YValue"]==null)
					{
						_old_point_height=null;
					}
					else
					{

						if(_yaxis_settings["MinValue"]<0)
						{
							var _point_height = ((_listofseries[i]["Items"][j]["YValue"]-0)/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))* _yaxis_line_height;						
						}
						else
						{
							var _point_height = ((_listofseries[i]["Items"][j]["YValue"]-_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))* _yaxis_line_height;
						}							
						
						if(_old_point_height!=null && _point_height!=null)
						{
							var _line = _addLine( this._padding_left+(_xaxis_line_width/_items_count)*(j-0.5) + 0.5,_area_height-this._padding_bottom-_min_to_zero_height - _old_point_height + 0.5, this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,_area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,_group_series);
							_stroke(_line,_listofseries[i]["Appearance"]["BackgroundColor"],1,1,"none");

							var _area = _addFourAngles(
							this._padding_left+(_xaxis_line_width/_items_count)*(j-0.5) + 0.5,
							_area_height-this._padding_bottom-_min_to_zero_height - _old_point_height + 0.5,
							 this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,
							 _area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,
							 this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,
							 _area_height-this._padding_bottom-_min_to_zero_height,
							 this._padding_left+(_xaxis_line_width/_items_count)*(j-0.5) + 0.5,
							 _area_height-this._padding_bottom-_min_to_zero_height,
							 _group_series);
							_stroke(_area,"none",0,0,_listofseries[i]["Appearance"]["BackgroundColor"],0.2);
							
							if(_settings["Transitions"])
							{
								//_setAttribute(_line,"visibility","hidden");
								_hidden(_line,i*0.4);
								_hidden(_area,i*0.4);
								_animate(_line,"y1",_area_height-this._padding_bottom-_min_to_zero_height,_area_height-this._padding_bottom-_min_to_zero_height - _old_point_height + 0.5,"0.4s",i*0.4+"s");
								_animate(_line,"y2",_area_height-this._padding_bottom-_min_to_zero_height,_area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,"0.4s",i*0.4+"s");							
								//Animate area
								_animate(_area,"d",
										_get_ourAngles_d(
												 this._padding_left+(_xaxis_line_width/_items_count)*(j-0.5) + 0.5,
												 _area_height-this._padding_bottom-_min_to_zero_height,
												 this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,
												 _area_height-this._padding_bottom-_min_to_zero_height,
												 this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,
												 _area_height-this._padding_bottom-_min_to_zero_height,
												 this._padding_left+(_xaxis_line_width/_items_count)*(j-0.5) + 0.5,
												 _area_height-this._padding_bottom-_min_to_zero_height							 								
										),
										_get_ourAngles_d(
													this._padding_left+(_xaxis_line_width/_items_count)*(j-0.5) + 0.5,
													_area_height-this._padding_bottom-_min_to_zero_height - _old_point_height + 0.5,
													 this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,
													 _area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,
													 this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,
													 _area_height-this._padding_bottom-_min_to_zero_height,
													 this._padding_left+(_xaxis_line_width/_items_count)*(j-0.5) + 0.5,
													 _area_height-this._padding_bottom-_min_to_zero_height								
										),
										"0.4s",i*0.4+"s");							
								
							
							}
							
							
							if(_shape!=null) _group_series.appendChild(_shape);
						}
						
						
						if(_listofseries[i]["MarkersAppearance"]["Visible"])
						{
							var _item_x = this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5);
							var _item_y = _area_height-this._padding_bottom-_min_to_zero_height - _point_height;
							switch (_listofseries[i]["MarkersAppearance"]["MarkersType"]) 
							{
								case "cirlce":
									var _shape = _addCircle(_item_x + 0.5, _item_y + 0.5, 4, _group_series)
									_stroke(_shape, _listofseries[i]["Appearance"]["BackgroundColor"], 2, 1, _settings["BackgroundColor"]);
									if(_settings["Transitions"])
									{
										_hidden(_shape,i*0.4);
										_animate(_shape,"cy",_item_y+_point_height,_item_y + 0.5,"0.4s",i*0.4+"s");
									}
									break;
								case "square":
									var _shape = _addRec(_item_x - 4 + 0.5, _item_y - 4 + 0.5, _item_x + 4 + 0.5, _item_y + 4 + 0.5, _group_series);
									_stroke(_shape, _listofseries[i]["Appearance"]["BackgroundColor"], 1, 1, _listofseries[i]["Appearance"]["BackgroundColor"]);
									if(_settings["Transitions"])
									{
										_hidden(_shape,i*0.4);
										_animate(_shape,"y",_item_y + _point_height - 4 + 0.5,_item_y - 4 + 0.5,"0.4s",i*0.4+"s");
									}
									break;
								case "triangles":
									var _shape = _addTriangles(_item_x + 0.5, _item_y + 0.5 - 4, 4 * Math.sqrt(3), _group_series);
									_stroke(_shape, _listofseries[i]["Appearance"]["BackgroundColor"], 1, 1, _listofseries[i]["Appearance"]["BackgroundColor"]);
									if(_settings["Transitions"])
									{
										_hidden(_shape,i*0.4);
										_animate(_shape,"d", 
												 _get_triagles_d(
													_item_x + 0.5,
													_item_y + _point_height + 0.5 - 4,
													4 * Math.sqrt(3)											
												),
												_get_triagles_d(
													_item_x + 0.5,
													_item_y + 0.5 - 4,
													4 * Math.sqrt(3)
												),
												"0.4s",i*0.4+"s");
									}
									break;
							}
						}
	
		
						if(_settings["Transitions"])
						{
							_hidden(_shape,i*0.4);
							_animate(_shape,"cy",_area_height-this._padding_bottom-_min_to_zero_height,_area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,"0.4s",i*0.4+"s",_animation_visible_onbegin);
						}
						
						
						
						_addEvent(_shape,"mouseover",_line_mark_onmouseover,false);
						_addEvent(_shape,"mouseout",_line_mark_onmouseout,false);
						
						
						_old_point_height=_point_height; 
					
						if(_listofseries[i]["LabelsAppearance"]["Visible"])
						{
							var _group_label = _newSVGNode("g",_group_series);
							var _label_text = _listofseries[i]["LabelsAppearance"]["DataFormatString"];
							_label_text = _label_text.replace("{0}",_listofseries[i]["Items"][j]["YValue"]);
							var _label = _addText(_label_text,_group_label);
							_style(_label,"font-size: "+_listofseries[i]["LabelsAppearance"]["FontSize"]+";font-family:"+_listofseries[i]["LabelsAppearance"]["FontFamily"]+";");	
							_fill(_label,_listofseries[i]["LabelsAppearance"]["FontColor"]);
							_rotate(_label,_listofseries[i]["LabelsAppearance"]["RotationAngle"]);
							
							
							
							switch(_listofseries[i]["LabelsAppearance"]["Position"])
							{
								case "above":
									_translate(_group_label,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) - _group_label.getBBox().width/2, _area_height-this._padding_bottom- _min_to_zero_height-_point_height   - 7 - _group_label.getBBox().height - _group_label.getBBox().y );
									break;
								case "below":
									_translate(_group_label,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) - _group_label.getBBox().width/2, _area_height-this._padding_bottom- _min_to_zero_height-_point_height   + 7  - _group_label.getBBox().y );
									break;
								case "right":
									_translate(_group_label,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 10, _area_height-this._padding_bottom- _min_to_zero_height-_point_height  - _group_label.getBBox().height/2  - _group_label.getBBox().y );
									break;
								case "left":
									_translate(_group_label,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) - _group_label.getBBox().width - 5, _area_height-this._padding_bottom- _min_to_zero_height-_point_height  - _group_label.getBBox().height/2  - _group_label.getBBox().y );
								default:
									break;													
							}
							if(_settings["Transitions"])
							{
								//_setAttribute(_label,"visibility","hidden");
								_hidden(_label,_listofseries.length*0.4+j*0.1);
								_animate(_label,"fill-opacity",0,1,"0.5s", _listofseries.length*0.4+j*0.1+"s");							
							}
						}


						
					}
							
				

				}
			}
			
		}
		
	},
	_draw_scatter_series:function(_g,_settings)
	{

		var _yaxis_settings = _settings["PlotArea"]["YAxis"];
		var _xaxis_settings = _settings["PlotArea"]["XAxis"];
		var _listofseries = _settings["PlotArea"]["ListOfSeries"];
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		var _xaxis_line_width = _area_width - this._padding_right - this._padding_left;
		var _yaxis_line_height = _area_height-this._padding_bottom - this._padding_top;
		var _min_to_zero_height = 0;
		var _min_to_zero_width = 0;
		
		if(_yaxis_settings["MinValue"]<0)
		{
			_min_to_zero_height = (Math.abs(_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*_yaxis_line_height;
		}
		if(_xaxis_settings["MinValue"]<0)
		{
			_min_to_zero_width = (Math.abs(_xaxis_settings["MinValue"])/(_xaxis_settings["MaxValue"]-_xaxis_settings["MinValue"]))*_xaxis_line_width;
		}

		var _old_item_x=null;
		var _old_item_y=null;
		
		
		for(var i=0;i<_listofseries.length;i++)
		{
			if(_listofseries[i]["ChartType"]=="Scatter")
			{
				var _group_series = _newSVGNode("g",_g);
				_group_series.id = this._id+"_plotarea_series"+i;
				for(var j=0;j<_listofseries[i]["Items"].length;j++)
				{
					
					if(_listofseries[i]["Items"][j]["YValue"]==null||_listofseries[i]["Items"][j]["XValue"]==null)
					{
						_old_item_x=null;
						_old_item_y=null;
					}
					else
					{
						var _item_x = this._padding_left+_min_to_zero_width+(_listofseries[i]["Items"][j]["XValue"]/(_xaxis_settings["MaxValue"]-_xaxis_settings["MinValue"]))*_xaxis_line_width;
						var _item_y = _area_height-this._padding_bottom- _min_to_zero_height - (_listofseries[i]["Items"][j]["YValue"]/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*_yaxis_line_height;
						
						
						if(_listofseries[i]["ItemConnected"])
						{
							if(_old_item_x!=null && _old_item_y!=null)
							{
								var _line = _addLine(_old_item_x,_old_item_y,_item_x,_item_y,_group_series);
								_stroke(_line,_listofseries[i]["Appearance"]["BackgroundColor"],1,1,"none");					
								if(_shape!=null) _group_series.appendChild(_shape);
							
							
								if(_settings["Transitions"])
								{
									_animate(_line,"opacity",0,1,"0.5s");
								}							
							}
							_old_item_x=_item_x;
							_old_item_y=_item_y;	
						}


						if (_listofseries[i]["MarkersAppearance"]["Visible"])
						{
							switch(_listofseries[i]["MarkersAppearance"]["MarkersType"])
							{
								case "square":
										var _shape = _addRec(_item_x-4+0.5,_item_y-4+0.5,_item_x+4+0.5,_item_y+4+0.5,_group_series);						
										_stroke(_shape,_listofseries[i]["Appearance"]["BackgroundColor"],1,1,_listofseries[i]["Appearance"]["BackgroundColor"]);						
									break;
								case "triangles":
										var _shape = _addTriangles(_item_x+0.5,_item_y+0.5-4,4*Math.sqrt(3),_group_series);						
										_stroke(_shape,_listofseries[i]["Appearance"]["BackgroundColor"],1,1,_listofseries[i]["Appearance"]["BackgroundColor"]);
									break;	
								case "cirlce":
								default:
										var _shape = _addCircle(_item_x+0.5,_item_y+0.5,4,_group_series);							
										_stroke(_shape,_listofseries[i]["Appearance"]["BackgroundColor"],2,1,_settings["BackgroundColor"]);
									break;
							}
							
							if(_settings["Transitions"])
							{
								_animate(_shape,"opacity",0,1,"0.5s");
							}
							
						}


						if(_listofseries[i]["LabelsAppearance"]["Visible"])
						{
							var _group_label = _newSVGNode("g",_group_series);
							var _label_text = _listofseries[i]["LabelsAppearance"]["DataFormatString"];
							
							_label_text = _label_text.replace("{0}",_listofseries[i]["Items"][j]["XValue"]);
							_label_text = _label_text.replace("{1}",_listofseries[i]["Items"][j]["YValue"]);
							
							var _label = _addText(_label_text,_group_label);
							_style(_label,"font-size: "+_listofseries[i]["LabelsAppearance"]["FontSize"]+";font-family:"+_listofseries[i]["LabelsAppearance"]["FontFamily"]+";");	
							_fill(_label,_listofseries[i]["LabelsAppearance"]["FontColor"]);
							_rotate(_label,_listofseries[i]["LabelsAppearance"]["RotationAngle"]);
							
							
							
							switch(_listofseries[i]["LabelsAppearance"]["Position"])
							{
								case "above":
									_translate(_group_label,_item_x - _group_label.getBBox().width/2 - _group_label.getBBox().x,_item_y - 7 - _group_label.getBBox().height - _group_label.getBBox().y );
									break;
								case "below":
									_translate(_group_label,_item_x - _group_label.getBBox().width/2- _group_label.getBBox().x, _item_y   + 7  - _group_label.getBBox().y );
									break;
								case "right":
									_translate(_group_label,_item_x + 10- _group_label.getBBox().x, _item_y  - _group_label.getBBox().height/2  - _group_label.getBBox().y );
									break;
								case "left":
									_translate(_group_label,_item_x - _group_label.getBBox().width - 5- _group_label.getBBox().x, _item_y  - _group_label.getBBox().height/2  - _group_label.getBBox().y );
								default:
									break;													
							}
							if(_settings["Transitions"])
							{
								_animate(_label,"fill-opacity",0,1,"0.5s");							
							}
						}		
						
					}

				}
						
				_old_item_x=null;
				_old_item_y=null;
				
			}
		}		
		
		
		
	},	
	_draw_line_series:function(_g,_settings)
	{
		var _yaxis_settings = _settings["PlotArea"]["YAxis"];
		var _xaxis_settings = _settings["PlotArea"]["XAxis"];
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		var _xaxis_line_width = _area_width - this._padding_right - this._padding_left;
		var _yaxis_line_height = _area_height-this._padding_bottom - this._padding_top;
		var _min_to_zero_height = 0;
		
		if(_yaxis_settings["MinValue"]<0)
		{
			_min_to_zero_height = (Math.abs(_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*_yaxis_line_height;
		}
		
		
		//Draw the columns and labels:
		var _items_count = _xaxis_settings["Items"].length;
		var _item_width = _xaxis_line_width/_items_count;
		var _listofseries = _settings["PlotArea"]["ListOfSeries"]; 
		for(var i=0;i<_listofseries.length;i++)
		{
			if(_listofseries[i]["ChartType"]=="Line")
			{
				var _group_series = _newSVGNode("g",_g);
				_group_series.id = this._id+"_plotarea_series"+i;				
				var _old_point_height=null;
				for(var j=0;j<_items_count;j++)
				{
				
					if(_listofseries[i]["Items"][j]["YValue"]==null)
					{
						_old_point_height=null;
					}
					else
					{

						if(_yaxis_settings["MinValue"]<0)
						{
							var _point_height = ((_listofseries[i]["Items"][j]["YValue"]-0)/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))* _yaxis_line_height;						
						}
						else
						{
							var _point_height = ((_listofseries[i]["Items"][j]["YValue"]-_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))* _yaxis_line_height;
						}							
						
						if(_old_point_height!=null && _point_height!=null)
						{
							var _line = _addLine( this._padding_left+(_xaxis_line_width/_items_count)*(j-0.5) + 0.5,_area_height-this._padding_bottom-_min_to_zero_height - _old_point_height + 0.5, this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,_area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,_group_series);
							_stroke(_line,_listofseries[i]["Appearance"]["BackgroundColor"],1,1,"none");
							if(_shape!=null) _group_series.appendChild(_shape);
	
							if(_settings["Transitions"])
							{
								//_setAttribute(_line,"visibility","hidden");
								_hidden(_line,i*0.4);
								_animate(_line,"y1",_area_height-this._padding_bottom-_min_to_zero_height,_area_height-this._padding_bottom-_min_to_zero_height - _old_point_height + 0.5,"0.4s",i*0.4+"s",_animation_visible_onbegin);
								_animate(_line,"y2",_area_height-this._padding_bottom-_min_to_zero_height,_area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,"0.4s",i*0.4+"s",_animation_visible_onbegin);							
							}
	
						}
						
						
						if(_listofseries[i]["MarkersAppearance"]["Visible"])
						{
							switch(_listofseries[i]["MarkersAppearance"]["MarkersType"])
							{
								case "square":
									var _shape = _addRec(this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) -4 + 0.5,_area_height-this._padding_bottom-_min_to_zero_height - _point_height  -4 + 0.5,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) +4 + 0.5,_area_height-this._padding_bottom-_min_to_zero_height - _point_height +4 + 0.5,_group_series);						
									_stroke(_shape,_listofseries[i]["Appearance"]["BackgroundColor"],2,1,_listofseries[i]["Appearance"]["BackgroundColor"]);						
									if(_settings["Transitions"])
									{
										_hidden(_shape,i*0.4);
										_animate(_shape,"y",_area_height-this._padding_bottom-_min_to_zero_height,_area_height-this._padding_bottom- _min_to_zero_height - _point_height,"0.4s",i*0.4+"s");
									}
									break;
								case "triangles":
									var _shape = _addTriangles( this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5),_area_height-this._padding_bottom-_min_to_zero_height - _point_height-4,4*Math.sqrt(3),_group_series);						
									_stroke(_shape,_listofseries[i]["Appearance"]["BackgroundColor"],1,1,_listofseries[i]["Appearance"]["BackgroundColor"]);							
									if(_settings["Transitions"])
									{
										_hidden(_shape,i*0.4);
										_animate(_shape,"d",
												_get_triagles_d (
													this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5),
													_area_height-this._padding_bottom-_min_to_zero_height-4,
													4*Math.sqrt(3)											
												),
												_get_triagles_d(
													this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5),
													_area_height-this._padding_bottom-_min_to_zero_height - _point_height-4,
													4*Math.sqrt(3)											
												),"0.4s",i*0.4+"s");
									}
									break;	
								case "circle":
								default:
									var _shape = _addCircle( this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 0.5,_area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,4,_group_series);							
									_stroke(_shape,_listofseries[i]["Appearance"]["BackgroundColor"],2,1,_settings["BackgroundColor"]);
									
									if(_settings["Transitions"])
									{
										_hidden(_shape,i*0.4);
										_animate(_shape,"cy",_area_height-this._padding_bottom-_min_to_zero_height,_area_height-this._padding_bottom-_min_to_zero_height - _point_height + 0.5,"0.4s",i*0.4+"s",_animation_visible_onbegin);
									}

									break;
							}						
							
							
		
							
							_addEvent(_shape,"mouseover",_line_mark_onmouseover,false);
							_addEvent(_shape,"mouseout",_line_mark_onmouseout,false);							
						}
						

						_old_point_height=_point_height; 
					
						if(_listofseries[i]["LabelsAppearance"]["Visible"])
						{
							var _group_label = _newSVGNode("g",_group_series);
							var _label_text = _listofseries[i]["LabelsAppearance"]["DataFormatString"];
							_label_text = _label_text.replace("{0}",_listofseries[i]["Items"][j]["YValue"]);
							var _label = _addText(_label_text,_group_label);
							_style(_label,"font-size: "+_listofseries[i]["LabelsAppearance"]["FontSize"]+";font-family:"+_listofseries[i]["LabelsAppearance"]["FontFamily"]+";");	
							_fill(_label,_listofseries[i]["LabelsAppearance"]["FontColor"]);
							_rotate(_label,_listofseries[i]["LabelsAppearance"]["RotationAngle"]);
							
							
							
							switch(_listofseries[i]["LabelsAppearance"]["Position"])
							{
								case "above":
									_translate(_group_label,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) - _group_label.getBBox().width/2, _area_height-this._padding_bottom- _min_to_zero_height-_point_height   - 7 - _group_label.getBBox().height - _group_label.getBBox().y );
									break;
								case "below":
									_translate(_group_label,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) - _group_label.getBBox().width/2, _area_height-this._padding_bottom- _min_to_zero_height-_point_height   + 7  - _group_label.getBBox().y );
									break;
								case "right":
									_translate(_group_label,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) + 10, _area_height-this._padding_bottom- _min_to_zero_height-_point_height  - _group_label.getBBox().height/2  - _group_label.getBBox().y );
									break;
								case "left":
									_translate(_group_label,this._padding_left+(_xaxis_line_width/_items_count)*(j+0.5) - _group_label.getBBox().width - 5, _area_height-this._padding_bottom- _min_to_zero_height-_point_height  - _group_label.getBBox().height/2  - _group_label.getBBox().y );
								default:
									break;													
							}
							if(_settings["Transitions"])
							{
								//_setAttribute(_label,"visibility","hidden");
								_hidden(_label,_listofseries.length*0.4+j*0.1);
								_animate(_label,"fill-opacity",0,1,"0.5s", _listofseries.length*0.4+j*0.1+"s");							
							}
						}


						
					}
							
				

				}
			}
			
		}
		
	},			
	_draw_column_series:function(_g,_settings)
	{
		var _yaxis_settings = _settings["PlotArea"]["YAxis"];
		var _xaxis_settings = _settings["PlotArea"]["XAxis"];
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		var _xaxis_line_width = _area_width - this._padding_right - this._padding_left;
		var _yaxis_line_height = _area_height-this._padding_bottom - this._padding_top;
		var _min_to_zero_height = 0;

		
		//Column gradient
		var _defs = _obj(this._id+"_defs");
		var _linearGradient = _newSVGNode("linearGradient",_defs);
		_linearGradient.id = this._id+"_column_gradient";
		_setAttribute(_linearGradient,"gradientTransform","rotate(0)");
		var _stop = _newSVGNode("stop",_linearGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0");
		_setAttribute(_stop,"offset","0%");
		var _stop = _newSVGNode("stop",_linearGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0.3");
		_setAttribute(_stop,"offset","25%");
		var _stop = _newSVGNode("stop",_linearGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0");
		_setAttribute(_stop,"offset","100%");



		
		if(_yaxis_settings["MinValue"]<0)
		{
			_min_to_zero_height = (Math.abs(_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*_yaxis_line_height;
		}
		
		
		//Draw the columns and labels:
		var _items_count = _xaxis_settings["Items"].length;
		var _item_width = _xaxis_line_width/_items_count;
		var _listofseries = _settings["PlotArea"]["ListOfSeries"]; 
		
		var _total_columns=0;
		for(var i=0;i<_listofseries.length;i++)
			if(_listofseries[i]["ChartType"]=="Column") _total_columns++;
		
		
		
		
		var _column_width = _item_width/(1+1.4*_total_columns);

		var _start = _column_width*0.7;
		for(var i=0;i<_listofseries.length;i++)
		{
			if(_listofseries[i]["ChartType"]=="Column")
			{
				var _group_series = _newSVGNode("g",_g);
				_group_series.id = this._id+"_plotarea_series"+i;
				for(var j=0;j<_items_count;j++)
				{
					var _base_x = this._padding_left+_item_width*j;
					
					
					
					if(_listofseries[i]["Items"][j]["YValue"]!=null)
					{
						if(_yaxis_settings["MinValue"]<0)
						{
							var _col_height = ((_listofseries[i]["Items"][j]["YValue"]-0)/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))* _yaxis_line_height;						
						}
						else
						{
							var _col_height = ((_listofseries[i]["Items"][j]["YValue"]-_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))* _yaxis_line_height;
						}
						
						//Columns
						var _group_col = _newSVGNode("g",_group_series);
						var _column = _addRec(_base_x+_start,_area_height-this._padding_bottom-_col_height - _min_to_zero_height,_base_x+_start+_column_width,_area_height-this._padding_bottom - _min_to_zero_height,_group_col);
						_stroke(_column,_brightness(_listofseries[i]["Appearance"]["BackgroundColor"],0.85),1,1,_listofseries[i]["Appearance"]["BackgroundColor"]);
						
						var _column_gradient = _addRec(_base_x+_start,_area_height-this._padding_bottom-_col_height- _min_to_zero_height,_base_x+_start+_column_width,_area_height-this._padding_bottom - _min_to_zero_height,_group_col);
						_fill(_column_gradient,"url(#"+this._id+"_column_gradient)");
						
	
						if(_settings["Transitions"])
						{
							//_setAttribute(_column,"visibility","hidden");
							//_setAttribute(_column_gradient,"visibility","hidden");
							
							if(_col_height<0)
							{
								_animate(_column,"height",0,-_col_height,"0.7s",null,_animation_visible_onbegin);
								_animate(_column_gradient,"height",0,-_col_height,"0.7s",null,_animation_visible_onbegin);						
							}
							else
							{
								_animate(_column,"y",_area_height-this._padding_bottom - _min_to_zero_height,_area_height-this._padding_bottom-_col_height - _min_to_zero_height,"0.7s",null,_animation_visible_onbegin);
								_animate(_column,"height",0,_col_height,"0.7s",null);
								_animate(_column_gradient,"y",_area_height-this._padding_bottom - _min_to_zero_height,_area_height-this._padding_bottom-_col_height - _min_to_zero_height,"0.7s",null,_animation_visible_onbegin);
								_animate(_column_gradient,"height",0,_col_height,"0.7s",null);						
							}						
						}
					
						//Add event
						_addEvent(_column_gradient,"mouseover",_column_mouseover,false);
						
						//Labels
						if(_listofseries[i]["LabelsAppearance"]["Visible"])
						{
							var _group_label = _newSVGNode("g",_group_col);
							var _label_text = _listofseries[i]["LabelsAppearance"]["DataFormatString"];
							_label_text = _label_text.replace("{0}",_listofseries[i]["Items"][j]["YValue"]);
							var _label = _addText(_label_text,_group_label);
							_style(_label,"font-size: "+_listofseries[i]["LabelsAppearance"]["FontSize"]+";font-family:"+_listofseries[i]["LabelsAppearance"]["FontFamily"]+";");	
							_fill(_label,_listofseries[i]["LabelsAppearance"]["FontColor"]);
							_rotate(_label,_listofseries[i]["LabelsAppearance"]["RotationAngle"]);
							
							switch(_listofseries[i]["LabelsAppearance"]["Position"])
							{
								case "center":
									_translate(_group_label,_base_x+_start+(_column_width-_group_label.getBBox().width)/2 - _group_label.getBBox().x,_area_height-this._padding_bottom- (_col_height + _group_label.getBBox().height)/2 - _group_label.getBBox().y - 5);
									break;
								case "insidebase":
									_translate(_group_label,_base_x+_start+(_column_width-_group_label.getBBox().width)/2 - _group_label.getBBox().x,_area_height-this._padding_bottom- _group_label.getBBox().height - _group_label.getBBox().y - 5);
									break;
								case "insideend":
									_translate(_group_label,_base_x+_start+(_column_width-_group_label.getBBox().width)/2 - _group_label.getBBox().x,_area_height-this._padding_bottom-_col_height - _group_label.getBBox().y + 5);
									break;
								case "outsideend":
								default:
									_translate(_group_label,_base_x+_start+(_column_width-_group_label.getBBox().width)/2 - _group_label.getBBox().x,_area_height-this._padding_bottom- _min_to_zero_height-_col_height   - ((_col_height<0)?(-5):(5+_group_label.getBBox().height)) - _group_label.getBBox().y );
									break;													
							}
							if(_settings["Transitions"])
							{
								//_setAttribute(_label,"visibility","hidden");
								_hidden(_label,0.7);							
								_animate(_label,"fill-opacity",0,1,"0.5s","0.7s");							
							}
						}
						
					}
				}
				_start+=_column_width*1.4;
			}

		}		
	},
	
	_draw_bar_series:function(_g,_settings)
	{

		var _yaxis_settings = _settings["PlotArea"]["YAxis"];
		var _xaxis_settings = _settings["PlotArea"]["XAxis"];
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		var _yaxis_line_width = _area_width - this._padding_right - this._padding_left;
		var _xaxis_line_height = _area_height-this._padding_bottom - this._padding_top;
		var _min_to_zero_width = 0;
		
		
		//Bar gradient
		var _defs = _obj(this._id+"_defs");
		var _linearGradient = _newSVGNode("linearGradient",_defs);
		_linearGradient.id = this._id+"_bar_gradient";
		_setAttribute(_linearGradient,"gradientTransform","rotate(90)");
		var _stop = _newSVGNode("stop",_linearGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0");
		_setAttribute(_stop,"offset","0%");
		var _stop = _newSVGNode("stop",_linearGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0.3");
		_setAttribute(_stop,"offset","25%");
		var _stop = _newSVGNode("stop",_linearGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0");
		_setAttribute(_stop,"offset","100%");		
		
		
		if(_yaxis_settings["MinValue"]<0)
		{
			_min_to_zero_width = (Math.abs(_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))*_yaxis_line_width;
		}
		
		
		//Draw the columns and labels:
		var _items_count = _xaxis_settings["Items"].length;
		var _item_height = _xaxis_line_height/_items_count;
		var _listofseries = _settings["PlotArea"]["ListOfSeries"]; 
		
		var _total_columns=0;
		for(var i=0;i<_listofseries.length;i++)
			if(_listofseries[i]["ChartType"]=="Bar") _total_columns++;
		
		
		
		
		var _column_height = _item_height/(1+1.4*_total_columns);

		var _start = _column_height*0.7;
		
		for(var i=0;i<_listofseries.length;i++)
		{
			if(_listofseries[i]["ChartType"]=="Bar")
			{
				var _group_series = _newSVGNode("g",_g);
				_group_series.id = this._id+"_plotarea_series"+i;
				for(var j=0;j<_items_count;j++)
				{
					var _base_y = _area_height - this._padding_bottom - _item_height*j;
					
					if(_listofseries[i]["Items"][j]["YValue"]!=null)
					{
						if(_yaxis_settings["MinValue"]<0)
						{
							var _col_width = ((_listofseries[i]["Items"][j]["YValue"]-0)/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))* _yaxis_line_width;						
						}
						else
						{
							var _col_width = ((_listofseries[i]["Items"][j]["YValue"]-_yaxis_settings["MinValue"])/(_yaxis_settings["MaxValue"]-_yaxis_settings["MinValue"]))* _yaxis_line_width;
						}
						
						//Columns
						var _group_col = _newSVGNode("g",_group_series);
						var _column = _addRec(this._padding_left + _min_to_zero_width+0.5,_base_y-_start-_column_height+0.5,this._padding_left + _min_to_zero_width + _col_width+0.5,_base_y-_start+0.5,_group_col);
						_stroke(_column,_brightness(_listofseries[i]["Appearance"]["BackgroundColor"],0.85),1,1,_listofseries[i]["Appearance"]["BackgroundColor"]);
						
						var _column_gradient = _addRec(this._padding_left + _min_to_zero_width+0.5,_base_y-_start-_column_height+0.5,this._padding_left + _min_to_zero_width + _col_width+0.5,_base_y-_start+0.5,_group_col);
						_fill(_column_gradient,"url(#"+this._id+"_bar_gradient)");
						
						
						if(_settings["Transitions"])
						{
							//_setAttribute(_column,"visibility","hidden");
							//_setAttribute(_column_gradient,"visibility","hidden");
							
							_hidden(_column,0);
							_hidden(_column_gradient,0);
	
							
							if(_col_width<0)
							{
								_animate(_column,"x",this._padding_left + _min_to_zero_width+0.5,this._padding_left + _min_to_zero_width + _col_width+0.5,"0.7s",null,_animation_visible_onbegin);
								_animate(_column,"width",0,-_col_width,"0.7s",null);
								_animate(_column_gradient,"x",this._padding_left + _min_to_zero_width+0.5,this._padding_left + _min_to_zero_width + _col_width+0.5,"0.7s",null,_animation_visible_onbegin);
								_animate(_column_gradient,"width",0,-_col_width,"0.7s",null);
							}
							else
							{
								_animate(_column,"width",0,_col_width,"0.7s",null,_animation_visible_onbegin);
								_animate(_column_gradient,"width",0,_col_width,"0.7s",null,_animation_visible_onbegin);						
							}						
						}
	
						/*
						
						//Add event
						_addEvent(_column_gradient,"mouseover",_column_mouseover,false);
						*/
						//Labels
						if(_listofseries[i]["LabelsAppearance"]["Visible"])
						{
							var _group_label = _newSVGNode("g",_group_col);
							var _label_text = _listofseries[i]["LabelsAppearance"]["DataFormatString"];
							_label_text = _label_text.replace("{0}",_listofseries[i]["Items"][j]["YValue"]);
							var _label = _addText(_label_text,_group_label);
							_style(_label,"font-size: "+_listofseries[i]["LabelsAppearance"]["FontSize"]+";font-family:"+_listofseries[i]["LabelsAppearance"]["FontFamily"]+";");	
							_fill(_label,_listofseries[i]["LabelsAppearance"]["FontColor"]);
							_rotate(_label,_listofseries[i]["LabelsAppearance"]["RotationAngle"]);
							
							switch(_listofseries[i]["LabelsAppearance"]["Position"])
							{
								case "center":
									_translate(_group_label, this._padding_left + _min_to_zero_width + (_col_width - _group_label.getBBox().width)/2  - _group_label.getBBox().x,_base_y-_start-(_column_height-_group_label.getBBox().height)/2 - _group_label.getBBox().height - _group_label.getBBox().y);
									break;
								case "insidebase":
									_translate(_group_label, this._padding_left + _min_to_zero_width + ((_col_width<0)?(-_group_label.getBBox().width-5):5)  - _group_label.getBBox().x,_base_y-_start-(_column_height-_group_label.getBBox().height)/2 - _group_label.getBBox().height - _group_label.getBBox().y);
									break;
								case "insideend":
									_translate(_group_label, this._padding_left + _min_to_zero_width + _col_width - ((_col_width<0)?(-5):(_group_label.getBBox().width+5))  - _group_label.getBBox().x,_base_y-_start-(_column_height-_group_label.getBBox().height)/2 - _group_label.getBBox().height - _group_label.getBBox().y);
									break;
								case "outsideend":
								default:
									_translate(_group_label, this._padding_left + _min_to_zero_width + _col_width + ((_col_width<0)?(-_group_label.getBBox().width-5):(5))  - _group_label.getBBox().x,_base_y-_start-(_column_height-_group_label.getBBox().height)/2 - _group_label.getBBox().height - _group_label.getBBox().y);
									break;													
							}
							if(_settings["Transitions"])
							{
								//_setAttribute(_label,"visibility","hidden");
								_hidden(_label,0.7);
								_animate(_label,"fill-opacity",0,1,"0.5s","0.7s");							
							}
						}
						
					}
					
				}
				_start+=_column_height*1.4;
			}

		}

		
	},
	_draw_pie_series:function(_g,_settings)
	{
		var _series = _settings["PlotArea"]["ListOfSeries"][0];
		var _area_width = _settings["Width"];
		var _area_height = _settings["Height"];
		var _center_x = this._padding_left+(_area_width-this._padding_left-this._padding_right)/2;		
		var _center_y = this._padding_top+(_area_height-this._padding_top-this._padding_bottom)/2;		
		var _total = 0;
		for(var i=0;i<_series["Items"].length;i++)
		{
			_total+=_series["Items"][i]["YValue"];
		}
		
		var _available_width = _area_width-this._padding_left-this._padding_right;
		var _available_height = _area_height-this._padding_top-this._padding_bottom;

		var _radius = ((_available_width<_available_height)?(_available_width-100):(_available_height-100))/2;


		
		//Column gradient
		var _defs = _obj(this._id+"_defs");
		var _radialGradient = _newSVGNode("radialGradient",_defs);
		_radialGradient.id = this._id+"_normal";
		_setAttribute(_radialGradient,"gradientUnits","userSpaceOnUse");
		_setAttribute(_radialGradient,"r",_radius);
		_setAttribute(_radialGradient,"cx",_center_x);
		_setAttribute(_radialGradient,"cy",_center_y);
		_setAttribute(_radialGradient,"fx",_center_x);
		_setAttribute(_radialGradient,"fy",_center_y);
		
		var _stop = _newSVGNode("stop",_radialGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0.06");
		_setAttribute(_stop,"offset","0%");
		var _stop = _newSVGNode("stop",_radialGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0.2");
		_setAttribute(_stop,"offset","83%");
		var _stop = _newSVGNode("stop",_radialGradient);
		_style(_stop,"stop-color:#fff;stop-opacity:0");
		_setAttribute(_stop,"offset","95%");
		

		var _group_pie = _newSVGNode("g",_g);
		_group_pie.id = this._id+"_plotarea_pie";


		
		
		
		var _start = -_series["StartAngle"];
		for(var i=0;i<_series["Items"].length;i++)
		{
			var _group_arc = _newSVGNode("g",_group_pie);
			
			var _angle = (_series["Items"][i]["YValue"]/_total)*360;
			
			var _exploded_x=0;
			var _exploded_y=0;
			
			if(_series["Items"][i]["Exploded"])
			{
				_exploded_x = _radius*0.15*Math.cos((_start+_angle/2)*Math.PI/180);				
				_exploded_y = _radius*0.15*Math.sin((_start+_angle/2)*Math.PI/180);				
			
				var _radialExploded = _radialGradient.cloneNode(true);
				_radialExploded.id=this._id+"_exploded"+i;
				_setAttribute(_radialExploded,"cx",_center_x+_exploded_x);
				_setAttribute(_radialExploded,"cy",_center_y+_exploded_y);
				_setAttribute(_radialExploded,"fx",_center_x+_exploded_x);
				_setAttribute(_radialExploded,"fy",_center_y+_exploded_y);
				_defs.appendChild(_radialExploded);
			}

			var _arc= _addArc(_center_x+_exploded_x,_center_y+_exploded_y,_radius,_start,_angle,_group_arc);
			_stroke(_arc,"none",0.5,1,_series["Items"][i]["BackgroundColor"],1);
			var _arc_gradient= _addArc(_center_x+_exploded_x,_center_y+_exploded_y,_radius,_start,_angle,_group_arc);
			_stroke(_arc_gradient,"none",0.5,1,"url(#"+this._id+  ((_series["Items"][i]["Exploded"])?"_exploded"+i:"_normal") +")",1);				

			//Event:
			_addEvent(_arc_gradient,"mouseover",_pie_onmouseover,true);
			//_arc_gradient.onmouseenter = _pie_onmouseover;
			
			//Labels
			var _group_label = _newSVGNode("g",_group_pie);
			var _label = _addText(_series["LabelsAppearance"]["DataFormatString"].replace("{0}",_series["Items"][i]["YValue"]),_group_label);
			_style(_label,"font-size: "+_series["LabelsAppearance"]["FontSize"]+";font-family:"+_series["LabelsAppearance"]["FontFamily"]+";");
			_fill(_label,_series["LabelsAppearance"]["FontColor"]);
			
			
			switch(_series["LabelsAppearance"]["Position"])
			{
				case "column":
					break;
				case "circle":
				default:
					var _label_radius = _radius*((_series["Items"][i]["Exploded"])?1.15:1)+30;
					var _label_x = _center_x+(_label_radius)*Math.cos((_start+_angle/2)*Math.PI/180);				
					var _label_y = _center_y+(_label_radius)*Math.sin((_start+_angle/2)*Math.PI/180);				
					var _line_x = _center_x+(_label_radius-25)*Math.cos((_start+_angle/2)*Math.PI/180);				
					var _line_y = _center_y+(_label_radius-25)*Math.sin((_start+_angle/2)*Math.PI/180);		
					var _line1 = _addLine(_line_x+0.5,_line_y+0.5,_label_x+0.5,_label_y+0.5,_group_pie);
					
					if(_start+_angle/2>90)
					{
						var _line2 = _addLine(_label_x+0.5,_label_y+0.5,_label_x+0.5-12,_label_y+0.5,_group_pie);
						_translate(_group_label,_label_x - _group_label.getBBox().width - 15 -_group_label.getBBox().x,_label_y-_group_label.getBBox().height/2 - _group_label.getBBox().y);												
					}
					else
					{
						var _line2 = _addLine(_label_x+0.5,_label_y+0.5,_label_x+12+0.5,_label_y+0.5,_group_pie);
						_translate(_group_label,_label_x +15-_group_label.getBBox().x,_label_y-_group_label.getBBox().height/2 - _group_label.getBBox().y);
					}
					_stroke(_line1,_series["LabelsAppearance"]["FontColor"]);	
					_stroke(_line2,_series["LabelsAppearance"]["FontColor"]);

					break;
			}
			
			if(_settings["Transitions"])
			{
				_hidden(_label,2.5+0.1*i);
				_hidden(_line1,2.5+0.1*i);
				_hidden(_line2,2.5+0.1*i);				
				
				_animate(_label,"opacity",0,1,"0.5s",2.5+0.1*i+"s");
				_animate(_line1,"opacity",0,1,"0.5s",2.5+0.1*i+"s");
				_animate(_line2,"opacity",0,1,"0.5s",2.5+0.1*i+"s");				
			}

			_start+=_angle;
		}
		
		this._end_animation = false;
		if(_settings["Transitions"])
		{
			_animateTransform(_group_pie,"rotate","360 "+_center_x+" "+_center_y+";0 "+_center_x+" "+_center_y,"0 0.3 0 1","2.5s",null,null);			
			setTimeout("pie_end_animtation("+this._id+")",2.5*1000);
		}
		
	},


	_handle_column_mouseover:function(_column)
	{
		_column_x = parseFloat(_column.getAttribute("x"));
		_column_y = parseFloat(_column.getAttribute("y"));
		_column_width = parseFloat(_column.getAttribute("width"));
		_column_height = parseFloat(_column.getAttribute("height"));
		
		var _rec = _obj(this._id+"_column_highlight");
		if(!_rec)
		{
			var _svg = _obj(this._id+"_canvas");
			var _rec = _addRec(_column_x,_column_y,_column_x+_column_width,_column_y+_column_height,_svg);			
			_rec.id=this._id+"_column_highlight";
			_stroke(_rec,"#ffffff",1,0.2,"#ffffff",0.2);
			_addEvent(_rec,"mouseout",_column_onmouseout,false);	
			//var _anim = _animate(_rec,"fill-opacity",0,0.2,"0.2s");
			//_anim.beginElement();
		}

	},
	_handle_column_onmouseout:function()
	{
		var _svg = _obj(this._id+"_canvas");
		var _rec = _obj(this._id+"_column_highlight");
		if(_rec)
		{
			_svg.removeChild(_rec);
		}
	},
	_handle_line_mark_onmouseover:function(_shape)
	{
		switch(_shape.nodeName.toLowerCase())
		{
			case "circle":
				var _color = _shape.getAttribute("stroke");
				_fill(_shape,_color);
				break;
		}
	},
	_handle_line_mark_onmouseout:function(_shape)
	{
		switch(_shape.nodeName.toLowerCase())
		{
			case "circle":
				var _color = _shape.getAttribute("stroke");
				_fill(_shape,"#ffffff");
				break;
		}		
	},
	_handle_pie_onmouseover:function(_shape)
	{
		_shape_d = _shape.getAttribute("d");
		
		var _arc = _obj(this._id+"_pie_highlight");
		if(!_arc && (this._end_animation==null||this._end_animation==true))
		{
			var _svg = _obj(this._id+"_canvas");
			var _arc = _newSVGNode("path",_svg);
			_setAttribute(_arc,"stroke-linejoin","round");
			_setAttribute(_arc,"stroke-linecap","square");
			_setAttribute(_arc,"d",_shape_d);
			_arc.id=this._id+"_pie_highlight";
			_stroke(_arc,"#ffffff",1,0.2,"#ffffff",0.2);
			_addEvent(_arc,"mouseout",_pie_onmouseout,false);	
		}
		
	},
	_handle_pie_onmouseout:function(_shape)
	{
		var _svg = _obj(this._id+"_canvas");
		var _arc = _obj(this._id+"_pie_highlight");
		if(_arc)
		{
			_svg.removeChild(_arc);
		}
	},	
	_loadSettings:function()
	{
		var _input = _obj(this._id+"_settings");
		return eval("__="+_input.value);
	},
	_getSVG:function()
	{
		
	}
}

function _get_chart(_div_chart)
{
	while(_div_chart.nodeName!="DIV" || _index("koolchart",_getClass(_div_chart))<0)
	{
		_div_chart = _goParentNode(_div_chart);
		if (_div_chart.nodeName == "BODY") return null;
	}
	return eval(_div_chart.id);	
}

function _animation_visible_onbegin()
{
	//alert(this.nodeName);
	_setAttribute(this.parentNode,"visibility","visible");
}

function _column_mouseover()
{
	var _chart = _get_chart(this);
	_chart._handle_column_mouseover(this);
}
function _column_onmouseout()
{
	var _chart = _get_chart(this);
	_chart._handle_column_onmouseout(this);	
}
function _line_mark_onmouseover()
{
	var _chart = _get_chart(this);
	_chart._handle_line_mark_onmouseover(this);	
}
function _line_mark_onmouseout()
{
	var _chart = _get_chart(this);
	_chart._handle_line_mark_onmouseout(this);		
}

function _pie_onmouseover()
{
	var _chart = _get_chart(this);
	_chart._handle_pie_onmouseover(this);	
}
function _pie_onmouseout()
{
	var _chart = _get_chart(this);
	_chart._handle_pie_onmouseout(this);		
}
function _pie_end_rotation()
{
	var _chart = _get_chart(this);
	_chart._end_animation = true;	
}

if(typeof(__KCHInits)!='undefined' && _exist(__KCHInits))
{	
	for(var i=0;i<__KCHInits.length;i++)
	{
		__KCHInits[i]();
	}
}

