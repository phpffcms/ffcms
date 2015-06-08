<?php

namespace Extend\Core\Arch;

use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Controller;
use Ffcms\Core\Cache\MemoryObject;
use Ffcms\Core\Exception\ForbiddenException;

class FrontController extends Controller
{

    public function __construct()
    {
        if (!$this->isEnabled()) {
            throw new ForbiddenException('This application is disabled or not installed!');
        }
        parent::__construct();
    }

    public function isEnabled()
    {
        $appName = App::$Request->getController();
        // check if this controller is enabled
        $data = MemoryObject::instance()->get('cache.apps.' . $appName);
        if ($data === null) {
            $data = AppRecord::where('type', '=', 'app')
                ->where('sys_name', '=', $appName)
                ->first();
            if ($data !== null) {
                MemoryObject::instance()->set('cache.apps.' . $appName, $data);
            }
        }

        // not exist? false
        if ($data === null) {
            return false;
        }

        // check if disabled (0 = enabled, anything else = on)
        return $data->disabled === 0;
    }
}