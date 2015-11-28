<?php

namespace Apps\Controller\Console;

use Ffcms\Console\App;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use \Illuminate\Database\Capsule\Manager as Capsule;
use Apps\Controller\Console\Db as DbController;

class Main
{
    // dirs to create & chmod
    protected $installDirs = [
        '/upload/user/', '/upload/gallery/', '/upload/images/',
        '/Private/Cache/', '/Private/Cache/HTMLPurifier/', '/Private/Sessions/', '/Private/Antivirus/',
        '/Private/Config/', '/Private/Config/Default.php', '/Private/Config/Routing.php'
    ];

    public function actionHelp()
    {
        $text = "You are using FFCMS console application. \n";
        $text .= "This application support next basic commands: \n\n";
        $text .= "\t main/info - show info about CMS\n";
        $text .= "\t main/install - install FFCMS from console line.\n";
        $text .= "\t main/update - update package to current minor version if available.\n";
        $text .= "\t main/chmod - update chmod for ffcms special folders. Can be used after project deployment.\n";
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
        $text .= "\t Used version: " . App::$Properties->version['num'] . ' [build: ' . App::$Properties->version['date'] . "]\n\n";
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
            $className = Str::firstIn(Str::lastIn($file, DIRECTORY_SEPARATOR, true), '.');
            // read as plain text
            $byte = File::read($file);
            preg_match_all('/public function action(\w*?)\(/', $byte, $matches); // matches[0] contains all methods ;)
            if (Obj::isArray($matches[1]) && count($matches[1]) > 0) {
                foreach ($matches[1] as $perm) {
                    $permissions[] = 'Admin/' . $className . '/' . $perm;
                }
            }
        }

        // prepare save string
        $stringSave = "<?php \n\nreturn " . var_export($permissions, true) . ';';
        File::write('/Private/Config/Permissions.php', $stringSave);

        return App::$Output->write('Permission mas is successful updated! Founded permissions: ' . count($permissions));
    }

    /**
     * Set chmod for system directories
     */
    public function actionChmod()
    {
        $errors = false;
        foreach ($this->installDirs as $obj) {
            if (Directory::exist($obj)) {
                Directory::recursiveChmod($obj, 0777);
            } elseif (File::exist($obj)) {
                chmod(root . $obj, 0777);
            } else {
                $errors .= App::$Output->write('Filesystem object is not founded: ' . $obj);
            }
        }

        return $errors === false ? App::$Output->write('Chmods are successful changed') : $errors;
    }

    public function actionInstall()
    {
        $config = App::$Properties->get('database');
        $newConfig = [];
        // creating default directory's
        foreach ($this->installDirs as $obj) {
            // looks like a directory
            if (!Str::contains('.', $obj)) {
                Directory::create($obj, 0777);
            }
        }
        echo App::$Output->write('Upload and private directories are successful created!');

        // set chmods
        echo $this->actionChmod();

        // database config from input
        echo App::$Output->writeHeader('Database connection configuration');
        echo 'Driver(default:' . $config['driver'] . '):';
        $dbDriver = App::$Input->read();
        if (Arr::in($dbDriver, ['mysql', 'pgsql', 'sqlite'])) {
            $newConfig['driver'] = $dbDriver;
        }

        // for sqlite its would be a path
        echo 'Host(default:' . $config['host'] . '):';
        $dbHost = App::$Input->read();
        if (!Str::likeEmpty($dbHost)) {
            $newConfig['host'] = $dbHost;
        }

        echo 'Database name(default:' . $config['database'] . '):';
        $dbName = App::$Input->read();
        if (!Str::likeEmpty($dbName)) {
            $newConfig['database'] = $dbName;
        }

        echo 'User(default:' . $config['username'] . '):';
        $dbUser = App::$Input->read();
        if (!Str::likeEmpty($dbUser)) {
            $newConfig['username'] = $dbUser;
        }

        echo 'Password(default:' . $config['password'] . '):';
        $dbPwd = App::$Input->read();
        if (!Str::likeEmpty($dbPwd)) {
            $newConfig['password'] = $dbPwd;
        }

        echo 'Table prefix(default:' . $config['prefix'] . '):';
        $dbPrefix = App::$Input->read();
        if (!Str::likeEmpty($dbPrefix)) {
            $newConfig['prefix'] = $dbPrefix;
        }

        // merge configs and add new connection to db pull
        $dbConfigs = Arr::merge($config, $newConfig);
        App::$Database->addConnection($dbConfigs, 'install');

        try {
            App::$Database->connection('install')->getDatabaseName();
        } catch (\Exception $e) {
            return 'Testing database connection is failed! Run installer again and pass tested connection data! Log: ' . $e->getMessage();
        }

        // autoload isn't work here
        include(root . '/Apps/Controller/Console/Db.php');

        // import db data
        $dbController = new DbController();
        echo $dbController->actionImportAll('install');

        // set website send from email from input
        $emailConfig = App::$Properties->get('adminEmail');
        echo 'Website sendFrom email(default: ' . $emailConfig . '):';
        $email = App::$Input->read();
        if (!Str::isEmail($email)) {
            $email = $emailConfig;
        }

        // generate other configuration data and security salt, key's and other
        echo App::$Output->writeHeader('Writing configurations');
        $allCfg = App::$Properties->getAll('default');
        $allCfg['database'] = $dbConfigs;
        $allCfg['adminEmail'] = $email;
        echo App::$Output->write('Generate password salt for BLOWFISH crypt');
        $allCfg['passwordSalt'] = Str::randomLatinNumeric(mt_rand(21, 30)) . '$';
        echo App::$Output->write('Generate security cookies for debug panel');
        $allCfg['debug']['cookie']['key'] = 'fdebug_' . Str::randomLatinNumeric(mt_rand(8, 32));
        $allCfg['debug']['cookie']['value'] = Str::randomLatinNumeric(mt_rand(32, 128));

        // write config data
        $writeCfg = App::$Properties->writeConfig('default', $allCfg);
        if ($writeCfg !== true) {
            return 'File /Private/Config/Default.php is unavailable to write data!';
        }

        return 'Configuration done! FFCMS 3 is successful installed! Visit your website. You can add administrator using command php console.php db/adduser';
    }
}