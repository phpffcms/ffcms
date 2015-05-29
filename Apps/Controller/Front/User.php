<?php

namespace Apps\Controller\Front;

use Apps\Model\Front\RegisterForm;
use Ffcms\Core\Arch\Controller;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ErrorException;
use Apps\Model\Front\LoginForm;
use Ffcms\Core\Exception\ForbiddenException;

/**
 * Class User - standard user controller: login/signup/logout/etc
 * @package Apps\Controller\Front
 */
class User extends Controller
{

    /**
     * View login form and process submit action
     */
    public function actionLogin()
    {
        if (App::$User->isAuth()) { // always auth? get the f*ck out
            throw new ForbiddenException();
        }

        $loginForm = new LoginForm();

        if ($loginForm->isPostSubmit() && $loginForm->validateRules()) {
            if ($loginForm->tryAuth()) {
                App::$Response->redirect('/'); // void header change & exit()
            }
            App::$Session->start();
            App::$Session->getFlashBag()->add('error', __('User is never exist or password is incorrect!'));
        }


        $this->response = App::$View->render('login', [
            'model' => $loginForm,
            'notify' => App::$Session->getFlashBag()->all()
        ]);
    }

    /**
     * View register form and process submit action
     */
    public function actionSignup()
    {
        if (App::$User->isAuth()) { // always auth? prevent any actions
            throw new ForbiddenException();
        }

        $registerForm = new RegisterForm();

        if ($registerForm->isPostSubmit() && $registerForm->validateRules()) {
            App::$Session->start();
            if ($registerForm->tryRegister()) {
                App::$Session->getFlashBag()->add('success', __('Your account is successful registered!'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Login or email is always used on website'));
            }
        }

        $this->response = App::$View->render('signup', [
            'model' => $registerForm,
            'notify' => App::$Session->getFlashBag()->all()
        ]);
    }

    /**
     * Make logout if user is signIn
     */
    public function actionLogout()
    {
        if (!App::$User->isAuth()) { // not auth? what you wanna?
            throw new ForbiddenException();
        }

        App::$Session->invalidate();

        App::$Response->redirect('/');
    }
}