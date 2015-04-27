<?php
// global environment
define('root', realpath(__DIR__ . '../../../'));
define('workground', 'Front');

error_reporting(E_ALL ^ E_NOTICE);

require_once(root . '/load.php');

\App::display();
