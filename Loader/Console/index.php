<?php
define('env_name', 'Console');
define('env_type', 'cli');

if (PHP_SAPI !== 'cli' || !defined('root')) {
    die();
}

require_once (root . '/Loader/Autoload.php');

class Console extends Ffcms\Console\Console {}

try {
    // prepare to run
    \Console::init([
        'Database' => true
    ]);
    // display output
    echo \Console::run();
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}