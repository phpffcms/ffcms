<?php

namespace Extend\Core\Arch;


use Ffcms\Core\Arch\Controller;
use Ffcms\Core\Helper\Type\Str;

class AdminWidgetController extends Controller
{
    public $widgets;
    public $widget;

    public function __construct()
    {
        $this->buildWidgets();
        parent::__construct();
    }

    public function buildWidgets()
    {
        $this->applications = \Apps\ActiveRecord\App::getAllByType('widget');
        $cname = Str::lastIn(get_class($this), '\\', true);
        foreach ($this->widgets as $widget) {
            if ($widget->sys_name === $cname) {
                $this->widget = $widget;
            }
        }
    }
}