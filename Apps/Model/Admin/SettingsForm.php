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
    public $theme_Front;
    public $theme_Admin;
    public $siteIndex;
    public $baseLanguage = 'en';
    public $passwordSalt;
    public $debug_all = false;
    public $debug_cookie_key;
    public $debug_cookie_value;

    /**
    * Magic method before example
    */
    public function before()
    {
        // set default values
        foreach (App::$Property->getAll() as $key => $value) {
            if (Object::isArray($value)) {
                foreach ($value as $key2 => $value2) {
                    if (Object::isArray($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            $this->{$key . '_' . $key2 . '_' . $key3} = $value3;
                        }
                    } else {
                        $this->{$key . '_' . $key2} = $value2;
                    }
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function setLabels()
    {
        return [
            'basePath' => 'Base path'
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validateRules()
    */
    public function setRules()
    {
        return [
            ['basePath', 'required']
        ];
    }

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
}