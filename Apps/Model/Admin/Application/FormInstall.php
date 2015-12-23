<?php

namespace Apps\Model\Admin\Application;

use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Apps\ActiveRecord\App as AppRecord;

class FormInstall extends Model
{
    public $sysname;

    private $_apps;
    private $_type;
    private $_definedControllers = [];

    /**
     * FormInstall constructor. Pass applications object from controller
     * @param $apps
     * @param string $type
     * @throws SyntaxException
     */
    public function __construct($apps, $type)
    {
        $this->_apps = $apps;
        // check if passed type is allowed to use
        if (!Arr::in($type, ['app', 'widget'])) {
            throw new SyntaxException('The type of extension is not defined!');
        }
        $this->_type = $type;
        parent::__construct();
    }

    /**
     * Insert applications defined controllers to local var
     */
    public function before()
    {
        foreach ($this->_apps as $app) {
            $this->_definedControllers[] = (string)$app->sys_name;
        }

        parent::before();
    }

    /**
     * Label form
     * @return array
     */
    public function labels()
    {
        return [
            'sysname' => __('System name')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        return [
            ['sysname', 'required'],
            ['sysname', 'notin', $this->_definedControllers]
        ];
    }

    public function make()
    {
        $cName = ucfirst(Str::lowerCase($this->sysname));
        $cPath = 'Apps\Controller\Admin\\' . $cName;
        // if object class is not loaded - prevent install
        if (!class_exists($cPath) || !defined($cPath . '::VERSION')) {
            return false;
        }

        // get ext version
        $cVersion = (float)constant($cPath . '::VERSION');
        if ($cVersion < 0.1) {
            $cVersion = 0.1;
        }

        // save row to db
        $record = new AppRecord();
        $record->type = $this->_type;
        $record->sys_name = $cName;
        $record->name = '';
        $record->disabled = 1;
        $record->version = $cVersion;
        $record->save();

        // callback to install method in extension
        if (method_exists($cPath, 'install')) {
            call_user_func($cPath . '::install');
        }

        return true;
    }
}