<?php

namespace Apps\Controller\Front;

use Ffcms\Core\Arch\Controller;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ErrorException;
/**
 * Class Profile - user profiles interaction
 * @package Apps\Controller\Front
 */
class Profile extends Controller
{
    public $_self = false;

    public function actionShow($userId)
    {
        $userId = (int)$userId;
        if ($userId < 1 || !App::$User->isExist($userId)) {
            $this->title = __('Forbidden!');
            return new ErrorException('This profile is never exist');
        }

        // if it a self profile
        $this->_self = (App::$User->isAuth() && App::$User->get('id') === $userId);

        $this->response = App::$View->render('show', [
            'user' => App::$User->getPerson($userId)
        ]);
    }
}