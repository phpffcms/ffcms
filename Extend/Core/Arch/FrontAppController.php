<?php

namespace Extend\Core\Arch;

use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Controller;
use Ffcms\Core\Cache\MemoryObject;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Object;

class FrontAppController extends Controller
{
    // information about application from table apps
    public $application;
    private $configs;

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
        $this->application = MemoryObject::instance()->get('cache.apps.' . $appName);
        if ($this->application === null) {
            $this->application = AppRecord::where('type', '=', 'app')
                ->where('sys_name', '=', $appName)
                ->first();
            if ($this->application !== null) {
                MemoryObject::instance()->set('cache.apps.' . $appName, $this->application);
            }
        }

        // not exist? false
        if ($this->application === null) {
            return false;
        }

        // check if disabled (0 = enabled, anything else = on)
        return $this->application->disabled === 0;
    }

    /**
     * Get current application configs as array
     * @return array
     */
    public function getConfigs()
    {
        if ($this->configs !== null) {
            return $this->configs;
        }
        $configs = (array)unserialize($this->application->configs); // data always stored like a "string" objects
        foreach ($configs as $cfg => $value) {
            if (Object::isLikeInt($value)) {
                $configs[$cfg] = (int)$value; // convert string 1 "1" to int 1 1
            }
        }
        $this->configs = $configs;

        return $this->configs;
    }
}