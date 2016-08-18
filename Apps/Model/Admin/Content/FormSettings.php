<?php

namespace Apps\Model\Admin\Content;

use Ffcms\Core\Arch\Model;

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
     * Pass config values from controller
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
    * Form labels
    */
    public function labels()
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
    */
    public function rules()
    {
        return [
            [['itemPerCategory', 'userAdd', 'multiCategories', 'galleryResize', 'rss', 'gallerySize'], 'required'],
            [['itemPerCategory', 'userAdd', 'multiCategories', 'galleryResize', 'rss', 'gallerySize'], 'int'],
            [['userAdd', 'multiCategories', 'rss', 'rssFull'], 'in', ['0', '1']]
        ];
    }
}