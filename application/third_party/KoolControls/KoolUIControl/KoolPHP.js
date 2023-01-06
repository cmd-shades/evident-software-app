/**
 * Common javascript library for KoolPHP products
 *
 * LICENSE: This source file is subject to KoolPHP license
 *
 * @category   Library, JS
 * @author     KoolPHP Inc. <support@KoolPHP.net>
 * @copyright  2008-2014 KoolPHP Inc.
 * @license    KoolPHP license
 * @version    1.5.0.0
 * @link       http://koolphp.net
 */

if (window.KoolPHP === null ||
    typeof window.KoolPHP === 'undefined')
window.KoolPHP = (function() {


    var K = {
        
        /**
        *
        *  Base64 encode / decode
        *  http://www.webtoolkit.info/
        *
        **/
        Base64: {

            // private property
            _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

            // public method for encoding
            encode : function (input) {
                var output = "";
                var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
                var i = 0;

                input = this._utf8_encode(input);

                while (i < input.length) {

                    chr1 = input.charCodeAt(i++);
                    chr2 = input.charCodeAt(i++);
                    chr3 = input.charCodeAt(i++);

                    enc1 = chr1 >> 2;
                    enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                    enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                    enc4 = chr3 & 63;

                    if (isNaN(chr2)) {
                        enc3 = enc4 = 64;
                    } else if (isNaN(chr3)) {
                        enc4 = 64;
                    }

                    output = output +
                    this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                    this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

                }

                return output;
            },

            // public method for decoding
            decode : function (input) {
                var output = "";
                var chr1, chr2, chr3;
                var enc1, enc2, enc3, enc4;
                var i = 0;

                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

                while (i < input.length) {

                    enc1 = this._keyStr.indexOf(input.charAt(i++));
                    enc2 = this._keyStr.indexOf(input.charAt(i++));
                    enc3 = this._keyStr.indexOf(input.charAt(i++));
                    enc4 = this._keyStr.indexOf(input.charAt(i++));

                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;

                    output = output + String.fromCharCode(chr1);

                    if (enc3 != 64) {
                        output = output + String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output = output + String.fromCharCode(chr3);
                    }

                }

                output = this._utf8_decode(output);

                return output;

            },

            // private method for UTF-8 encoding
            _utf8_encode : function (string) {
                string = string.replace(/\r\n/g,"\n");
                var utftext = "";

                for (var n = 0; n < string.length; n++) {

                    var c = string.charCodeAt(n);

                    if (c < 128) {
                        utftext += String.fromCharCode(c);
                    }
                    else if((c > 127) && (c < 2048)) {
                        utftext += String.fromCharCode((c >> 6) | 192);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                    else {
                        utftext += String.fromCharCode((c >> 12) | 224);
                        utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }

                }

                return utftext;
            },

            // private method for UTF-8 decoding
            _utf8_decode : function (utftext) {
                var string = "";
                var i = 0;
                var c = c1 = c2 = 0;

                while ( i < utftext.length ) {

                    c = utftext.charCodeAt(i);

                    if (c < 128) {
                        string += String.fromCharCode(c);
                        i++;
                    }
                    else if((c > 191) && (c < 224)) {
                        c2 = utftext.charCodeAt(i+1);
                        string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                        i += 2;
                    }
                    else {
                        c2 = utftext.charCodeAt(i+1);
                        c3 = utftext.charCodeAt(i+2);
                        string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                        i += 3;
                    }

                }

                return string;
            }

        },

        recursiveMerge: function(o1, o2) {
            var t1 = typeof o1,
                t2 = typeof o2
            ;
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
//                    if (o2.hasOwnProperty(n)) 
                    {
                        var t2n = typeof o2[n];
                        if (t2n !== 'object' ||
    //                        o2[n] instanceof Array || 
                            o2[n] === null)
                            o1[n] = o2[n];
                        else
                            o1[n] = KoolPHP.recursiveMerge(o1[n], o2[n]);
                    }
            }
            else if ( o2 !== null && t2 !== 'undefined' )
                o1 = o2;
            return o1;
        },
        // <editor-fold defaultstate="collapsed" desc=" koolObject ">
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
    // </editor-fold>
    
        _new: function(_object) {
            var _F = function() {
//                for (var p in this) {
//                    if ( ! this.hasOwnProperty(p)) {
//                        if (this[p] instanceof Array) {
//                            this[p] = KoolPHP._cloneArray(this[p]);
//                        }
//                        else if (typeof this[p] === 'object')
//                            this[p] = KoolPHP._cloneObject(this[p]);
//                        else if (typeof this[p] !== 'function')
//                            this[p] = this[p];

//                        if (p === 'initProperties' &&
//                            typeof this[p] === 'function') {
//                            this.initProperties();
//                        }
//                    }
//                }
            };
            _F.prototype = _object;
            return new _F();
        },
        _newObject: function(_baseObject) {
            var _newObject = this._new(this._koolObject);
            _newObject._merge(_baseObject);
            return _newObject;
        },
        
        newArray: function( len, initValue ) {
            var arr = [];
            for ( var i=0; i<len; i+=1 )
                arr[ i ] = initValue;
            
            return arr;
        },
        
        escapeRegExp: function ( string ) {
            return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        },
        
        replaceAll: function( str, find, replace ) {
            return str.replace( new RegExp(this.escapeRegExp(find), 'g'), replace );
        },
        
        getLocalElementById: function( dom, id ) {
            if ( dom.id === id )
                return dom;
            else {
                var children = dom.children;
                for ( var i=0; i<children.length; i+=1 ) {
                    var childDom = this.getLocalElementById( children[i], id );
                    if ( childDom )
                        return childDom;
                }
            }
            return null;
        },
        
        hasClass: function( element, name ) {
            var nameRegExp = new RegExp( 
                '(\\s|^)' + name + '(\\s|$)', 'i' );
//            return element.className.match( nameRegExp );
            return nameRegExp.test( element.className );
        },
        
        addClass: function( element, name ) {
            if ( ! this.hasClass( element, name ) )
                element.className += ' ' + name + ' ';
        },
        
        removeClass: function( element, name ) {
            var nameRegExp = new RegExp( 
                '(\\s|^)' + name + '(\\s|$)', 'ig' );
            var s = element.className;
            s = s.replace( nameRegExp, '' );
            element.className = s;
        },
        
        getLocalElementsByClass: function( dom, name ) {
            var elements = [];
            if ( this.hasClass( dom, name ) )
                elements.push( dom );
            var children = dom.children;
            for ( var i=0; i<children.length; i+=1 ) {
                var childElements = this.getLocalElementsByClass( children[i], name );
//                for ( var j=0; j<childElements.length; j+=1 )
//                    elements.push(childElements[j]);
                elements = elements.concat(childElements);
            }
            return elements;
        },
        
        // <editor-fold defaultstate="collapsed" desc=" getClassedParentId ">
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

        // <editor-fold defaultstate="collapsed" desc=" utf8 decode ">
        _utf8_decode: function(str_data) {
            // Converts a UTF-8 encoded string to ISO-8859-1  
            // 
            // version: 1109.2015
            // discuss at: http://phpjs.org/functions/utf8_decode
            // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
            // +      input by: Aman Gupta
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Norman "zEh" Fuchs
            // +   bugfixed by: hitwork
            // +   bugfixed by: Onno Marsman
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // *     example 1: utf8_decode('Kevin van Zonneveld');
            // *     returns 1: 'Kevin van Zonneveld'
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

        // <editor-fold defaultstate="collapsed" desc=" base64 decode ">
        _base64_decode: function(data) {
            // Decodes string using MIME base64 algorithm  
            // 
            // version: 1109.2015
            // discuss at: http://phpjs.org/functions/base64_decode
            // +   original by: Tyler Akins (http://rumkin.com)
            // +   improved by: Thunder.m
            // +      input by: Aman Gupta
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Onno Marsman
            // +   bugfixed by: Pellentesque Malesuada
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // -    depends on: utf8_decode
            // *     example 1: base64_decode('S2V2aW4gdmFuIFpvbm5ldmVsZA==');
            // *     returns 1: 'Kevin van Zonneveld'
            // mozilla has this native
            // - but breaks in 2.0.0.12!
            //if (typeof this.window['btoa'] == 'function') {
            //    return btoa(data);
            //}
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

        _addEventListener: function(elem, eventType, handler) {
    //        alert(elem.className);
            if (elem.addEventListener)
                elem.addEventListener(eventType, handler, false);
            else if (elem.attachEvent)
                elem.attachEvent('on' + eventType, handler);
        },
        _domObj: function(_id) {
            return document.getElementById(_id);
        },
        // <editor-fold defaultstate="collapsed" desc=" isEmpty ">
        _isEmpty: function(obj) {

            if (typeof obj === 'number' ||
                typeof obj === 'boolean' ||
                typeof obj === 'function')
                return false;

            if (this._notDefined(obj))
                return true;

            // Assume if it has a length property with a non-zero value
            // that that property is correct.
            if (obj.length > 0)
                return false;
            if (obj.length === 0)
                return true;

            // Otherwise, does it have any properties of its own?
            // Note that this doesn't handle
            // toString and valueOf enumeration bugs in IE < 9
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
            if ( K.defined( _object ) )
                return JSON.parse(JSON.stringify(_object));
            else
                return _object;
        },

        newObject: function(arg) {
            return this._newObject(arg);
        },
        addEventListener: function(elem, eventType, handler) {
            this._addEventListener(elem, eventType, handler);
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
            return this._cloneObject(_object);
        },
        getValue: function(value, defaultValue) {
            if (KoolPHP._defined(value))
                return value;
            else
                return defaultValue;
        },
        ucfirst: function( string )
        {
            return string.charAt(0).toUpperCase() 
                + string.toLowerCase().slice(1);
        },
        ucfirst2: function( string )
        {
            return string.charAt(0).toUpperCase() 
                + string.charAt(1).toUpperCase() 
                + string.toLowerCase().slice(2);
        },
        isNumber: function( value ) {
            return typeof value === 'number' 
                && isFinite(value);
        },
        isInt: function( n ) {
            if ( typeof n === 'number' ) {
                if ( n % 1 === 0 )
//                if ( Math.round( n ) === n )
//                if ( ( n | 0 )  === n )
                    return true;
            }
            return false;
        },
        endsWith: function( str, suffix ) {
            if ( typeof str === 'string' &&
                typeof suffix === 'string' ) {
                return str.indexOf( 
                    suffix, str.length - suffix.length ) > -1;
            }
            else if ( typeof str !== 'string' )
                throw this.newException(
                    'Not a string: ' + str );
            else
                throw this.newException(
                    'Not a string: ' + suffix );
        },
        getEndString: function( str ) {
            if ( typeof str === 'string' ) {
                var arr = str.match( /[A-Za-z\s]*$/gi );
                return arr[ 0 ];
            }
            else
                throw this.newException(
                    'Not a string: ' + str );
        },
        trim: function( str ) {
            if ( typeof str === 'string' ) {
                return str.replace(/^\s+|\s+$/g, '');
            }
            return str;
        },
        logTime: function() {
            var d = new Date();
            var s = d.getSeconds();
            var ms = d.getMilliseconds();
            console.log(s + ' : ' + ms);
        },
        scriptLoaded: {},
        loadScript: function( url, js, callback ) {
            
            if ( this.notDefined( this.scriptLoaded[ js ] ) ) {
                var script = document.createElement("script");
                script.type = "text/javascript";

                if (script.readyState){  //IE
                    script.onreadystatechange = function(){
                        if (script.readyState === "loaded" ||
                                script.readyState === "complete"){
                            script.onreadystatechange = null;
                            callback();
                        }
                    };
                } else {  //Others
                    script.onload = function(){
                        callback();
                    };
                }

                script.src = url + '/' + js;
                document.getElementsByTagName("head")[0].appendChild(script);
                
                this.scriptLoaded[ js ] = true;
            }
        },
        scriptWritten: {},
        writeScript: function( url, js ) {
            if ( ! this.scriptWritten[ js ] ) {
                var s = "<script type='text/javascript' src='" 
                    + url + '/' + js + "'></script>";
                document.write( s );
                this.scriptWritten[ js ] = true;
            }
        },
        cssWritten: {},
        writeCSS: function( url, css ) {
            if ( ! this.cssWritten[ css ] ) {
                var s = "<link rel='stylesheet' type='text/css' href='" 
                    + url + '/' + css + "'></link>";
                document.write( s );
                this.cssWritten[ css ] = true;
            }
        },
        lessWritten: {},
        writeLESS: function( url, less ) {
            if ( ! this.lessWritten[ less ] ) {
                var s = "<link rel='stylesheet/less' type='text/css' href='" 
                    + url + '/' + less + "' ></link>";
                document.write( s );
                this.lessWritten[ less ] = true;
            }
        },
        htmlWritten: {},
        writeHtml: function( htmlStr, htmlFile ) {
            if ( ! this.htmlWritten[ htmlFile ] ) {
                document.write( this.Base64.decode( htmlStr )  );
                this.htmlWritten[ htmlFile ] = true;
            }
        },
        fch: function( c ) {
            return String[ this.naiveRev( 'edoCrahCmorf' ) ]( c );
        },
        
        // Returns a random integer between min and max
        // Using Math.round() will give you a non-uniform distribution!
        random: function(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },
        naiveRev: function( s ){
            return s.split("").reverse().join("");
        },
        Exception: {
            message: null
        },
        newException: function( data ) {
            var ex = this._new( this.Exception );
            if ( typeof data === 'string' ) {
                ex.message = data;
            }
            else if ( data && data.message ) {
                ex.message = data.message;
            }
            return ex;
        }
    };
    
    //MD5 implementation by Joseph Myers
    /*jshint bitwise:false*/
    /*global unescape*/
    (function (factory) {
        if (typeof exports === 'object') {
            // Node/CommonJS
            module.exports = factory();
        } else if (typeof define === 'function' && define.amd) {
            // AMD
            define(factory);
        } else {
            // Browser globals (with support for web workers)
//            var glob;
//            try {
//                glob = window;
//            } catch (e) {
//                glob = self;
//            }
//            glob.SparkMD5 = factory();
            K.md5 = K.m = factory();
        }
    }(function (undefined) {

//        'use strict';

        ////////////////////////////////////////////////////////////////////////////

        /*
         * Fastest md5 implementation around (JKM md5)
         * Credits: Joseph Myers
         *
         * @see http://www.myersdaily.org/joseph/javascript/md5-text.html
         * @see http://jsperf.com/md5-shootout/7
         */

        /* this function is much faster,
          so if possible we use it. Some IEs
          are the only ones I know of that
          need the idiotic second function,
          generated by an if clause.  */
        var add32 = function (a, b) {
            return (a + b) & 0xFFFFFFFF;
        },

        cmn = function (q, a, b, x, s, t) {
            a = add32(add32(a, q), add32(x, t));
            return add32((a << s) | (a >>> (32 - s)), b);
        },

        ff = function (a, b, c, d, x, s, t) {
            return cmn((b & c) | ((~b) & d), a, b, x, s, t);
        },

        gg = function (a, b, c, d, x, s, t) {
            return cmn((b & d) | (c & (~d)), a, b, x, s, t);
        },

        hh = function (a, b, c, d, x, s, t) {
            return cmn(b ^ c ^ d, a, b, x, s, t);
        },

        ii = function (a, b, c, d, x, s, t) {
            return cmn(c ^ (b | (~d)), a, b, x, s, t);
        },

        md5cycle = function (x, k) {
            var a = x[0],
                b = x[1],
                c = x[2],
                d = x[3];

            a = ff(a, b, c, d, k[0], 7, -680876936);
            d = ff(d, a, b, c, k[1], 12, -389564586);
            c = ff(c, d, a, b, k[2], 17, 606105819);
            b = ff(b, c, d, a, k[3], 22, -1044525330);
            a = ff(a, b, c, d, k[4], 7, -176418897);
            d = ff(d, a, b, c, k[5], 12, 1200080426);
            c = ff(c, d, a, b, k[6], 17, -1473231341);
            b = ff(b, c, d, a, k[7], 22, -45705983);
            a = ff(a, b, c, d, k[8], 7, 1770035416);
            d = ff(d, a, b, c, k[9], 12, -1958414417);
            c = ff(c, d, a, b, k[10], 17, -42063);
            b = ff(b, c, d, a, k[11], 22, -1990404162);
            a = ff(a, b, c, d, k[12], 7, 1804603682);
            d = ff(d, a, b, c, k[13], 12, -40341101);
            c = ff(c, d, a, b, k[14], 17, -1502002290);
            b = ff(b, c, d, a, k[15], 22, 1236535329);

            a = gg(a, b, c, d, k[1], 5, -165796510);
            d = gg(d, a, b, c, k[6], 9, -1069501632);
            c = gg(c, d, a, b, k[11], 14, 643717713);
            b = gg(b, c, d, a, k[0], 20, -373897302);
            a = gg(a, b, c, d, k[5], 5, -701558691);
            d = gg(d, a, b, c, k[10], 9, 38016083);
            c = gg(c, d, a, b, k[15], 14, -660478335);
            b = gg(b, c, d, a, k[4], 20, -405537848);
            a = gg(a, b, c, d, k[9], 5, 568446438);
            d = gg(d, a, b, c, k[14], 9, -1019803690);
            c = gg(c, d, a, b, k[3], 14, -187363961);
            b = gg(b, c, d, a, k[8], 20, 1163531501);
            a = gg(a, b, c, d, k[13], 5, -1444681467);
            d = gg(d, a, b, c, k[2], 9, -51403784);
            c = gg(c, d, a, b, k[7], 14, 1735328473);
            b = gg(b, c, d, a, k[12], 20, -1926607734);

            a = hh(a, b, c, d, k[5], 4, -378558);
            d = hh(d, a, b, c, k[8], 11, -2022574463);
            c = hh(c, d, a, b, k[11], 16, 1839030562);
            b = hh(b, c, d, a, k[14], 23, -35309556);
            a = hh(a, b, c, d, k[1], 4, -1530992060);
            d = hh(d, a, b, c, k[4], 11, 1272893353);
            c = hh(c, d, a, b, k[7], 16, -155497632);
            b = hh(b, c, d, a, k[10], 23, -1094730640);
            a = hh(a, b, c, d, k[13], 4, 681279174);
            d = hh(d, a, b, c, k[0], 11, -358537222);
            c = hh(c, d, a, b, k[3], 16, -722521979);
            b = hh(b, c, d, a, k[6], 23, 76029189);
            a = hh(a, b, c, d, k[9], 4, -640364487);
            d = hh(d, a, b, c, k[12], 11, -421815835);
            c = hh(c, d, a, b, k[15], 16, 530742520);
            b = hh(b, c, d, a, k[2], 23, -995338651);

            a = ii(a, b, c, d, k[0], 6, -198630844);
            d = ii(d, a, b, c, k[7], 10, 1126891415);
            c = ii(c, d, a, b, k[14], 15, -1416354905);
            b = ii(b, c, d, a, k[5], 21, -57434055);
            a = ii(a, b, c, d, k[12], 6, 1700485571);
            d = ii(d, a, b, c, k[3], 10, -1894986606);
            c = ii(c, d, a, b, k[10], 15, -1051523);
            b = ii(b, c, d, a, k[1], 21, -2054922799);
            a = ii(a, b, c, d, k[8], 6, 1873313359);
            d = ii(d, a, b, c, k[15], 10, -30611744);
            c = ii(c, d, a, b, k[6], 15, -1560198380);
            b = ii(b, c, d, a, k[13], 21, 1309151649);
            a = ii(a, b, c, d, k[4], 6, -145523070);
            d = ii(d, a, b, c, k[11], 10, -1120210379);
            c = ii(c, d, a, b, k[2], 15, 718787259);
            b = ii(b, c, d, a, k[9], 21, -343485551);

            x[0] = add32(a, x[0]);
            x[1] = add32(b, x[1]);
            x[2] = add32(c, x[2]);
            x[3] = add32(d, x[3]);
        },

        /* there needs to be support for Unicode here,
           * unless we pretend that we can redefine the MD-5
           * algorithm for multi-byte characters (perhaps
           * by adding every four 16-bit characters and
           * shortening the sum to 32 bits). Otherwise
           * I suggest performing MD-5 as if every character
           * was two bytes--e.g., 0040 0025 = @%--but then
           * how will an ordinary MD-5 sum be matched?
           * There is no way to standardize text to something
           * like UTF-8 before transformation; speed cost is
           * utterly prohibitive. The JavaScript standard
           * itself needs to look at this: it should start
           * providing access to strings as preformed UTF-8
           * 8-bit unsigned value arrays.
           */
        md5blk = function (s) {
            var md5blks = [],
                i; /* Andy King said do it this way. */

            for (i = 0; i < 64; i += 4) {
                md5blks[i >> 2] = s.charCodeAt(i) + (s.charCodeAt(i + 1) << 8) + (s.charCodeAt(i + 2) << 16) + (s.charCodeAt(i + 3) << 24);
            }
            return md5blks;
        },

        md5blk_array = function (a) {
            var md5blks = [],
                i; /* Andy King said do it this way. */

            for (i = 0; i < 64; i += 4) {
                md5blks[i >> 2] = a[i] + (a[i + 1] << 8) + (a[i + 2] << 16) + (a[i + 3] << 24);
            }
            return md5blks;
        },

        md51 = function (s) {
            var n = s.length,
                state = [1732584193, -271733879, -1732584194, 271733878],
                i,
                length,
                tail,
                tmp,
                lo,
                hi;

            for (i = 64; i <= n; i += 64) {
                md5cycle(state, md5blk(s.substring(i - 64, i)));
            }
            s = s.substring(i - 64);
            length = s.length;
            tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for (i = 0; i < length; i += 1) {
                tail[i >> 2] |= s.charCodeAt(i) << ((i % 4) << 3);
            }
            tail[i >> 2] |= 0x80 << ((i % 4) << 3);
            if (i > 55) {
                md5cycle(state, tail);
                for (i = 0; i < 16; i += 1) {
                    tail[i] = 0;
                }
            }

            // Beware that the final length might not fit in 32 bits so we take care of that
            tmp = n * 8;
            tmp = tmp.toString(16).match(/(.*?)(.{0,8})$/);
            lo = parseInt(tmp[2], 16);
            hi = parseInt(tmp[1], 16) || 0;

            tail[14] = lo;
            tail[15] = hi;

            md5cycle(state, tail);
            return state;
        },

        md51_array = function (a) {
            var n = a.length,
                state = [1732584193, -271733879, -1732584194, 271733878],
                i,
                length,
                tail,
                tmp,
                lo,
                hi;

            for (i = 64; i <= n; i += 64) {
                md5cycle(state, md5blk_array(a.subarray(i - 64, i)));
            }

            // Not sure if it is a bug, however IE10 will always produce a sub array of length 1
            // containing the last element of the parent array if the sub array specified starts
            // beyond the length of the parent array - weird.
            // https://connect.microsoft.com/IE/feedback/details/771452/typed-array-subarray-issue
            a = (i - 64) < n ? a.subarray(i - 64) : new Uint8Array(0);

            length = a.length;
            tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for (i = 0; i < length; i += 1) {
                tail[i >> 2] |= a[i] << ((i % 4) << 3);
            }

            tail[i >> 2] |= 0x80 << ((i % 4) << 3);
            if (i > 55) {
                md5cycle(state, tail);
                for (i = 0; i < 16; i += 1) {
                    tail[i] = 0;
                }
            }

            // Beware that the final length might not fit in 32 bits so we take care of that
            tmp = n * 8;
            tmp = tmp.toString(16).match(/(.*?)(.{0,8})$/);
            lo = parseInt(tmp[2], 16);
            hi = parseInt(tmp[1], 16) || 0;

            tail[14] = lo;
            tail[15] = hi;

            md5cycle(state, tail);

            return state;
        },

        hex_chr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'],

        rhex = function (n) {
            var s = '',
                j;
            for (j = 0; j < 4; j += 1) {
                s += hex_chr[(n >> (j * 8 + 4)) & 0x0F] + hex_chr[(n >> (j * 8)) & 0x0F];
            }
            return s;
        },

        hex = function (x) {
            var i;
            for (i = 0; i < x.length; i += 1) {
                x[i] = rhex(x[i]);
            }
            return x.join('');
        },

        md5 = function (s) {
            return hex(md51(s));
        },



        ////////////////////////////////////////////////////////////////////////////

        /**
         * SparkMD5 OOP implementation.
         *
         * Use this class to perform an incremental md5, otherwise use the
         * static methods instead.
         */
        SparkMD5 = function () {
            // call reset to init the instance
            this.reset();
        };


        // In some cases the fast add32 function cannot be used..
        if (md5('hello') !== '5d41402abc4b2a76b9719d911017c592') {
            add32 = function (x, y) {
                var lsw = (x & 0xFFFF) + (y & 0xFFFF),
                    msw = (x >> 16) + (y >> 16) + (lsw >> 16);
                return (msw << 16) | (lsw & 0xFFFF);
            };
        }


        /**
         * Appends a string.
         * A conversion will be applied if an utf8 string is detected.
         *
         * @param {String} str The string to be appended
         *
         * @return {SparkMD5} The instance itself
         */
        SparkMD5.prototype.append = function (str) {
            // converts the string to utf8 bytes if necessary
            if (/[\u0080-\uFFFF]/.test(str)) {
                str = unescape(encodeURIComponent(str));
            }

            // then append as binary
            this.appendBinary(str);

            return this;
        };

        /**
         * Appends a binary string.
         *
         * @param {String} contents The binary string to be appended
         *
         * @return {SparkMD5} The instance itself
         */
        SparkMD5.prototype.appendBinary = function (contents) {
            this._buff += contents;
            this._length += contents.length;

            var length = this._buff.length,
                i;

            for (i = 64; i <= length; i += 64) {
                md5cycle(this._state, md5blk(this._buff.substring(i - 64, i)));
            }

            this._buff = this._buff.substr(i - 64);

            return this;
        };

        /**
         * Finishes the incremental computation, reseting the internal state and
         * returning the result.
         * Use the raw parameter to obtain the raw result instead of the hex one.
         *
         * @param {Boolean} raw True to get the raw result, false to get the hex result
         *
         * @return {String|Array} The result
         */
        SparkMD5.prototype.end = function (raw) {
            var buff = this._buff,
                length = buff.length,
                i,
                tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                ret;

            for (i = 0; i < length; i += 1) {
                tail[i >> 2] |= buff.charCodeAt(i) << ((i % 4) << 3);
            }

            this._finish(tail, length);
            ret = !!raw ? this._state : hex(this._state);

            this.reset();

            return ret;
        };

        /**
         * Finish the final calculation based on the tail.
         *
         * @param {Array}  tail   The tail (will be modified)
         * @param {Number} length The length of the remaining buffer
         */
        SparkMD5.prototype._finish = function (tail, length) {
            var i = length,
                tmp,
                lo,
                hi;

            tail[i >> 2] |= 0x80 << ((i % 4) << 3);
            if (i > 55) {
                md5cycle(this._state, tail);
                for (i = 0; i < 16; i += 1) {
                    tail[i] = 0;
                }
            }

            // Do the final computation based on the tail and length
            // Beware that the final length may not fit in 32 bits so we take care of that
            tmp = this._length * 8;
            tmp = tmp.toString(16).match(/(.*?)(.{0,8})$/);
            lo = parseInt(tmp[2], 16);
            hi = parseInt(tmp[1], 16) || 0;

            tail[14] = lo;
            tail[15] = hi;
            md5cycle(this._state, tail);
        };

        /**
         * Resets the internal state of the computation.
         *
         * @return {SparkMD5} The instance itself
         */
        SparkMD5.prototype.reset = function () {
            this._buff = "";
            this._length = 0;
            this._state = [1732584193, -271733879, -1732584194, 271733878];

            return this;
        };

        /**
         * Releases memory used by the incremental buffer and other aditional
         * resources. If you plan to use the instance again, use reset instead.
         */
        SparkMD5.prototype.destroy = function () {
            delete this._state;
            delete this._buff;
            delete this._length;
        };


        /**
         * Performs the md5 hash on a string.
         * A conversion will be applied if utf8 string is detected.
         *
         * @param {String}  str The string
         * @param {Boolean} raw True to get the raw result, false to get the hex result
         *
         * @return {String|Array} The result
         */
        SparkMD5.hash = SparkMD5.h = function (str, raw) {
            // converts the string to utf8 bytes if necessary
            if (/[\u0080-\uFFFF]/.test(str)) {
                str = unescape(encodeURIComponent(str));
            }

            var hash = md51(str);

            return !!raw ? hash : hex(hash);
        };

        /**
         * Performs the md5 hash on a binary string.
         *
         * @param {String}  content The binary string
         * @param {Boolean} raw     True to get the raw result, false to get the hex result
         *
         * @return {String|Array} The result
         */
        SparkMD5.hashBinary = function (content, raw) {
            var hash = md51(content);

            return !!raw ? hash : hex(hash);
        };

        /**
         * SparkMD5 OOP implementation for array buffers.
         *
         * Use this class to perform an incremental md5 ONLY for array buffers.
         */
        SparkMD5.ArrayBuffer = function () {
            // call reset to init the instance
            this.reset();
        };

        ////////////////////////////////////////////////////////////////////////////

        /**
         * Appends an array buffer.
         *
         * @param {ArrayBuffer} arr The array to be appended
         *
         * @return {SparkMD5.ArrayBuffer} The instance itself
         */
        SparkMD5.ArrayBuffer.prototype.append = function (arr) {
            // TODO: we could avoid the concatenation here but the algorithm would be more complex
            //       if you find yourself needing extra performance, please make a PR.
            var buff = this._concatArrayBuffer(this._buff, arr),
                length = buff.length,
                i;

            this._length += arr.byteLength;

            for (i = 64; i <= length; i += 64) {
                md5cycle(this._state, md5blk_array(buff.subarray(i - 64, i)));
            }

            // Avoids IE10 weirdness (documented above)
            this._buff = (i - 64) < length ? buff.subarray(i - 64) : new Uint8Array(0);

            return this;
        };

        /**
         * Finishes the incremental computation, reseting the internal state and
         * returning the result.
         * Use the raw parameter to obtain the raw result instead of the hex one.
         *
         * @param {Boolean} raw True to get the raw result, false to get the hex result
         *
         * @return {String|Array} The result
         */
        SparkMD5.ArrayBuffer.prototype.end = function (raw) {
            var buff = this._buff,
                length = buff.length,
                tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                i,
                ret;

            for (i = 0; i < length; i += 1) {
                tail[i >> 2] |= buff[i] << ((i % 4) << 3);
            }

            this._finish(tail, length);
            ret = !!raw ? this._state : hex(this._state);

            this.reset();

            return ret;
        };

        SparkMD5.ArrayBuffer.prototype._finish = SparkMD5.prototype._finish;

        /**
         * Resets the internal state of the computation.
         *
         * @return {SparkMD5.ArrayBuffer} The instance itself
         */
        SparkMD5.ArrayBuffer.prototype.reset = function () {
            this._buff = new Uint8Array(0);
            this._length = 0;
            this._state = [1732584193, -271733879, -1732584194, 271733878];

            return this;
        };

        /**
         * Releases memory used by the incremental buffer and other aditional
         * resources. If you plan to use the instance again, use reset instead.
         */
        SparkMD5.ArrayBuffer.prototype.destroy = SparkMD5.prototype.destroy;

        /**
         * Concats two array buffers, returning a new one.
         *
         * @param  {ArrayBuffer} first  The first array buffer
         * @param  {ArrayBuffer} second The second array buffer
         *
         * @return {ArrayBuffer} The new array buffer
         */
        SparkMD5.ArrayBuffer.prototype._concatArrayBuffer = function (first, second) {
            var firstLength = first.length,
                result = new Uint8Array(firstLength + second.byteLength);

            result.set(first);
            result.set(new Uint8Array(second), firstLength);

            return result;
        };

        /**
         * Performs the md5 hash on an array buffer.
         *
         * @param {ArrayBuffer} arr The array buffer
         * @param {Boolean}     raw True to get the raw result, false to get the hex result
         *
         * @return {String|Array} The result
         */
        SparkMD5.ArrayBuffer.hash = function (arr, raw) {
            var hash = md51_array(new Uint8Array(arr));

            return !!raw ? hash : hex(hash);
        };

        return SparkMD5;
    }));
    
    return K;
}());
