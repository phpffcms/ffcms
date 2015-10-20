<?php

namespace Widgets\Front\StaticContent;

use Extend\Core\Arch\FrontWidget;


class StaticContent extends FrontWidget
{
    public $id;
    public $sys;
    public $response;

    public function init()
    {
        /**if ($this->id !== null && (int)$this->id > 0) {

        } else {

        }*/
    }


    public function display()
    {
        return 'Test';
    }
}