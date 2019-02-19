<?php
define('env_name', 'Console');
define('env_type', 'cli');

use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\FileSystem\File;

if (PHP_SAPI !== 'cli' || !defined('root')) {
    die();
}

require_once(root . '/Private/Loader/Autoload.php');

// initialize console app
$app = new Symfony\Component\Console\Application('FFCMS', '3.0.0');

// list classmap and add existing commands
$classMap = $loader->getPrefixes();
foreach ($classMap['Apps\\'] as $path) {
    $path .= '/Apps/Console';
    $files = File::listFiles($path, ['.php'], true);
    foreach ($files as $file) {
        $class = Str::cleanExtension($file);
        $namespace = 'Apps\Console\\' . $class;
        if (class_exists($namespace) && is_a($namespace, 'Symfony\Component\Console\Command\Command', true)) {
            $cmd = new $namespace;
            $app->add($cmd);
            $cmd = null;
        }
    }
}
