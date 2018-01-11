<?php

namespace Apps\Controller\Front\User;

use Apps\ActiveRecord\User;
use Apps\Model\Front\User\FormLogin;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionApprove
 * @package Apps\Controller\Front\User
 * @property View $view
 * @property Request $request
 * @property Response $response
 */
trait ActionApprove
{

    /**
     * Approve user profile via $email and $token params
     * @param string $email
     * @param string $token
     * @throws ForbiddenException
     */
    public function approve($email, $token)
    {
        // validate token length and email format
        if (App::$User->isAuth() || Str::length($token) < 32 || !Str::isEmail($email)) {
            throw new ForbiddenException(__('Wrong recovery data'));
        }

        // lets find token&email
        /** @var User $user */
        $user = App::$User->where('approve_token', $token)
            ->where('email', '=', $email)
            ->first();

        // check if record is exist by token and email
        if (!$user) {
            throw new ForbiddenException();
        }

        // update approve_token value to confirmed
        $user->approve_token = '0';
        $user->save();

        // open session and redirect to main
        $loginModel = new FormLogin();
        $loginModel->openSession($user);
        $this->response->redirect('/'); // session is opened, refresh page
    }
}
