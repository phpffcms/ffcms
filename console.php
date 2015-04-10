<?php
define('root', __DIR__);

// check is started from console
if(PHP_SAPI !== 'cli') {
    die();
}

function readIN()
{
    $handle = fopen('php://stdin', 'r');
    return trim(fgets($handle));
}

echo '    ##############################' . "\n"; // 30 ;)
echo '    ######## FFCMS CONSOLE #######' . "\n";
echo '    ######## LICENSE: MIT ########' . "\n";
echo '    #### AUTHOR: Pyatinskyi M. ###' . "\n";
echo '    ### WEBSITE: www.ffcms.org ###' . "\n";
echo '    ##############################' . "\n";
echo "\n";

if ($argv[1] === 'install') {
    echo '-->* STARTING INSTALL' . "\n";
    echo 'Database connection configuration...' . "\n";
    echo 'Enter database type(mysql,pgsql,sqlite):';
    if (readIN() === 'mysql') {
        echo "WELCOME MYSQL!";
    }
}