<?php

if (!defined('env_name')) {
    exit('Environment (env_name) is not defined');
}
if (!defined('root')) {
    exit('Root path is not defined');
}

// define global UTF8 for mb_ ext
mb_internal_encoding('UTF-8');

// load composer packages
$loader = require root . '/vendor/autoload.php';
// enable autoload for general namespaces user apps
$loader->add('Apps\\', root);
$loader->add('Extend\\', root);
$loader->add('Widgets\\', root);
