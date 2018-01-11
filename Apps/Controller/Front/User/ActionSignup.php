<?php

namespace Apps\Controller\Front\User;

use Apps\ActiveRecord\Invite;
use Apps\Model\Front\User\FormLogin;
use Apps\Model\Front\User\FormRegister;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSignup
 * @package Apps\Controller\Front\User
 * @property View $view
 * @property Response $response
 * @property Request $request
 * @method array getConfigs()
 */
trait ActionSignup
{

    /**
     * View register form and process submit action
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function signup(): ?string
    {
        // check if user is authorized
        if (App::$User->isAuth()) {
            throw new ForbiddenException(__('You are always authorized on website, registration not allowed'));
        }

        // load configs
        $configs = $this->getConfigs();

        // init register model
        $registerForm = new FormRegister($configs['captchaOnRegister'] === 1);

        // registration based on invite. Check conditions.
        if ($configs['registrationType'] === 0) {
            // get token and email
            $inviteToken = $this->request->query->get('token');
            $inviteEmail = $this->request->query->get('email');
            // check if token length & email is valid format
            if (Str::length($inviteToken) < 32 || !Str::isEmail($inviteEmail)) {
                throw new ForbiddenException(__('Registration allowed only if you have invite!'));
            }

            // remove deprecated data
            Invite::clean();
            // try to find token
            $find = Invite::where('token', $inviteToken)
                ->where('email', $inviteEmail)
                ->count();

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
                    $this->response->redirect('/'); // session is opened, refresh page
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
        return $this->view->render('signup', [
            'model' => $registerForm,
            'config' => $configs,
            'useCaptcha' => $configs['captchaOnRegister'] === 1
        ]);
    }
}
