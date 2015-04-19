<?php

// global environment
define('root', realpath(__DIR__ . '../../../'));
define('workground', 'Admin');

error_reporting(E_ERROR);

require_once(root . '/load.php');

\App::display();
