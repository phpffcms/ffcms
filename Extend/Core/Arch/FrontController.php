<?php

namespace Extend\Core\Arch;

use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Controller;
use Ffcms\Core\Cache\MemoryObject;

class FrontController extends Controller
{
    public function __construct()
    {
        if (!$this->isEnabled()) {
            //throw new \Exception('Fail');
        }
        parent::__construct();
    }

    public function isEnabled()
    {
        $appName = App::$Request->getController();
        // check if this controller is enabled
        $data = MemoryObject::instance()->get('cache.apps.' . $appName);
        if ($data === null) {
            $data = AppRecord::where('sys_name', '=', $appName)->first();
            //var_dump($data);
        }
        return false;
    }
}