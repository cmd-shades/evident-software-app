<?php

if (! defined('BASEPATH')) {
    // exit('No direct script access allowed');
}

function debug($todebug, $print_OR_dump = "print", $exit = true)
{
    if ($todebug) {
        echo "<pre>";

        if ($print_OR_dump == "print") {
            print_r($todebug);
        } elseif ($print_OR_dump == "dump") {
            var_dump($todebug);
        } else {
            exit("Incorrect type");
        }

        echo "</pre>";

        if ($exit) {
            exit;
        }
    } else {
        exit("Value is false");
    }
}

if (false === function_exists('dd')) {
    function dd(...$arg)
    {
        echo '<pre>';
        //var_dump($arg);
        print_r($arg);
        echo '</pre>';
        exit();
    }
}
/* End of file dump_helper.php */
