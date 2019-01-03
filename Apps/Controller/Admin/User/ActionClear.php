<?php

namespace Apps\Controller\Admin\User;


use Apps\ActiveRecord\User;
use Apps\Model\Admin\User\FormUserClear;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionClear
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionClear
{

    /** Cleanup user added data - content, comments, feedback
     * @param string $id
     * @return string|null
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function clear($id): ?string
    {
        // find user object by passed id
        $user = User::with('profile')->find($id);
        if (!$user) {
            throw new NotFoundException(__('User not found'));
        }

        // initialize and process form model
        $model = new FormUserClear($user);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('User input data clear successful'));
            $this->response->redirect('user/index');
        }

        // render output view
        return $this->view->render('user/user_clear', [
            'model' => $model
        ]);
    }
}