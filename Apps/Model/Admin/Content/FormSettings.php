<?php

namespace Apps\Model\Admin\Content;

use Ffcms\Core\Arch\Model;

/**
 * Class FormSettings. Business logic of content app settings
 * @package Apps\Model\Admin\Content
 */
class FormSettings extends Model
{
    public $itemPerCategory;
    public $userAdd;
    public $multiCategories;
    public $galleryResize;
    public $gallerySize;
    public $rss;
    public $rssFull;

    private $_configs;

    /**
     * FormSettings constructor. Pass config values from controller
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
                $this->$property = $value;
            }
        }
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'itemPerCategory' => __('Content per page'),
            'userAdd' => __('User add'),
            'multiCategories' => __('Multi categories'),
            'galleryResize' => __('Gallery resize'),
            'gallerySize' => __('Image size'),
            'rss' => __('Rss feed')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            [['itemPerCategory', 'userAdd', 'multiCategories', 'galleryResize', 'rss', 'gallerySize'], 'required'],
            ['itemPerCategory', 'int'],
            [['userAdd', 'multiCategories', 'rss', 'rssFull'], 'boolean']
        ];
    }
}
