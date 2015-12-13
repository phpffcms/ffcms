<?php

namespace Extend\Core\Arch;

use Ffcms\Core\Arch\Controller;
use Ffcms\Core\App;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

class AdminAppController extends Controller
{

    public $applications;
    public $application;

    // make check's
    public function __construct()
    {
        $this->checkAccess();
        $this->buildApps();

        parent::__construct();
    }

    /**
     * Check if current user can access to admin controllers
     */
    private function checkAccess()
    {
        $user = App::$User->identity();
        // user is not authed ?
        if ($user === null || !App::$User->isAuth()) {
            $redirectUrl = App::$Alias->scriptUrl . '/user/login';
            App::$Response->redirect($redirectUrl, true);
            exit();
        }

        $permission = env_name . '/' . App::$Request->getController() . '/' . App::$Request->getAction();

        // doesn't have permission? get the f*ck out
        if (!$user->getRole()->can($permission)) {
            App::$Session->invalidate();

            $redirectUrl = App::$Alias->scriptUrl . '/user/login';
            App::$Response->redirect($redirectUrl, true);
            exit();
        }
    }

    /**
     * Build application list to memory object
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    private function buildApps()
    {
        $this->applications = \Apps\ActiveRecord\App::getAllByType('app');
        $cname = Str::lastIn(get_class($this), '\\', true);
        foreach ($this->applications as $app) {
            if ($app->sys_name === $cname) {
                $this->application = $app;
            }
        }
    }

    /**
     * Get current application configs as array
     * @return array
     */
    public function getConfigs()
    {
        return (array)unserialize($this->application->configs);
    }

    /**
     * Save application configs
     * @param array $configs
     * @return bool
     */
    public function setConfigs(array $configs = null)
    {
        if ($configs === null || !Obj::isArray($configs) || count($configs) < 1) {
            return false;
        }

        $serialized = serialize($configs);

        $obj = \Apps\ActiveRecord\App::find($this->application->id);

        if ($obj === null) {
            return false;
        }

        $obj->configs = $serialized;
        $obj->save();
        return true;

    }
}