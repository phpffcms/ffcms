<?php

namespace Apps\Controller\Front;

use Ffcms\Core\Arch\Controller;
use Extend\Core\App;
use Ffcms\Core\Exception\ErrorException;
use Apps\Model\Front\LoginForm;

class User extends Controller
{

    /**
     * View login form
     */
    public function actionLogin()
    {
        if (App::$User->isAuth()) {
            return new ErrorException('You are always log in');
        }

        $loginForm = new LoginForm();

        if (App::$Request->post('submit') && $loginForm->validateRules()) {
            if ($loginForm->checkData()) {

            }
        }

        $this->response = App::$View->render('login', [
            'model' => $loginForm
        ]);
    }
}