<?php
/** @var object $loader */
// check if loader is initialized
if (!defined('root')) {
    die('Hack attempt');
}

// global environment
define('env_name', 'Install');
define('env_type', 'html');

require_once(root . '/Loader/Autoload.php');

// make fast-access alias \App::$Object
// class_alias('Ffcms\Core\App', 'App');
class App extends Ffcms\Core\App {}
/**
 * Alias for translate function for fast usage. Example: __('Welcome my friend')
 * @param string $text
 * @param array $params
 * @return string
 */
function __($text, array $params = []) {
    return \App::$Translate->translate($text, $params);
}

try {
    // prepare to run
    \App::init([
        'Database' => true,
        'Session' => true
    ], $loader);
    // display output
    \App::run();
} catch (Exception $e) {
    (new \Ffcms\Core\Exception\NativeException($e->getMessage()))->display();
}
