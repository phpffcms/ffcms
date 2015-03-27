<?php

// load ffcms-core
$loader = require root . '/vendor/autoload.php';
$loader->add('Ffcms\\', root . '/vendor/phpffcms/ffcms-core/src/');

require_once(root . "/Core/App.php");
/**
 * Class App
 * Simple street magic ;)
 */
class App extends \Core\App {}

class load {
    public static function _init() {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }
    protected static function autoload($class) {
        $pathname = root . '/';
        $pathname .= str_replace("\\", "/", $class);
        $pathname .= ".class.php";
        if(is_readable($pathname))
            require_once($pathname);
    }
}
load::_init();