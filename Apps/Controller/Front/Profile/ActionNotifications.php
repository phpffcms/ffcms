<?php

namespace Apps\Controller\Front\Profile;

use Apps\ActiveRecord\UserNotification;
use Apps\Model\Front\Profile\EntityNotificationsList;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionNotifications
 * @package Apps\Controller\Front\Profile
 * @property View $view
 * @property Request $request
 * @property Response $response
 */
trait ActionNotifications
{
    /**
     * Show user notifications
     * @param string $type
     * @return string
     * @throws ForbiddenException
     */
    public function notifications(?string $type = 'all'): ?string
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get page index and current user object
        $page = (int)$this->request->query->get('page', 0);
        $offset = $page * static::NOTIFY_PER_PAGE;
        $user = App::$User->identity();

        // try to find notifications in database as active record
        $query = UserNotification::where('user_id', '=', $user->id)
            ->orderBy('created_at', 'DESC');
        if ($type === 'unread') {
            $query = $query->where('readed', '=', 0);
        }

        // get total row count for pagination
        $totalCount = $query->count();

        // get current records as object and build response
        $records = $query->skip($offset)->take(static::NOTIFY_PER_PAGE);
        $data = $records->get();
        $model = new EntityNotificationsList($data);
        $model->make();

        // update reader records
        $records->update(['readed' => 1]);

        return $this->view->render('profile/notifications', [
            'model' => $model,
            'pagination' => [
                'page' => $page,
                'step' => static::NOTIFY_PER_PAGE,
                'total' => $totalCount
            ]
        ]);
    }
}
