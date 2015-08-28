<?php

// check if loader is initialized
if (!defined('root')) {
    die('Hack attempt');
}

// global environment
define('env_name', 'Api');
define('type', 'web');
define('env_no_layout', true);

require_once(root . '/Loader/WebLoader.php');

\App::display();
