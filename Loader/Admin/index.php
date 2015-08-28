<?php

// check if loader is initialized
if (!defined('root')) {
    die('Hack attempt');
}

// global environment
define('env_name', 'Admin');
define('env_no_uri', false);
define('type', 'web');

require_once(root . '/Loader/WebLoader.php');

\App::display();
