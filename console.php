#!/usr/bin/env php
<?php
define('root', __DIR__);

// set error level
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

// make autoload - intit app class and load exist commands
require __DIR__ . '/Private/Loader/Console/index.php';
\Ffcms\Console\Console::factory([
    'Database' => true
]);
// execute run
$app->run();