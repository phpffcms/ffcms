<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\Invite;
use Apps\Model\Front\RegisterForm;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Apps\Model\Front\LoginForm;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\String;

/**
 * Class User - standard user controller: login/signup/logout/etc
 * @package Apps\Controller\Front
 */
class User extends FrontAppController
{

    /**
     * View login form and process submit action
     * @throws ForbiddenException
     */
    public function actionLogin()
    {
        if (App::$User->isAuth()) { // always auth? get the f*ck out
            throw new ForbiddenException();
        }

        $configs = $this->getConfigs();
        // load login model
        $loginForm = new LoginForm($configs['captchaOnLogin'] === 1);

        // check if data is send and valid
        if ($loginForm->send() && $loginForm->validate()) {
            if ($loginForm->tryAuth()) {
                App::$Response->redirect('/'); // void header change & exit()
            }
            App::$Session->getFlashBag()->add('error', __('User is never exist or password is incorrect!'));
        }

        // render view
        $this->response = App::$View->render('login', [
            'model' => $loginForm->export(),
            'useCaptcha' => $configs['captchaOnLogin'] === 1
        ]);
    }

    /**
     * View register form and process submit action
     * @throws ForbiddenException
     */
    public function actionSignup()
    {
        if (App::$User->isAuth()) { // always auth? prevent any actions
            throw new ForbiddenException();
        }

        // load configs
        $configs = $this->getConfigs();

        // init register model
        $registerForm = new RegisterForm($configs['captchaOnRegister'] === 1);

        // registration based on invite. Check conditions.
        if ($configs['registrationType'] === 0) {
            // get token and email
            $inviteToken = App::$Request->query->get('token');
            $inviteEmail = App::$Request->query->get('email');
            // data sounds like a invalid?
            if (String::length($inviteToken) < 32 || !String::isEmail($inviteEmail)) {
                throw new ForbiddenException(__('Registration allowed only if you have invite!'));
            }
            // remove oldest data
            Invite::clean();
            // try to find token
            $find = Invite::where('token', '=', $inviteToken)
                ->where('email', '=', $inviteEmail)->count();

            // token not foud? invalid invite key
            if ($find !== 1) {
                throw new ForbiddenException(__('Your invite token is invalid! Contact with administrator'));
            }
            // notify the invite token is accepted
            if (!$registerForm->send()) {
                App::$Session->getFlashBag()->add('success', __('Invite was accepted! Continue registration'));
            }

            // set email from token data
            $registerForm->email = $inviteEmail;
        }

        // if register data is send and valid
        if ($registerForm->send() && $registerForm->validate()) {
            if ($registerForm->tryRegister($configs['registrationType'] === 1)) {
                App::$Session->getFlashBag()->add('success', __('Your account is registered. You must confirm account via email'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Login or email is always used on website'));
            }
        }

        // render view
        $this->response = App::$View->render('signup', [
            'model' => $registerForm->export(),
            'config' => $configs,
            'useCaptcha' => $configs['captchaOnRegister'] === 1
        ]);
    }

    /**
     * Make logout if user is signIn
     * @throws ForbiddenException
     */
    public function actionLogout()
    {
        if (!App::$User->isAuth()) { // not auth? what you wanna?
            throw new ForbiddenException();
        }

        // unset session data
        App::$Session->invalidate();

        // redirect to main
        App::$Response->redirect('/');
    }

    public function actionApprove($email, $token)
    {
        // sounds like a not valid token
        if (App::$User->isAuth() || String::length($token) < 32 || !String::isEmail($email)) {
            throw new ForbiddenException();
        }
        // lets find token&email
        $find = App::$User->where('approve_token', '=', $token)
            ->where('email', '=', $email);

        // not found? exit
        if ($find->count() !== 1) {
            throw new ForbiddenException();
        }

        // get row and update approve information
        $user = $find->first();
        $user->approve_token = '0';
        $user->save();

        // open session and redirect to main
        $loginModel = new LoginForm();
        $loginModel->openSession($user);
        App::$Response->redirect('/'); // session is opened, refresh page
    }
}