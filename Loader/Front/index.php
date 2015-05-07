<?php
// global environment
define('root', realpath(__DIR__ . '../../../'));
define('workground', 'Front');
define('type', 'web');

error_reporting(E_ALL ^ E_NOTICE);

require_once(root . '/Loader/WebLoader.php');

\App::display();
