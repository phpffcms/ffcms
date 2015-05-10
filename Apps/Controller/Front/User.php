<?php

namespace Apps\Controller\Front;

use Apps\Model\Front\RegisterForm;
use Ffcms\Core\Arch\Controller;
use Extend\Core\App;
use Ffcms\Core\Exception\ErrorException;
use Apps\Model\Front\LoginForm;
use Ffcms\Core\Helper\String;

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

        if (App::$Request->get('submit', false) && $loginForm->validateRules()) {
            if ($loginForm->tryAuth()) {
                App::$Response->redirect(String::replace('::', '/', App::$Property->get('siteIndex'))); // void header change & exit()
            }
            App::$Message->set('user/login', 'error', __('User is never exist or password is incorrect!'));
        }

        $this->response = App::$View->render('login', [
            'model' => $loginForm
        ]);
    }

    public function actionSignup()
    {
        if (App::$User->isAuth()) {
            return new ErrorException('You are always log in');
        }

        $registerForm = new RegisterForm();

        if (App::$Request->get('submit', false) && $registerForm->validateRules()) {
            if ($registerForm->tryRegister()) {
                App::$Message->set('user/signup', 'success', __('Your account is successful registered!'));
            } else {
                App::$Message->set('user/signup', 'error', __('Login or email is always used on website'));
            }
        }

        $this->response = App::$View->render('signup', [
            'model' => $registerForm
        ]);
    }
}