<?php
namespace Extend\Core\Arch;

use Ffcms\Core\App;
use Ffcms\Core\Arch\Widget as NativeWidget;
use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class FrontWidget. Special controller type for front widgets.
 * @package Extend\Core\Arch
 */
class FrontWidget extends NativeWidget
{
    public static $name;

    /**
     * Display widget compiled data.
     * @param array|null $params
     * @return null|string
     * @throws \Exception
     */
    public static function widget(array $params = null)
    {
        // get widget class-namespace callback and single class name
        if (self::$name === null || self::$class === null) {
            self::$class = get_called_class();
            self::$name = Str::lastIn(self::$class, '\\', true);
        }

        $wData = AppRecord::getItem('widget', self::$name);
        // widget is not founded, deny run
        if ($wData === null) {
            if (App::$Debug !== null) {
                App::$Debug->addMessage('Widget with name "' . App::$Security->strip_tags(self::$name) . '"[' . self::$class . '] is not founded!', 'error');
            }
            return null;
        }

        // if widget is disabled - lets return nothing
        if ((int)$wData->disabled === 1) {
            return null;
        }

        // call parent method
        return parent::widget($params);
    }

}