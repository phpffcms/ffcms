<?php
/** @var object $loader */
// check if loader is initialized
if (!defined('root')) {
    die('Hack attempt');
}

// global environment
define('env_name', 'Cron');
// this environment have no layouts
define('env_no_layout', true);
define('env_no_uri', true);
define('env_type', 'cli');
/** set default locale */
$_GET['lang'] = 'en';

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
function __($text, array $params = [])
{
    return \App::$Translate->translate($text, $params);
}

try {
    // prepare to run
    $app = \App::factory([
        'Database' => true,
        'User' => true,
        'Mailer' => true,
        'Cache' => true
    ]);

    $cronManager = new \Ffcms\Core\Managers\CronManager();
    $logs = $cronManager->run();
    if (PHP_SAPI === 'cli') {
        if ($logs && \Ffcms\Core\Helper\Type\Any::isArray($logs) && count($logs) > 0) {
            echo 'Run cron tasks: ' . PHP_EOL . implode(PHP_EOL, $logs);
        } else {
            echo 'No tasks runned';
        }
    }
} catch (Exception $e) {
    echo (new \Ffcms\Core\Exception\NativeException($e->getMessage()))->display();
}
