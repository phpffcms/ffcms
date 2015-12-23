<?php

namespace Extend\Core\Arch;

use Extend\Core\Arch\Controller as AbstractController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

class AdminAppController extends AbstractController
{
    protected $applications;
    protected $application;

    /**
     * AdminAppController constructor.
     * @param bool $checkVersion
     * @throws ForbiddenException
     */
    public function __construct($checkVersion = true)
    {
        parent::__construct();

        // build app and check access
        $this->buildApps();
        $this->checkAccess();

        // if version is not necessary to check - continue
        if ($checkVersion === false) {
            return;
        } elseif ($this->application === null) {
            // check if appdata is loaded from db
            throw new ForbiddenException('This application is not installed!');
        }

        // check app version matching
        if (!method_exists($this->application, 'checkVersion') || $this->application->checkVersion() !== true) {
            App::$Session->getFlashBag()->add(
                'error',
                __('Attention! Version of this application scripts is no match to database version. Please, make update!')
            );
        }
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
        // each all applications
        foreach ($this->table as $app) {
            // check if type is mach for current controller abstraction
            if ($app->type === 'app') {
                // add to all type-based list
                $this->applications[] = $app;
                $currentAppName = Str::lastIn(get_class($this), '\\', true);
                // if this row is a current runned controller - set object for fast access
                if ($app->sys_name === $currentAppName) {
                    $this->application = $app;
                }
            }
        }
    }

    /**
     * Get current application data as stdClass object
     * @return object|null
     */
    public function getAppData()
    {
        return $this->application;
    }

    /**
     * Get all applications data as array of objects
     * @return array|null
     */
    public function getAllApps()
    {
        return $this->applications;
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