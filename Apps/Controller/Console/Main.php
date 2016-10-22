<?php

namespace Apps\Controller\Console;

use Apps\Controller\Console\Db as DbController;
use Extend\Version;
use Ffcms\Console\Console;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

class Main
{
    // dirs to create & chmod
    public static $installDirs = [
        '/upload/user/', '/upload/gallery/', '/upload/images/', '/upload/flash/', '/upload/files/', '/upload/sitemap/',
        '/Private/Cache/', '/Private/Cache/HTMLPurifier/', '/Private/Sessions/', '/Private/Antivirus/', '/Private/Install/',
        '/Private/Config/', '/Private/Config/Default.php', '/Private/Config/Routing.php', '/Private/Config/Cron.php'
    ];

    /**
     * Show available command list
     * @return string
     */
    public function actionHelp()
    {
        $text = "You are using FFCMS console application." . PHP_EOL;
        $text .= "This application support next basic commands:" . PHP_EOL;
        $text .= "\t main/info - show info about CMS" . PHP_EOL;
        $text .= "\t main/install - install FFCMS from console line." . PHP_EOL;
        $text .= "\t main/update - update package to current minor version if available." . PHP_EOL;
        $text .= "\t main/chmod - update chmod for ffcms special folders. Can be used after project deployment." . PHP_EOL;
        $text .= "\t main/buildperms - build and update permissions map for applications." . PHP_EOL;
        $text .= "\t create/model workground/modelName - create model carcase default." . PHP_EOL;
        $text .= "\t create/ar activeRecordName - create active record table and model." . PHP_EOL;
        $text .= "\t create/controller workground/controllerName - create default controller carcase." . PHP_EOL;
        $text .= "\t create/widget workground/widgetName/widgetName - create default widget carcase." . PHP_EOL;
        $text .= "\t db/import activeRecordName - import to database single schema from ar model." . PHP_EOL;
        $text .= "\t db/importAll - import all active record tables to database." . PHP_EOL;
        $text .= "\t db/adduser - add new user into database." . PHP_EOL;
        return $text;
    }

    /**
     * Display system information
     * @return string
     */
    public function actionInfo()
    {
        $text = "Information about FFCMS package and environment:" . PHP_EOL;
        $text .= "\t PHP version: " . phpversion() . PHP_EOL;
        $text .= "\t Dist path: " . root . PHP_EOL;
        $text .= "\t Used version: " . Version::VERSION . ' [build: ' . Version::DATE . ']' . PHP_EOL;
        $text .= "Information about FFCMS cmf packages:" . PHP_EOL;

        $composerInfo = File::read('/composer.lock');
        if (false !== $composerInfo) {
            $jsonInfo = json_decode($composerInfo);
            foreach ($jsonInfo->packages as $item) {
                $text .= "\t Package: " . $item->name . ' => ' . $item->version . PHP_EOL;
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

        return 'Permissions configuration is successful updated! Founded permissions: ' . count($permissions);
    }

    /**
     * Set chmod for system directories
     * @return string
     */
    public function actionChmod()
    {
        $errors = false;
        foreach (self::$installDirs as $obj) {
            if (Directory::exist($obj)) {
                Directory::recursiveChmod($obj, 0777);
            } elseif (File::exist($obj)) {
                chmod(root . $obj, 0777);
            } else {
                $errors .= Console::$Output->write('Filesystem object is not founded: ' . $obj);
            }
        }

        return $errors === false ? Console::$Output->write('Chmods are successful changed') : $errors;
    }

    /**
     * Console installation
     * @return string
     * @throws NativeException
     */
    public function actionInstall()
    {
        if (File::exist('/Private/Install/install.lock')) {
            throw new NativeException('Installation is locked! Please delete /Private/Install/install.lock');
        }

        echo Console::$Output->writeHeader('License start');
        echo File::read('/LICENSE') . PHP_EOL;
        echo Console::$Output->writeHeader('License end');

        $config = Console::$Properties->get('database');
        $newConfig = [];
        // creating default directory's
        foreach (self::$installDirs as $obj) {
            // looks like a directory
            if (!Str::contains('.', $obj)) {
                Directory::create($obj, 0777);
            }
        }
        echo Console::$Output->write('Upload and private directories are successful created!');

        // set chmods
        echo $this->actionChmod();

        // database config from input
        echo Console::$Output->writeHeader('Database connection configuration');
        echo 'Driver(default:' . $config['driver'] . '):';
        $dbDriver = Console::$Input->read();
        if (Arr::in($dbDriver, ['mysql', 'pgsql', 'sqlite'])) {
            $newConfig['driver'] = $dbDriver;
        }

        // for sqlite its would be a path
        echo 'Host(default:' . $config['host'] . '):';
        $dbHost = Console::$Input->read();
        if (!Str::likeEmpty($dbHost)) {
            $newConfig['host'] = $dbHost;
        }

        echo 'Database name(default:' . $config['database'] . '):';
        $dbName = Console::$Input->read();
        if (!Str::likeEmpty($dbName)) {
            $newConfig['database'] = $dbName;
        }

        echo 'User(default:' . $config['username'] . '):';
        $dbUser = Console::$Input->read();
        if (!Str::likeEmpty($dbUser)) {
            $newConfig['username'] = $dbUser;
        }

        echo 'Password(default:' . $config['password'] . '):';
        $dbPwd = Console::$Input->read();
        if (!Str::likeEmpty($dbPwd)) {
            $newConfig['password'] = $dbPwd;
        }

        echo 'Table prefix(default:' . $config['prefix'] . '):';
        $dbPrefix = Console::$Input->read();
        if (!Str::likeEmpty($dbPrefix)) {
            $newConfig['prefix'] = $dbPrefix;
        }

        // merge configs and add new connection to db pull
        $dbConfigs = Arr::merge($config, $newConfig);
        Console::$Database->addConnection($dbConfigs, 'install');

        try {
            Console::$Database->connection('install')->getDatabaseName();
        } catch (\Exception $e) {
            return 'Testing database connection is failed! Run installer again and pass tested connection data! Log: ' . $e->getMessage();
        }

        // autoload isn't work here
        include(root . '/Apps/Controller/Console/Db.php');

        // import db data
        $dbController = new DbController();
        echo $dbController->actionImportAll('install');

        // set website send from email from input
        $emailConfig = Console::$Properties->get('adminEmail');
        echo 'Website sendFrom email(default: ' . $emailConfig . '):';
        $email = Console::$Input->read();
        if (!Str::isEmail($email)) {
            $email = $emailConfig;
        }

        // set base domain
        echo 'Website base domain name(ex. ffcms.org):';
        $baseDomain = Console::$Input->read();
        if (Str::likeEmpty($baseDomain)) {
            $baseDomain = Console::$Properties->get('baseDomain');
        }

        // generate other configuration data and security salt, key's and other
        echo Console::$Output->writeHeader('Writing configurations');
        /** @var array $allCfg */
        $allCfg = Console::$Properties->getAll('default');
        $allCfg['database'] = $dbConfigs;
        $allCfg['adminEmail'] = $email;
        $allCfg['baseDomain'] = $baseDomain;
        echo Console::$Output->write('Generate password salt for BLOWFISH crypt');
        $allCfg['passwordSalt'] = '$2a$07$' . Str::randomLatinNumeric(mt_rand(21, 30)) . '$';
        echo Console::$Output->write('Generate security cookies for debug panel');
        $allCfg['debug']['cookie']['key'] = 'fdebug_' . Str::randomLatinNumeric(mt_rand(8, 32));
        $allCfg['debug']['cookie']['value'] = Str::randomLatinNumeric(mt_rand(32, 128));

        // write config data
        $writeCfg = Console::$Properties->writeConfig('default', $allCfg);
        if ($writeCfg !== true) {
            return 'File /Private/Config/Default.php is unavailable to write data!';
        }

        File::write('/Private/Install/install.lock', 'Install is locked');

        return 'Configuration done! FFCMS 3 is successful installed! Visit your website. You can add administrator using command php console.php db/adduser';
    }
}