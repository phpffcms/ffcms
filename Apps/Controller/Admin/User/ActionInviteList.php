<?php

namespace Apps\Controller\Admin\User;

use Apps\ActiveRecord\Invite;
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
trait ActionInviteList
{
    public function inviteList(): ?string
    {
        // init invite object
        $record = new Invite();

        // set current page num and offset
        $page = (int)$this->request->query->get('page', 0);
        $offset = $page * self::ITEM_PER_PAGE;

        // prepare pagination data
        $pagination = [
            'url' => ['user/invitelist'],
            'page' => $page,
            'step' => self::ITEM_PER_PAGE,
            'total' => $record->count()
        ];

        // get invite list
        $records = $record->orderBy('id', 'desc')
            ->skip($offset)
            ->take(self::ITEM_PER_PAGE)
            ->get();

        return $this->view->render('user/invite_list', [
            'records' => $records,
            'pagination' => $pagination,
            'configs' => $this->getConfigs()
        ]);
    }
}
