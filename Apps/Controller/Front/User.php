<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\Invite;
use Apps\ActiveRecord\UserRecovery;
use Apps\Model\Front\User\FormRecovery;
use Apps\Model\Front\User\FormRegister;
use Apps\Model\Front\User\FormSocialAuth;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Apps\Model\Front\User\FormLogin;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Apps\ActiveRecord\UserLog;

/**
 * Class User - standard user controller: login/signup/logout/etc
 * @package Apps\Controller\Front
 */
class User extends FrontAppController
{
    const EVENT_USER_LOGIN_SUCCESS = 'user.login.success';
    const EVENT_USER_LOGIN_FAIL = 'user.login.fail';
    const EVENT_USER_REGISTER_SUCCESS = 'user.signup.success';
    const EVENT_USER_REGISTER_FAIL = 'user.signup.fail';

    /**
     * View login form and process submit action
     * @throws ForbiddenException
     * @throws NativeException
     * @throws SyntaxException
     */
    public function actionLogin()
    {
        if (App::$User->isAuth()) { // always auth? get the f*ck out
            throw new ForbiddenException();
        }

        $configs = $this->getConfigs();
        // load login model
        $loginForm = new FormLogin($configs['captchaOnLogin'] === 1);

        // check if data is send and valid
        if ($loginForm->send() && $loginForm->validate()) {
            if ($loginForm->tryAuth()) {
                // initialize success event
                App::$Event->run(static::EVENT_USER_LOGIN_SUCCESS, [
                    'model' => $loginForm
                ]);
                App::$Response->redirect('/'); // void header change & exit()
            }
            App::$Session->getFlashBag()->add('error', __('User is never exist or password is incorrect!'));
            // initialize fail event
            App::$Event->run(static::EVENT_USER_LOGIN_FAIL, [
               'model' => $loginForm
            ]);
        }

        // render view
        return App::$View->render('login', [
            'model' => $loginForm->filter(),
            'useCaptcha' => $configs['captchaOnLogin'] === 1
        ]);
    }

    /**
     * Authorization in social networks over hybridauth layer. How its work:
     *  1. User visit actionSocialauth and initialize openid instance
     *  2. 3rd party software generate redirect to @api -> User::actionEndpoint() (as endpoint) where create hash's, tokens and other shit
     *  3. After successful auth on service user redirect back to actionSocialauth and we can work with $userIdentity if no exceptions catched.
     * Don't aks me "why did you do this sh@t"? I want to make container in User class, but this shit work only on direct call on endpoint.
     * @param string $provider
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     * @throws SyntaxException
     */
    public function actionSocialauth($provider)
    {
        // get hybridauth instance
        /** @var \Hybrid_Auth $instance */
        $instance = App::$User->getOpenidInstance();
        if ($instance === null) {
            throw new ForbiddenException(__('OpenID auth is disabled'));
        }

        // try to get user identity data from remove service
        $userIdentity = null;
        try {
            $adapter = $instance->authenticate($provider);
            $userIdentity = $adapter->getUserProfile();
        } catch (\Exception $e) {
            throw new SyntaxException(__('Authorization failed: %e%', ['e' => $e->getMessage()]));
        }

        // check if openid data provided
        if ($userIdentity === null || Str::likeEmpty($userIdentity->identifier)) {
            throw new ForbiddenException(__('User data not provided!'));
        }

        // initialize model and pass user identity
        $model = new FormSocialAuth($provider, $userIdentity);
        // check if user is always registered
        if ($model->identityExists()) {
            $model->makeAuth();
            App::$Response->redirect('/');
            return null;
        }
        // its a new identify, check if finish register form is submited
        if ($model->send() && $model->validate()) {
            if ($model->tryRegister()) {
                // registration is completed, lets open new session
                $loginModel = new FormLogin();
                $loginModel->openSession($model->_userObject);
                App::$Response->redirect('/'); // session is opened, refresh page
            } else { // something gonna wrong, lets notify user
                App::$Session->getFlashBag()->add('error', __('Login or email is always used on website'));
            }
        }

        // render output view
        return App::$View->render('social_signup', [
            'model' => $model
        ]);
    }

    /**
     * View register form and process submit action
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSignup()
    {
        if (App::$User->isAuth()) { // always auth? prevent any actions
            throw new ForbiddenException();
        }

        // load configs
        $configs = $this->getConfigs();

        // init register model
        $registerForm = new FormRegister($configs['captchaOnRegister'] === 1);

        // registration based on invite. Check conditions.
        if ($configs['registrationType'] === 0) {
            // get token and email
            $inviteToken = App::$Request->query->get('token');
            $inviteEmail = App::$Request->query->get('email');
            // data sounds like a invalid?
            if (Str::length($inviteToken) < 32 || !Str::isEmail($inviteEmail)) {
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
            $activation = $configs['registrationType'] === 1;
            if ($registerForm->tryRegister($activation)) {
                // initialize succes signup event
                App::$Event->run(static::EVENT_USER_REGISTER_SUCCESS, [
                   'model' => $registerForm
                ]);
                // if no activation is required - just open session and redirect user to main page
                if (!$activation) {
                    $loginModel = new FormLogin();
                    $loginModel->openSession($registerForm->_userObject);
                    App::$Response->redirect('/'); // session is opened, refresh page
                }
                // send notification of successful registering
                App::$Session->getFlashBag()->add('success', __('Your account is registered. You must confirm account via email'));
            } else {
                // init fail signup event
                App::$Event->run(static::EVENT_USER_REGISTER_FAIL, [
                   'model' => $registerForm
                ]);
                App::$Session->getFlashBag()->add('error', __('Login or email is always used on website'));
            }
        }

        // render view
        return App::$View->render('signup', [
            'model' => $registerForm->filter(),
            'config' => $configs,
            'useCaptcha' => $configs['captchaOnRegister'] === 1
        ]);
    }

    /**
     * Recovery form and recovery submit action
     * @param int|null $id
     * @param string|null $token
     * @return string
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionRecovery($id = null, $token = null)
    {
        if (App::$User->isAuth()) { // always auth? prevent any actions
            throw new ForbiddenException();
        }

        // is recovery submit?
        if (Obj::isLikeInt($id) && Str::length($token) >= 64) {
            $rObject = UserRecovery::where('id', '=', $id)
                ->where('token', '=', $token)
                ->where('archive', '=', false);
            // check if recovery row exist
            if ($rObject->count() !== 1) {
                throw new NotFoundException('This recovery data is not found');
            }

            $rData = $rObject->first();
            // check if user with this "user_id" in recovery row exist
            $rUser = App::$User->identity($rData->user_id);
            if ($rUser === null) {
                throw new NotFoundException('User is not found');
            }

            // all is ok, lets set new pwd
            $rUser->password = $rData->password;
            $rUser->save();

            $rData->archive = true;
            $rData->save();

            // add notification
            App::$Session->getFlashBag()->add('success', __('Your account are successful recovered. We recommend you change password'));

            // lets open user session with recovered data
            $loginModel = new FormLogin();
            $loginModel->openSession($rUser);
            App::$Response->redirect('/'); // session is opened, refresh page
        }

        // lets work with recovery form data
        $model = new FormRecovery();
        if ($model->send()) {
            if ($model->validate()) {
                $model->make();
                App::$Session->getFlashBag()->add('success', __('We send to you email with instruction to recovery your account'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render visual form content
        return App::$View->render('recovery', [
            'model' => $model->filter()
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

    /**
     * Approve user profile via $email and $token params
     * @param string $email
     * @param string $token
     * @throws ForbiddenException
     */
    public function actionApprove($email, $token)
    {
        // sounds like a not valid token
        if (App::$User->isAuth() || Str::length($token) < 32 || !Str::isEmail($email)) {
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
        $loginModel = new FormLogin();
        $loginModel->openSession($user);
        App::$Response->redirect('/'); // session is opened, refresh page
    }
}