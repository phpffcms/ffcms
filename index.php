<?php
// global environment
define('root', dirname(__FILE__));
define('workground', 'Front');

error_reporting(E_ERROR);

require_once(root . '/load.php');

\App::display();
