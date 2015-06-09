<?php

namespace Extend\Core\Arch;

use Ffcms\Core\Arch\Controller;
use Ffcms\Core\App;

class AdminController extends Controller
{

    public $applications;

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
            App::$Session->start();
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
    private function buildApps() {
        $this->applications = \Apps\ActiveRecord\App::getAll();
    }
}