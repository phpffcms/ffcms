<?php

namespace Apps\Controller\Front\User;

use Apps\Model\Front\User\FormLogin;
use Apps\Model\Front\User\FormSocialAuth;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionSocialAuth
 * @package Apps\Controller\Front\User
 * @property View $view
 * @property Response $response
 * @property Request $request
 * @method array getConfigs()
 */
trait ActionSocialAuth
{

    /**
     * Authorization in social networks over hybridauth layer. How its work:
     *  1. User visit actionSocialauth and initialize openid instance
     *  2. 3rd party software generate redirect to @api -> User::actionEndpoint() (as endpoint) where create hash's, tokens and other shit
     *  3. After successful auth on service user redirect back to actionSocialauth and we can work with $userIdentity if no exceptions catched.
     * Don't aks me "why did you do this sh@t"? I want to make container in User class, but this shit work only on direct call on endpoint.
     * @param string $provider
     * @return string
     * @throws ForbiddenException
     * @throws SyntaxException
     */
    public function socialauth(string $provider)
    {
        // get hybridauth instance
        /** @var \Hybrid_Auth $instance */
        $instance = App::$User->getOpenidInstance();
        if (!$instance) {
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
        if (!$userIdentity || Str::likeEmpty($userIdentity->identifier)) {
            throw new ForbiddenException(__('User data not provided!'));
        }

        // initialize model and pass user identity
        $model = new FormSocialAuth($provider, $userIdentity);
        // check if user is always registered
        if ($model->identityExists()) {
            $model->makeAuth();
            $this->response->redirect('/');
            return null;
        }
        // its a new identify, check if finish register form is submited
        if ($model->send() && $model->validate()) {
            if ($model->tryRegister()) {
                // registration is completed, lets open new session
                $loginModel = new FormLogin();
                $loginModel->openSession($model->_userObject);
                $this->response->redirect('/'); // session is opened, refresh page
            } else { // something gonna wrong, lets notify user
                App::$Session->getFlashBag()->add('error', __('Email is always used on website'));
            }
        }

        // render output view
        return $this->view->render('user/social_signup', [
            'model' => $model
        ]);
    }
}
