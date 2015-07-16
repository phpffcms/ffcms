<?php

namespace Apps\Controller\Console;

use Ffcms\Console\App;
use Ffcms\Console\Transfer\Output;
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\File;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;

class Main
{
    // php console.php main/index
    public function actionIndex($id = null)
    {
        return 'Hello, console! ' . $id;
    }

    public function actionHelp()
    {
        $text = "You are using FFCMS console application. \n";
        $text .= "This application support next basic commands: \n\n";
        $text .= "\t main/info - show info about CMS\n";
        $text .= "\t main/install - install FFCMS from console line.\n";
        $text .= "\t main/update - update package to current minor version if available.\n";
        $text .= "\t main/buildperms - build and update permissions map for applications. \n";
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
            foreach ($jsonInfo->packages as $item) {
                $text .= "\t Package: " . $item->name . ' => ' . $item->version . "\n";
            }
        } else {
            $text .= "\t Composer is never be used - no information available.";
        }

        return $text;
    }

    /**
     * Scan available permissions and write to cfg file
     * @return string
     */
    public function actionBuildperms()
    {
        // default permissions
        $permissions = [
            'global/write',
            'global/modify',
            'global/file',
            'global/all'
        ];

        // admin controllers
        $AdminAppControllers = '/Apps/Controller/Admin/';

        // scan directory
        $scan = File::listFiles($AdminAppControllers, ['.php']);

        foreach ($scan as $file) {
            $className = String::firstIn(String::lastIn($file, DIRECTORY_SEPARATOR, true), '.');
            // read as plain text
            $byte = File::read($file);
            preg_match_all('/public function action(\w*?)\(/', $byte, $matches); // matches[0] contains all methods ;)
            if (Object::isArray($matches[1]) && count($matches[1]) > 0) {
                foreach ($matches[1] as $perm) {
                    $permissions[] = 'Admin/' . $className . '/' . $perm;
                }
            }
        }

        // prepare save string
        $stringSave = "<?php \n\nreturn " . var_export($permissions, true) . ';';
        File::write('/Private/Config/PermissionMap.php', $stringSave);

        return App::$Output->write('Permission mas is successful updated! Finded permissions: ' . count($permissions));
    }
}