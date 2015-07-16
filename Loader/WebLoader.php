<?php

if (!defined('env_name')) {
    exit('Environment (env_name) is not defined');
}
if (!defined('root')) {
    exit('Root path is not defined');
}

// load composer packages
$loader = require root . '/vendor/autoload.php';
// load app's model's
$loader->add('Apps\\Model\\', root);
// load app's active records
$loader->add('Apps\\ActiveRecord\\', root);
// load core extending
$loader->add('Extend\\Core\\', root);
// load widgets
$loader->add('Widgets\\', root);


/**
 * Alias for fast access
 */
class App extends Ffcms\Core\App {}

\App::build();

/**
 * Use internalization for text with(in/out) params
 * @param string $text
 * @param array $params
 * @return string
 */
function __($text, array $params = []) {
    return \App::$Translate->translate($text, $params);
}