<?php

namespace Apps\Model\Admin\Newcontent;

use Ffcms\Core\Arch\Model;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\Serialize;

class FormSettings extends Model
{
    public $categories = array();
    public $count;
    public $cache = 300;
    
    private $_configs;
    
    /**
     * FormSettings constructor. Pass configs inside from controller
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
    * Set default values from configs
    */
    public function before()
    {
        $this->categories = Serialize::decode($this->_configs['categories']);
        $this->count = (int)$this->_configs['count'];
        $this->cache = (int)$this->_configs['cache'];
    }

    /**
    * Labels for form
    */
    public function labels()
    {
        return [
            'categories' => __('Categories'),
            'count' => __('Count'),
            'cache' => __('Cache')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        return [
            [['count', 'categories'], 'required'],
            [['count', 'cache'], 'int']
        ];
    }
    
    /**
     * Get model output
     * @return array
     */
    public function getResult()
    {
        return [
            'count' => (int)$this->count,
            'cache' => (int)$this->cache,
            'categories' => Serialize::encode($this->categories)
        ];
    }
    
    public function getCategories()
    {
        return ContentCategory::getSortedCategories();
    }
}