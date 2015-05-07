<?php

namespace Extend\Core;

use Ffcms\Core\Exception\NativeException;

class App extends \Ffcms\Core\App
{

    /** @var  \Apps\Model\Basic\User */
    public static $User;

    /** @var \Ffcms\Core\Session\DefaultSession */
    public static $Session;


    public static function build()
    {
        // run core builds
        parent::build();
        $cfgPath = root . '/Private/Config/Object.php';
        if (!file_exists($cfgPath)) {
            new NativeException('Object config initializer is not founded in: /Private/Config/Object.php');
        }
        $objectConfig = include_once($cfgPath);
        self::$User = $objectConfig['User'];
        self::$Session = $objectConfig['Session'];

    }
}