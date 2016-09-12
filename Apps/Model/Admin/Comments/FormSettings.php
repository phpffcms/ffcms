<?php

namespace Apps\Model\Admin\Comments;

use Ffcms\Core\Arch\Model;

/**
 * Class FormSettings. Model for settings of comments application
 * @package Apps\Model\Admin\Comments
 */
class FormSettings extends Model
{
    public $perPage;
    public $delay;
    public $minLength;
    public $maxLength;
    public $guestAdd;
    public $guestModerate;
    public $onlyLocale;

    /** @var array|null */
    private $_configs;

    /**
     * FormSettings constructor. Pass array configs from controller.
     * @param array|null $configs
     */
    public function __construct(array $configs = null)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
     * Set model properties based on defaults config values
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
     * Labels for form
     * @return array
     */
    public function labels()
    {
        return [
            'perPage' => __('Comments count'),
            'delay' => __('Delay'),
            'minLength' => __('Minimal length'),
            'maxLength' => __('Maximum length'),
            'guestAdd' => __('Guest add'),
            'guestModerate' => __('Guest moderate'),
            'onlyLocale' => __('Only locale')
        ];
    }

    /**
     * Validation rules for comments settings
     * @return array
     */
    public function rules()
    {
        return [
            [['perPage', 'delay', 'minLength', 'maxLength', 'guestAdd', 'guestModerate', 'onlyLocale'], 'required'],
            [['minLength', 'maxLength', 'delay', 'perPage', 'onlyLocale'], 'int'],
            [['guestAdd', 'onlyLocale', 'guestModerate'], 'in', ['0', '1']]
        ];
    }
}