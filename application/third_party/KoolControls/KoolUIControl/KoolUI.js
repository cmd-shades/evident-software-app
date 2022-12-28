/**
 * Common javascript UI library
 *
 * LICENSE: This source file is subject to KoolPHP license
 *
 * @category   Library, JS, UI
 * @author     KoolPHP Inc. <support@KoolPHP.net>
 * @copyright  2008-2014 KoolPHP Inc.
 * @license    KoolPHP license
 * @version    1.0.0.0
 * @link       http://koolphp.net
 */


if (window.KoolUI === null ||
    typeof window.KoolUI === 'undefined')
window.KoolUI = (function() {
    
    var K = KoolPHP;
    
    var trialMessage = 'KoolUI - Trial Version';
    
    var KControlTypes = [
        'KText', 
        'KIcon', 
        'KIconfa',
        'KCheckbox',
        'KRadio',
        'KImg', 
        'KItem', 
        'KButton', 
        'KToolbar', 
        'KListbox',
        'KBarcode',
        'KPanel',
        'KDatePicker'
    ];
    
    var 
        x = window,
        d = document,
        ce = 'createElement',
        tc = 'textContent',
        ac = 'appendChild',
        rc = 'removeChild',
        ib = 'insertBefore',
        di = 'div',
        did = 'divId',
        sto = 'setTimeout',
        gi = 'getElementById',
        rd = 'random',
        fc = 'firstChild'
    ;
    
    var replaceDomHtml = function( dom, regExp, replacer ) {
        var atts = dom.attributes;
        for ( var i=0; i<atts.length; i+=1 )
        {
            var a = atts[i];
//            a.name = a.name.replace( regExp, replacer );
            a.value = a.value.replace( regExp, replacer );
        }

        var child = dom[ fc ];
        while ( K.defined( child ) ) {
            if ( child.nodeType === Node.TEXT_NODE )
                child.data = child.data
                    .replace( regExp, replacer );
            child = child.nextSibling;
        }
    };
    
    var KControl = {
        
//        getKClass: (function() {
//            var kClass;
//            return function() {
//                return kClass;
//            };
//        }),
        
        clone: function( dom ) {
            var cloneDom = UI.DomBuilder
                .cloneDom( this.domElement );
            UI.DomBuilder.addBehavior( cloneDom );
            var kControls = cloneDom.KControls;
            for ( var p in kControls ) {
                var kControl = kControls[ p ];
                if ( UI.getKControlType( kControl ) === 'KItem' )
                    return kControl;
            }
            return null;
        },
        
        processClass: function( act, name ) {
            var dom = this.domElement;
            var kClass = this.KClass;
            K[ act ]( dom, UI.getClassName( kClass ) + '-' + name );
            return dom;
        },

        hasClass: function( name ) {
            var dom = this.domElement;
            var kClass = this.KClass;
            return K.hasClass( dom, 
                UI.getClassName( kClass  ) + '-' + name );
        },

        addClass: function( name ) {
            return this.processClass( 'addClass', name );
        },

        removeClass: function( name ) {
            return this.processClass( 'removeClass', name );
        },
        
        addStandardEventListeners: function() {
            var dom = this.domElement;
            var events = [
                'Click',
                'MouseOver',
                'MouseOut',
                'MouseDown',
                'MouseUp',
                'FocusIn',
                'FocusOut',
                'Blur'
            ];
            for ( var i=0; i<events.length; i+=1 ) {
                var event = events[ i ];
                var getListener = this [ 'getListener' + event ];
                if ( getListener )
                    K.addEventListener( 
                        dom, event.toLowerCase(), getListener( this ) );
            }
        },
        
        calculateChildControls: function( dom ) {
            if ( K.notDefined( dom ) )
                dom = this.domElement;
            var childControls;
            for ( var i=0; i<KControlTypes.length; i+=1 ) {
                var controlType = KControlTypes[ i ];
                var kControl = UI.getKControl( dom, controlType );
                if ( K.defined( kControl ) ) {
                    if ( K.notDefined( childControls ) )
                        childControls = {};
                    if ( K.notDefined( childControls[ controlType ] ) )
                        childControls[ controlType ] = [];
                    childControls[ controlType ].push( kControl );
                }
            }
            
            if ( K.defined( dom.KControls ) ) {
                var kControls = dom.KControls;
                var controls = {};
                for ( var p in kControls ) {
                    var kControl = kControls[ p ];
                    controls[ kControl.KClass ] = kControl;
                }
                dom.KControls = controls;
            }
            
            var children = dom.children;
            for ( var j=0; j<children.length; j+=1 ) {
                var childChildControls = this
                    .calculateChildControls( children[ j ] );
                if ( K.defined( childChildControls ) ) {
                    for ( var i=0; i<KControlTypes.length; i+=1 ) {
                        var controlType = KControlTypes[ i ];
                        if ( K.defined( childChildControls[ controlType ] ) ) {
                            if ( K.notDefined( childControls ) )
                                childControls = {};
                            if ( K.notDefined( childControls[ controlType ] ) )
                                childControls[ controlType ] = [];
                            childControls[ controlType ] = childControls[ controlType ]
                                .concat( childChildControls[ controlType ] );
                        }
                    }
                }
            }
            
            dom.ChildKControls = childControls;

            return dom.ChildKControls;
        },
        
        getSetting: function() {
            return this.setting;
        }
        
    };
    
    var controls = {
        
        KText: {
            KClass: 'KText',
            
            getListenerClick: function( that ) {
                var dom = that.domElement;
                return function( event ) {
                    //console.log( 'text ' + dom.id + ' clicked');
                };
            },
            
            addStandardListeners: true,
            
            getText: function() {
                return this.domElement[ tc ];
            }
        },
        
        KIcon: {
            
            KClass: 'KIcon',

            getListenerIconClick: function( that ) {
                var st = that.setting;
                var dom = that.domElement;
                return function( event ) {
                    //console.log( 'icon ' + dom.id + ' clicked' );
                };
            },
            
            addEventListeners: function() {
                var dom = this.domElement;
                K.addEventListener( dom, 'click', 
                    this.getListenerIconClick( this ) );
            }
        },
        
        KImg: {
            KClass: 'KImg',
            
            getListenerClick: function( that ) {
                var dom = that.domElement;
                return function( event ) {
                    //console.log( 'img ' + dom.id + ' clicked');
                };
            },
            
            addStandardListeners: true
        },
        
        KButton: {
            KClass: 'KButton',
            
//            getListenerClick: function( that ) {
//                var dom = that.domElement;
//                return function( event ) {
//                    //console.log( 'button ' + dom.id + ' clicked');
//                };
//            },
            
            getListenerMouseOver: function( that ) {
                return function( event ) {
                    that.addClass( 'mouseover' );
                };
            },
            
            getListenerMouseOut: function( that ) {
                return function( event ) {
                    that.removeClass( 'mouseover' );
                };
            },
            
            getListenerButtonDocMouseUp: function() {
                UI.hasButtonDocMouseUpListener = true;
    //                alert( 'added BtnDocMouseUpListener' );
                return function( event ) {
    //                    alert('doc mouse up');
                    var kControl = UI.lastMouseDownButton;
                    if ( K.defined( kControl ) ) {
                        kControl.removeClass( 'mousedown' );
                        UI.lastMouseDownButton = null;
                    }
                };
            },
            
            getListenerMouseDown: function( that ) {
                return function( event ) {
                    that.addClass( 'mousedown' );
                    //call event.preventDefault() when mousedown to 
                    //fire mouseup even after mousemove
    //                    event.preventDefault();
                    UI.lastMouseDownButton = that;
                };
            },
            
            addStandardListeners: true,
            
            addEventListeners: function() {
                if ( ! UI.hasButtonDocMouseUpListener )
                    K.addEventListener( d, 'mouseup', 
                        this.getListenerButtonDocMouseUp() );
            }
        },
        
        KItem: {

            KClass: 'KItem',

            isSelected: function( selected ) {
                if ( typeof selected !== 'boolean' )
                    selected = true;
                var hasSelect  = this.hasClass( 'select');
                return selected ? hasSelect : ! hasSelect;
            },

            setSelect: function( selected ) {
                var act = selected === false ?
                    'removeClass' : 'addClass';
                this[ act ]( 'select' );
            },

            setInvert: function() {
                if (this.hasClass( 'select')) {
                    this.removeClass( 'select' );
                }
                else {
                    this.addClass( 'select' );
                }
            },

            removeDom: function() {
                var dom = this.domElement;
                dom.parentNode.removeChild( dom );
                return dom;
            },

            getListenerItemDocMouseUp: function() {
                UI.hasItemDocMouseUpListener = true;
    //                alert( 'added BtnDocMouseUpListener' );
                return function( event ) {
    //                    alert('doc mouse up');
                    var kControl = UI.lastMouseDownItem;
                    if ( K.defined( kControl ) ) {
                        kControl.removeClass( 'mousedown' );
                        UI.lastMouseDownItem = null;
                    }
                };
            },

            getListenerItemDocMouseDown: function() {
                var that = this;
                var itemDiv = this.domElement;
                UI.hasItemDocMouseDownListener = true;
                return function( event ) {
    //                    alert('itemDiv doc mouse down');

                    if ( itemDiv !== UI.lastFocusItem 
                        && K.defined( UI.lastFocusItem ) ) {
                        UI.lastFocusItem.removeClass( 'focus' );
                        UI.lastFocusItem = null;
                    } 

                    if ( K.defined( itemDiv ) ) {
                        for ( var p in itemDiv.KControls ) {
                            var kControl = itemDiv.KControls[ p ];
                            var st = kControl.setting;
                            if (st.focusable) {
                                if ( itemDiv !== UI.lastFocusItem ) {
                                    that.addClass( 'focus' );
                                    UI.lastFocusItem = that;
                                }
                            }
                        }
                    }
                };
            },

            getListenerItemDocClick: function() {
                UI.hasItemDocClickListener = true;
                return function( event ) {
                };
            },

            getListenerBlur: function( that ) {
                return function( event ) {
                };
            },

            getListenerFocusIn: function( that ) {
                return function( event ) {
                };
            },

            getListenerFocusOut: function( that ) {
                return function( event ) {
                };
            },

            getListenerMouseOver: function( that ) {
                return function( event ) {
                    that.addClass( 'mouseover' );
                };
            },
            
            getListenerMouseOut: function( that ) {
                return function( event ) {
                    that.removeClass( 'mouseover' );
                };
            },

            getListenerMouseDown: function( that ) {
                return function( event ) {
                    that.addClass( 'mousedown' );
                    //call event.preventDefault() when mousedown to 
                    //fire mouseup even after mousemove
    //                    event.preventDefault();
                    UI.lastMouseDownItem = that;
                };
            },

            getListenerMouseUp: function( that ) {
                return function( event ) {
                };
            },
            
            getListenerClick: function( that ) {
                var dom = that.domElement;
                return function( event ) {
                    //console.log( 'item ' + dom.id + ' clicked');
                };
            },
            
            addChildEventListeners: function( dom ) {
                var kListeners = dom.getAttribute( 'kListeners' );
                if ( K.notEmpty( kListeners ) )
                    kListeners = JSON.parse( kListeners );
                if ( kListeners ) {
                    for ( var c in kListeners ) 
                        if ( c === UI.getKControlType( this ) ) {
                            var handlerTypes = kListeners[ c ];
                            for ( var h in handlerTypes ) {
                                var handlers = handlerTypes[ h ];
                                if ( typeof handlers === 'string' )
                                handlers = [ handlers ];
                                if ( handlers instanceof Array )
                                    for ( var i=0; i<handlers.length; i+=1 )
                                        if ( this[ handlers[ i ] ])
                                            K.addEventListener( 
                                                dom, h, this[ handlers[ i ] ]( this ) );
                            }
                        }
                }
                var children = dom.children;
                for ( var i=0; i<children.length; i+=1 )
                    this.addChildEventListeners( children[ i ] );
            },
            
            addEventListeners: function() {
                if ( ! UI.hasItemDocMouseDownListener )
                    K.addEventListener( d, 'mousedown', 
                        this.getListenerItemDocMouseDown() );
                if ( ! UI.hasItemDocMouseUpListener )
                    K.addEventListener( d, 'mouseup', 
                        this.getListenerItemDocMouseUp() );
                if ( ! UI.hasItemDocClickListener )
                    K.addEventListener( d, 'click', 
                        this.getListenerItemDocClick() );
                var dom = this.domElement;
                this.addChildEventListeners( dom );
            },
            

//            adjustFirstIcon: true,
//
//            adjustFirstImg: true,

            getText: function( kItem ) {
                var text = '';
                var dom = kItem.domElement;
                if ( K.defined( dom ) ) {
                    var children = dom.children;
                    for ( var i=0; i<children.length; i+=1 ) {
                        var child = children[ i ];
                        var $child = $( child );
                        if ( $child.hasClass( '-text' ) )
                            text += child[ tc ] + ' ';
                    }
                }
                return text.trim();
            },

            getitemDiv: function( childDiv, kClass ) {
                if ( K.notDefined( childDiv ) )
                    return null;
                else {
                    var div = childDiv;
                    while ( ! K.hasClass( 
                        div, UI.getClassName( kClass ) ) ) {
                        div = div.parentNode;
                        if ( div===null )
                            return div;
                    } 
                    return div;
                }
            }
            
//            adjustFirstIcon: function( kitemDiv ) {
//                var itemDiv = kitemDiv.domElement;
//                var kControl = itemDiv.KControl;
//                var kClass = kControl.kClass;
//                var elements = itemDiv.children;
//                var firstIcon=null, maxTop = 0;
//                for ( var j=0; j<elements.length; j+=1 ) {
//                    var element = elements[ j ];
//                    var $element = $(element);
//
//                    if ( $element.hasClass( kClass+'-icon' )
//                        && !K.defined( firstIcon )) {
//                        firstIcon = element;
//                    }
//                    else if (element.offsetTop > maxTop)
//                        maxTop = element.offsetTop;
//                }
//                if ( K.defined( firstIcon ) ) {
//                    var newHeight = maxTop - firstIcon.offsetTop;
//                    if ( newHeight > firstIcon.offsetHeight )
//                        firstIcon.style.minHeight = newHeight + 'px';
//                }
//            },
//
//            adjustFirstImg: function( kitemDiv ) {
//                var itemDiv = kitemDiv.domElement;
//                var kControl = itemDiv.KControl;
//                var kClass = kControl.kClass;
//                var elements = itemDiv.children;
//                var firstImg=null, maxTop = 0;
//                for ( var j=0; j<elements.length; j+=1 ) {
//                    var element = elements[ j ];
//                    var $element = $(element);
//
//                    if ( $element.hasClass( kClass+'-img' )
//                        && !K.defined( firstImg )) {
//                        firstImg = element;
//                    }
//                    else if (element.offsetTop > maxTop)
//                        maxTop = element.offsetTop;
//                }
//                if ( K.defined( firstImg ) ) {
//                    var newHeight = maxTop - firstImg.offsetTop;
//                    if ( newHeight > firstImg.offsetHeight )
//                        firstImg.style.minHeight = newHeight + 'px';
//                }
//            }
        },
        
        KToolbar: {

            KClass: 'KToolbar',

            getButton: function( text ) {
                for ( var btnId in this.listChildren ) {
                    var kBtn = this.listChildren[ btnId ];
                    if ( kBtn.getText() === text )
                        return kBtn;
                }
                return null;
            },

            getButtons: function() {
                var arrBtn = [];
                var btnDivs = this.domElement.children;
                for ( var i=0; i<btnDivs.length; i+=1 ) {
                    var id = btnDivs[ i ].id;
                    if ( this.listChildren[ id ] )
                        arrBtn.push( this.listChildren[ id ] );
                }
                return arrBtn;
            },

            getDomElement: function() {
                return this.domElement;
            },

            setButtonOnClick: function( btnText, f ) {
                var kButton = this.getButton( btnText );
                if ( kButton ) {
                    UI.KItemHelper.setOnClick( kButton, f );
                    return kButton;
                }
                else
                    return null;
            },

            setOnclick: function( f ) {
                if ( typeof f === 'string' )
                    f = x[ f ];
                if ( typeof f === 'function' ) {
                    if ( K.notDefined( this.handlers[ 'click' ] ) )
                        this.handlers[ 'click' ] = [];
                    this.handlers[ 'click' ].push( f );
                }
            },
            
            calculateItems: function() {
                var dom = this.domElement;
                this.calculateChildControls();
//                this.Items = this.getAllItems( dom );
                this.Buttons = dom.ChildKControls[ 'KButton' ];
            },

            customize: function() {
                this.calculateItems();
            }
            
        },
        
        KListbox: {
            
            KClass: 'KListbox',
            
            setItemsOrder: function() {
                for ( var i=0; i<this.Items.length; i+=1 )
                    this.Items[ i ].order = i;
            },

//            getAllItems: function( dom ) {
//                var items = [];
//                var kControl = UI.getKControl( dom, 'KItem' );
//                if ( kControl !== null )
//                    items.push( kControl );
//                var children = dom.children;
//                for ( var i=0; i<children.length; i+=1 )
//                    items = items.concat(this.getAllItems( children[ i ] ) );
//                
//                return items;
//            },
            
            calculateItems: function() {
                var dom = this.domElement;
                this.calculateChildControls();
//                this.Items = this.getAllItems( dom );
                this.Items = dom.ChildKControls[ 'KItem' ];
                this.setItemsOrder();
            },

            customize: function() {
                this.customizeSetting();
                this.calculateItems();
                
            },

            customizeSetting: function() {
//                var st = this.setting;
//                st.selectable = true;
            },
            
            getListenerItemClick: function( dom, that ) {
                var st = that.setting;
                return function( event ) {
                    if ( st.selectable === true ) {
                        var parent = dom;
                        while ( K.defined( parent) ) {
                            var kItem = UI.getKControl( parent, 'KItem' );
                            if ( kItem ) {
                                var itemDom = parent;
                                break;
                            }
                            parent = parent.parentNode;
                        }
                        if ( itemDom ) {
                            var itemControl = kItem;
                            if ( st.multiSelect !== true ) {
                                var items = that.Items;
                                for ( var i=0; i<items.length; i+=1 )
                                    if ( items[ i ] !== itemControl )
                                        items[ i ].setSelect( false );
                            }
                            itemControl.setSelect( ! itemControl.isSelected() );
                        }
                    }
                };
            },
            
            addChildEventListeners: function( dom ) {
                var kListeners = dom.getAttribute( 'kListeners' );
                if ( K.notEmpty( kListeners ) )
                    kListeners = JSON.parse( kListeners );
                if ( kListeners ) {
                    for ( var c in kListeners ) 
                        if ( c === UI.getKControlType( this ) ) {
                            var handlerTypes = kListeners[ c ];
                            for ( var h in handlerTypes ) {
                                var handlers = handlerTypes[ h ];
                                if ( typeof handlers === 'string' )
                                handlers = [ handlers ];
                                if ( handlers instanceof Array )
                                    for ( var i=0; i<handlers.length; i+=1 )
                                        if ( this[ handlers[ i ] ])
                                            K.addEventListener( 
                                                dom, h, this[ handlers[ i ] ]( dom, this ) );
                            }
                        }
                }
                var children = dom.children;
                for ( var i=0; i<children.length; i+=1 )
                    this.addChildEventListeners( children[ i ] );
            },

            addEventListeners: function() {
                var dom = this.domElement;
                this.addChildEventListeners( dom );
            },

            getItem: function( arg ) {
                if ( typeof arg === 'number' ) {
                    var items = this.Items;
                    if ( arg>-1 && arg<items.length )
                        return items[arg];
                    else
                        throw 'Index out of range: ' + arg;
                }
                else if ( UI.getKControlType( arg.KClass ) === 'KItem' )
                    return arg;
                else 
                    throw 'Neither an index nor a KItem: ' + arg;
            },

            getItems: function( arg ) {
                var items = this.Items;
                var arrItems = [];
                if ( K.notDefined( arg )) {
                    arrItems = items;
                }
                else if ( typeof arg === 'number' ) {
                    var item = this.getItem( arg );
                    if ( K.defined( item ) ) 
                        arrItems.push( item );
                }
                else if ( arg instanceof Array ) {
                    for (var i=0; i<arg.length; i+=1 ) {
                        var item = this.getItem( arg[ i ] );
                        if ( K.defined( item ) ) 
                            arrItems.push( item );
                    }
                }
                
                return arrItems;
            },

            selectItems: function( arg, selected ) {
                selected = K.getValue( selected, true );
                var items = this.getItems( arg );
                for ( var i=0; i<items.length; i+=1 )
                    items[ i ].setSelect( selected );
                
                return items;
            },

            clearItems: function( arg ) {
                return this.selectItems( arg, false );
            },

            invertItems: function( arg ) {
                var items = this.getItems( arg );
                if ( items )
                    for ( var i=0; i<items.length; i+=1 )
                        items[ i ].setInvert();

                return items;
            },

            cutItems: function( arg ) {
                var items = this.getItems( arg );
                for ( var i=0; i<items.length; i+=1 ) {
                    var item = items[ i ];
                    item.removeDom();
                    this.Items.splice( item.order - i, 1 );
                }
//                this.setItemsOrder();
                this.calculateItems();
                return items;
            },
            
            addItem: function( arg, position ) {
                if ( UI.getKControlType( arg ) === 'KItem' ) {
                    var len = this.Items.length;
                    if ( typeof position !== 'number' )
                        position = len;
                    else if ( position < 0 ) position = 0;
                    else if ( position > len ) position = len;

                    var items = this.Items;
                    var item = items[ 
                        position < len ? position : len-1 ];
                    var dom = item.domElement;
                    var parent = dom.parentNode;
                    parent[ ib ]( arg.domElement, 
                        position < len ? dom : dom.nextSibling );
                    this.addChildEventListeners( arg.domElement );
                    this.Items.splice( position, 0, arg );
                }
                else {
                    if ( K.notDefined( arg.templateSelector ) )
                        arg.templateSelector = 
                            this.Items[0].setting.templateSelector;
                    var newItem = UI.getKControl(
                            UI.DomBuilder.build( arg ), "KItem" );
                    this.addItem( newItem, position );
                }
            },

            add: function( arg, position ) {
                if ( arg instanceof Array ) 
                    for (var i=0; i<arg.length; i+=1 )
                        this.addItem( arg[ i ], position );
                else
                    this.addItem( arg, position );
//                this.setItemsOrder();
                this.calculateItems();
//                this.addEventListeners();
            },

            getSelectedItems: function( selected ) {
                var arrItems = [];
                var items = this.Items;
                for ( var i=0; i<items.length; i+=1 ) {
                    var item = items[ i ];
                    if ( item.isSelected( selected ) )
                        arrItems.push( item );
                }
                return arrItems;
            },

            getSelectedIndex: function( selected ) {
                var arrItems = [];
                var items = this.Items;
                for ( var i=0; i<items.length; i+=1 ) {
                    var item = items[ i ];
                    if ( item.isSelected( selected ) )
                        arrItems.push( i );
                }
                return arrItems;
            },
            
            copyItems: function( arg ) {
                var arrItems = [];
                var items = this.getItems( arg );
                for ( var i=0; i<items.length; i+=1 )
                    arrItems.push( items[ i ].clone() );

                return arrItems;
            },
            
            translateItems: function( arg, step ) {
                if ( typeof step === 'number' && step !== 0) {
                    var cutItems = this.cutItems( arg );
                    var negativeMoveItems = [];
//                    if ( step < 0 ) cutItems.reverse();
                    for ( var i=0; i<cutItems.length; i+=1 ) {
                        var item = cutItems[ i ];
                        var moveStep = item.order + step
                            - negativeMoveItems.length;
                        if ( moveStep > 0 )
                            this.addItem( item, moveStep);
                        else
                            negativeMoveItems.push( item );
                    }
                    negativeMoveItems.reverse();
                    for ( var i=0; i<negativeMoveItems.length; i+=1 ) {
                        var item = negativeMoveItems[ i ];
                        var moveStep = item.order + step
                            - negativeMoveItems.length + i;
                        this.addItem( item, moveStep );
                    }
                    this.setItemsOrder();
                }
                this.calculateItems();
            },
            
            switchItems: function( arg ) {
                var compareNumbers = function(a, b) {
                    return a - b;
                };
                arg.sort( compareNumbers );
                var items = this.getItems( arg );
                var len = items.length;
                if ( len > 1) {
                    var parents = [], doms = [], nextDoms = [];
                    for ( var i=0; i<len; i+=1 ) {
                        doms.push( items[ i ].domElement );
                        nextDoms.push( doms[ i ].nextSibling);
                        parents.push( doms[ i ].parentNode );
                    }
                    var nextDom = doms[ len-1 ].nextSibling;
                    for ( var i=0; i<len; i+=1 ) {
                        var j = ( i === 0 ) ? len-1 : i-1;
                        var dom = ( i < len-1 ) ? doms[ i ] : nextDom;
                        parents[ i ][ ib ]( 
                            doms[ j ], dom);
                    }
                    
//                    for ( var i=len-1; i>-1; i-=1 ) 
//                        this.Items.splice( items[ i ].order, 1 );
//                    for ( var i=0; i<len; i+=1 ) {
//                        var j = i-1 >= 0 ? i-1 : len-1;
//                        this.Items.splice( items[ i ].order, 0, items[ j ]);
//                    }
                }
//                this.setItemsOrder();
                this.calculateItems();
            }
        },
        
        KBarcode: {
            
            KClass: 'KBarcode',
            
            toIntArr: function( s ) {
                if ( typeof s === 'string' ) {
                    var len = s.length;
                    var arr = [];
                    for ( var i=0; i<len; i+=1 ) {
                        var number = parseInt( s.charAt( i ) );
                        if ( isFinite( number ) )
                            arr[ i ] =  number;
                        else {
                            var msg = s.charAt( i ) 
                                + ' could not be converted to integer.';
                            //console.log( msg );
                            throw K.newException( msg );
                        }
                    }
                    return arr;
                }
                else if ( K.isInt( s ) ) {
                    s = s.toString();
                    return this.toIntArr( s );
                }
                else {
                    var msg = 'Not an inteter or an integer string.';
//                    //console.log( msg );
                    throw K.newException( msg );
                }
            },
            
            EANWeights: (function() {
                
                var weightLengh = 17;
                var weights = [];
                for ( var i=0; i<weightLengh; i+=1 )
                    if ( i%2 === 0) weights[ i ] = 3;
                    else weights[ i ] = 1;
                    
                return weights;
            }()),
            
            EANChecksum: function( s ) {
                
                if ( typeof s === 'string' ) {
                    var len = s.length;
                    var arr = this.toIntArr( s );
                    var weight = this.EANWeights;
                    var wLen = weight.length;
                    var sum = 0;
                    for ( var i=0; i<len; i+=1 ) {
                        sum += weight[ wLen - len + i ] * arr[ i ]; 
                    }
                    var checksum = 10 - ( sum % 10 );
                    if ( checksum === 10 ) checksum = 0;
                    return checksum;
                }
                else {
                    throw "Not a string.";
                }
            },
            
            getEncodePattern: function( encodePattern ) {
                var arr = [];
                for ( var i=0; i<encodePattern.length; i+=1 )
                    arr[ i ] = this.toIntArr( 
                        encodePattern[ i ] );
                
                return arr;
            },
            
            EAN13: {
                
                encodePattern: [
                    '111111', '110100', '110010', '110001', '101100', 
                    '100110', '100011', '101010', '101001', '100101'
                ],
                
                barWidth: [
                    '3211', '2221', '2122', '1411', '1132', 
                    '1231', '1114', '1312', '1213', '3112'
                ],

                getOddBarWidth: function() {
                    if ( K.notDefined( this.oddArr ) ) {
                        var barWidth = this.barWidth;
                        var arr = [];
                        for ( var i=0; i<barWidth.length; i+=1 )
                            arr[ i ] = UI.KBarcode.toIntArr( 
                                barWidth[ i ] );
                        this.oddArr = arr;
                    }
                    return this.oddArr;
                },

                getEvenBarWidth: function() {
                    if ( K.notDefined( this.evenArr ) ) {
                        var barWidth = this.barWidth;
                        var arr = [];
                        for ( var i=0; i<barWidth.length; i+=1 )
                            arr[ i ] = UI.KBarcode.toIntArr( 
                                barWidth[ i ] ).reverse();
                        this.evenArr = arr;
                    }
                    return this.evenArr;
                },
                
                calculateBarcode: function( st ) {
                    var value = st.value;
                    value = K.replaceAll( value.toString(), ' ', '');
                    if ( typeof value === 'string' &&
                        value.length === 12 ) {
                    
                        var startMarker = endMarker = K.newArray( 3, 1 );
                        var centreMarker = K.newArray( 5, 1 );
                        var barcode = {};
                        var widths = [];
                        var checksum = UI.KBarcode.EANChecksum( value );
                        barcode.checksum = checksum;
                        barcode.displayText = value;
                        if ( st.showChecksum === true )
                            barcode.displayText += ' ' + checksum;
                        value = value + checksum;
                        
                        var firstDigit = parseInt( value.charAt( 0 ) );
                        var encodePattern = UI.KBarcode.getEncodePattern
                            ( this.encodePattern )[ firstDigit ];
                        var firstGroup = [];
                        for ( var i=1; i<7; i+=1 ) {
                            var n = parseInt( value.charAt( i ) );
                            var parity = encodePattern[ i-1 ];
                            if ( parity === 0 ) 
                                firstGroup = firstGroup.concat( 
                                    this.getEvenBarWidth()[ n ] );
                            else
                                firstGroup = firstGroup.concat( 
                                    this.getOddBarWidth()[ n ] );
                        }
                        
                        var secondGroup = [];
                        for ( var i=7; i<13; i+=1 ) {
                            var n = parseInt( value.charAt( i ) );
                            secondGroup = secondGroup.concat( 
                                this.getOddBarWidth()[ n ] );
                        }
                        
                        widths = widths.concat( startMarker );
                        widths = widths.concat( firstGroup );
                        widths = widths.concat( centreMarker );
                        widths = widths.concat( secondGroup );
                        widths = widths.concat( endMarker );
                        
                        barcode.widths = widths;
                        barcode.heights = this.getBarcodeHeight();
                        barcode.totalWidth = 95;
                        
                        return barcode;
                    }
                    else {
                        throw K.newException(
                            'Incorrect 12 digit number.');
                    }
                },
                
                getBarcodeHeight: function() {
                    var startMarker = endMarker = K.newArray( 3, 1 );
                    var centreMarker = K.newArray( 5, 1 );
                    var digit = K.newArray( 4, 0 );
                    var height = [];
                    height = height.concat( startMarker );
                    for ( var i=0; i<6; i+=1 )
                        height = height.concat( digit );
                    height = height.concat( centreMarker );
                    for ( var i=0; i<6; i+=1 )
                        height = height.concat( digit );
                    height = height.concat( endMarker );
                    return height;
                }
            },
            
            EAN8: {
                
                calculateBarcode: function( st ) {
                    var value = st.value;
                    value = K.replaceAll( value.toString(), ' ', '');
                    if ( typeof value === 'string' &&
                        value.length === 7 ) {
                    
                        var startMarker = endMarker = [1, 1, 1];
                        var centreMarker = [1, 1, 1, 1, 1];
                        var barcode = {};
                        var widths = [];
                        var checksum = UI.KBarcode.EANChecksum( value );
                        barcode.checksum = checksum;
                        barcode.displayText = value;
                        if ( st.showChecksum === true )
                            barcode.displayText += ' ' + checksum;
                        value = value + checksum;
                        
                        
                        var firstGroup = [];
                        for ( var i=0; i<4; i+=1 ) {
                            var n = parseInt( value.charAt( i ) );
                            firstGroup = firstGroup.concat( 
                                UI.KBarcode.EAN13.getOddBarWidth()[ n ] );
                        }
                        
                        var secondGroup = [];
                        for ( var i=4; i<8; i+=1 ) {
                            var n = parseInt( value.charAt( i ) );
                            secondGroup = secondGroup.concat( 
                                UI.KBarcode.EAN13.getOddBarWidth()[ n ] );
                        }
                        
                        widths = widths.concat( startMarker );
                        widths = widths.concat( firstGroup );
                        widths = widths.concat( centreMarker );
                        widths = widths.concat( secondGroup );
                        widths = widths.concat( endMarker );
                        
                        barcode.widths = widths;
                        barcode.heights = this.getBarcodeHeight();
                        barcode.totalWidth = 67;
                        
                        return barcode;
                    }
                    else {
                        throw K.newException(
                            'Incorrect 7 digit number.');
                    }
                },
                
                getBarcodeHeight: function() {
                    var startMarker = endMarker = [1, 1, 1];
                    var centreMarker = [1, 1, 1, 1, 1];
                    var digit = [0, 0, 0, 0];
                    var height = [];
                    height = height.concat( startMarker );
                    for ( var i=0; i<4; i+=1 )
                        height = height.concat( digit );
                    height = height.concat( centreMarker );
                    for ( var i=0; i<4; i+=1 )
                        height = height.concat( digit );
                    height = height.concat( endMarker );
                    return height;
                }
            },
            
            UPC: {
                
                upcaSystem: 0,
        
                encodePattern: [
                    '000111', '001011', '001101', '001110', '010011', 
                    '011001', '011100', '010101', '010110', '011010'
                ],
                
                toUPCA: function( st ) {
                    var value = st.value;
                    var upca = K.getValue( 
                        st.upcaSystem , this.upcaSystem );
                    upca = upca.toString();
                    var a = UI.KBarcode.toIntArr( value );
                    var lastDigit = a[ 5 ];
                    a.splice( 5, 1 );
                    if ( lastDigit < 3 ) {
                        a.splice( 2, 0, lastDigit + '0000' );
                        upca += a.join( '' );
                    }
                    else if ( lastDigit < 5 ) {
                        a.splice( lastDigit, 0, '00000' );
                        upca += a.join( '' );
                    }
                    else {
                        a.splice( 5, 0, '0000' + lastDigit );
                        upca += a.join( '' );
                    }
                    
                    return upca;
                },
                
                calculateBarcodeUPCA: function( st ) {
                    var value = st.value;
                    value = K.replaceAll( value.toString(), ' ', '');
                    if ( typeof value === 'string' &&
                        value.length === 11 ) {
                    
                        var startMarker = endMarker = [1, 1, 1];
                        var centreMarker = [1, 1, 1, 1, 1];
                        var barcode = {};
                        var widths = [];
                        var checksum = UI.KBarcode.EANChecksum( value );
                        barcode.checksum = checksum;
                        barcode.displayText = value;
                        if ( st.showChecksum === true )
                            barcode.displayText += ' ' + checksum;
                        value = value + checksum;
                        
                        var firstGroup = [];
                        for ( var i=0; i<6; i+=1 ) {
                            var n = parseInt( value.charAt( i ) );
                            firstGroup = firstGroup.concat( 
                                UI.KBarcode.EAN13.getOddBarWidth()[ n ] );
                        }
                        
                        var secondGroup = [];
                        for ( var i=6; i<12; i+=1 ) {
                            var n = parseInt( value.charAt( i ) );
                            secondGroup = secondGroup.concat( 
                                UI.KBarcode.EAN13.getOddBarWidth()[ n ] );
                        }
                        
                        widths = widths.concat( startMarker );
                        widths = widths.concat( firstGroup );
                        widths = widths.concat( centreMarker );
                        widths = widths.concat( secondGroup );
                        widths = widths.concat( endMarker );
                        
                        barcode.widths = widths;
                        barcode.heights = this.getBarcodeHeightUPCA();
                        barcode.totalWidth = 95;
                        
                        return barcode;
                    }
                    else {
                        throw K.newException(
                            'Incorrect 11 digit number.' );
                    }
                },
                
                calculateBarcodeUPCE: function( st ) {
                    var value = st.value;
                    value = K.replaceAll( value.toString(), ' ', '');
                    if ( typeof value === 'string' &&
                        value.length === 6 ) {
                    
                        var startMarker = [1, 1, 1];
                        var endMarker = [1, 1, 1, 1, 1, 1];
                        var barcode = {};
                        var widths = [];
                        
                        var upca = this.toUPCA( st );
                        var checksum = UI.KBarcode.EANChecksum( upca );
                        barcode.checksum = checksum;
                        barcode.displayText = value;
                        if ( st.showChecksum === true )
                            barcode.displayText += ' ' + checksum;
                        var encodePattern = UI.KBarcode.getEncodePattern
                            ( this.encodePattern )[ checksum ];
                        var group = [];
                        for ( var i=0; i<6; i+=1 ) {
                            var n = parseInt( value.charAt( i ) );
                            var parity = encodePattern[ i ];
                            if ( this.upcaSystem === 1 )
                                parity = 1 - parity;
                            if ( parity === 0 ) 
                                group = group.concat( 
                                    UI.KBarcode.EAN13.getEvenBarWidth()[ n ] );
                            else
                                group = group.concat( 
                                    UI.KBarcode.EAN13.getOddBarWidth()[ n ] );
                        }
                        
                        widths = widths.concat( startMarker );
                        widths = widths.concat( group );
                        widths = widths.concat( endMarker );
                        
                        barcode.widths = widths;
                        barcode.heights = this.getBarcodeHeightUPCE();
                        barcode.totalWidth = 51;
                        
                        return barcode;
                    }
                    else {
                        throw K.newException(
                            'Incorrect 6 digit number.' );
                    }
                },
                
                getBarcodeHeightUPCA: function() {
                    var startMarker = endMarker = [1, 1, 1];
                    var centreMarker = [1, 1, 1, 1, 1];
                    var digitLong = [1, 1, 1, 1];
                    var digit = [0, 0, 0, 0];
                    var height = [];
                    height = height.concat( startMarker );
                    height = height.concat( digitLong );
                    for ( var i=0; i<5; i+=1 )
                        height = height.concat( digit );
                    height = height.concat( centreMarker );
                    for ( var i=0; i<5; i+=1 )
                        height = height.concat( digit );
                    height = height.concat( digitLong );
                    height = height.concat( endMarker );
                    return height;
                },
                
                getBarcodeHeightUPCE: function() {
                    var startMarker = [1, 1, 1];
                    var endMarker = [1, 1, 1, 1, 1, 1];
                    var digit = [0, 0, 0, 0];
                    var height = [];
                    height = height.concat( startMarker );
                    for ( var i=0; i<6; i+=1 )
                        height = height.concat( digit );
                    height = height.concat( endMarker );
                    return height;
                },
                
                calculateBarcode: function( st ) {
                    if ( K.trim( st.subtype ).toLowerCase() === 'e' )
                        return this.calculateBarcodeUPCE( st );
                    else
                        return this.calculateBarcodeUPCA( st );
                }
            },
            
            Code39: {
                
                WideNarrowRatio: 2.5, 
        
                BetweenCharsSpace: 1,
                
                chars: [
                    '1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
                    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
                    'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
                    'U', 'V', 'W', 'X', 'Y', 'Z', '-', '.', ' ', '*',
                    '$', '/', '+', '%'
                ], 
                
                barWidthArr: [
                    '308', '328', '302', '348', '304', '324', '368', '306', '326', '346', 
                    '508', '528', '502', '548', '504', '524', '568', '506', '526', '546', 
                    '708', '728', '702', '748', '704', '724', '768', '706', '726', '746', 
                    '108', '128', '102', '148', '104', '124', '168', '106', '126', '146',
                    '135', '137', '157', '357'
                ],
                
                getBarWidth: function( st ) {
                    var wideNarrowRatio = K.getValue( 
                        st.WideNarrowRatio, this.WideNarrowRatio );
                    if ( K.notDefined( this.barwidth ) ) {
                        var barwidth = {};
                        for ( var i=0; i<this.chars.length; i+=1 ) {
                            var width = UI.KBarcode
                                .toIntArr( this.barWidthArr[ i ] );
                            var arr = K.newArray( 9, 1 );
                            for ( var j=0; j<width.length; j+=1 )
                                arr[ width[ j ] ] = wideNarrowRatio;
                            
                            barwidth[ this.chars[ i ] ] = arr;
                        }
                        this.barwidth = barwidth;
                    }
                    
                    return this.barwidth;
                },
                
                calculateChecksum: function( s ) {
                    return '';
                },
                
                calculateBarcode: function( st ) {
                    var value = st.value;
                    if ( typeof value === 'string') {

                        var barwidth = this.getBarWidth( st );
                    
                        var startMarker = endMarker = barwidth[ '*' ];
                        var barcode = {};
                        var widths = [];
                        
                        barcode.displayText = value;
                        if ( st && st.showChecksum === true ) {
                            var checksum = this.calculateChecksum( value );
                            barcode.checksum = checksum;
                            if ( st.showChecksum === true )
                                barcode.displayText += (checksum !== '' ) ?
                                    ' ' + checksum : '';
                            value = value + checksum;
                        }
                        barcode.displayText = '*' + barcode.displayText + '*';
                        
                        var betweenCharsSpace = K.getValue( 
                            st.BetweenCharsSpace, this.BetweenCharsSpace );
                        var group = [];
                        for ( var i=0; i<value.length; i+=1 ) {
                            var width = barwidth[ value.charAt( i ) ];
                            if ( K.notDefined( width ) )
                                throw K.newException( 
                                    'Not a legal Code 39 string.' );
                            group = group.concat( width );
                            group = group.concat( [ betweenCharsSpace ] );
                        }
                        
                        var totalWidth = 
                            ( 9 + 3 * ( this.WideNarrowRatio - 1 ) 
                            + betweenCharsSpace ) * ( value.length + 2 )
                            - betweenCharsSpace;
                        
                        widths = widths.concat( startMarker );
                        widths = widths.concat( [ betweenCharsSpace ] );
                        widths = widths.concat( group );
                        widths = widths.concat( endMarker );
                        
                        barcode.widths = widths;
                        barcode.heights = this.getBarcodeHeight( st );
                        barcode.totalWidth = totalWidth;
                        
                        return barcode;
                    }
                    else {
                        throw K.newException( 'Not a string.' );
                    }
                },
                
                getBarcodeHeight: function( st ) {
                    var numberOfBars = 10 * ( st.value.length + 2 );
                    var barcodeHeight = K.newArray( numberOfBars, 0 );
                    return barcodeHeight;
                }
            },
            
            Code128: {
                
                CodeSet: [ 'A', 'B', 'C' ],
        
                barWidth: [
                    '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213', '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132', '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211', '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313', '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331', '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', '114131', '311141', '411131', '211412', '211214', '211232', '2331112'
                ],
                
                CodeSetA: [ 
                    ' ', '!', '"', '#', '$', ' %', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ' :', ' ;', '<', '=', '>', ' ?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', '\\', ']', '^', '_', 'NUL', 'SOH', 'STX', 'ETX', 'EOT', 'ENQ', 'ACK', 'BEL', 'BS', 'HT', 'LF', 'VT', 'FF', 'CR', 'SO', 'SI', 'DLE', 'DC1', 'DC2', 'DC3', 'DC4', 'NAK', 'SYN', 'ETB', 'CAN', 'EM', 'SUB', 'ESC', 'FS', 'GS', 'RS', 'US', 'FNC3', 'FNC2', 'Shift B', 'Code C', 'Code B', 'FNC4', 'FNC1', 'Code128A', 'Code128B', 'Code128C', 'Stop'
                ],
                
                CodeSetB: [
                    ' ', '!', '"', '#', '$', ' %', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ' :', ' ;', '<', '=', '>', ' ?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', '\\', ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', 'DEL', 'FNC3', 'FNC2', 'Shift A', 'Code C', 'FNC4', 'Code A', 'FNC1', 'Code128A', 'Code128B', 'Code128C', 'Stop'
                ],
                
                CodeSetC: [
                    '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99', 'Code B', 'Code A', 'FNC1', 'Code128A', 'Code128B', 'Code128C', 'Stop'
                ],
                
                getBarWidth: function() {
                    if ( K.notDefined( this.barW ) ) {
                        this.barW = [];
                        for ( var i=0; i<this.barWidth.length; i+=1 )
                            this.barW[ i ] = UI.KBarcode
                                .toIntArr( this.barWidth[ i ] );
                    }
                    return this.barW;
                },
                
                getCode128: function( codeSet ) {
                    codeSet = 'CodeSet' + codeSet;
                    var code128 = 'code128' + codeSet;
                    if ( K.notDefined( this[ code128 ] ) ) {
                        this[ code128 ] = {};
                        for ( var i=0; i<this[ codeSet ].length; i+=1 ) {
                            var barwidth = this.getBarWidth();
                            var arr = barwidth[ i ];
                            var code = {
                                value: i,
                                barwidth: arr
                            };
                            this[ code128 ][ this[ codeSet ][ i ] ] = code;
                        }
                    }
                    return this[ code128 ];
                },
                
                getCodeValue: function( chr ) {
                    for ( var i=0; i<this.CodeSet.length; i+=1 ) {
                        var codeSet = this.getCode128( this.CodeSet[ i ] );
                        if ( K.defined( codeSet[ chr ] ) )
                            return codeSet[ chr ].value;
                    }
                },
                
                calculateBarcode: function( st ) {
                    var value = st.value;
                    if ( typeof value === 'string') {
                        st.type = K.ucfirst( K.trim( st.type ) );
                        st.subtype = K.trim( st.subtype ).toUpperCase();
                        st.fulltype = st.type + st.subtype;
                        var code128 = this.getCode128( st.subtype );
                        var code128A = this.getCode128( 'A' );
                        var startMarker = code128[ st.type + st.subtype ].barwidth;
                        var endMarker = code128[ 'Stop' ].barwidth;
                        var barcode = {};
                        var widths = [];
                        
                        var sum = this.getCodeValue( st.fulltype );
                        var group = [];
                        var width, totalWidth = numberOfBars = 0;
                        var position = 1;
                        var len = value.length;
                        if ( st.subtype === 'C' ) {
                            for ( var i=0; i<len; i+=2 ) {
                                if ( i + 1 < len ) {
                                    var c = value.charAt( i ) + value.charAt( i + 1 );
                                    sum += this.getCodeValue( c ) * position;
                                    position += 1;
                                    width = code128[ c ];
                                }
                                else {
                                    var c = value.charAt( i );
                                    sum += this.getCodeValue( 'Code A' ) * position;
                                    position += 1;
                                    sum += this.getCodeValue( c ) * position;
                                    position += 1;
                                    group = group.concat( code128[ 'Code A' ].barwidth );
                                    totalWidth += 11;
                                    numberOfBars += 6;
                                    width = code128A[ c ];
                                }
                                if ( K.notDefined( width ) )
                                    throw K.newException( 
                                        'Not a legal ' + st.fulltype + ' string' );
                                group = group.concat( width.barwidth );
                                totalWidth += 11;
                                numberOfBars += 6;
                            }
                        }
                        else  {
                            for ( var i=0; i<len; i+=1 ) {
                                var c = value.charAt( i );
                                sum += this.getCodeValue( c ) * position;
                                width = code128[ c ];
                                if ( K.notDefined( width ) )
                                    throw K.newException( 
                                        'Not a legal ' + st.fulltype + ' string' );
                                group = group.concat( width.barwidth );
                                totalWidth += 11;
                                numberOfBars += 6;
                                position += 1;
                            }
                        }
                        var checksum = sum % 103;
                        barcode.checksum = checksum;
                        barcode.displayText = value;
                        if ( st.showChecksum === true )
                            barcode.displayText += ' ' + checksum;
                        group = group.concat( 
                            this.getBarWidth()[ checksum ] );
                        totalWidth += 11;
                        numberOfBars += 6;
                    
                        if ( st.noStartStopChars !== true ) {
                            widths = widths.concat( startMarker );
                            totalWidth += 11;
                            numberOfBars += 6;
                        }
                        widths = widths.concat( group );
                        if ( st.noStartStopChars !== true ) {
                            widths = widths.concat( endMarker );
                            totalWidth += 13;
                            numberOfBars += 7;
                        }
                        
                        barcode.widths = widths;
                        barcode.heights = K.newArray( numberOfBars, 0 );
                        barcode.totalWidth = totalWidth;
                        
                        return barcode;
                    }
                    else {
                        throw K.newException( 'Not a string.' );
                    }
                }
            },
            
            'GS1 128': {
                
                calculateBarcode: function( st ) {
                    var value = st.value;
                    if ( typeof value === 'string') {
                        
                        var KCode128 = UI.KBarcode.Code128;
                        var type = 'Code128C';
                        var code128C = KCode128.getCode128( 'C' );
                        var code128A = KCode128.getCode128( 'A' );
                        var startMarker = code128C[ type ].barwidth;
                        var endMarker = code128C[ 'Stop' ].barwidth;
                        var barcode = {};
                        var widths = [];
                        
                        var sum = KCode128.getCodeValue( type );
                        var group = [];
                        var width, totalWidth, numberOfBars;
                        var values = value.split( '[FNC1]' );
                        totalWidth = numberOfBars = 0;
                        var position = 1;
                        
                        for ( var j=0; j<values.length; j+=1 ) {
                            var v = K.replaceAll( values[ j ], ' ', '' );
                            var len = v.length;
                            if ( len > 0 ) {
                                sum += KCode128.getCodeValue( 'FNC1' ) * position;
                                position += 1;
                                group = group.concat( code128C[ 'FNC1' ].barwidth );
                                totalWidth += 11;
                                numberOfBars += 6;
                                for ( var i=0; i<len; i+=2 ) {
                                    if ( i + 1 < len ) {
                                        var c = v.charAt( i ) + v.charAt( i + 1 );
                                        sum += KCode128.getCodeValue( c ) * position;
                                        position += 1;
                                        width = code128C[ c ];
                                    }
                                    else {
                                        var c = v.charAt( i );
                                        sum += KCode128.getCodeValue( 'Code B' ) * position;
                                        position += 1;
                                        sum += KCode128.getCodeValue( c ) * position;
                                        position += 1;
                                        group = group.concat( code128C[ 'Code B' ].barwidth );
                                        totalWidth += 11;
                                        numberOfBars += 6;

                                        if ( i< values.length - 1 ) {
                                            sum += KCode128.getCodeValue( 'Code C' ) * position;
                                            position += 1;
                                            group = group.concat( code128A[ 'Code C' ].barwidth );
                                            totalWidth += 11;
                                            numberOfBars += 6;
                                        }

                                        width = code128A[ c ];
                                    }
                                    if ( K.notDefined( width ) )
                                        throw 'Not a legal ' + st.fulltype + ' string';
                                    group = group.concat( width.barwidth );
                                    totalWidth += 11;
                                    numberOfBars += 6;
                                }
                            }
                        }
                            
                        var checksum = sum % 103 ;
                        barcode.checksum = checksum;
                        barcode.displayText = value;
                        if ( st.showChecksum === true )
                            barcode.displayText += ' ' + checksum;
                        group = group.concat( 
                            KCode128.getBarWidth()[ checksum ] );
                        totalWidth += 11;
                        numberOfBars += 6;   
                        
                        if ( st.noStartStopChars !== true ) {
                            widths = widths.concat( startMarker );
                            totalWidth += 11;
                            numberOfBars += 6;
                        }
                        widths = widths.concat( group );
                        if ( st.noStartStopChars !== true ) {
                            widths = widths.concat( endMarker );
                            totalWidth += 13;
                            numberOfBars += 7;
                        }
                        
                        barcode.widths = widths;
                        barcode.heights = K.newArray( numberOfBars, 0 );
                        barcode.totalWidth = totalWidth;
                        
                        return barcode;
                    }
                    else {
                        throw K.newException( 
                            'Not a string' );
                    }
                }
                
            },
            
            MSI: {
                
                chars: [
                    'Start', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'Stop'
                ],
                
                barwidth: [
                    '21', '12121212', '12121221', '12122112', '12122121', '12211212', 
                    '12211221', '12212112', '12212121', '21121212', '21121221', '121'
                ],
                
                getBarwidth: function() {
                    if ( K.notDefined( this.barW ) ) {
                        this.barW = {};
                        for ( var i=0; i<this.barwidth.length; i+=1 )
                            this.barW[ this.chars[ i ] ] = UI.KBarcode
                                .toIntArr( this.barwidth[ i ] );
                    }
                    return this.barW;
                },
                
                sumDigit: function( number ) {
                    var sum = 0, r;
                    while ( typeof number === 'number'
                        && number !== 0 ) {
                        r = number % 10;
                        sum += r;
                        number = ( number - r ) / 10;
                    }
                    return sum;
                },
                
                mod10Checksum: function( value ) {
                    //Luhn algorithm
                    value = UI.KBarcode.toIntArr( value );
                    value = value.reverse();
                    var sum = 0;
                    for ( var i=0; i<value.length; i+=1 ) {
                        if ( i % 2 === 0 )
                            sum += this.sumDigit( 2 * value [ i ] );
                        else
                            sum += value [ i ];
                    }
                    sum = sum % 10;
                    if ( sum !== 0 ) sum = 10 - sum;
                    return sum;
                },
                
                mod11Checksum: function( value, algorithm ) {
                    value = UI.KBarcode.toIntArr( value );
                    value = value.reverse();
                    var sum = 0, weight = 2;
                    var maxWeight = ( algorithm === 'NCR' ) ? 
                        9 : 7;
                    for ( var i=0; i<value.length; i+=1 ) {
                        sum += value [ i ] * weight;
                        weight += 1;
                        if ( weight > maxWeight ) weight = 2;
                    }
                    sum = sum % 11;
                    if ( sum !== 0 ) sum = 11 - sum;
                    return sum;
                },
                
                mod1010Checksum: function( value ) {
                    var chk = this.mod10Checksum( value );
                    var chk2 = this.mod10Checksum( value.toString() + chk );
                    return chk + chk2;
                },
                
                mod1110Checksum: function( value ) {
                    var chk = this.mod11Checksum( value );
                    var chk2 = this.mod10Checksum( value.toString() + chk );
                    return chk + chk2;
                },
                
                calculateBarcode: function( st ) {
                    var value = K.replaceAll( st.value, ' ', '' );
                    var barwidth = this.getBarwidth();
                    var startMarker = barwidth[ 'Start' ];
                    var endMarker = barwidth[ 'Stop' ];
                    var barcode = {};
                    var widths = [];

                    var group = [];
                    var width, totalWidth, numberOfBars;
                    totalWidth = numberOfBars = 0;

                    var len = value.length;
                    if ( len > 0 ) {
                        var checksum = this[ 
                            st.subtype.toLowerCase() + 'Checksum' ]( value );
                        barcode.checksum = checksum;
                        barcode.displayText = value;
                        if ( st.showChecksum === true )
                            barcode.displayText += ' ' + checksum;
                        value = value.toString() + checksum;
                        len = value.length;
                        for ( var i=0; i<len; i+=1 ) {
                            var c = value.charAt( i );
                            width = barwidth[ c ];
                            if ( K.defined( width ) )
                                group = group.concat( width );
                            else
                                throw K.newException(
                                    'Not a legal MSI barcode character:' + c );
                        }
                        numberOfBars += 8 * len;
                        totalWidth += 12 * len;
                    }

                    widths = widths.concat( startMarker );
                    numberOfBars += 2;
                    totalWidth += 3;
                    widths = widths.concat( group );
                    widths = widths.concat( endMarker );
                    numberOfBars += 3;
                    totalWidth += 4;

                    barcode.widths = widths;
                    barcode.heights = K.newArray( numberOfBars, 0 );
                    barcode.totalWidth = totalWidth;

                    return barcode;
                }
                
            },
            
            QRCode: 
                // [qr.js](http://neocotic.com/qr.js)  
                // (c) 2014 Alasdair Mercer  
                // Freely distributable under the MIT license.  
                // Based on [jsqrencode](http://code.google.com/p/jsqrencode/)  
                // (c) 2010 tz@execpc.com  
                // Licensed under the GPL Version 3 license.  
                // For all details and documentation:  
                // <http://neocotic.com/qr.js>
                (function(root) {

//                'use strict';

                // Private constants
                // -----------------

                // Alignment pattern.
                var ALIGNMENT_DELTA = [
                    0, 11, 15, 19, 23, 27, 31,
                    16, 18, 20, 22, 24, 26, 28, 20, 22, 24, 24, 26, 28, 28, 22, 24, 24,
                    26, 26, 28, 28, 24, 24, 26, 26, 26, 28, 28, 24, 26, 26, 26, 28, 28
                ];
                // Default MIME type.
                var DEFAULT_MIME = 'image/png';
                // MIME used to initiate a browser download prompt when `qr.save` is called.
                var DOWNLOAD_MIME = 'image/octet-stream';
                // There are four elements per version. The first two indicate the number of blocks, then the
                // data width, and finally the ECC width.
                var ECC_BLOCKS = [
                    1, 0, 19, 7,  1, 0, 16, 10,  1, 0, 13, 13,  1, 0, 9, 17,
                    1, 0, 34, 10, 1, 0, 28, 16, 1, 0, 22, 22, 1, 0, 16, 28,
                    1, 0, 55, 15, 1, 0, 44, 26, 2, 0, 17, 18, 2, 0, 13, 22,
                    1, 0, 80, 20, 2, 0, 32, 18, 2, 0, 24, 26, 4, 0, 9, 16,
                    1, 0, 108, 26, 2, 0, 43, 24, 2, 2, 15, 18, 2, 2, 11, 22,
                    2, 0, 68, 18, 4, 0, 27, 16, 4, 0, 19, 24, 4, 0, 15, 28,
                    2, 0, 78, 20, 4, 0, 31, 18, 2, 4, 14, 18, 4, 1, 13, 26,
                    2, 0, 97, 24, 2, 2, 38, 22, 4, 2, 18, 22, 4, 2, 14, 26,
                    2, 0, 116, 30, 3, 2, 36, 22, 4, 4, 16, 20, 4, 4, 12, 24,
                    2, 2, 68, 18, 4, 1, 43, 26, 6, 2, 19, 24, 6, 2, 15, 28,
                    4, 0, 81, 20, 1, 4, 50, 30, 4, 4, 22, 28, 3, 8, 12, 24,
                    2, 2, 92, 24, 6, 2, 36, 22, 4, 6, 20, 26, 7, 4, 14, 28,
                    4, 0, 107, 26, 8, 1, 37, 22, 8, 4, 20, 24, 12, 4, 11, 22,
                    3, 1, 115, 30, 4, 5, 40, 24, 11, 5, 16, 20, 11, 5, 12, 24,
                    5, 1, 87, 22, 5, 5, 41, 24, 5, 7, 24, 30, 11, 7, 12, 24,
                    5, 1, 98, 24, 7, 3, 45, 28, 15, 2, 19, 24, 3, 13, 15, 30,
                    1, 5, 107, 28, 10, 1, 46, 28, 1, 15, 22, 28, 2, 17, 14, 28,
                    5, 1, 120, 30, 9, 4, 43, 26, 17, 1, 22, 28, 2, 19, 14, 28,
                    3, 4, 113, 28, 3, 11, 44, 26, 17, 4, 21, 26, 9, 16, 13, 26,
                    3, 5, 107, 28, 3, 13, 41, 26, 15, 5, 24, 30, 15, 10, 15, 28,
                    4, 4, 116, 28, 17, 0, 42, 26, 17, 6, 22, 28, 19, 6, 16, 30,
                    2, 7, 111, 28, 17, 0, 46, 28, 7, 16, 24, 30, 34, 0, 13, 24,
                    4, 5, 121, 30, 4, 14, 47, 28, 11, 14, 24, 30, 16, 14, 15, 30,
                    6, 4, 117, 30, 6, 14, 45, 28, 11, 16, 24, 30, 30, 2, 16, 30,
                    8, 4, 106, 26, 8, 13, 47, 28, 7, 22, 24, 30, 22, 13, 15, 30,
                    10, 2, 114, 28, 19, 4, 46, 28, 28, 6, 22, 28, 33, 4, 16, 30,
                    8, 4, 122, 30, 22, 3, 45, 28, 8, 26, 23, 30, 12, 28, 15, 30,
                    3, 10, 117, 30, 3, 23, 45, 28, 4, 31, 24, 30, 11, 31, 15, 30,
                    7, 7, 116, 30, 21, 7, 45, 28, 1, 37, 23, 30, 19, 26, 15, 30,
                    5, 10, 115, 30, 19, 10, 47, 28, 15, 25, 24, 30, 23, 25, 15, 30,
                    13, 3, 115, 30, 2, 29, 46, 28, 42, 1, 24, 30, 23, 28, 15, 30,
                    17, 0, 115, 30, 10, 23, 46, 28, 10, 35, 24, 30, 19, 35, 15, 30,
                    17, 1, 115, 30, 14, 21, 46, 28, 29, 19, 24, 30, 11, 46, 15, 30,
                    13, 6, 115, 30, 14, 23, 46, 28, 44, 7, 24, 30, 59, 1, 16, 30,
                    12, 7, 121, 30, 12, 26, 47, 28, 39, 14, 24, 30, 22, 41, 15, 30,
                    6, 14, 121, 30, 6, 34, 47, 28, 46, 10, 24, 30, 2, 64, 15, 30,
                    17, 4, 122, 30, 29, 14, 46, 28, 49, 10, 24, 30, 24, 46, 15, 30,
                    4, 18, 122, 30, 13, 32, 46, 28, 48, 14, 24, 30, 42, 32, 15, 30,
                    20, 4, 117, 30, 40, 7, 47, 28, 43, 22, 24, 30, 10, 67, 15, 30,
                    19, 6, 118, 30, 18, 31, 47, 28, 34, 34, 24, 30, 20, 61, 15, 30
                ];
                // Map of human-readable ECC levels.
                var ECC_LEVELS = {
                    L: 1,
                    M: 2,
                    Q: 3,
                    H: 4
                };
                // Final format bits with mask (level << 3 | mask).
                var FINAL_FORMAT = [
                    0x77c4, 0x72f3, 0x7daa, 0x789d, 0x662f, 0x6318, 0x6c41, 0x6976, /* L */
                    0x5412, 0x5125, 0x5e7c, 0x5b4b, 0x45f9, 0x40ce, 0x4f97, 0x4aa0, /* M */
                    0x355f, 0x3068, 0x3f31, 0x3a06, 0x24b4, 0x2183, 0x2eda, 0x2bed, /* Q */
                    0x1689, 0x13be, 0x1ce7, 0x19d0, 0x0762, 0x0255, 0x0d0c, 0x083b  /* H */
                ];
                // Galois field exponent table.
                var GALOIS_EXPONENT = [
                    0x01, 0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1d, 0x3a, 0x74, 0xe8, 0xcd, 0x87, 0x13, 0x26,
                    0x4c, 0x98, 0x2d, 0x5a, 0xb4, 0x75, 0xea, 0xc9, 0x8f, 0x03, 0x06, 0x0c, 0x18, 0x30, 0x60, 0xc0,
                    0x9d, 0x27, 0x4e, 0x9c, 0x25, 0x4a, 0x94, 0x35, 0x6a, 0xd4, 0xb5, 0x77, 0xee, 0xc1, 0x9f, 0x23,
                    0x46, 0x8c, 0x05, 0x0a, 0x14, 0x28, 0x50, 0xa0, 0x5d, 0xba, 0x69, 0xd2, 0xb9, 0x6f, 0xde, 0xa1,
                    0x5f, 0xbe, 0x61, 0xc2, 0x99, 0x2f, 0x5e, 0xbc, 0x65, 0xca, 0x89, 0x0f, 0x1e, 0x3c, 0x78, 0xf0,
                    0xfd, 0xe7, 0xd3, 0xbb, 0x6b, 0xd6, 0xb1, 0x7f, 0xfe, 0xe1, 0xdf, 0xa3, 0x5b, 0xb6, 0x71, 0xe2,
                    0xd9, 0xaf, 0x43, 0x86, 0x11, 0x22, 0x44, 0x88, 0x0d, 0x1a, 0x34, 0x68, 0xd0, 0xbd, 0x67, 0xce,
                    0x81, 0x1f, 0x3e, 0x7c, 0xf8, 0xed, 0xc7, 0x93, 0x3b, 0x76, 0xec, 0xc5, 0x97, 0x33, 0x66, 0xcc,
                    0x85, 0x17, 0x2e, 0x5c, 0xb8, 0x6d, 0xda, 0xa9, 0x4f, 0x9e, 0x21, 0x42, 0x84, 0x15, 0x2a, 0x54,
                    0xa8, 0x4d, 0x9a, 0x29, 0x52, 0xa4, 0x55, 0xaa, 0x49, 0x92, 0x39, 0x72, 0xe4, 0xd5, 0xb7, 0x73,
                    0xe6, 0xd1, 0xbf, 0x63, 0xc6, 0x91, 0x3f, 0x7e, 0xfc, 0xe5, 0xd7, 0xb3, 0x7b, 0xf6, 0xf1, 0xff,
                    0xe3, 0xdb, 0xab, 0x4b, 0x96, 0x31, 0x62, 0xc4, 0x95, 0x37, 0x6e, 0xdc, 0xa5, 0x57, 0xae, 0x41,
                    0x82, 0x19, 0x32, 0x64, 0xc8, 0x8d, 0x07, 0x0e, 0x1c, 0x38, 0x70, 0xe0, 0xdd, 0xa7, 0x53, 0xa6,
                    0x51, 0xa2, 0x59, 0xb2, 0x79, 0xf2, 0xf9, 0xef, 0xc3, 0x9b, 0x2b, 0x56, 0xac, 0x45, 0x8a, 0x09,
                    0x12, 0x24, 0x48, 0x90, 0x3d, 0x7a, 0xf4, 0xf5, 0xf7, 0xf3, 0xfb, 0xeb, 0xcb, 0x8b, 0x0b, 0x16,
                    0x2c, 0x58, 0xb0, 0x7d, 0xfa, 0xe9, 0xcf, 0x83, 0x1b, 0x36, 0x6c, 0xd8, 0xad, 0x47, 0x8e, 0x00
                ];
                // Galois field log table.
                var GALOIS_LOG = [
                    0xff, 0x00, 0x01, 0x19, 0x02, 0x32, 0x1a, 0xc6, 0x03, 0xdf, 0x33, 0xee, 0x1b, 0x68, 0xc7, 0x4b,
                    0x04, 0x64, 0xe0, 0x0e, 0x34, 0x8d, 0xef, 0x81, 0x1c, 0xc1, 0x69, 0xf8, 0xc8, 0x08, 0x4c, 0x71,
                    0x05, 0x8a, 0x65, 0x2f, 0xe1, 0x24, 0x0f, 0x21, 0x35, 0x93, 0x8e, 0xda, 0xf0, 0x12, 0x82, 0x45,
                    0x1d, 0xb5, 0xc2, 0x7d, 0x6a, 0x27, 0xf9, 0xb9, 0xc9, 0x9a, 0x09, 0x78, 0x4d, 0xe4, 0x72, 0xa6,
                    0x06, 0xbf, 0x8b, 0x62, 0x66, 0xdd, 0x30, 0xfd, 0xe2, 0x98, 0x25, 0xb3, 0x10, 0x91, 0x22, 0x88,
                    0x36, 0xd0, 0x94, 0xce, 0x8f, 0x96, 0xdb, 0xbd, 0xf1, 0xd2, 0x13, 0x5c, 0x83, 0x38, 0x46, 0x40,
                    0x1e, 0x42, 0xb6, 0xa3, 0xc3, 0x48, 0x7e, 0x6e, 0x6b, 0x3a, 0x28, 0x54, 0xfa, 0x85, 0xba, 0x3d,
                    0xca, 0x5e, 0x9b, 0x9f, 0x0a, 0x15, 0x79, 0x2b, 0x4e, 0xd4, 0xe5, 0xac, 0x73, 0xf3, 0xa7, 0x57,
                    0x07, 0x70, 0xc0, 0xf7, 0x8c, 0x80, 0x63, 0x0d, 0x67, 0x4a, 0xde, 0xed, 0x31, 0xc5, 0xfe, 0x18,
                    0xe3, 0xa5, 0x99, 0x77, 0x26, 0xb8, 0xb4, 0x7c, 0x11, 0x44, 0x92, 0xd9, 0x23, 0x20, 0x89, 0x2e,
                    0x37, 0x3f, 0xd1, 0x5b, 0x95, 0xbc, 0xcf, 0xcd, 0x90, 0x87, 0x97, 0xb2, 0xdc, 0xfc, 0xbe, 0x61,
                    0xf2, 0x56, 0xd3, 0xab, 0x14, 0x2a, 0x5d, 0x9e, 0x84, 0x3c, 0x39, 0x53, 0x47, 0x6d, 0x41, 0xa2,
                    0x1f, 0x2d, 0x43, 0xd8, 0xb7, 0x7b, 0xa4, 0x76, 0xc4, 0x17, 0x49, 0xec, 0x7f, 0x0c, 0x6f, 0xf6,
                    0x6c, 0xa1, 0x3b, 0x52, 0x29, 0x9d, 0x55, 0xaa, 0xfb, 0x60, 0x86, 0xb1, 0xbb, 0xcc, 0x3e, 0x5a,
                    0xcb, 0x59, 0x5f, 0xb0, 0x9c, 0xa9, 0xa0, 0x51, 0x0b, 0xf5, 0x16, 0xeb, 0x7a, 0x75, 0x2c, 0xd7,
                    0x4f, 0xae, 0xd5, 0xe9, 0xe6, 0xe7, 0xad, 0xe8, 0x74, 0xd6, 0xf4, 0xea, 0xa8, 0x50, 0x58, 0xaf
                ];
                // *Badness* coefficients.
                var N1 = 3;
                var N2 = 3;
                var N3 = 40;
                var N4 = 10;
                // Version pattern.
                var VERSION_BLOCK = [
                    0xc94, 0x5bc, 0xa99, 0x4d3, 0xbf6, 0x762, 0x847, 0x60d, 0x928, 0xb78, 0x45d, 0xa17, 0x532,
                    0x9a6, 0x683, 0x8c9, 0x7ec, 0xec4, 0x1e1, 0xfab, 0x08e, 0xc1a, 0x33f, 0xd75, 0x250, 0x9d5,
                    0x6f0, 0x8ba, 0x79f, 0xb0b, 0x42e, 0xa64, 0x541, 0xc69
                ];
                // Mode for node.js file system file writes.
                var WRITE_MODE = parseInt('0666', 8);

                // Private variables
                // -----------------

                // Run lengths for badness.
                var badBuffer = [];
                // Constructor for `canvas` elements in the node.js environment.
                var Canvas;
                // Data block.
                var dataBlock;
                // ECC data blocks and tables.
                var eccBlock, neccBlock1, neccBlock2;
                // ECC buffer.
                var eccBuffer = [];
                // ECC level (defaults to **L**).
                var eccLevel = 1;
                // Image buffer.
                var frameBuffer = [];
                // Fixed part of the image.
                var frameMask = [];
                // File system within the node.js environment.
                var fs;
                // Constructor for `img` elements in the node.js environment.
                var Image;
                // Indicates whether or not this script is running in node.js.
                var inNode = false;
                // Generator polynomial.
                var polynomial = [];
                // Save the previous value of the `qr` variable.
                var previousQr = root.qr;
                // Data input buffer.
                var stringBuffer = [];
                // Version for the data.
                var version;
                // Data width is based on `version`.
                var width;

                // Private functions
                // -----------------

                // Create a new canvas  using `document.createElement` unless script is running in node.js, in
                // which case the `canvas` module is used.
                function createCanvas() {
                    return inNode ? new Canvas() : root.document.createElement('canvas');
                }

                // Create a new image using `document.createElement` unless script is running in node.js, in
                // which case the `canvas` module is used.
                function createImage() {
                    return inNode ? new Image() : root.document.createElement('img');
                }

                // Force the canvas image to be downloaded in the browser.  
                // Optionally, a `callback` function can be specified which will be called upon completed. Since
                // this is not an asynchronous operation, this is merely convenient and helps simplify the
                // calling code.
                function download(cvs, data, callback) {
                    var mime = data.mime || DEFAULT_MIME;

                    root.location.href = cvs.toDataURL(mime).replace(mime, DOWNLOAD_MIME);

                    if (typeof callback === 'function') callback();
                }

                // Normalize the `data` that is provided to the main API.
                function normalizeData(data) {
                    if (typeof data === 'string') data = {value: data};
                    return data || {};
                }

                // Override the `qr` API methods that require HTML5 canvas support to throw a relevant error.
                function overrideAPI(qr) {
                    var methods = ['canvas', 'image', 'save', 'saveSync', 'toDataURL'];
                    var i;

                    function overrideMethod(name) {
                        qr[name] = function() {
                            throw new Error(name + ' requires HTML5 canvas element support');
                        };
                    }

                    for (i = 0; i < methods.length; i++) {
                        overrideMethod(methods[i]);
                    }
                }

                // Asynchronously write the data of the rendered canvas to a given file path.
                function writeFile(cvs, data, callback) {
                    if (typeof data.path !== 'string') {
                        return callback(new TypeError('Invalid path type: ' + typeof data.path));
                    }

                    var fd, buff;

                    // Write the buffer to the open file stream once both prerequisites are met.
                    function writeBuffer() {
                        fs.write(fd, buff, 0, buff.length, 0, function(error) {
                            fs.close(fd);

                            callback(error);
                        });
                    }

                    // Create a buffer of the canvas' data.
                    cvs.toBuffer(function(error, _buff) {
                        if (error) return callback(error);

                        buff = _buff;
                        if (fd) {
                            writeBuffer();
                        }
                    });

                    // Open a stream for the file to be written.
                    fs.open(data.path, 'w', WRITE_MODE, function(error, _fd) {
                        if (error) return callback(error);

                        fd = _fd;
                        if (buff) {
                            writeBuffer();
                        }
                    });
                }

                // Write the data of the rendered canvas to a given file path.
                function writeFileSync(cvs, data) {
                    if (typeof data.path !== 'string') {
                        throw new TypeError('Invalid path type: ' + typeof data.path);
                    }

                    var buff = cvs.toBuffer();
                    var fd = fs.openSync(data.path, 'w', WRITE_MODE);

                    try {
                        fs.writeSync(fd, buff, 0, buff.length, 0);
                    } finally {
                        fs.closeSync(fd);
                    }
                }

                // Set bit to indicate cell in frame is immutable (symmetric around diagonal).
                function setMask(x, y) {
                    var bit;

                    if (x > y) {
                        bit = x;
                        x = y;
                        y = bit;
                    }

                    bit = y;
                    bit *= y;
                    bit += y;
                    bit >>= 1;
                    bit += x;

                    frameMask[bit] = 1;
                }

                // Enter alignment pattern. Foreground colour to frame, background to mask. Frame will be merged
                // with mask later.
                function addAlignment(x, y) {
                    var i;

                    frameBuffer[x + width * y] = 1;

                    for (i = -2; i < 2; i++) {
                        frameBuffer[(x + i) + width * (y - 2)] = 1;
                        frameBuffer[(x - 2) + width * (y + i + 1)] = 1;
                        frameBuffer[(x + 2) + width * (y + i)] = 1;
                        frameBuffer[(x + i + 1) + width * (y + 2)] = 1;
                    }

                    for (i = 0; i < 2; i++) {
                        setMask(x - 1, y + i);
                        setMask(x + 1, y - i);
                        setMask(x - i, y - 1);
                        setMask(x + i, y + 1);
                    }
                }

                // Exponentiation mod N.
                function modN(x) {
                    while (x >= 255) {
                        x -= 255;
                        x = (x >> 8) + (x & 255);
                    }

                    return x;
                }

                // Calculate and append `ecc` data to the `data` block. If block is in the string buffer the
                // indices to buffers are used.
                function appendData(data, dataLength, ecc, eccLength) {
                    var bit, i, j;

                    for (i = 0; i < eccLength; i++) {
                        stringBuffer[ecc + i] = 0;
                    }

                    for (i = 0; i < dataLength; i++) {
                        bit = GALOIS_LOG[stringBuffer[data + i] ^ stringBuffer[ecc]];

                        if (bit !== 255) {
                            for (j = 1; j < eccLength; j++) {
                                stringBuffer[ecc + j - 1] = stringBuffer[ecc + j] ^
                                        GALOIS_EXPONENT[modN(bit + polynomial[eccLength - j])];
                            }
                        } else {
                            for (j = ecc; j < ecc + eccLength; j++) {
                                stringBuffer[j] = stringBuffer[j + 1];
                            }
                        }

                        stringBuffer[ecc + eccLength - 1] = bit === 255 ? 0 :
                                GALOIS_EXPONENT[modN(bit + polynomial[0])];
                    }
                }

                // Check mask since symmetricals use half.
                function isMasked(x, y) {
                    var bit;

                    if (x > y) {
                        bit = x;
                        x = y;
                        y = bit;
                    }

                    bit = y;
                    bit += y * y;
                    bit >>= 1;
                    bit += x;

                    return frameMask[bit] === 1;
                }

                // Apply the selected mask out of the 8 options.
                function applyMask(mask) {
                    var x, y, r3x, r3y;

                    switch (mask) {
                        case 0:
                            for (y = 0; y < width; y++) {
                                for (x = 0; x < width; x++) {
                                    if (!((x + y) & 1) && !isMasked(x, y)) {
                                        frameBuffer[x + y * width] ^= 1;
                                    }
                                }
                            }

                            break;
                        case 1:
                            for (y = 0; y < width; y++) {
                                for (x = 0; x < width; x++) {
                                    if (!(y & 1) && !isMasked(x, y)) {
                                        frameBuffer[x + y * width] ^= 1;
                                    }
                                }
                            }

                            break;
                        case 2:
                            for (y = 0; y < width; y++) {
                                for (r3x = 0, x = 0; x < width; x++, r3x++) {
                                    if (r3x === 3) r3x = 0;

                                    if (!r3x && !isMasked(x, y)) {
                                        frameBuffer[x + y * width] ^= 1;
                                    }
                                }
                            }

                            break;
                        case 3:
                            for (r3y = 0, y = 0; y < width; y++, r3y++) {
                                if (r3y === 3) r3y = 0;

                                for (r3x = r3y, x = 0; x < width; x++, r3x++) {
                                    if (r3x === 3) r3x = 0;

                                    if (!r3x && !isMasked(x, y)) {
                                        frameBuffer[x + y * width] ^= 1;
                                    }
                                }
                            }

                            break;
                        case 4:
                            for (y = 0; y < width; y++) {
                                for (r3x = 0, r3y = ((y >> 1) & 1), x = 0; x < width; x++, r3x++) {
                                    if (r3x === 3) {
                                        r3x = 0;
                                        r3y = !r3y;
                                    }

                                    if (!r3y && !isMasked(x, y)) {
                                        frameBuffer[x + y * width] ^= 1;
                                    }
                                }
                            }

                            break;
                        case 5:
                            for (r3y = 0, y = 0; y < width; y++, r3y++) {
                                if (r3y === 3) r3y = 0;

                                for (r3x = 0, x = 0; x < width; x++, r3x++) {
                                    if (r3x === 3) r3x = 0;

                                    if (!((x & y & 1) + !(!r3x | !r3y)) && !isMasked(x, y)) {
                                        frameBuffer[x + y * width] ^= 1;
                                    }
                                }
                            }

                            break;
                        case 6:
                            for (r3y = 0, y = 0; y < width; y++, r3y++) {
                                if (r3y === 3) r3y = 0;

                                for (r3x = 0, x = 0; x < width; x++, r3x++) {
                                    if (r3x === 3) r3x = 0;

                                    if (!(((x & y & 1) + (r3x && (r3x === r3y))) & 1) && !isMasked(x, y)) {
                                        frameBuffer[x + y * width] ^= 1;
                                    }
                                }
                            }

                            break;
                        case 7:
                            for (r3y = 0, y = 0; y < width; y++, r3y++) {
                                if (r3y === 3) r3y = 0;

                                for (r3x = 0, x = 0; x < width; x++, r3x++) {
                                    if (r3x === 3) r3x = 0;

                                    if (!(((r3x && (r3x === r3y)) + ((x + y) & 1)) & 1) && !isMasked(x, y)) {
                                        frameBuffer[x + y * width] ^= 1;
                                    }
                                }
                            }

                            break;
                    }
                }

                // Using the table for the length of each run, calculate the amount of bad image. Long runs or
                // those that look like finders are called twice; once for X and Y.
                function getBadRuns(length) {
                    var badRuns = 0;
                    var i;

                    for (i = 0; i <= length; i++) {
                        if (badBuffer[i] >= 5) {
                            badRuns += N1 + badBuffer[i] - 5;
                        }
                    }

                    // FBFFFBF as in finder.
                    for (i = 3; i < length - 1; i += 2) {
                        if (badBuffer[i - 2] === badBuffer[i + 2] &&
                                badBuffer[i + 2] === badBuffer[i - 1] &&
                                badBuffer[i - 1] === badBuffer[i + 1] &&
                                badBuffer[i - 1] * 3 === badBuffer[i] &&
                                // Background around the foreground pattern? Not part of the specs.
                                        (badBuffer[i - 3] === 0 || i + 3 > length ||
                                                badBuffer[i - 3] * 3 >= badBuffer[i] * 4 ||
                                                badBuffer[i + 3] * 3 >= badBuffer[i] * 4)) {
                            badRuns += N3;
                        }
                    }

                    return badRuns;
                }

                // Calculate how bad the masked image is (e.g. blocks, imbalance, runs, or finders).
                function checkBadness() {
                    var b, b1, bad, big, bw, count, h, x, y;
                    bad = bw = count = 0;

                    // Blocks of same colour.
                    for (y = 0; y < width - 1; y++) {
                        for (x = 0; x < width - 1; x++) {
                            // All foreground colour.
                            if ((frameBuffer[x + width * y] &&
                                    frameBuffer[(x + 1) + width * y] &&
                                    frameBuffer[x + width * (y + 1)] &&
                                    frameBuffer[(x + 1) + width * (y + 1)]) ||
                                    // All background colour.
                                    !(frameBuffer[x + width * y] ||
                                            frameBuffer[(x + 1) + width * y] ||
                                            frameBuffer[x + width * (y + 1)] ||
                                            frameBuffer[(x + 1) + width * (y + 1)])) {
                                bad += N2;
                            }
                        }
                    }

                    // X runs.
                    for (y = 0; y < width; y++) {
                        badBuffer[0] = 0;

                        for (h = b = x = 0; x < width; x++) {
                            if ((b1 = frameBuffer[x + width * y]) === b) {
                                badBuffer[h]++;
                            } else {
                                badBuffer[++h] = 1;
                            }

                            b = b1;
                            bw += b ? 1 : -1;
                        }

                        bad += getBadRuns(h);
                    }

                    if (bw < 0) bw = -bw;

                    big = bw;
                    big += big << 2;
                    big <<= 1;

                    while (big > width * width) {
                        big -= width * width;
                        count++;
                    }

                    bad += count * N4;

                    // Y runs.
                    for (x = 0; x < width; x++) {
                        badBuffer[0] = 0;

                        for (h = b = y = 0; y < width; y++) {
                            if ((b1 = frameBuffer[x + width * y]) === b) {
                                badBuffer[h]++;
                            } else {
                                badBuffer[++h] = 1;
                            }

                            b = b1;
                        }

                        bad += getBadRuns(h);
                    }

                    return bad;
                }

                // Generate the encoded QR image for the string provided.
                function generateFrame(str) {
                    var i, j, k, m, t, v, x, y;

                    // Find the smallest version that fits the string.
                    t = str.length;

                    version = 0;

                    do {
                        version++;

                        k = (eccLevel - 1) * 4 + (version - 1) * 16;

                        neccBlock1 = ECC_BLOCKS[k++];
                        neccBlock2 = ECC_BLOCKS[k++];
                        dataBlock = ECC_BLOCKS[k++];
                        eccBlock = ECC_BLOCKS[k];

                        k = dataBlock * (neccBlock1 + neccBlock2) + neccBlock2 - 3 + (version <= 9);

                        if (t <= k) break;
                    } while (version < 40);

                    // FIXME: Ensure that it fits insted of being truncated.
                    // width = edge length of the QR Code, from 21x21 (version 1) -> 177x177 (version 40)
                    width = 17 + 4 * version;

                    // Allocate, clear and setup data structures.
                    v = dataBlock + (dataBlock + eccBlock) * (neccBlock1 + neccBlock2) + neccBlock2;

                    for (t = 0; t < v; t++) {
                        eccBuffer[t] = 0;
                    }

                    stringBuffer = str.slice(0);

                    for (t = 0; t < width * width; t++) {
                        frameBuffer[t] = 0;
                    }

                    for (t = 0; t < (width * (width + 1) + 1) / 2; t++) {
                        frameMask[t] = 0;
                    }

                    // Insert finders: Foreground colour to frame and background to mask.
                    for (t = 0; t < 3; t++) {
                        k = y = 0;

                        if (t === 1) k = (width - 7);
                        if (t === 2) y = (width - 7);

                        frameBuffer[(y + 3) + width * (k + 3)] = 1;

                        for (x = 0; x < 6; x++) {
                            frameBuffer[(y + x) + width * k] = 1;
                            frameBuffer[y + width * (k + x + 1)] = 1;
                            frameBuffer[(y + 6) + width * (k + x)] = 1;
                            frameBuffer[(y + x + 1) + width * (k + 6)] = 1;
                        }

                        for (x = 1; x < 5; x++) {
                            setMask(y + x, k + 1);
                            setMask(y + 1, k + x + 1);
                            setMask(y + 5, k + x);
                            setMask(y + x + 1, k + 5);
                        }

                        for (x = 2; x < 4; x++) {
                            frameBuffer[(y + x) + width * (k + 2)] = 1;
                            frameBuffer[(y + 2) + width * (k + x + 1)] = 1;
                            frameBuffer[(y + 4) + width * (k + x)] = 1;
                            frameBuffer[(y + x + 1) + width * (k + 4)] = 1;
                        }
                    }

                    // Alignment blocks.
                    if (version > 1) {
                        t = ALIGNMENT_DELTA[version];
                        y = width - 7;

                        for (; ; ) {
                            x = width - 7;

                            while (x > t - 3) {
                                addAlignment(x, y);

                                if (x < t) break;

                                x -= t;
                            }

                            if (y <= t + 9) break;

                            y -= t;

                            addAlignment(6, y);
                            addAlignment(y, 6);
                        }
                    }

                    // Single foreground cell.
                    frameBuffer[8 + width * (width - 8)] = 1;

                    // Timing gap (mask only).
                    for (y = 0; y < 7; y++) {
                        setMask(7, y);
                        setMask(width - 8, y);
                        setMask(7, y + width - 7);
                    }

                    for (x = 0; x < 8; x++) {
                        setMask(x, 7);
                        setMask(x + width - 8, 7);
                        setMask(x, width - 8);
                    }

                    // Reserve mask, format area.
                    for (x = 0; x < 9; x++) {
                        setMask(x, 8);
                    }

                    for (x = 0; x < 8; x++) {
                        setMask(x + width - 8, 8);
                        setMask(8, x);
                    }

                    for (y = 0; y < 7; y++) {
                        setMask(8, y + width - 7);
                    }

                    // Timing row/column.
                    for (x = 0; x < width - 14; x++) {
                        if (x & 1) {
                            setMask(8 + x, 6);
                            setMask(6, 8 + x);
                        } else {
                            frameBuffer[(8 + x) + width * 6] = 1;
                            frameBuffer[6 + width * (8 + x)] = 1;
                        }
                    }

                    // Version block.
                    if (version > 6) {
                        t = VERSION_BLOCK[version - 7];
                        k = 17;

                        for (x = 0; x < 6; x++) {
                            for (y = 0; y < 3; y++, k--) {
                                if (1 & (k > 11 ? version >> (k - 12) : t >> k)) {
                                    frameBuffer[(5 - x) + width * (2 - y + width - 11)] = 1;
                                    frameBuffer[(2 - y + width - 11) + width * (5 - x)] = 1;
                                } else {
                                    setMask(5 - x, 2 - y + width - 11);
                                    setMask(2 - y + width - 11, 5 - x);
                                }
                            }
                        }
                    }

                    // Sync mask bits. Only set above for background cells, so now add the foreground.
                    for (y = 0; y < width; y++) {
                        for (x = 0; x <= y; x++) {
                            if (frameBuffer[x + width * y]) {
                                setMask(x, y);
                            }
                        }
                    }

                    // Convert string to bit stream. 8-bit data to QR-coded 8-bit data (numeric, alphanum, or kanji
                    // not supported).
                    v = stringBuffer.length;

                    // String to array.
                    for (i = 0; i < v; i++) {
                        eccBuffer[i] = stringBuffer.charCodeAt(i);
                    }

                    stringBuffer = eccBuffer.slice(0);

                    // Calculate max string length.
                    x = dataBlock * (neccBlock1 + neccBlock2) + neccBlock2;

                    if (v >= x - 2) {
                        v = x - 2;

                        if (version > 9) v--;
                    }

                    // Shift and re-pack to insert length prefix.
                    i = v;

                    if (version > 9) {
                        stringBuffer[i + 2] = 0;
                        stringBuffer[i + 3] = 0;

                        while (i--) {
                            t = stringBuffer[i];

                            stringBuffer[i + 3] |= 255 & (t << 4);
                            stringBuffer[i + 2] = t >> 4;
                        }

                        stringBuffer[2] |= 255 & (v << 4);
                        stringBuffer[1] = v >> 4;
                        stringBuffer[0] = 0x40 | (v >> 12);
                    } else {
                        stringBuffer[i + 1] = 0;
                        stringBuffer[i + 2] = 0;

                        while (i--) {
                            t = stringBuffer[i];

                            stringBuffer[i + 2] |= 255 & (t << 4);
                            stringBuffer[i + 1] = t >> 4;
                        }

                        stringBuffer[1] |= 255 & (v << 4);
                        stringBuffer[0] = 0x40 | (v >> 4);
                    }

                    // Fill to end with pad pattern.
                    i = v + 3 - (version < 10);

                    while (i < x) {
                        stringBuffer[i++] = 0xec;
                        stringBuffer[i++] = 0x11;
                    }

                    // Calculate generator polynomial.
                    polynomial[0] = 1;

                    for (i = 0; i < eccBlock; i++) {
                        polynomial[i + 1] = 1;

                        for (j = i; j > 0; j--) {
                            polynomial[j] = polynomial[j] ? polynomial[j - 1] ^
                                    GALOIS_EXPONENT[modN(GALOIS_LOG[polynomial[j]] + i)] : polynomial[j - 1];
                        }

                        polynomial[0] = GALOIS_EXPONENT[modN(GALOIS_LOG[polynomial[0]] + i)];
                    }

                    // Use logs for generator polynomial to save calculation step.
                    for (i = 0; i <= eccBlock; i++) {
                        polynomial[i] = GALOIS_LOG[polynomial[i]];
                    }

                    // Append ECC to data buffer.
                    k = x;
                    y = 0;

                    for (i = 0; i < neccBlock1; i++) {
                        appendData(y, dataBlock, k, eccBlock);

                        y += dataBlock;
                        k += eccBlock;
                    }

                    for (i = 0; i < neccBlock2; i++) {
                        appendData(y, dataBlock + 1, k, eccBlock);

                        y += dataBlock + 1;
                        k += eccBlock;
                    }

                    // Interleave blocks.
                    y = 0;

                    for (i = 0; i < dataBlock; i++) {
                        for (j = 0; j < neccBlock1; j++) {
                            eccBuffer[y++] = stringBuffer[i + j * dataBlock];
                        }

                        for (j = 0; j < neccBlock2; j++) {
                            eccBuffer[y++] = stringBuffer[(neccBlock1 * dataBlock) + i + (j * (dataBlock + 1))];
                        }
                    }

                    for (j = 0; j < neccBlock2; j++) {
                        eccBuffer[y++] = stringBuffer[(neccBlock1 * dataBlock) + i + (j * (dataBlock + 1))];
                    }

                    for (i = 0; i < eccBlock; i++) {
                        for (j = 0; j < neccBlock1 + neccBlock2; j++) {
                            eccBuffer[y++] = stringBuffer[x + i + j * eccBlock];
                        }
                    }

                    stringBuffer = eccBuffer;

                    // Pack bits into frame avoiding masked area.
                    x = y = width - 1;
                    k = v = 1;

                    // inteleaved data and ECC codes.
                    m = (dataBlock + eccBlock) * (neccBlock1 + neccBlock2) + neccBlock2;

                    for (i = 0; i < m; i++) {
                        t = stringBuffer[i];

                        for (j = 0; j < 8; j++, t <<= 1) {
                            if (0x80 & t) {
                                frameBuffer[x + width * y] = 1;
                            }

                            // Find next fill position.
                            do {
                                if (v) {
                                    x--;
                                } else {
                                    x++;

                                    if (k) {
                                        if (y !== 0) {
                                            y--;
                                        } else {
                                            x -= 2;
                                            k = !k;

                                            if (x === 6) {
                                                x--;
                                                y = 9;
                                            }
                                        }
                                    } else {
                                        if (y !== width - 1) {
                                            y++;
                                        } else {
                                            x -= 2;
                                            k = !k;

                                            if (x === 6) {
                                                x--;
                                                y -= 8;
                                            }
                                        }
                                    }
                                }

                                v = !v;
                            } while (isMasked(x, y));
                        }
                    }

                    // Save pre-mask copy of frame.
                    stringBuffer = frameBuffer.slice(0);

                    t = 0;
                    y = 30000;

                    // Using `for` instead of `while` since in original Arduino code if an early mask was *good
                    // enough* it wouldn't try for a better one since they get more complex and take longer.
                    for (k = 0; k < 8; k++) {
                        // Returns foreground-background imbalance.
                        applyMask(k);

                        x = checkBadness();

                        // Is current mask better than previous best?
                        if (x < y) {
                            y = x;
                            t = k;
                        }

                        // Don't increment `i` to a void redoing mask.
                        if (t === 7) break;

                        // Reset for next pass.
                        frameBuffer = stringBuffer.slice(0);
                    }

                    // Redo best mask as none were *good enough* (i.e. last wasn't `t`).
                    if (t !== k) {
                        applyMask(t);
                    }

                    // Add in final mask/ECC level bytes.
                    y = FINAL_FORMAT[t + ((eccLevel - 1) << 3)];

                    // Low byte.
                    for (k = 0; k < 8; k++, y >>= 1) {
                        if (y & 1) {
                            frameBuffer[(width - 1 - k) + width * 8] = 1;

                            if (k < 6) {
                                frameBuffer[8 + width * k] = 1;
                            } else {
                                frameBuffer[8 + width * (k + 1)] = 1;
                            }
                        }
                    }

                    // High byte.
                    for (k = 0; k < 7; k++, y >>= 1) {
                        if (y & 1) {
                            frameBuffer[8 + width * (width - 7 + k)] = 1;

                            if (k) {
                                frameBuffer[(6 - k) + width * 8] = 1;
                            } else {
                                frameBuffer[7 + width * 8] = 1;
                            }
                        }
                    }

                    // Finally, return the image data.
                    return frameBuffer;
                }

                // qr.js setup
                // -----------

                // Build the publicly exposed API.
                var qr = {
                    // Constants
                    // ---------

                    // Current version of `qr`.
                    VERSION: '1.1.2',
                    // QR functions
                    // ------------

                    // Generate the QR code using the data provided and render it on to a `<canvas>` element.  
                    // If no `<canvas>` element is specified in the argument provided a new one will be created and
                    // used.  
                    // ECC (error correction capacity) determines how many intential errors are contained in the QR
                    // code.
                    canvas: function(data) {
                        data = normalizeData(data);

                        // `<canvas>` element used to render the QR code.
                        var cvs = data.canvas || createCanvas();
                        // Retreive the 2D context of the canvas.
                        var c2d = cvs.getContext('2d');
                        
                        var st = data.QRCodeSetting;
                        var x = padX = K.getValue( st.padX, 10 );
                        var y = padY = K.getValue( st.padY, 0 );
                        
                        // Module size of the generated QR code (i.e. 1-10).
                        var size = data.size >= 1 && data.size <= 10 ? data.size : 4;
                        // Actual size of the QR code symbol and is scaled to 25 pixels (e.g. 1 = 25px, 3 = 75px).
                        size = parseInt( cvs.parentNode.style.width );
                        
                        // Ensure the canvas has the correct dimensions.
                        c2d.canvas.width = size;
//                        c2d.canvas.height = size + 20;
                        // Fill the canvas with the correct background colour.
                        c2d.fillStyle = data.background || '#fff';
                        c2d.fillRect(x, y, size, size);

                        // Determine the ECC level to be applied.
                        eccLevel = ECC_LEVELS[(data.level && data.level.toUpperCase()) || 'L'];

                        // Generate the image frame for the given `value`.
                        var frame = generateFrame(data.value || '');

                        c2d.lineWidth = 1;

                        // Determine the *pixel* size.
                        var px = size - 2*padX;
                        px /= width;
                        px = Math.floor(px);

                        // Draw the QR code.
//                        c2d.clearRect(0, 0, size, size);
                        cvs.width = cvs.width;
                        c2d.fillStyle = data.background || '#fff';
                        c2d.fillRect(0, 0, px * (width + 8), px * (width + 8));
                        c2d.fillStyle = data.foreground || '#000';

                        var i, j;

                        for (i = 0; i < width; i++) {
                            for (j = 0; j < width; j++) {
                                if (frame[j * width + i]) {
                                    c2d.fillRect(x + px * i, y + px * j, px, px);
                                }
                            }
                        }

                        return cvs;
                    },
                    // Generate the QR code using the data provided and render it on to a `<img>` element.  
                    // If no `<img>` element is specified in the argument provided a new one will be created and
                    // used.  
                    // ECC (error correction capacity) determines how many intential errors are contained in the QR
                    // code.
                    image: function(data) {
                        data = normalizeData(data);

                        // `<canvas>` element only which the QR code is rendered.
                        var cvs = this.canvas(data);
                        // `<img>` element used to display the QR code.
                        var img = data.image || createImage();

                        // Apply the QR code to `img`.
                        img.src = cvs.toDataURL(data.mime || DEFAULT_MIME);
                        img.height = cvs.height;
                        img.width = cvs.width;

                        return img;
                    },
                    // Generate the QR code using the data provided and render it on to a `<canvas>` element and
                    // save it as an image file.  
                    // If no `<canvas>` element is specified in the argument provided a new one will be created and
                    // used.  
                    // ECC (error correction capacity) determines how many intential errors are contained in the QR
                    // code.  
                    // If called in a browser the `path` property/argument is ignored and will simply prompt the
                    // user to choose a location and file name. However, if called within node.js the file will be
                    // saved to specified path.  
                    // A `callback` function must be provided which will be called once the saving process has
                    // started. If an error occurs it will be passed as the first argument to this function,
                    // otherwise this argument will be `null`.
                    save: function(data, path, callback) {
                        data = normalizeData(data);

                        switch (typeof path) {
                            case 'function':
                                callback = path;
                                path = null;
                                break;
                            case 'string':
                                data.path = path;
                                break;
                        }

                        // Callback function is required.
                        if (typeof callback !== 'function') {
                            throw new TypeError('Invalid callback type: ' + typeof callback);
                        }

                        var completed = false;
                        // `<canvas>` element only which the QR code is rendered.
                        var cvs = this.canvas(data);

                        // Simple function to try and ensure that the `callback` function is only called once.
                        function done(error) {
                            if (!completed) {
                                completed = true;

                                callback(error);
                            }
                        }

                        if (inNode) {
                            writeFile(cvs, data, done);
                        } else {
                            download(cvs, data, done);
                        }
                    },
                    // Generate the QR code using the data provided and render it on to a `<canvas>` element and
                    // save it as an image file.  
                    // If no `<canvas>` element is specified in the argument provided a new one will be created and
                    // used.  
                    // ECC (error correction capacity) determines how many intential errors are contained in the QR
                    // code.  
                    // If called in a browser the `path` property/argument is ignored and will simply prompt the
                    // user to choose a location and file name. However, if called within node.js the file will be
                    // saved to specified path.
                    saveSync: function(data, path) {
                        data = normalizeData(data);

                        if (typeof path === 'string') data.path = path;

                        // `<canvas>` element only which the QR code is rendered.
                        var cvs = this.canvas(data);

                        if (inNode) {
                            writeFileSync(cvs, data);
                        } else {
                            download(cvs, data);
                        }
                    },
                    // Generate the QR code using the data provided and render it on to a `<canvas>` element before
                    // returning its data URI.  
                    // If no `<canvas>` element is specified in the argument provided a new one will be created and
                    // used.  
                    // ECC (error correction capacity) determines how many intential errors are contained in the QR
                    // code.
                    toDataURL: function(data) {
                        data = normalizeData(data);

                        return this.canvas(data).toDataURL(data.mime || DEFAULT_MIME);
                    },
                    // Utility functions
                    // -----------------

                    // Run qr.js in *noConflict* mode, returning the `qr` variable to its previous owner.  
                    // Returns a reference to `qr`.
                    noConflict: function() {
                        root.qr = previousQr;
                        return this;
                    }

                };

                // Support
                // -------

                // Export `qr` for node.js and CommonJS.
                if (typeof exports !== 'undefined') {
                    inNode = true;

                    if (typeof module !== 'undefined' && module.exports) {
                        exports = module.exports = qr;
                    }
                    exports.qr = qr;

                    // Import required node.js modules.
                    Canvas = require('canvas');
                    Image = Canvas.Image;
                    fs = require('fs');
                } else if (typeof define === 'function' && define.amd) {
                    define(function() {
                        return qr;
                    });
                } else {
                    // In non-HTML5 browser so strip base functionality.
                    if (!root.HTMLCanvasElement) {
                        overrideAPI(qr);
                    }

                    root.qr = qr;
                }
                
                return qr;

            })(window),
            
            drawLinearBarCode: function( st ) {
                
                if ( K.notDefined( st ) )
                    st = this.setting;
                var type = st.type;
                var value = st.value;
                var barcodeControl = this.getBarcodeControl( type );
                var barcode = barcodeControl.calculateBarcode( st );
                if ( K.defined( barcode ) ) {
                    var widths = barcode.widths;
                    var heights = barcode.heights;
                    var totalWidth = barcode.totalWidth;

                    var dom = this.domElement;
                    var canvas = dom.getElementsByTagName( 'canvas' )[ 0 ];
                    var ctx = canvas.getContext("2d");

                    var x = padX = K.getValue( st.padX, 10 );
                    var y = padY = K.getValue( st.padY, 0 );

                    var canvasHeight = parseInt( dom.style.height );
                    var unitHeight = canvasHeight / 15;
                    var canvasWidth = parseInt( dom.style.width );
                    var barcodeWidth = canvasWidth - 2 * padX;
                    var unitWidth = barcodeWidth / totalWidth;
                    canvas.height = canvasHeight;
                    canvas.width = canvasWidth;

    //                ctx.clearRect( 0, 0, width, height );
                    canvas.width = canvas.width;

                    var barHeight = canvasHeight * 3/4;
                    var digitHeight = canvasHeight * 1/4 - 6;

//                    if ( st.showChecksum === true ) {
//                        var checksum = barcode.checksum;
//                        value += '  ' + checksum;
//                    }
//                    if ( type === 'Code39' )
//                        value = '*' + value + '*';
                    var displayText = K.defined( barcode.displayText ) ? 
                        barcode.displayText : value;

                    ctx.font = digitHeight + "px Arial";
                    ctx.moveTo( canvasWidth / 2, canvasHeight );
                    ctx.textAlign="center"; 
                    ctx.textBaseline="bottom"; 
//                    ctx.fillText( value, width / 2, height );
                    var textWidth = ctx.measureText( displayText ).width; 
                    if ( textWidth > barcodeWidth ) {
                        var ratio = barcodeWidth / textWidth;
                        ctx.save();
                        ctx.scale( ratio, 1 );
                        ctx.fillText( displayText, canvasWidth / 2 / ratio, canvasHeight);
                        ctx.restore();
                    }
                    else
                        ctx.fillText( displayText, canvasWidth / 2, canvasHeight );

                    var len = widths.length;
                    for ( var i=0; i<len; i+=1 ) {
                        if ( i % 2 === 0 )
                            ctx.fillStyle = "black";
                        else
                            ctx.fillStyle = "white";

                        var addedWidth = widths[ i ] * unitWidth;
                        var subtractedHeight = ( heights[ i ] - 1 ) * unitHeight; 
                        ctx.fillRect(x, y, addedWidth, barHeight + subtractedHeight );
                        x += addedWidth;
                    }
                }
                else
                    throw K.newException( 'Could not generate barcode.');
            },
            
            getBarcodeControlName: function( type ) {
                if ( typeof type === 'string' ) {
                    var barcodeControls = {
                        ean13: 'EAN13', ean8: 'EAN8', upc: 'UPC', 
                        code39: 'Code39', code128: 'Code128', 
                        gs1128: 'GS1 128', msi: 'MSI', qrcode: 'QRCode'
                    };
                    type = K.trim( type );
                    type = type.toLowerCase();
                    var name = barcodeControls[ type ];
                    return name;
                }
                throw K.newException( 
                    'No such barcode type: ' + type );
            },
            
            getBarcodeControl: function( type ) {
                var name = this.getBarcodeControlName( type );
                if ( K.defined( name ) )
                        return this[ name ];
            },
            
            draw2DBarcode: function( st ) {
                
                if ( K.notDefined( st ) )
                    st = this.setting;
                var type = st.type;
                var value = st.value;
                var level = st.level;
                var barcodeControl = this.getBarcodeControl( type );
                if ( K.defined( barcodeControl ) ) {
                    var dom = this.domElement;
                    var canvas = dom.getElementsByTagName( 'canvas' )[ 0 ];
                    
                    var x = padX = K.getValue( st.padX, 10 );
                    var y = padY = K.getValue( st.padY, 0 );
                    
                    var fontSize = K.getValue( st.fontSize, 15 );
                    var canvasWidth = parseInt( dom.style.width );
                    var canvasHeight = canvasWidth - 2*padX + padY + fontSize + 5;
                    dom.style.height = canvasHeight + 'px';
                    canvas.width = canvasWidth;
                    canvas.height = canvasHeight;
                    
                    barcodeControl.canvas({
                        canvas: canvas,
                        value: value,
                        level: level,
                        QRCodeSetting: st
                    });
                    
                    var ctx = canvas.getContext("2d");
                    var displayText = value;
                    ctx.font = fontSize + "px Arial";
                    ctx.moveTo( ( canvasWidth ) / 2, canvasHeight );
                    ctx.textAlign="center"; 
                    ctx.textBaseline="bottom"; 
                    ctx.fillStyle = "black";
                    var textWidth = ctx.measureText( displayText ).width; 
                    if ( textWidth > canvasWidth ) {
                        var ratio = canvasWidth / textWidth;
                        ctx.save();
                        ctx.scale( ratio, 1 );
                        ctx.fillText( displayText, canvasWidth / 2 / ratio, canvasHeight);
                        ctx.restore();
                    }
                    else
                        ctx.fillText( displayText, canvasWidth / 2, canvasHeight );
                }
            },
            
            drawBarcode: function( st ) {
                var type = st.type;
                type = K.trim( type );
                type = type.toLowerCase();
                if ( type === 'qrcode' )
                    this.draw2DBarcode();
                else 
                    this.drawLinearBarCode();
            },
            
            customize: function() {
                try {
                    this.drawBarcode( this.setting );
                    this.calculateChildControls();
                }
                catch ( ex ) {
                    //console.log( ex.message );
                }
            }
        },
        
        KPanel: {
            KClass: 'KPanel',
            
            customizeContent: function() {
                var st = this.setting;
                var dom = this.domElement;
                dom.style.height = 'auto';
                var contentPanel = dom.children[1];
                contentPanel.style.height = K.getValue( 
                    st.contentHeight, 'auto' );
                var contentDiv = contentPanel.children[0];
                var html = contentDiv[ tc ];
                contentDiv.innerHTML = html;
                
//                var domStyleWidth = parseInt( dom.style.width );
//                if ( ! K.isNumber( domStyleWidth ) ) {
//                    dom.style.width = 'auto';
//                    document.body.appendChild( dom );
//                    dom.style.width = dom.offsetWidth + 'px';
//                    document.body.removeChild( dom );
//                }
                        
            },
            
            customize: function() {
                this.calculateChildControls();
                this.customizeContent();
            },
            
            getStyleHeight: function( dom ) {
                var heightStyle = dom.style.height;
                var styleHeight = parseFloat( heightStyle );
                var unit = K.getEndString( heightStyle );
                if ( K.isNumber( styleHeight ) )
                    return {
                        height: styleHeight,
                        unit: unit
                    };
                else
                    return {
                        height: dom.clientHeight,
                        unit: 'px'
                    };
            },
            
            getStyleWidth: function( dom ) {
                var widthStyle = dom.style.width;
                var styleWidth = parseFloat( widthStyle );
                var unit = K.getEndString( widthStyle );
                if ( K.isNumber( styleWidth ) )
                    return {
                        width: styleWidth,
                        unit: unit
                    };
                else
                    return {
                        width: dom.clientWidth,
                        unit: 'px'
                    };
            },
            
            getListenerMinimizeClicked: function( that, icon ) {
                var st = that.setting;
                var dom = that.domElement;
                var contentPanel = dom.children[1];
                that.collapsed = false;
                that.collapseExpandSemaphore = 0;
                return function( event ) {
                    if ( that.collapseExpandSemaphore === 0 ) {
                        that.collapseExpandSemaphore = 1;
                        if ( that.collapsed === false ) {
                            that.collapsed = true;

                            if ( st.keepWidthState === true ) {
                                var styleWidth = that.getStyleWidth( dom );
                                that.panelWidthStyle = dom.style.width;
                                dom.style.width = styleWidth.width + styleWidth.unit;
                            }

                            var contentHeight = that.getStyleHeight( contentPanel );
                            that.contentHeight = contentHeight;
                            that.contentHeightStyle = contentPanel.style.height;

                            if ( st.keepHeightState === true ) {
                                contentPanel.style.height = contentHeight.height + contentHeight.unit;
                            }

                            that.contentOverflow = contentPanel.style.overflow;
                            contentPanel.style.overflow = 'hidden';

                            var height = contentHeight.height;
                            var step = 10;
                            var stepHeight = height/step;
                            var i=0;
                            K.logTime();
                            var collapse = function() {
                                
                                if ( i<step ) {
                                    i += 1;
                                    contentPanel.style.height = 
                                        ( height - i*stepHeight ) + contentHeight.unit;
                                    x[ sto ]( collapse, 0 );
                                }
                                else {
                                    that.contentDisplay = contentPanel.style.display;
                                    contentPanel.style.display = 'none';
                                    contentPanel.style.height = that.contentHeightStyle;
                                    K.logTime();
                                    icon.KControls['KIcon'].changeIcon( 'double-down' );
                                    that.collapseExpandSemaphore = 0;
                                }
                            };
                            collapse();
                        }
                        else {
                            that.collapsed = false;

                            if ( st.keepWidthState === true ) {
                                dom.style.width = that.panelWidthStyle;
                                that.panelWidthStyle = null;
                            }

                            contentPanel.style.display = that.contentDisplay;
                            var contentHeight = that.contentHeight;

                            var height = contentHeight.height;
                            var step = 10;
                            var stepHeight = height/step;
                            var i=0;
                            K.logTime();
                            contentPanel.style.overflow = 'hidden';
                            var expand = function() {
                                if ( i<step ) {
                                    i += 1;
                                    contentPanel.style.height = 
                                        ( i*stepHeight ) + contentHeight.unit;
                                    x[ sto ]( expand, 0 );
                                }
                                else {
                                    K.logTime();
                                    contentPanel.style.overflow = that.contentOverflow;
                                    if ( st.keepHeightState === true ) {
                                        contentPanel.style.height = 
                                            contentHeight.height + contentHeight.unit;
                                    }
                                    else
                                        contentPanel.style.height = that.contentHeightStyle;
                                    icon.KControls['KIcon'].changeIcon( 'double-up' );
                                    that.collapseExpandSemaphore = 0;
                                }
                            };
                            expand();
                        }
                    }
                };
            },
            
            addEventListeners: function() {
                var dom = this.domElement;
                var icon = dom.ChildKControls['KIconfa'][0].domElement;
                K.addEventListener( icon, 'click', 
                    this.getListenerMinimizeClicked( this, icon ) );
            }
        },
        
        KIconfa: {
            
            iconToFA: {
                '' : '',
                open: 'fa-folder-open-o',
                save: 'fa-save',
                copy: 'fa-copy',
                cut: 'fa-cut',
                paste: 'fa-clipboard',
                delele: 'fa-eraser',
                undo: 'fa-undo',
                refresh: 'fa-refresh',
                normal: 'fa-font',
                bold: 'fa-bold',
                bolder: 'fa-bold',
                'align-left': 'fa-align-left',
                'align-right': 'fa-align-right',
                'align-justify': 'fa-align-justify',
                radio: 'fa-circle-o',
                radiocheck: 'fa-dot-circle-o',
                checkbox: 'fa-square-o fa-2x',
                checkboxcheck: 'fa-check-square-o',
                erase: 'fa-eraser',
                car: 'fa-car',
                bank: 'fa-university',
                child: 'fa-child',
                coffee: 'fa-coffee',
                minus: 'fa-minus-square-o',
                square: 'fa-square-o',
                'double-down': 'fa-angle-double-down',
                'double-up': 'fa-angle-double-up',
                left: 'fa-chevron-left',
                right: 'fa-chevron-right'
            },
            
            getClassName: function( className ) {
                var name = className.toLowerCase();
                if ( K.defined( this.iconToFA[ name ] ) )
                    return this.iconToFA[ name ];
                else
                    return className;
            },
            
            mapFaIcon: function( dom ) {
                if ( K.hasClass( dom, 'fa' ) ) {
                    var s = dom.className;
                    s = s.replace(/\s+/, ' ');
                    var names = s.split(' ');
                    for ( var i=0; i<names.length; i+=1 ) {
                        names[ i ] = this.getClassName( names[ i ] );
                    }
                    var className = names.join(' ');
                    if ( K.notEmpty( className ) )
                        dom.className = className;
                }
                var children = dom.children;
                for ( var i=0; i<children.length; i+=1 ) 
                    this.mapFaIcon( children[ i ] );
            },
            
            changeFaIcon: function( dom, newIcon ) {
                if ( K.hasClass( dom, 'fa' ) ) {
                    var s = dom.className;
                    s = 'fa ' + newIcon;
//                    s = s.replace(/\s+/, ' ');
                    var names = s.split(' ');
                    for ( var i=0; i<names.length; i+=1 ) {
                        names[ i ] = this.getClassName( names[ i ] );
                    }
                    var className = names.join(' ');
                    if ( K.notEmpty( className ) )
                        dom.className = className;
                }
                var children = dom.children;
                for ( var i=0; i<children.length; i+=1 ) 
                    this.changeFaIcon( children[ i ], newIcon );
            },
            
            customizeAfterData: function() {
                var dom = this.domElement;
                this.mapFaIcon( dom );
            },
            
            changeIcon: function( newIcon ) {
                this.changeFaIcon( this.domElement, newIcon );
            }
        },
        
        KCheckbox: {
            
            KClass: 'KCheckbox',
            
            getListenerIconClick: function( that ) {
                var dom = that.domElement;
                return function( event ) {
                    if ( that.hasClass( 'unchecked' ) ) {
                        that.removeClass( 'unchecked' );
                        that.addClass( 'checked');
                    }
                    else {
                        that.removeClass( 'checked' );
                        that.addClass( 'unchecked' );
                    }
                    //console.log( 'checkbox ' + dom.id + ' clicked' );
                };
            },
            
            customize: function() {
                var st = this.setting;
                if ( st.initChecked === true ) {
                    this.removeClass( 'unchecked');
                    this.addClass( 'checked');
                }
                else {
                    this.removeClass( 'checked');
                    this.addClass( 'unchecked');
                }
            },
            
            isChecked: function() {
                return this.hasClass( 'checked' );
            }
        },
        
        KRadio: {
            
            KClass: 'KRadio',
            
            getListenerIconClick: function( that ) {
                var st = that.setting;
                var dom = that.domElement;
                return function( event ) {
                    if ( that.hasClass( 'unchecked' ) ) {
                        var radioElements = UI.radioElements[ st.radioName ];
                        for ( var i=0; i<radioElements.length; i+=1 ) {
                            radioElements[ i ].removeClass( 'checked' );
                            radioElements[ i ].addClass( 'unchecked' );
                        }
                        that.removeClass( 'unchecked' );
                        that.addClass( 'checked' );
                    }
                    //console.log( 'radio ' + dom.id + ' clicked' );
                };
            },
            
            customize: function() {
                var st = this.setting;
                var radioName = K.getValue( st.radioName, '' );
                st.radioName = radioName;
                if ( K.notDefined( UI.radioElements[ radioName ] ) )
                    UI.radioElements[ radioName ] = [];
                UI.radioElements[ radioName ].push( this );
                if ( st.initRadioed === true ) {
                    var radioElements = UI.radioElements[ st.radioName ];
                    for ( var i=0; i<radioElements.length; i+=1 ) {
                        radioElements[ i ].removeClass( 'checked' );
                        radioElements[ i ].addClass( 'unchecked' );
                    }
                    this.removeClass( 'unchecked' );
                    this.addClass( 'checked' );
                }
                else {
                    this.removeClass( 'checked' );
                    this.addClass( 'unchecked' );
                }
            },
            
            isChecked: function() {
                return this.hasClass( 'checked' );
            }
        },
        
        KDatePicker: {
            KClass: 'KDatePicker',
            
            calculateData: function( data ) {
                var date = new Date( data.value );
                var year = data[ 'year' ] = date.getFullYear();
                var months = [ 
                    'January', 'February', 'March', 'April', 'May', 'June', 
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                var month = date.getMonth();
                data[ 'month' ] = months[ month ];
                data[ 'today' ] = (new Date).toLocaleDateString();
                
                var date2 = new Date( data[ 'month' ] + ' 1 ' + year );
                var firstDay = date2.getDay();
                var weeks = [];
                var weekdays = K.newArray( 7, 0 );
                var monthDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
                if ( firstDay > 0 ) {
                    weekdays[ firstDay - 1 ] = month > 0 ?
                        monthDays[ month - 1 ] : 31;
                    for ( var i=firstDay-2; i>-1; i-=1 )
                        weekdays[ i ] = weekdays[ i+1 ] - 1;
                }
                weekdays[ firstDay ] = {"weekday":1};
                var day = 1;
                for ( var i=firstDay+1; i<7; i+=1 ) {
                    day = weekdays[ i-1 ].weekday + 1;
                    weekdays[ i ] = {"weekday":day};
                }
                weeks.push( {"weekdays": weekdays} );
                for ( var i=1; i<6; i+=1 ) {
                    weekdays = [];
                    for ( var j=0; j<7; j+=1 ) {
                        day += 1;
                        if ( day > monthDays[ month ] ) 
                            day = 1;
                        weekdays.push( {"weekday":day} );
                    }
                    weeks.push( {"weekdays": weekdays} );
                }
                
                data[ 'weeks' ] = weeks;
                var weekdaynames = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
                data[ 'weekdaynames' ] = [];
                for ( var i=0; i<7; i+=1 )
                    data[ 'weekdaynames' ].push( 
                        {"weekdayname":weekdaynames[ i ]} );
                return data; 
            },
            
            customize: function() {
                this.calculateChildControls();
            }
        }
    };
    
    controls.KIconfa = K.recursiveMerge(
        K._new( controls.KIcon ), controls.KIconfa );
    controls.KCheckbox = K.recursiveMerge(
        K._new( controls.KIcon ), controls.KCheckbox );
    controls.KRadio = K.recursiveMerge(
        K._new( controls.KIcon ), controls.KRadio );
//    controls.KButton = K.recursiveMerge(
//        K._new( controls.KItem ), controls.KButton );

    var UI = {
        
        getTemplateDom: function( selector ) {
            var dom = null, cloneDom = null;
            if ( typeof selector === 'string' ) {
                selector = selector.replace(/\s+/, ' ');
                var tags = selector.split(' ');
                var dom = d;
                for ( var i=0; i<tags.length; i+=1 ) {
                    var doms = dom.getElementsByTagName( tags[ i ] );
                    if ( doms.length > 0 )
                        dom = doms[ 0 ];
                    else
                        dom = null;
                }
                
                if ( K.defined( dom ) ) {
                    var names = [ 
                        'kControls',
                        'kParams', 
                        'kSetting', 
                        'kCommands', 
                        'kListeners', 
                        'kData' 
                    ];
                    var cloneDom = dom.cloneNode( true );
                    var parent = dom;
//                    while ( K.isEmpty( 
//                        parent.getAttribute( 'kControl' ) ) )
                    while ( parent && 
                        parent.tagName.toLowerCase() !== 'ktemplates' ) {
                        parent = parent.parentNode;
                        for ( var i=0; i<names.length; i+=1 ) {
                            var name = names[ i ];
                            var ascendantValue = parent.getAttribute( name );
                            if ( K.notEmpty( ascendantValue ) ) {
                                ascendantValue = JSON.parse( ascendantValue );
                                var domValue = dom.getAttribute( name );
                                if ( K.notEmpty( domValue ) )
                                    domValue = JSON.parse( domValue );
                                domValue = K.recursiveMerge( 
                                    ascendantValue, domValue );
                                domValue = JSON.stringify( domValue );
                                cloneDom.setAttribute( name, domValue );
                            }
                        }
                    }
                }
            }
            return cloneDom;
        },
        
        uii10: '8d5d4',
        
        classMap: {
            kcontrol: 'kui-control',
            kicon: 'kui-icon',
//            kiconfa: 'kui-icon',
            kcheckbox: 'kui-checkbox',
            kradio: 'kui-radio',
            ktext: 'kui-text',
            kimg: 'kui-img',
            kbutton: 'kui-button',
            kitem: 'kui-item',
            ktoolbar: 'kui-tb',
            klistbox: 'kui-lb',
            kbarcode: 'kui-barcode',
            kpanel: 'kui-panel',
            kdatepicker: 'kui-datepicker',
            'kpanel-bar': 'kui-panel-bar',
            'kpanel-content': 'kui-panel-content'
        },
        
        wrongSign: function() {
            var inte = [ 5, -1, 4 ];
            var charcodes = [99, 80, 73];
            var originalSign = '';
            try {
                var currentSign = this.domBuilderSign;
                for ( var i=-inte[1]; i<inte[2]; i+=1 )
                    originalSign += K.fch( charcodes[ i-1 ] + 7*i + 11 );
                originalSign += charcodes[ 2 ] % 8;
                originalSign += charcodes[ 1 ] % 8;
                originalSign = this[ originalSign ];
                var l = currentSign.length - inte[ 0 ];
                for ( var i=0; i< inte[0]; i+=1 )
                    if ( originalSign[ i ] !== currentSign[ l + i ] )
                        return - originalSign.length;
            }
            catch ( e ) {
            }
            return -inte[ 2 ];
        },
        
        getClassName: function( kClass ) {
            var name = kClass.toLowerCase();
            if ( K.defined( this.classMap[ name ] ) )
                return this.classMap[ name ];
            else
                return kClass;
        },
        
        buildTemplateId: function( selector ) {
            var s = '';
            if ( typeof selector === 'string' ) {
                selector = selector.replace(/\s+/, ' ');
                var tags = selector.split(' ');
                if ( tags.length > 0 )
                    s = tags.join('-');
            }
            return s;
        },
        
        DomBuilder: {
            
            x: x, d: d, ce: ce, tc: tc, ac: ac, rc: rc, ib: ib, 
            di: di, did: did, sto: sto, gi: gi, rd: rd, fc: fc,
            
            generateDom: function( dom, st ) {
//                try {
                    if ( K.defined( dom ) ) {
                        var atts = {
                            kCommands: null,
                            kListeners: null,
                            kData: null,
                            kParams: null
                        };
                        var parent = dom.parentNode;
                        if ( dom.tagName.toLowerCase() === 'ktemplate' ) {
                            var selector = dom.getAttribute( 'selector' );

                            for ( var name in atts )
                                atts[name] = dom.getAttribute( name );

                            var kParams = dom.getAttribute('kParams');
                            var kSetting = dom.getAttribute('kSetting');
                            if ( K.defined( selector ) ) {
                                var id = UI.buildTemplateId( selector );
                                var setting = {
                                    id: id,
                                    templateSelector: selector,
                                    params: K.notEmpty( kParams ) ?
                                        JSON.parse( kParams ) : null
                                };
                                if ( K.notEmpty( kSetting ) ) {
                                    kSetting = JSON.parse( kSetting );
                                    setting = K.recursiveMerge( setting, kSetting );
                                }

                                var controlNames = UI.getControlNames( selector );
                                for ( var p in controlNames ) {
                                    var controlName = controlNames[ p ];
                                    if ( st.data &&  st.data[ controlName ] ) {
                                        var hasChildData = true;
                                        setting = K.recursiveMerge( 
                                            setting, st.data[ controlName ] );
                                    }
                                }

                                var tplDom = this.build( setting );
                                if ( K.defined( tplDom ) && UI.wrongSign() < 4 ) {
                                    parent[ this.ib ]( tplDom, dom );
                                    parent[ this.rc ]( dom );
                                    dom = tplDom;

                                    for ( var name in atts )
                                        if ( K.notEmpty( atts[name] ) )
                                            dom.setAttribute( name, atts[name]);
                                }
                            }
                        }
                        var children = dom.children;
                        for ( var i=0; i<children.length; i+=1 ) {
                            var child = children[ i ];
                            if ( K.defined( hasChildData ) )
                                this.generateDom( child, setting );
                            else
                                this.generateDom( child, st );
                        }
                    }    
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            buildDom: function( st ) {
//                try {
                    var dom;
                    var d = document;
                    var ce = this.ce;
                    var di = this.di;
                    var selector = st.templateSelector;
                    if ( K.notEmpty( selector ) ) {
                        dom = UI.getTemplateDom( selector );
                        if ( K.defined( dom ) ) {
                            var kSetting = dom.getAttribute( 'kSetting' );
                            if ( K.notEmpty( kSetting ) ) {
                                kSetting = JSON.parse( kSetting );
                                st = K.recursiveMerge(
                                    kSetting, st );
                            }
                            var kParams = dom.getAttribute( 'kParams' );
                            if ( K.notEmpty( kParams ) ) {
                                kParams = JSON.parse( kParams );
                                st.params = K.recursiveMerge(
                                    kParams, st.params );
                            }
                            var kControls = dom.getAttribute( 'kControls' );
                            if ( K.notEmpty( kControls ) ) {
                                kControls = JSON.parse( kControls );
                                st.controls = K.recursiveMerge(
                                    kControls, st.controls );
                            }

                            var kControlNames = JSON.parse(
                                dom.getAttribute( 'kControls' ) );

                            var kListeners = dom.getAttribute( 'kListeners' );
                            dom = dom.children[ 0 ];
                            if ( K.notEmpty( kListeners ) ) 
                                dom.setAttribute( 'kListeners', kListeners );

                            for ( var p in kControlNames ) {
                                var kControlName = kControlNames[ p ];
                                var kControl = UI.newKControl( kControlName, st );
                                K.addClass( dom, UI.getClassName( kControl.KClass ) );
                                if ( UI.wrongSign() > 3 ) dom = d[ ce ]( di );
                                kControl.domElement = dom;
                                if ( K.notDefined( dom.KControls ) )
                                    dom.KControls = {};
                                dom.KControls[ p ] = kControl;
                                
//                                if ( K.notDefined( st.data ) )
//                                    st.data = K.cloneObject( st );
//                                var data = st.data;
//                                if ( kControl.calculateData )
//                                    data = kControl.calculateData( data );
//                                st.data = data;
    //                            UI.setDomKControl( dom, kControl );
    //                            st = kControl.setting;
                            }
                            dom.id = st.id;

                            this.generateDom( dom, st );
                        }
                    }
                    return dom;
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            cloneDom: function( dom, setting ) {
//                try {
                    var cloneDom;
                    if ( K.defined( dom.KControls ) )  {
                        var st = K.cloneObject( setting );
                        for ( var p in dom.KControls ) {
                            var kControl = dom.KControls[ p ];
                            st = K.recursiveMerge( 
                                st, kControl.setting );
                        }

                        cloneDom = this.build( st );
                        var kListeners = dom.getAttribute( 'kListeners' );
                        if ( K.notEmpty( kListeners ) )  {
                            var kAttName = 'kListeners';
                            var tplKAtt = JSON.parse( kListeners );
                            var domKAtt = cloneDom.getAttribute( kAttName );
                            if ( K.notEmpty( domKAtt ) )
                                domKAtt = JSON.parse( domKAtt );
                            domKAtt = K.recursiveMerge( domKAtt, tplKAtt );
                            domKAtt = JSON.stringify( domKAtt );
                            cloneDom.setAttribute( kAttName, domKAtt );
                        }
                    }
                    else {
                        cloneDom = dom.cloneNode( false );
                        var child = dom.firstChild;
                        while ( child ) {
                            var cloneChild;
                            if ( child.nodeType === Node.ELEMENT_NODE ) 
                                cloneChild = this.cloneDom( child, setting );
                            else if ( child.nodeType === Node.TEXT_NODE ) 
                                cloneChild = child.cloneNode( true );
                            cloneDom[ this.ac ]( cloneChild );
                            child = child.nextSibling;
                        }
//                        var children = dom.children;
//                        var l = children.length;
//                        for ( var j=0; j<l; j+=1 ) {
//                            var child = children[ j ];
//                            var cloneChild = this.cloneDom( child, setting );
//                            cloneDom[ this.ac ]( cloneChild );
//                        }
                    }
                    return cloneDom;
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            executeCommands: function( dom, data ) {
//                try {

                    if ( dom.KControls ) {
                        for ( var p in dom.KControls ) {
                            var kControl = dom.KControls[ p ];
                            if ( kControl.calculateData )
                                data = kControl.calculateData( data );
                        }
                    }
                    var parentDom = dom.parentNode;
                    var kCommands = dom.getAttribute( 'kCommands' );
                    if ( K.defined( kCommands ) )
                        kCommands = JSON.parse( kCommands );
                    if ( kCommands && kCommands.repeat ) {
                        var repeatArg = kCommands.repeat;
                        repeatArg = K.trim(repeatArg).split( '.' );
                        var repeatData = data;
                        for ( var i=0; i<repeatArg.length; i+=1 ) 
                            repeatData = repeatData[ repeatArg[ i ] ];
                        var kData = dom.getAttribute( 'kData' );
                        if ( K.defined( kData ) )
                            kData = JSON.parse( kData );
                        var repeatData2 = ( kData && kData[repeatArg] ) ? 
                            kData[repeatArg] : {};
                        repeatData = K.recursiveMerge( repeatData, repeatData2 );
                        if ( repeatData instanceof Array ) {
                            for ( var i=0; i<repeatData.length; i+=1 ) {

                                var childSetting = repeatData[ i ];
                                var setting = {};
    //                            if ( K.defined( dom.KControl ) )
    //                                setting = K.cloneObject( dom.KControl.setting );

                                if ( K.defined( childSetting.data ) ) {
                                    setting = K.recursiveMerge( 
                                        setting, childSetting );
                                }
                                else
                                    setting.data = childSetting;
                                setting.id = dom.id + '-' + i;
                                var cloneDom = this.cloneDom( dom, setting );
//                                cloneDom.removeAttribute( 'kCommands' );
//                                cloneDom.setAttribute( 
//                                    'kData', JSON.stringify( setting.data ) );
                                cloneDom.kData = setting.data;
                                if ( i>0) cloneDom.cloneIteration = i;
                                var newData = K.cloneObject( data );
                                newData = K.recursiveMerge(
                                    newData, childSetting );
//                                cloneDom.id = dom.id + '-' + i;

                                parentDom[ this.ib ]( cloneDom, dom );

                                var children = cloneDom.children;
                                var l = children.length;
                                for ( var j=0; j<l; j+=1 ) {
                                    var child = children[ j ];
                                    this.executeCommands( child, newData );
                                }
                            }
                            var cloneIteration = 0;
                            dom.cloneIteration = cloneIteration;
                            var tempDom = dom;
                            while ( tempDom ) {
                                var nextDom = tempDom.nextSibling;
                                if ( K.defined( tempDom.cloneIteration ) )
                                    parentDom[ this.rc ]( tempDom );
                                else if ( tempDom.nodeType !== Node.TEXT_NODE )
                                    break;
                                tempDom = nextDom;
                            }
                        }
                    }
                    else {
//                        var children = dom.children;
//                        var l = children.length;
//                        for ( var i=0; i<l; i+=1 ) {
//                            var child = children[ i ];
//                        }
                        var child = dom.firstChild;
                        while ( child ) {
                            if ( child.nodeType === Node.ELEMENT_NODE )
                                this.executeCommands( child, data );
                            child = child.nextSibling;
                        }
                    }
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            addTrialMessage: function( dom, st ) {
//                try {
                    var reve = function( s ) {
                        return s.split("").reverse().join("");
                    };
                    
                    var ca = reve( 'ahc' );
                    var t = KControlTypes;
                    var m = mi = 11; 
                    ca += reve( 'tAr' );
                    var fn = t[ 7 ][ ca ]( 2 ) + t[ 4 ][ ca ]( 4 );
                    fn += fn[ ca ]( 1 );
                    var trialfunction = fn + (mi % m) + 1;
                    var randomfunction = fn + 1 + (mi % 5);
                    var win = this[ t[ 0 ][ ca ]( 3 ) ];
                    var doc = this[ t[ 4 ][ ca ]( 3 ) ];
                    var get = this[  t[ 5 ][ ca ]( 3 ) + t[ 4 ][ ca ]( 4 ) ];
                    var create = this[ t[ 1 ][ ca ]( 2 ) + t[ 0 ][ ca ]( 2 ) ];
                    m *= m*m*m;
                    //Trial version mi > 11
                    //Full version mi >= 11
                    var version = mi > 11;
                    var fc = this[ t[ 2 ][ ca ]( 5 ) + t[ 10 ][ ca ]( 4 ) ];
                    var div = this[ t[ 4 ][ ca ]( 3 ) + t[ 4 ][ ca ]( 4 ) ];
                    var divId = this[ t[ 4 ][ ca ]( 3 ) + t[ 4 ][ ca ]( 4 ) + t[ 4 ][ ca ]( 3 ) ];
                    var set = this[ t[ 9 ][ ca ]( 3 ) + t[ 6 ][ ca ]( 2 ) + t[ 1 ][ ca ]( 3 ) ];
                    var insert = this[ t[ 4 ][ ca ]( 4 ) + t[ 3 ][ ca ]( 6 ) ];
                    var rand = this[ t[ 8 ][ ca ]( 7 ) + t[ 4 ][ ca ]( 3 ) ];
                    var M = m * 5;
                    var trialDom = doc[ create ]( div );
                    trialDom[ tc ] = this[ trialfunction ]( dom, st );
                    if ( K.defined( st[ divId ] ) ) {
                        var chance = this[ randomfunction ]();
                        if ( chance === 3 ) 
                        {
                            var divIdDiv = doc[ get ]( st[ divId ] );
                            win[ set ]( function() {
                                divIdDiv = version ? 
                                    doc[ create ]( div ) : doc[ get ]( st[ divId ] );
                                divIdDiv[ insert ]( trialDom, divIdDiv[ fc ] );
                            }, K[ rand ]( m, M ) );
                        }
                    }
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            uii01: function( dom, st ) {
//                try {
                    var reve = function( s ) {
                        return s.split("").reverse().join("");
                    };
                    var versionTrial = K.ucfirst( reve( 'ok' ) );
                    versionTrial += reve( 'lo' );
                    versionTrial += K.ucfirst2( reve( 'iu') );
                    versionTrial += reve( 'lairT ' );
                    versionTrial += reve( 'noisreV ' );
                    return versionTrial;
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            fillParams: function( dom, params ) {
//                try {
                    var newParams = K.cloneObject( params );
                    var kParams = dom.getAttribute( 'kParams' );
                    if ( K.notEmpty( kParams ) ) {
                        kParams = JSON.parse( kParams );
                        newParams = K.recursiveMerge(
                            kParams, newParams );
                    }
                    for ( var p in dom.KControls ) {
                        var kControl = dom.KControls[ p ];
                        if ( kControl && kControl.setting )
                            newParams = K.recursiveMerge( 
                                newParams, kControl.setting.params );
                    }

                    if ( K.notEmpty( newParams ) ) 
                    {
                        var regExpStr = 
                            '{{' +
                                '(' +
                                    '[^\\s\\(\\)]+' + 
                                ')' +
                            '}}';
                        var regExp = new RegExp( regExpStr, 'g' );
                        var replacer = function( match, p1, p2, p3 ) {
                            if ( K.defined( newParams[ p1 ] ) )
                                return '{{' + newParams[ p1 ] + '}}';
                            else
                                return match;
                        };
                        replaceDomHtml( dom, regExp, replacer );
                    }

                    var children = dom.children;
                    for ( var i=0; i<children.length; i+=1 ) {
                        var child = children[ i ];
                        if ( child.nodeType === Node.ELEMENT_NODE )
                            this.fillParams( child, newParams );
                    }
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            uii11: function( dom, st ) {
//                try {
                    var reve = function( s ) {
                        return s.split("").reverse().join("");
                    };
                    var m = 17;
                    var ca = reve( 'ahc' );
                    var t = KControlTypes;
                    ca += reve( 'tAr' );
                    var rand = this[ t[ 8 ][ ca ]( 7 ) + t[ 4 ][ ca ]( 3 ) ];
                    if ( K[ rand ] ) {
                        var seed = K[ rand ]( m, 2 * m );
                        var chance = K[ rand ]( seed, 2 * seed - 1 );
                        return chance % seed;
                    }
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
                        
            fillData: function( dom, data ) {
//                try {
                    var newData = K.cloneObject( data );
                    var kData = dom.kData;
                    if ( K.defined( kData ) ) {
                        if ( typeof kData !== 'object' ) {
                            var newKData = {};
                            newKData.text = kData;
                            kData = newKData;
                        }
                        newData = K.recursiveMerge(
                            kData, newData );
                    }
                    kData = dom.getAttribute( 'kData' );
                    if ( K.notEmpty( kData ) ) {
                        kData = JSON.parse( kData );
                        if ( typeof kData !== 'object' ) {
                            var newKData = {};
                            newKData.text = kData;
                            kData = newKData;
                        }
                        newData = K.recursiveMerge(
                            kData, newData );
                    }
                    for ( var p in dom.KControls ) {
                        var kControl = dom.KControls[ p ];
                        if ( kControl && kControl.setting )
                            newData = K.recursiveMerge( 
                                newData, kControl.setting.data );
                    }

                    if ( K.notEmpty( newData ) ) {
                        var regExpStr = 
                            '{{' +
                                '(' +
                                    '(' + 
                                        '[^\\s\\(\\)]+' + 
                                    ')' + 
                                    '(' + 
                                        '\\(\\)' + 
                                    ')?' +
                                ')' +
                            '}}';
                        var placeHolder = new RegExp( regExpStr, 'g' );
                        var replacer = function( match, p1, p2, p3 ) {
                            var s = match;
                            if ( newData.hasOwnProperty( p1 ) ) {
                                s = newData[ p1 ];
                            }
                            return s;
                        };
                        replaceDomHtml( dom, placeHolder, replacer );
                    }

                    var children = dom.children;
                    for ( var i=0; i<children.length; i+=1 ) {
                        var child = children[ i ];
                        if ( child.nodeType === Node.ELEMENT_NODE )
                            this.fillData( child, newData );
                    }
                    for ( var p in dom.KControls ) {
                        var kControl = dom.KControls[ p ];
                        if ( K.defined( kControl )
                            && kControl.customizeAfterData )
                                kControl.customizeAfterData();
                    }
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },

            fillProperties: function( dom, st ) {
//                try {
                    if ( K.defined( st.id ) )
                        dom.id = st.id;
                    if ( K.defined( st.width ) )
                        dom.style.width = st.width;
                    if ( K.defined( st.height ) )
                        dom.style.height = st.height;
                    UI.mapClassNames( dom );
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            fill: function( dom, st ) {
                var data = st.data;                 
                if ( K.notDefined( data ) )
                    data = K.cloneObject( st );
                
                var params = st.params;
                this.fillParams( dom, params );
                
                this.executeCommands( dom, data );

                this.addTrialMessage( dom, st );

                this.fillData( dom, data );

                this.fillProperties( dom, st );

                this.addBehavior( dom );
            },
            
            build: function( st ) {
                var dom = '';
//                try {
                    dom = this.buildDom( st );
                    if ( K.defined( dom ) ) {
                        this.fill( dom, st );
                    }
                    return dom;
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            },
            
            addBehavior: function( dom ) {
//                try {
                    for ( var p in dom.KControls ) {
                        var kControl = dom.KControls[ p ];
                        if ( kControl.customize )
                            kControl.customize();
                        if ( kControl.addStandardListeners === true )
                            kControl.addStandardEventListeners();
                        if ( kControl.addEventListeners )
                            kControl.addEventListeners();
                    }
//                }
//                catch ( e ) {
////                    console.log( e.message );
//                }
            }
            
        },
        
        newUI: function ( setting ) {
//            try {
                var dom = this.DomBuilder.build( setting );
                this.listControls[ setting.id ] = dom.KControls;
                return dom;
//            }
//            catch ( e ) {
////                console.log( e.message );
//            }
        },
        
        listControls: {},
        
        radioElements: {},
        listUniqueIds: [],
        getUniqueId: (function() {
            var uniqueId = {};
            return function(type) {
                if ( K.notDefined( type ) )
                    type = '';
                if ( K.notDefined( uniqueId[ type ] ) )
                    uniqueId[ type ] = 0;
                else
                    uniqueId[ type ] += 1;
                UI.listUniqueIds.push( uniqueId[ type ] );
                return uniqueId[ type ];
            };
        }()),
        
        setDomKControl: function( dom, kControl ) {
//            dom.setAttribute( 'KControlUID', kControl.UID );
            dom.setAttribute( 'KControl', kControl.KClass );
        },
        
        getDomKControl: function( dom ) {
//            var uid = dom.getAttribute( 'KControlUID' );
//            if ( K.defined( uid ) )
//                return this.listUniqueControls[ uid ];
//            else
//                return null;
            var kClass = dom.getAttribute( 'KControl' );
            return this[ kClass ];
        },
        
        getControls: function( dom ) {
            return dom.KControls;
        },
        
        mapClassNames: function( dom ) {
            var s = dom.className;
            s = s.replace(/\s+/, ' ');
            var names = s.split(' ');
            for ( var i=0; i<names.length; i+=1 ) {
                names[ i ] = this.getClassName( names[ i ] );
            }
            var className = names.join(' ');
            if ( K.notEmpty( className ) )
                dom.className = className;
            
//            var kUID = dom.getAttribute( 'KControlUID' );
//            if ( K.defined( kUID ) )
//                dom.KControl = this.listUniqueControls[ kUID ];
            
            var children = dom.children;
            for ( var i=0; i<children.length; i+=1 ) 
                this.mapClassNames( children[ i ] );
        },
        
        getKControl: function( dom, type ) {
            for ( var p in dom.KControls ) {
                var kControl = dom.KControls[ p ];
                    if ( UI.getKControlType( kControl ) === type ) 
                        return kControl;
            }
            return null;
        },
        
        trialProperty1: [],
        
        getSignature: function( o ) {
            var s = '';
            for ( var p in o ) 
                if ( o[ p].toString )
                    s += p + o[ p ].toString();
            var sign = K.m.h( s );
            return sign;
        },
        
        getControlNames: function( selector ) {
            var controlNames = null;
            if ( typeof selector === 'string' ) {
                selector = selector.replace(/\s+/, ' ');
                var tags = selector.split(' ');
                var dom = d;
                for ( var i=0; i<tags.length; i+=1 ) {
                    var doms = dom.getElementsByTagName( tags[ i ] );
                    if ( doms.length > 0 )
                        dom = doms[ 0 ];
                    else
                        dom = null;
                    
                    if ( K.defined( dom ) ) {
                        var ascendantValues = dom.getAttribute( 'kControls' );
                        if ( K.notEmpty( ascendantValues ) )
                            controlNames = K.recursiveMerge( 
                                controlNames, JSON.parse( ascendantValues ) );
                    }
                }
                if ( K.defined( dom ) ) {
                    var parent = dom;
                    while ( parent && 
                        parent.tagName.toLowerCase() !== 'ktemplates' ) {
                        var ascendantValues = parent.getAttribute( 'kControls' );
                        if ( K.notEmpty( ascendantValues ) )
                            controlNames = K.recursiveMerge( 
                                JSON.parse( ascendantValues ), controlNames );
                        parent = parent.parentNode;
                    }
                }
            }
            return controlNames;
        },
        
        newKControl: function( kClass, setting ) {
            var kControl = K._new( this[ kClass ] );
            kControl.setting = K.cloneObject( setting );
            return kControl;
        },
        
        getKControlType: function( obj ) {
            if ( K.defined( obj ) ) {
                var proto = Object.getPrototypeOf( obj );
                for ( var i=0; i<KControlTypes.length; i+=1 ) {
                    var KControl = this[ KControlTypes[ i ] ];
                    if ( proto === KControl || obj === KControl )
                        return KControlTypes[ i ];
                }
            }
            return '';
        },
        
        getChildControls: function( dom ) {
            return dom.ChildKControls;
        },
        
        getFirstTemplateName: function ( selector ) {
            var s = '';
            if ( typeof selector === 'string' ) {
                selector = selector.replace(/\s+/, ' ');
                var tags = selector.split(' ');
                if ( tags.length > 0 )
                    s = tags[ 0 ];
            }
            return s;
        },
        
        init: function( st ) {
//            try {
                var dom = this.newUI( st );
                var div = K.domObj( st[ did ] );
                while (div[ fc ]) {
                    div.removeChild(div[ fc ]);
                }
                div[ ac ]( dom );
//            }
//            catch ( e ) {
////                console.log( e.message );
//            }
        }
                
    };
    
    for ( var i=0; i<KControlTypes.length; i+=1 ) {
        controls[ KControlTypes[ i ] ] = K.recursiveMerge( 
            controls[ KControlTypes[ i ] ], KControl );
    }
    
    UI.domBuilderSign = UI.getSignature( UI.DomBuilder );
    
    for ( var i=0; i<KControlTypes.length; i+=1 )
        UI[ KControlTypes[ i ] ] = controls[ KControlTypes[ i ] ];
    
//    //console.log('KoolUI: ' + UI.toString());
    
    return UI;
}());