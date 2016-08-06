<?php
/** @var object $loader */
// check if loader is initialized
if (!defined('root')) {
    die('Hack attempt');
}

// global environment
define('env_name', 'Admin');
define('env_no_uri', false);
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
    // build app factory instance
    $app = \App::factory([
        'Database' => true,
        'Session' => true,
        'Debug' => true,
        'User' => true,
        'Mailer' => true,
        'Captcha' => true,
        'Cache' => true
    ], $loader);
    // display output
    $app->run();
} catch (Exception $e) {
    (new \Ffcms\Core\Exception\NativeException($e->getMessage()))->display();
}