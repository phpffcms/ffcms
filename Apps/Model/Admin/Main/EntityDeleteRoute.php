<?php

namespace Apps\Model\Admin\Main;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Arr;

class EntityDeleteRoute extends Model
{
    public $type;
    public $loader;
    public $source;
    public $target;

    private $_cfg;

    /**
     * EntityDeleteRoute constructor. Pass parameters from controller
     * @param null $type
     * @param null $loader
     * @param null $source
     */
    public function __construct($type = null, $loader = null, $source = null)
    {
        $this->type = $type;
        $this->loader = $loader;
        $this->source = $source;
        $this->_cfg = App::$Properties->getAll('Routing');
        parent::__construct();
    }

    /**
    * Check passed params from constructor
    */
    public function before()
    {
        // check rule type
        if (!Arr::in($this->type, ['Alias', 'Callback'])) {
            throw new SyntaxException();
        }

        // check loader env type
        if (!Arr::in($this->loader, ['Front', 'Admin', 'Api'])) {
            throw new SyntaxException();
        }

        // prepare source path
        if ($this->type === 'Alias') {
            $this->source = '/' . trim($this->source, '/');
        } else {
            $this->source = ucfirst($this->source);
        }

        if (!isset($this->_cfg[$this->type][$this->loader][$this->source])) {
            throw new SyntaxException();
        }
    }

    /**
     * Define labels for entity
     */
    public function labels()
    {
        return [
            'loader' => __('Loader environment'),
            'type' => __('Routing type'),
            'source' => __('Source path/controller')
        ];
    }

    public function make()
    {
        unset($this->_cfg[$this->type][$this->loader][$this->source]);
        App::$Properties->writeConfig('Routing', $this->_cfg);
    }
}