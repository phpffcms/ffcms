<?php

namespace Apps\Model\Admin\Main;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\FileSystem\Directory;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;

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
    public $timezone;
    public $debug;
    public $userCron;
    public $testSuite;

    // theme & database configs
    public $theme;
    public $database;

    // mail configs
    public $mail;

    // lang cfgs
    public $baseLanguage = 'en';
    public $multiLanguage;
    public $singleLanguage;
    public $languages;

    public $languageDomainAlias;

    // other
    public $trustedProxy;
    public $captcha;

    public $_name = 'formAdminConfig';

    /**
    * Set property values from configurations
    */
    public function before()
    {
        $properties = App::$Properties->getAll();
        if (!$properties || !Any::isArray($properties)) {
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
    public function labels(): array
    {
        return [
            'baseDomain' => __('Base domain'),
            'baseProto' => __('Base protocol'),
            'basePath' => __('Base path'),
            'timezone' => __('Timezone'),
            'userCron' => __('User run cron'),
            'debug.all' => __('Debug for all'),
            'testSuite' => __('Test suite'),
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
            'mail.enable' => __('Use mail features'),
            'mail.dsn' => __("DSN connection"),
            'mail.from' => __('From'),
            'debug.cookie.key' => __('Debug cookie key'),
            'debug.cookie.value' => __('Debug cookie value'),
            'trustedProxy' => __('Proxy list'),
            'captcha.smart' => __('Smart captcha'),
            'captcha.time' => __('Captcha lifetime'),
            'captcha.threshold' => __('Captcha threshold')
        ];
    }

    /**
     * Config validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            [['debug.all', 'multiLanguage', 'trustedProxy', 'languages', 'userCron'], 'used'],
            [['baseProto', 'baseDomain', 'basePath', 'singleLanguage', 'timezone', 'testSuite'], 'required'],
            [['debug.cookie.key', 'debug.cookie.value'], 'required'],
            [['theme.Front', 'theme.Admin'], 'required'],
            [['database.driver', 'database.database', 'database.host', 'database.username', 'database.password', 'database.prefix'], 'required'],
            [['database.charset', 'database.collation'], 'used'],
            ['captcha.smart', 'required'],
            [['captcha.time', 'captcha.threshold'], 'used'],
            ['mail.enable', 'required'],
            ['mail.enable', 'int'],
            [['mail.from', 'mail.dsn'], 'used'],
            ['mail.from', 'email'],
            ['timezone', 'string'],
            ['baseProto', 'in', ['http', 'https']],
            [['userCron', 'testSuite'], 'boolean'],
            [['captcha.time', 'captcha.threshold'], 'int']
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
