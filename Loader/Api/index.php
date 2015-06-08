<?php
// global environment
define('root', realpath(__DIR__ . '../../../'));
define('env_name', 'Api');
define('type', 'web');
define('env_no_layout', true);

error_reporting(E_ALL ^ E_NOTICE);

require_once(root . '/Loader/WebLoader.php');

\App::display();
