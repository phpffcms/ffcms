<?php
define('env_name', 'Console');
define('env_type', 'cli');

if (PHP_SAPI !== 'cli' || !defined('root')) {
    die();
}

require_once (root . '/Loader/Autoload.php');

class App extends Ffcms\Console\App {}

try {
    // prepare to run
    \App::init([
        'Database' => true
    ]);
    // display output
    echo \App::run();
} catch (Exception $e) {
    echo $e . "\n";
}