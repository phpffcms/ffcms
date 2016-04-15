<?php

namespace Apps\Model\Admin\Newcomment;

use Ffcms\Core\Arch\Model;

class FormSettings extends Model
{
    public $count;
    public $snippet;
    public $cache;
    
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
        $this->snippet = (int)$this->_configs['snippet'];
        $this->count = (int)$this->_configs['count'];
        $this->cache = (int)$this->_configs['cache'];
    }

    /**
    * Labels for form display
    */
    public function labels()
    {
        return [
            'snippet' => __('Max length'),
            'count' => __('Comments count'),
            'cache' => __('Cache')
        ];
    }

    /**
    * Settings validation rules
    */
    public function rules()
    {
        return [
            [['snippet', 'count', 'cache'], 'required'],
            [['snippet', 'count', 'cache'], 'int']
        ];
    }
}