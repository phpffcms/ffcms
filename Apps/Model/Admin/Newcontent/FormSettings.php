<?php

namespace Apps\Model\Admin\Newcontent;

use Ffcms\Core\Arch\Model;
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\Serialize;

/**
 * Class FormSettings. New content widget settings business logic
 * @package Apps\Model\Admin\Newcontent
 */
class FormSettings extends Model
{
    public $categories = array();
    public $count;
    public $cache = 300;

    private $_configs;

    /**
     * FormSettings constructor. Pass configs inside from controller
     * @param array|null $configs
     */
    public function __construct(array $configs = null)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
     * Set default values from configs
     */
    public function before()
    {
        if ($this->_configs === null) {
            return;
        }
        $this->categories = Serialize::decode($this->_configs['categories']);
        $this->count = (int)$this->_configs['count'];
        $this->cache = (int)$this->_configs['cache'];
    }

    /**
     * Labels for form display
     * @return array
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
     * @return array
     */
    public function rules()
    {
        return [
            [['count', 'categories'], 'required'],
            [['count', 'cache'], 'int']
        ];
    }

    /**
     * Get all categories as sorted array
     * @return array
     */
    public function getCategories()
    {
        return ContentCategory::getSortedCategories();
    }
}