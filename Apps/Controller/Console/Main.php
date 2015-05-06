<?php

namespace Apps\Controller\Console;

use Ffcms\Console\App;
use Ffcms\Core\Helper\File;

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
        $text .= "\t main/info - show info about CMS\n";
        $text .= "\t main/install - install FFCMS from console line.\n";
        $text .= "\t main/update - update package to current minor version if available.\n";
        $text .= "\t create/model workground/modelName - create model carcase default.\n";
        $text .= "\t create/ar activeRecordName - create active record table and model.\n";
        $text .= "\t create/controller workground/controllerName - create default controller carcase.\n";
        $text .= "\t db/import activeRecordName - import to database single schema from ar model.\n";
        $text .= "\t db/importAll - import all active record tables to database.\n";
        return $text;
    }

    public function actionInfo()
    {
        $text = "\nInformation about FFCMS package and environment: \n\n";
        $text .= "\t PHP version: " . phpversion() . "\n";
        $text .= "\t Dist path: " . root . "\n";
        $text .= "\t Used version: " . App::$Property->version['num'] . ' [build: ' . App::$Property->version['date'] . "]\n\n";
        $text .= "Information about FFCMS cmf packages: \n\n";

        $composerInfo = File::read('/composer.lock');
        if (false !== $composerInfo) {
            $jsonInfo = json_decode($composerInfo);
            foreach($jsonInfo->packages as $item) {
                $text .= "\t Package: " . $item->name . ' => ' . $item->version . "\n";
            }
        } else {
            $text .= "\t Composer is never be used - no information available.";
        }

        return $text;
    }
}