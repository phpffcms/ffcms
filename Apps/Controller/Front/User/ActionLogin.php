<?php


namespace Apps\Controller\Front\User;

use Apps\Model\Front\User\FormLogin;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionLogin
 * @package Apps\Controller\Front\User
 * @property View $view
 * @property Response $response
 * @property Request $request
 * @method array getConfigs
 */
trait ActionLogin
{
    /**
     * View login form and process submit action
     * @throws ForbiddenException
     * @throws SyntaxException
     */
    public function login(): ?string
    {
        // check if user is always authorized
        if (App::$User->isAuth()) {
            throw new ForbiddenException(__('You are always authorized on website'));
        }

        $configs = $this->getConfigs();
        // load login model
        $loginForm = new FormLogin((bool)$configs['captchaOnLogin']);

        // build redirect back route
        $redirectRoute = '/';
        if ($this->request->query->has('r') && !preg_match('/[^A-Za-z0-9\/]/i', $this->request->query->get('r'))) {
            $redirectRoute = $this->request->query->get('r');
        }

        // check if data is send and valid
        if ($loginForm->send() && $loginForm->validate()) {
            if ($loginForm->tryAuth()) {
                // initialize success event
                App::$Event->run(static::EVENT_USER_LOGIN_SUCCESS, [
                    'model' => $loginForm
                ]);
                $this->response->redirect($redirectRoute); // void header change & exit()
            }
            App::$Session->getFlashBag()->add('error', __('User is never exist or password is incorrect!'));
            // initialize fail event
            App::$Event->run(static::EVENT_USER_LOGIN_FAIL, [
                'model' => $loginForm
            ]);
        }

        // render view
        return $this->view->render('login', [
            'model' => $loginForm,
            'useCaptcha' => $configs['captchaOnLogin'] === 1,
            'redirect' => $redirectRoute
        ]);
    }
}
