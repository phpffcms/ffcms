<?php
// check if loader is initialized
if (!defined('root')) {
    die('Hack attempt');
}

// global environment
define('env_name', 'Install');
define('type', 'web');
define('nodebug', true);

require_once(root . '/Loader/WebLoader.php');

\App::display();