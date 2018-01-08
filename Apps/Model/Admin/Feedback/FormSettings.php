<?php

namespace Apps\Model\Admin\Feedback;

use Ffcms\Core\Arch\Model;

/**
 * Class FormSettings. Feedback app settings business logic
 * @package Apps\Model\Admin\Feedback
 */
class FormSettings extends Model
{
    public $guestAdd;
    public $useCaptcha;

    /** @var array|null $_configs */
    private $_configs;

    /**
     * FormSettings constructor. Pass array of configs inside the model
     * @param array|null $configs
     */
    public function __construct(array $configs = null)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
     * Set default model values by passed config array
     */
    public function before()
    {
        if ($this->_configs === null) {
            return;
        }

        foreach ($this->_configs as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
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
    public function labels(): array
    {
        return [
            'guestAdd' => __('Guest add'),
            'useCaptcha' => __('Use captcha')
        ];
    }
}
