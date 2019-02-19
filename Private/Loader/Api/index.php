<?php
/** @var object $loader */
// check if loader is initialized
if (!defined('root')) {
    die('Hack attempt');
}

// global environment
define('env_name', 'Api');
// this environment have no layouts
define('env_no_layout', true);
// this environment is based on json response type
define('env_type', 'json');

require_once(root . '/Private/Loader/Autoload.php');

// make fast-access alias \App::$Object
// class_alias('Ffcms\Core\App', 'App');
class App extends Ffcms\Core\App
{
}
/**
 * Alias for translate function for fast usage. Example: __('Welcome my friend')
 * @param string $text
 * @param array $params
 * @return string
 */
function __($text, array $params = [])
{
    return \App::$Translate->translate($text, $params);
}

try {
    // build app entry point factory instance
    $app = \App::factory([
        'Database' => true,
        'Session' => true,
        'Debug' => false,
        'User' => true,
        'Mailer' => true,
        'Captcha' => true,
        'Cache' => true
    ], $loader);
    // display output
    $app->run();
} catch (Exception $e) {
    echo (new \Ffcms\Core\Exception\NativeException($e->getMessage()))->display();
}
