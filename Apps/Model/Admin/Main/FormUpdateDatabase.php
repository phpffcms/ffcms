<?php

namespace Apps\Model\Admin\Main;

use Apps\ActiveRecord\System;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Managers\MigrationsManager;

/**
 * Class FormUpdateDatabase. Update database business logic model
 * @package Apps\Model\Admin\Main
 */
class FormUpdateDatabase extends Model
{
    private $dbVersion;
    private $scriptVersion;
    public $updateQueries = [];

    /**
     * FormUpdateDatabase constructor. Pass db and script version inside
     * @param string $db
     * @param string $script
     */
    public function __construct($db, $script)
    {
        $this->dbVersion = $db;
        $this->scriptVersion = $script;
        parent::__construct(true);
    }

    /**
     * Find update files with sql queries
     */
    public function findUpdateFiles()
    {
        // find all file with update sql queries between $dbVersion<->scriptVersion (dbVer <= x <= scriptVer)
        $migrations = new MigrationsManager('/Private/Migrations/Updates/');
        $search = $migrations->search('update');
        foreach ($search as $file) {
            $fullName = Str::cleanExtension(basename($file));
            $name = Str::firstIn($fullName, '-');
            // get update version number from migration name
            list($type, $obj, $version) = explode('_', $name);
            $intVersion = (int)Str::replace('.', '', $this->dbVersion);
            // if migration version > db version - implement it
            if ($version > $intVersion) {
                $this->updateQueries[] = $file;
            }
        }
        sort($this->updateQueries);
    }

    /**
     * Include files with update queries
     */
    public function make()
    {
        // run update queries from migrations
        $migration = new MigrationsManager('/Private/Migrations/Updates/');
        $migration->makeUp($this->updateQueries);
        // update version in db table
        $row = System::getVar('version');
        $row->data = $this->scriptVersion;
        $row->save();
    }
}
