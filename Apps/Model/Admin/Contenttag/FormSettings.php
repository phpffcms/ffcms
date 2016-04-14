<?php

namespace Apps\Model\Admin\Contenttag;

use Ffcms\Core\Arch\Model;

class FormSettings extends Model
{
    public $count;
    public $cache;
    
    private $_configs;
    
    /**
     * FormSettings constructor. Pass configuration inside as factory.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    /**
    * Set default model properties from global configuration
    */
    public function before()
    {
        $this->count = (int)$this->_configs['count'];
        $this->cache = (int)$this->_configs['cache'];
    }

    /**
    * Form display labels 
    */
    public function labels()
    {
        return [
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
            [['count', 'cache'], 'required'],
            [['count', 'cache'], 'int']
        ];
    }
}