<?php

namespace Apps\Model\Admin\Main;


use Apps\ActiveRecord\System;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;

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
        $all = File::listFiles('/Private/Database/Updates/', ['.php'], true);
        foreach ($all as $file) {
            $file = Str::cleanExtension(basename($file));
            // $file="3.0.0-3.0.1" become to $start = 3.0.0,$end=3.0.1
            list($start,$end) = explode('-', $file);
            // true: start <= db & script >= $end
            if (version_compare($this->dbVersion, $start) !== 1 &&  version_compare($this->scriptVersion, $end) !== -1) {
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
        // run update queries from included files
        foreach ($this->updateQueries as $file) {
            @include root . '/Private/Database/Updates/' . $file . '.php';
        }
        // update version in db table
        $row = System::getVar('version');
        $row->data = $this->scriptVersion;
        $row->save();
    }
}