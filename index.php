<?php
// define available loader list
$loaderList = ['Front', 'Admin', 'Api', 'Install'];

// set error level
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

// define document root as project root folder
define('root', __DIR__);

// get current URI path
$uriRequest = $_SERVER['REQUEST_URI'];

// check if SAPI client == cli (php built-in dev server)
if (php_sapi_name() === 'cli-server') {
    $clearPath = strtok($uriRequest, '?'); // fix file.css?version=1.2.3 or ?time marks
    $path = root . DIRECTORY_SEPARATOR . ltrim($clearPath, '/');
    // if static file exist
    if (is_file($path)) {
        // check if it looks like standalone php script
        if (strtolower(substr($path, -4)) == '.php') {
            include($path);
            return true;
        }
        return false;
    }
}


// get configs to prepare posible route to switch environment
$configs = require(root . '/Private/Config/Default.php');
// remove base path
$uriRequest = substr($uriRequest, strlen($configs['basePath']));
$uriArray = explode('/', $uriRequest);

// extract 1st element
$uriLoader = array_shift($uriArray);
// prepare path to default filesystem type
$uriLoader = ucfirst(strtolower(str_replace('.', '', $uriLoader)));

// if loader of interface is available - require it
if (in_array($uriLoader, $loaderList, true) && file_exists(root . '/Loader/' . $uriLoader . '/index.php')) {
    require_once (root . '/Loader/' . $uriLoader . '/index.php');
} else { // else - try to load default interface
    require_once (root . '/Loader/Front/index.php');
}