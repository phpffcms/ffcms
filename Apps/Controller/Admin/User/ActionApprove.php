<?php

namespace Apps\Controller\Admin\User;


use Apps\ActiveRecord\User;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionApprove
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionApprove
{
    /**
     * Approve user and redirect to user list
     * @param string $id
     * @return void
     * @throws NotFoundException
     */
    public function approve($id)
    {
        /** @var User $record */
        $record = User::where('id', $id)
            ->whereNotNull('approve_token')
            ->first();

        if (!$record) {
            throw new NotFoundException(__('User not found'));
        }

        $record->approve_token = null;
        $record->save();
        $this->response->redirect('user/index');
    }
}