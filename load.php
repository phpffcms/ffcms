<?php

// load ffcms-core
$loader = require root . '/vendor/autoload.php';
$loader->add('Ffcms\\', root . '/vendor/phpffcms/ffcms-core/src/');
$loader->add("Model\\", root);
$loader->add("Core", root);


/**
 * Alias for fast access
 */
class App extends \Core\App {}

\App::build();

/**
 * Use internalization for text with(in/out) params
 * @param string $text
 * @param array $params
 * @return string
 */
function __($text, $params = []) {
    return \App::$Translate->translate($text, $params);
}