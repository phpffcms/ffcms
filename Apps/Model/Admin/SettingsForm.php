<?php

namespace Apps\Model\Admin;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Directory;
use Ffcms\Core\Helper\File;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;

class SettingsForm extends Model
{

    public $basePath;
    public $siteIndex;
    public $passwordSalt;
    public $debug_all = false;

    public $debug;
    public $theme;
    public $database;

    // lang cfgs
    public $baseLanguage = 'en';
    public $multiLanguage;
    public $singleLanguage;
    public $languages;

    /**
    * Magic method before example
    */
    public function before()
    {
        // set default values
        foreach (App::$Property->getAll() as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function setLabels()
    {
        return [
            'basePath' => 'Base path',
            'siteIndex' => 'Main callback',
            'passwordSalt' => 'Hashing salt',
            'debug.all' => 'Debug for all',
            'singleLanguage' => 'Default language',
            'languages' => 'Available languages',
            'multiLanguage' => 'Multi-languages',
            'theme.Front' => 'User theme',
            'theme.Admin' => 'Admin theme'
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validateRules()
    */
    public function setRules()
    {
        return [
            [['basePath', 'siteIndex', 'passwordSalt', 'singleLanguage'], 'required'],
            [['debug.all', 'debug.cookie.key', 'debug.cookie.value'], 'required'],
            [['theme.Front', 'theme.Admin'], 'required'],
            [['database.driver', 'database.database'], 'required'],
            ['passwordSalt', 'length_min', 20]
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

    public function makeSave()
    {
        $post_data = App::$Request->request->all();
    }
}