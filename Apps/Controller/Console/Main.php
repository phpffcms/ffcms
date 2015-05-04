<?php

namespace Apps\Controller\Console;

class Main
{
    // php console.php main/index
    public function actionIndex($id = null)
    {
        return 'Hello, console! Id: ' . $id;
    }

    public function actionHelp()
    {
        $text = "You are using FFCMS console application. \n";
        $text .= "This application support next basic commands: \n\n";
        $text .= "\t main/archsync - sync extends of framework classes to local folders.\n";
        $text .= "\t main/install - install FFCMS from console line.\n";
        $text .= "\t main/update - update package to current minor version if available.\n";
        $text .= "\t create/model workground/modelName - create model carcase default.\n";
        $text .= "\t create/ar activeRecordName - create active record table and model.\n";
        $text .= "\t create/controller workground/controllerName - create default controller carcase.\n";
        $text .= "\t db/import activeRecordName - import to database single schema from ar model.\n";
        $text .= "\t db/import_all - import all active record tables to database.\n";
        return $text;
    }
}