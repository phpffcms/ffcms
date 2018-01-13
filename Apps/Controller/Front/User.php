<?php

namespace Apps\Controller\Front;

use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;

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
    const EVENT_USER_RECOVERY_SUCCESS = 'user.recovery.success';

    use User\ActionLogin {
        login as actionLogin;
    }

    use User\ActionSignup {
        signup as actionSignup;
    }

    use User\ActionSocialAuth {
        socialauth as actionSocialauth;
    }

    use User\ActionRecovery {
        recovery as actionRecovery;
    }

    use User\ActionApprove {
        approve as actionApprove;
    }

    /**
     * Make logout if user is signIn
     * @throws ForbiddenException
     */
    public function actionLogout()
    {
        // check if user authorized
        if (!App::$User->isAuth()) {
            throw new ForbiddenException(__('You are not authorized user'));
        }

        // unset session data
        App::$Session->invalidate();

        // redirect to main
        $this->response->redirect('/');
    }
}
