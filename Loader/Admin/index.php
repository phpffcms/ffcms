<?php

// global environment
define('root', realpath(__DIR__ . '../../../'));
define('env_name', 'Admin');
define('env_no_uri', false);
define('type', 'web');

error_reporting(E_ERROR);

require_once(root . '/Loader/WebLoader.php');

\App::display();
