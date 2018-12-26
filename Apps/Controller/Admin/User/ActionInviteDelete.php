<?php

namespace Apps\Controller\Admin\User;

use Apps\ActiveRecord\Invite;
use Apps\Model\Admin\User\FormInviteDelete;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;


/**
 * Trait ActionInviteDelete
 * @package Apps\Controller\Admin\User
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionInviteDelete
{
    /**
     * Render invite delete action
     * @return string|null
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function inviteDelete($id): ?string
    {
        $record = Invite::find($id);
        if (!$record) {
            throw new NotFoundException(__('Invite with id %id% not exist', ['id' => $id]));
        }

        $model = new FormInviteDelete($record);
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Session->getFlashBag()->add('success', __('Invite successful removed'));
            $this->response->redirect('user/invitelist');
        }

        return $this->view->render('user/invite_delete', [
            'record' => $record,
            'model' => $model
        ]);
    }
}