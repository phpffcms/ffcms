<?php

namespace Apps\Model\Admin\Feedback;


use Ffcms\Core\Arch\Model;

class FormSettings extends Model
{
    public $guestAdd;
    public $useCaptcha;

    /** @var array $_configs */
    private $_configs;

    /**
     * FormSettings constructor. Pass array of configs inside the model
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
     * Set default model values by passed config array
     */
    public function before()
    {
        foreach ($this->_configs as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules()
    {
        return [
            [['guestAdd', 'useCaptcha'], 'required'],
            [['guestAdd', 'useCaptcha'], 'int'],
        ];
    }

    /**
     * Labels to display in form
     * @return array
     */
    public function labels()
    {
        return [
            'guestAdd' => __('Guest add'),
            'useCaptcha' => __('Use captcha')
        ];
    }

}