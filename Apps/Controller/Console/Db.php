<?php

namespace Apps\Controller\Console;

class Db
{
    public function actionImport($activeRecord)
    {
        $importFile = root . '/Private/Database/Tables/' . ucfirst(strtolower($activeRecord)) . '.php';
        if(!file_exists($importFile)) {
            return 'Database model table not founded: ' . $activeRecord;
        }

        @include($importFile);
        return "Database table import runned: " . $activeRecord;
    }
}