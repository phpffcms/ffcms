<?php

namespace Widgets\Front\Test;

use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Arch\Widget as AbstractWidget;


class Widget extends AbstractWidget
{
    public $message;

    public function init()
    {
        if ($this->message === null)
            $this->message = 'Hello, world';
    }

    public function display()
    {
        return App::$View->render('widget/test', null, __DIR__);
    }
}