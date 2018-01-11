<?php

namespace Apps\Controller\Front\Profile;

use Apps\ActiveRecord\Blacklist;
use Apps\Model\Front\Profile\FormIgnoreDelete;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Url;
use Ffcms\Core\Network\Response;

/**
 * Class ActionUnblock
 * @package Apps\Controller\Front\Profile
 * @property Response $response
 * @property View $view
 */
trait ActionUnblock
{
    /**
     * Unblock always blocked user
     * @param string $targetId
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws \Exception
     */
    public function unblock($targetId)
    {
        // check if user is auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // check if target is defined
        if (!Any::isInt($targetId) || $targetId < 1 || !App::$User->isExist($targetId)) {
            throw new NotFoundException();
        }

        $user = App::$User->identity();

        // check if target user in blacklist of current user
        if (!Blacklist::have($user->getId(), $targetId)) {
            throw new NotFoundException();
        }

        $model = new FormIgnoreDelete($user, $targetId);
        if ($model->send() && $model->validate()) {
            $model->make();
            $this->response->redirect(Url::to('profile/ignore'));
        }

        return $this->view->render('unblock', [
            'model' => $model
        ]);
    }
}
