<?php
// global environment
define('root', realpath(__DIR__ . '../../../'));
define('env_name', 'Front');
define('env_no_uri', true);
define('type', 'web');

error_reporting(E_ERROR);

require_once(root . '/Loader/WebLoader.php');

\App::display();