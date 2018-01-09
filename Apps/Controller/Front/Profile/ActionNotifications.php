<?php
/**
 * Created by PhpStorm.
 * User: zenn1
 * Date: 09.01.2018
 * Time: 21:05
 */

namespace Apps\Controller\Front\Profile;


use Apps\ActiveRecord\UserNotification;
use Apps\Model\Front\Profile\EntityNotificationsList;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Helper\HTML\SimplePagination;
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
    private $_notifyPerPage = 25;
    /**
     * Show user notifications
     * @param string $type
     * @return string
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function notifications($type = 'all')
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get page index and current user object
        $page = (int)$this->request->query->get('page', 0);
        $offset = $page * $this->_notifyPerPage;
        $user = App::$User->identity();

        // try to find notifications in database as active record
        $query = UserNotification::where('user_id', '=', $user->id)
            ->orderBy('created_at', 'DESC');
        if ($type === 'unread') {
            $query = $query->where('readed', '=', 0);
        }

        $pagination = new SimplePagination([
            'url' => ['profile/notifications'],
            'page' => $page,
            'step' => $this->_notifyPerPage,
            'total' => $query->count()
        ]);

        // get current records as object and build response
        $records = $query->skip($offset)->take($this->_notifyPerPage);
        $data = $records->get();
        $model = new EntityNotificationsList($data);
        $model->make();

        // update reader records
        $records->update(['readed' => 1]);

        return $this->view->render('notifications', [
            'model' => $model,
            'pagination' => $pagination
        ]);
    }
}