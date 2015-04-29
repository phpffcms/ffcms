<?php
define('workground', 'console');
define('type', 'cli');


if(PHP_SAPI !== 'cli' || !defined('root')) {
    die();
}

// load ffcms-core
$loader = require root . '/vendor/autoload.php';
$loader->add('Model\\', root);
$loader->add('Core', root);
$loader->add('Console', root);

class App extends \Console\App {}

\App::build();
echo \App::display();

//var_dump(\App::$Property->getAll());