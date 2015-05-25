<?php

namespace Extend\Core\Arch;

use Ffcms\Core\Arch\Controller;
use Ffcms\Core\App;

class AdminController extends Controller
{

    // make check's
    public function __construct()
    {
        $user = App::$User->identity();
        // user is not authed ?
        if ($user === null || !App::$User->isAuth()) {
            $redirectUrl = App::$Alias->scriptUrl . '/user/login';
            App::$Response->redirect($redirectUrl, true);
            exit();
        }

        if (!$user->getRole()->can('global/all')) {
            App::$Session->start();
            App::$Session->invalidate();

            $redirectUrl = App::$Alias->scriptUrl . '/user/login';
            App::$Response->redirect($redirectUrl, true);
            exit();
        }

        parent::__construct();
    }
}