<?php

namespace Controller\Front;

use Core\Arch\Controller;
use Core\App;
use Core\Exception\ErrorException;
use Model\Front\LoginForm;

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