<?php

namespace Apps\Model\Install\Main;

use Apps\Controller\Console\Main;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class EntityCheck. Main admin business logic model of statistics and environment status
 * @package Apps\Model\Install\Main
 */
class EntityCheck extends Model
{
    public $phpVersion;
    public $pdo;
    public $gd;

    private $_chmodDirs = [];

    public $chmodCheck = [];

    /**
    * Get default server information and prepare chmod info
    */
    public function before()
    {
        $this->phpVersion = phpversion();
        $this->pdo = extension_loaded('pdo');
        $this->gd = extension_loaded('gd') && function_exists('gd_info');

        // autoload is disabled, lets get chmod file & dirs from console app data
        File::inc('/Apps/Controller/Console/Main.php');
        $this->_chmodDirs = Main::$installDirs;
        // for each file or directory in list - check permissions
        foreach ($this->_chmodDirs as $object) {
            if (Str::endsWith('.php', $object)) { // sounds like a file
                $this->chmodCheck[$object] = (File::exist($object) && File::writable($object));
            } else {
                $this->chmodCheck[$object] = (Directory::exist($object) && Directory::writable($object));
            }
        }
    }

    /**
     * Check php version
     * @return bool
     */
    public function checkPhpVersion()
    {
        return version_compare($this->phpVersion, '5.4', '>=');
    }

    /**
     * Check all params
     * @return bool
     */
    public function checkAll()
    {
        return $this->checkPhpVersion() && $this->pdo && $this->gd && !Arr::in(false, $this->chmodCheck);
    }
}