<?php

namespace Apps\Model\Admin;

use Ffcms\Core\Arch\Model;

class UserSettings extends Model
{
    public $registrationType;
    public $captchaOnLogin;
    public $captchaOnRegister;

    private $_config;

    public function __construct(array $config)
    {
        $this->_config = $config;
        parent::__construct();
    }

    /**
    * Load configs from app data
    */
    public function before()
    {
        foreach ($this->_config as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
    * Example of usage magic labels for future form helper usage
    */
    public function labels()
    {
        return [
            'registrationType' => __('Registration type'),
            'captchaOnLogin' => __('Captcha on login'),
            'captchaOnRegister' => __('Captcha on registration')
        ];
    }

    /**
    * Example of usage magic rules for future usage in condition $model->validate()
    */
    public function rules()
    {
        return [
            [['registrationType', 'captchaOnLogin'], 'required']
        ];
    }
}