<?php

namespace Apps\Controller\Console;

use Ffcms\Console\App;
use Ffcms\Core\Helper\FileSystem\File;

class Db
{
    public function actionImport($activeRecord)
    {
        $importFile = root . '/Private/Database/Tables/' . ucfirst(strtolower($activeRecord)) . '.php';
        if (!File::exist($importFile)) {
            return App::$Output->write('Database model table not founded: ' . $activeRecord);
        }

        @include($importFile);
        return App::$Output->write('Database table import runned: ' . $activeRecord);
    }

    public function actionImportAll($connectName = 'default')
    {
        $importFile = root . '/Private/Database/install.php';
        if (!File::exist($importFile)) {
            return App::$Output->write('Import file is not exist: ' . $importFile);
        }
        @include($importFile);
        return App::$Output->write('All database tables was imported!');
    }


}