<?php

namespace Apps\Model\Admin\Application;

use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\NotFoundException;

class FormUpdate extends Model
{
    private $_controller;
    private $_object;

    public function __construct($controller)
    {
        $this->_controller = $controller;
        parent::__construct();
    }

    /**
    * Magic method before example
    */
    public function before()
    {
        $class = 'Apps\Controller\Admin\\' . $this->_controller;
        if (class_exists($class)) {
            $this->_object = new $class;
        } else {
            throw new NotFoundException('Admin controller is not founded - %c%', ['c' => $this->_controller]);
        }
    }

    public function make()
    {

    }
}