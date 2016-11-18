<?php

namespace Apps\Model\Admin\Main;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;

/**
 * Class FormSettings. Admin system settings business logic
 * @package Apps\Model\Admin\Main
 */
class FormSettings extends Model
{
    // base cfg
    public $baseProto;
    public $baseDomain;
    public $basePath;
    public $passwordSalt;
    public $timezone;
    public $adminEmail;
    public $debug;
    public $userCron;

    // theme & database configs
    public $theme;
    public $database;

    // lang cfgs
    public $baseLanguage = 'en';
    public $multiLanguage;
    public $singleLanguage;
    public $languages;

    public $languageDomainAlias;

    // google analytics settings
    public $gaClientId;
    public $gaTrackId;

    // other
    public $trustedProxy;

    /**
    * Set property values from configurations
    */
    public function before()
    {
        $properties = App::$Properties->getAll();
        if ($properties === false || !Obj::isArray($properties)) {
            return;
        }
        // set default values
        foreach (App::$Properties->getAll() as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Set form display labels
     * @return array
     */
    public function labels()
    {
        return [
            'baseDomain' => __('Base domain'),
            'baseProto' => __('Base protocol'),
            'basePath' => __('Base path'),
            'adminEmail' => __('Admin email'),
            'timezone' => __('Timezone'),
            'userCron' => __('User run cron'),
            'debug.all' => __('Debug for all'),
            'singleLanguage' => __('Default language'),
            'languages' => __('Available languages'),
            'multiLanguage' => __('Multi-languages'),
            'theme.Front' => __('User theme'),
            'theme.Admin' => __('Admin theme'),
            'database.driver' => __('Database driver'),
            'database.host' => __('Database host'),
            'database.database' => __('Database name'),
            'database.username' => __('Database user'),
            'database.password' => __('Database user pass'),
            'database.charset' => __('Charset'),
            'database.collation' => __('Collation'),
            'database.prefix' => __('Tables prefix'),
            'debug.cookie.key' => __('Debug cookie key'),
            'debug.cookie.value' => __('Debug cookie value'),
            'gaClientId' => __('GA Client ID'),
            'gaTrackId' => __('GA Track ID'),
            'trustedProxy' => __('Proxy list'),
        ];
    }

    /**
     * Config validation rules
     * @return array
     */
    public function rules()
    {
        return [
            [['debug.all', 'multiLanguage', 'gaClientId', 'gaTrackId', 'trustedProxy', 'languages', 'userCron'], 'used'],
            [['baseProto', 'baseDomain', 'basePath', 'singleLanguage', 'adminEmail', 'timezone'], 'required'],
            [['debug.cookie.key', 'debug.cookie.value'], 'required'],
            [['theme.Front', 'theme.Admin'], 'required'],
            [['database.driver', 'database.database'], 'required'],
            ['adminEmail', 'email'],
            ['timezone', 'string'],
            ['baseProto', 'in', ['http', 'https']],
            ['userCron', 'in', [0, 1]]
        ];
    }

    /**
     * Get available themes for environment
     * @param $env_name
     * @return array
     */
    public function getAvailableThemes($env_name)
    {
        $path = root . '/Apps/View/' . $env_name . '/';
        if (!Directory::exist($path)) {
            return [];
        }

        $scan = Directory::scan($path);
        $response = [];

        foreach ($scan as $object) {
            $response[] = substr(strrchr($object, '/'), 1);
        }

        return $response;
    }

    /**
     * Save model properties as configurations
     * @return bool
     */
    public function makeSave()
    {
        $toSave = App::$Security->strip_php_tags($this->getAllProperties());
        $stringSave = '<?php return ' . Arr::exportVar($toSave, null, true) . ';';

        $cfgPath = '/Private/Config/Default.php';
        if (File::exist($cfgPath) && File::writable($cfgPath)) {
            File::write($cfgPath, $stringSave);
            return true;
        }

        return false;
    }
}