<?php

namespace Apps\Model\Admin\User;

use Ffcms\Core\Arch\Model;

/**
 * Class FormUserSettings. Settings of user app business logic model
 * @package Apps\Model\Admin\User
 */
class FormUserSettings extends Model
{
    public $registrationType;
    public $captchaOnLogin;
    public $captchaOnRegister;

    private $_config;

    /**
     * FormUserSettings constructor. Pass configs inside the model
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        $this->_config = $config;
        parent::__construct();
    }

    /**
    * Load configs from app data
    */
    public function before()
    {
        if ($this->_config === null) {
            return;
        }
        foreach ($this->_config as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Form display labels
     * @return array
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
     * Validation rules
     * @return array
     */
    public function rules()
    {
        return [
            [['registrationType', 'captchaOnLogin'], 'required']
        ];
    }
}