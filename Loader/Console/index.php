<?php
define('env_name', 'Console');
define('type', 'cli');


if (PHP_SAPI !== 'cli' || !defined('root')) {
    die();
}

// load ffcms-core
$loader = require root . '/vendor/autoload.php';
// load app's model's
$loader->add('Apps\\Model\\', root);
// load app's active records
$loader->add('Apps\\ActiveRecord\\', root);
// load core extending
$loader->add('Extend\\Core\\', root);
// load console extending
$loader->add('Extend\\Console\\', root);

class App extends Ffcms\Console\App {}

\App::build();
echo \App::display();