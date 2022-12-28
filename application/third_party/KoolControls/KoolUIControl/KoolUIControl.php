<?php

$_version = "1.0.0.0";

class KoolUI
{
    
    var $_version = "1.0.0.0";
    
    private $setting = array(
        'id' => 'KoolUIControl1',
        'style' => 'default'
    );
    
    private $htmlTemplate = '';
    
    public static function newUI($info)
    {
        $ui = new KoolUI();
        $ui->set($info);
        return $ui;
    }
    
    public function set($s)
    {
        $this->setting = $this->array_merge_replace($this->setting, $s);
    }
    
    public function getSetting()
    {
        return $this->setting;
    }
    
    public function array_merge_replace($_arr1, $_arr2)
    {
        foreach ($_arr2 as $_k => $_v)
            if (is_array($_v))
            {
                if (!isset($_arr1[$_k]))
                    $_arr1[$_k] = array();
                $_arr1[$_k] = $this->array_merge_replace($_arr1[$_k], $_v);
            }
            else
                $_arr1[$_k] = $_v;

        return $_arr1;
    }
    
    public function process()
    {
        $this->setting['divId'] = 'div-' . $this->setting['id'];
        $this->setting['currentPath'] = $this->getCurrentPath();
        $this->htmlTemplate = base64_encode( file_get_contents( 
            dirname(__FILE__) . '/KoolUITemplates.html') );
    }
    
    private function getRoot()
    {
        $_php_self = str_replace("\\", "/", strtolower($_SERVER["SCRIPT_NAME"])); 
        $_php_self = str_replace(strrchr($_php_self, "/"), "", $_php_self);
        $_realpath = str_replace("\\", "/", realpath(".")); 
        $_root = str_replace($_php_self, "", strtolower($_realpath));
        return $_root;
    }
    
    private function getComponentURI()
    {
        $_root = $this->getRoot();
        $_file = substr(str_replace("\\", "/", __FILE__), strlen($_root));
        return $_file;
    }
    
    function getCurrentPath()
    {
        $_com_uri = $this->getComponentURI();
        $_styles_folder = str_replace(strrchr($_com_uri, "/"), "", $_com_uri);
        return $_styles_folder;
    }
    
    function getStylePath()
    {
        $_com_uri = $this->getComponentURI();
        $_styles_folder = str_replace(strrchr($_com_uri, "/"), "", $_com_uri) . "/styles";
//        echo $_styles_folder."<br>";
        return $_styles_folder;
    }

    private function registerCSS()
    {
        $style = $this->setting['style'];
        $script = '<link rel="stylesheet" href="{src}">';
        $stylePath = $this->getStylePath() . '/' . $style . '/' . $style . '.css';
        $script = str_replace("{src}",  $stylePath, $script);
//        echo $stylePath;
        return $script;
    }
    
    private function registerJS()
    {
        $script =  '';
        $script .= " <script type='text/javascript'>
                if (window.KoolPHPScriptWritten === null ||
                    typeof window.KoolPHPScriptWritten === 'undefined')
                   KoolPHPScriptWritten = {};
                if (window.KoolPHPWriteScript === null ||
                    typeof window.KoolPHPWriteScript === 'undefined') {
                    var KoolPHPWriteScript = function( url, js ) {            
                        if ( ! KoolPHPScriptWritten[ js ] ) {

                            var s = \"%3Cscript type='text/javascript' src='\" + url + '/' + js + \"'></script%3E\";
                            document.write( decodeURIComponent( s ) );

                            KoolPHPScriptWritten[ js ] = true;
                        }
                    };        
                }
                KoolPHPWriteScript( '{KoolUILibrary}', 'KoolPHP.js' ); 
            </script>"
        ;
//        $script .= "<script type='text/javascript'> 
//                KoolPHP.writeScript( '{KoolUILibrary}', 'KoolUI.js' );
//                KoolPHP.writeCSS( '{KoolUILibrary}', 'KoolUI.css' );
//                KoolPHP.writeHtml( '{htmlTemplate}', 'KoolUITemplates.html' ); 
//            </script>"
//        ;
        $script .= "<script type='text/javascript'> 
                KoolPHP.writeScript( '{KoolUILibrary}', 'KoolUI.js' );
                KoolPHP.writeLESS( '{KoolUILibrary}', 'KoolUI.less' );
                KoolPHP.writeScript( '{KoolUILibrary}', 'less-1.7.0.min.js' );
                KoolPHP.writeHtml( '{htmlTemplate}', 'KoolUITemplates.html' ); 
            </script>"
        ;
        
        $script = str_replace("{KoolUILibrary}", $this->getCurrentPath(), $script);
        $script = str_replace("{htmlTemplate}", $this->htmlTemplate, $script);
        return $script;
    }
    
    private function getSettingJSON()
    {
        $variable = json_encode(
            $this->setting, JSON_HEX_TAG | JSON_HEX_APOS |
            JSON_HEX_QUOT | JSON_HEX_AMP |
            JSON_UNESCAPED_UNICODE);
        return $variable;
    }
    
    private function startupJS()
    {
        echo $this->registerJS();
        $startup = '';
        $startup .= "
            <script type='text/javascript'> 
                var startupJS_{id} = function() {
                    console.log( 'startupJS_{id} ran.' );
                    KoolUI.init( {setting} );
                };
                startupJS_{id}();
            </script>"
        ;
        $startup = str_replace('{id}', $this->setting['id'], $startup);
        $startup = str_replace('{setting}', $this->getSettingJSON(), $startup);
        return $startup;
    }
    
    public function render()
    {
        $s = '';
        $s .= $this->getBasedHtml();
        $s .= $this->startupJS();
        return $s;
    }
    
    public function getBasedHtml()
    {
        $s = '';
        $div = '<div id="{id}" class="kui-{class}"></div>';
        $div = str_replace("{id}", $this->setting['divId'], $div);
        $div = str_replace("{class}", $this->setting['style'], $div);
        $s .= $div;
        return $s;
    }
    
    public function getStartupJS() {
        echo $this->registerJS();
        $startup = '';
        $startup .= "
            <script type='text/javascript'> 
                var startupJS_{id} = function() {
                    console.log( 'startupJS_{id} ran.' );
                    KoolUI.init( {setting} );
                };
            </script>"
        ;
        $startup = str_replace('{id}', $this->setting['id'], $startup);
        $startup = str_replace('{setting}', $this->getSettingJSON(), $startup);
        echo $startup;
        return 'startupJS_' . $this->setting['id'];
    }
}

