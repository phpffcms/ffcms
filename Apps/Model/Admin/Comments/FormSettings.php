<?php

namespace Apps\Model\Admin\Comments;

use Ffcms\Core\Arch\Model;

/**
 * Class FormSettings. Model for settings of comments
 * @package Apps\Model\Admin\Comments
 */
class FormSettings extends Model
{
    public $perPage;
    public $delay;
    public $minLength;
    public $maxLength;
    public $guestAdd;
    public $editTime;

    private $_configs;

    /**
     * FormSettings constructor. Pass array configs from controller.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
     * Set model properties based on defaults config values
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
    * Labels for form
    */
    public function labels()
    {
        return [
            'perPage' => __('Comments count'),
            'delay' => __('Delay'),
            'minLength' => __('Minimal length'),
            'maxLength' => __('Maximum length'),
            'guestAdd' => __('Guest add'),
            'editTime' => __('Edit time')
        ];
    }

    /**
    * Validation rules for comments settings
    */
    public function rules()
    {
        return [
            [['perPage', 'delay', 'minLength', 'maxLength', 'guestAdd', 'editTime'], 'required'],
            [['minLength', 'maxLength', 'delay', 'perPage', 'editTime'], 'int'],
            ['guestAdd', 'in', ['0', '1']]
        ];
    }
}