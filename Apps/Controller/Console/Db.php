<?php

namespace Apps\Controller\Console;

use Ffcms\Core\Helper\File;

class Db
{
    public function actionImport($activeRecord)
    {
        $importFile = root . '/Private/Database/Tables/' . ucfirst(strtolower($activeRecord)) . '.php';
        if(!File::exist($importFile)) {
            return 'Database model table not founded: ' . $activeRecord;
        }

        @include($importFile);
        return 'Database table import runned: ' . $activeRecord;
    }

    public function actionImportAll()
    {
        $importFile = root . '/Private/Database/install.php';
        if (!File::exist($importFile)) {
            return 'Import file is not exist: ' . $importFile;
        }
        @include($importFile);
        return 'All database tables was imported!';
    }


}