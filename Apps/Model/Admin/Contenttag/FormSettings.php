<?php

namespace Apps\Model\Admin\Contenttag;

use Ffcms\Core\Arch\Model;

/**
 * Class FormSettings. Content tags widget settings business logic model
 * @package Apps\Model\Admin\Contenttag
 */
class FormSettings extends Model
{
    public $count;
    public $cache;

    private $_configs;

    /**
     * FormSettings constructor. Pass configuration inside as factory.
     * @param array $configs
     */
    public function __construct(array $configs = null)
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
     * @return array
     */
    public function labels(): array
    {
        return [
            'count' => __('Count'),
            'cache' => __('Cache')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            [['count', 'cache'], 'required'],
            [['count', 'cache'], 'int']
        ];
    }
}
