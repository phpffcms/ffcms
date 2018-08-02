<?php

namespace Apps\Controller\Admin\User;

use Apps\Model\Admin\User\FormInviteSend;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionInvite
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionInviteSend
{
    /**
     * Send invite to user by email
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function inviteSend(): ?string
    {
        // init model
        $model = new FormInviteSend();

        if ($model->send()) {
            if ($model->validate()) {
                if ($model->make()) {
                    App::$Session->getFlashBag()->add('success', __('Invite was successful send!'));
                } else {
                    App::$Session->getFlashBag()->add('error', __('Mail server connection is failed!'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return $this->view->render('user/invite', [
            'model' => $model
        ]);
    }
}
