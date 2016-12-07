#!/usr/bin/env php
<?php
define('root', __DIR__);

// make autoload - intit app class and load exist commands
require __DIR__.'/Loader/Console/index.php';
\Ffcms\Console\Console::factory([
    'Database' => true
]);
// execute run
$app->run();