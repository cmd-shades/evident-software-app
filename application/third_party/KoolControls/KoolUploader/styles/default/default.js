/*
 * This defined function will be called to init the style
 */
function defaultKULInit(id)
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