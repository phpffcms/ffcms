<?php
// global environment
define('root', dirname(__FILE__));
define('env', 'PRODUCTION');

require_once(root . '/load.php');
\App::build();

\App::display();

?>