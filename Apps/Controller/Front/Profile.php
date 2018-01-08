<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\Blacklist;
use Apps\ActiveRecord\Profile as ProfileRecords;
use Apps\ActiveRecord\UserLog;
use Apps\ActiveRecord\UserNotification;
use Apps\ActiveRecord\WallPost;
use Apps\Model\Front\Profile\EntityNotificationsList;
use Apps\Model\Front\Profile\FormAvatarUpload;
use Apps\Model\Front\Profile\FormIgnoreAdd;
use Apps\Model\Front\Profile\FormIgnoreDelete;
use Apps\Model\Front\Profile\FormPasswordChange;
use Apps\Model\Front\Profile\FormSettings;
use Apps\Model\Front\Profile\FormUserSearch;
use Apps\Model\Front\Profile\FormWallPost;
use Apps\Model\Front\Profile\FormWallPostDelete;
use Apps\Model\Front\Sitemap\EntityBuildMap;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/**
 * Class Profile. User profiles application front controller
 * @package Apps\Controller\Front
 */
class Profile extends FrontAppController
{
    const BLOCK_PER_PAGE = 10;
    const NOTIFY_PER_PAGE = 25;
    const EVENT_CHANGE_PASSWORD = 'profile.changepassword.success';

    /**
     * List user profiles on website by defined filter
     * @param string $name
     * @param null|string|int $value
     * @return string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex($name, $value = null)
    {
        $records = null;

        // set current page and offset
        $page = (int)$this->request->query->get('page', 0);
        $cfgs = $this->application->configs;
        $userPerPage = (int)$cfgs['usersOnPage'];
        if ($userPerPage < 1) {
            $userPerPage = 1;
        }
        $offset = $page * $userPerPage;

        switch ($name) {
            case 'rating': // rating list, order by rating DESC
                // check if rating is enabled
                if ((int)$cfgs['rating'] !== 1) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->orderBy('rating', 'DESC');
                break;
            case 'hobby': // search by hobby
                if (Str::likeEmpty($value)) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('hobby', 'like', '%' . $value . '%');
                break;
            case 'city':
                if (Str::likeEmpty($value)) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('city', $value);
                break;
            case 'born':
                if ($value === null || !Any::isInt($value)) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('birthday', 'like', $value . '-%');
                break;
            case 'all':
                $records = (new ProfileRecords())->orderBy('id', 'DESC');
                break;
            default:
                $this->response->redirect('profile/index/all');
                break;
        }

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/index', $name, $value],
            'page' => $page,
            'step' => $userPerPage,
            'total' => $records->count()
        ]);

        // get profile list with relation for user and role tables in 1 query
        $profiles = $records->with(['user', 'user.role'])
            ->skip($offset)
            ->take($userPerPage)
            ->get();

        // render output view
        return $this->view->render('index', [
            'records' => $profiles,
            'pagination' => $pagination,
            'id' => $name,
            'add' => $value,
            'ratingOn' => (int)$cfgs['rating']
        ]);
    }

    /**
     * Show user profile: data, wall posts, other features
     * @param int $userId
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws NotFoundException
     * @throws ForbiddenException
     */
    public function actionShow($userId)
    {
        $cfg = $this->application->configs;
        if (!(bool)$cfg['guestView'] && !App::$User->isAuth()) {
            throw new ForbiddenException(__('You must login to view other profile'));
        }

        // check if target exists
        if (!App::$User->isExist($userId)) {
            throw new NotFoundException(__('This profile is not exist'));
        }

        $targetPersone = App::$User->identity($userId); // target user object instance of Apps\ActiveRecord\User
        $viewerPersone = App::$User->identity(); // current user object(viewer) instance of Apps\ActiveRecord\User

        $wallModel = null;
        // if current user is auth - allow to post messages on wall current user
        if (App::$User->isAuth() && $viewerPersone->role->can('global/write')) {
            $wallModel = new FormWallPost();
            // check if request post is done and rules validated
            if ($wallModel->send() && $wallModel->validate()) {
                // maybe in blacklist?
                if (!Blacklist::check($viewerPersone->getId(), $targetPersone->getId())) {
                    App::$Session->getFlashBag()->add('error', __('This user are in your black list or you are in blacklist!'));
                } else {
                    // check if message added
                    if ($wallModel->makePost($targetPersone, $viewerPersone, (int)$cfg['delayBetweenPost'])) {
                        App::$Session->getFlashBag()->add('success', __('The message was successful posted!'));
                    } else {
                        App::$Session->getFlashBag()->add('warning', __('Posting message was failed! Please, wait few seconds'));
                    }
                }
            }
        }

        // pagination and query params
        $wallPage = (int)$this->request->query->get('page');
        $wallItems = (int)$cfg['wallPostOnPage'];
        $wallOffset = $wallPage * $wallItems;

        // get wall posts by target user_id
        $wallQuery = WallPost::where('target_id', $targetPersone->getId());

        // build pagination
        $wallPagination = new SimplePagination([
            'url' => ['profile/show', $userId, null],
            'page' => $wallPage,
            'step' => $wallItems,
            'total' => $wallQuery->count()
        ]);

        // get wall messages as object
        $wallRecords = $wallQuery->with(['senderUser', 'senderUser.profile', 'senderUser.role'])
            ->orderBy('id', 'desc')
            ->skip($wallOffset)
            ->take($wallItems)
            ->get();

        // render output view
        return $this->view->render('show', [
            'user' => $targetPersone,
            'viewer' => $viewerPersone,
            'isSelf' => ($viewerPersone !== null && $viewerPersone->id === $targetPersone->id),
            'wall' => $wallModel,
            'notify' => App::$Session->getFlashBag()->all(),
            'wallRecords' => $wallRecords,
            'pagination' => $wallPagination,
            'ratingOn' => (int)$cfg['rating'] === 1
        ]);
    }

    /**
     * Show all users feed activity from wall posts
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionFeed()
    {
        $cfg = $this->application->configs;
        // get pagination page id and calc offset
        $page = (int)$this->request->query->get('page');
        $items = 10;
        if ((int)$cfg['wallPostOnFeed'] >= 1) {
            $items = (int)$cfg['wallPostOnFeed'];
        }

        $offset = $page * $items;

        // total wall posts count
        $query = new WallPost();

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/feed'],
            'page' => $page,
            'step' => $items,
            'total' => $query->count()
        ]);

        // get records from database as object related with User, Role, Profile objects
        $records = $query->with(['senderUser', 'senderUser.role', 'senderUser.profile'])
            ->orderBy('id', 'DESC')
            ->skip($offset)
            ->take($items)
            ->get();

        // render output view
        return $this->view->render('feed', [
            'records' => $records,
            'pagination' => $pagination
        ]);
    }

    /**
     * User avatar management
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function actionAvatar()
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('You must be authorized user!');
        }

        // get user identity and model object
        $user = App::$User->identity();
        $model = new FormAvatarUpload(true);

        // validate model post data
        if ($model->send()) {
            if ($model->validate()) {
                $model->copyFile($user);
                App::$Session->getFlashBag()->add('success', __('Avatar is successful changed'));
            } else {
                App::$Session->getFlashBag()->add('error', __('File upload is failed!'));
            }
        }

        return $this->view->render('avatar', [
            'user' => $user,
            'model' => $model
        ]);
    }

    /**
     * Allow post owners and targets delete
     * @param int $postId
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function actionWalldelete($postId)
    {
        // is user auth?
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // is postId is integer?
        if (!Any::isInt($postId) || $postId < 1) {
            throw new NotFoundException();
        }

        // try to find the wall post
        /** @var WallPost $wallPost */
        $wallPost = WallPost::find($postId);
        if (!$wallPost) {
            throw new NotFoundException();
        }

        // get user and check if he can delete this post
        $user = App::$User->identity();
        if ($wallPost->sender_id !== $user->id && $wallPost->target_id !== $user->id) {
            throw new ForbiddenException();
        }

        // check if submit sended
        $wallModel = new FormWallPostDelete($wallPost);
        if ($wallModel->send() && $wallModel->validate()) {
            $wallModel->make();
            $this->response->redirect('profile/show/' . $wallPost->target_id);
        }

        return $this->view->render('wall_delete', [
            'post' => $wallPost,
            'model' => $wallModel
        ]);
    }

    /**
     * Show user messages (based on ajax, all in template)
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     */
    public function actionMessages()
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        return $this->view->render('messages');
    }

    /**
     * Show user notifications
     * @param string $type
     * @return string
     * @throws ForbiddenException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionNotifications($type = 'all')
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

        $pagination = new SimplePagination([
            'url' => ['profile/notifications'],
            'page' => $page,
            'step' => static::NOTIFY_PER_PAGE,
            'total' => $query->count()
        ]);

        // get current records as object and build response
        $records = $query->skip($offset)->take(static::NOTIFY_PER_PAGE);
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

    /**
     * User profile settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function actionSettings()
    {
        // check if auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get user object
        $user = App::$User->identity();
        // create model and pass user object
        $model = new FormSettings($user);

        // check if form is submited
        if ($model->send() && $model->validate()) {
            $model->save();
            App::$Session->getFlashBag()->add('success', __('Profile data are successful updated'));
        }

        // render view
        return $this->view->render('settings', [
            'model' => $model
        ]);
    }

    /**
     * Action change user password
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function actionPassword()
    {
        // check if user is authed
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get user object and create model with user object
        $user = App::$User->identity();
        $model = new FormPasswordChange($user);

        // check if form is submited and validation is passed
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Event->run(static::EVENT_CHANGE_PASSWORD, [
                'model' => $model
            ]);

            App::$Session->getFlashBag()->add('success', __('Password is successful changed'));
        }

        // set response output
        return $this->view->render('password', [
            'model' => $model
        ]);
    }

    /**
     * Show users in blacklist and allow add new users
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     */
    public function actionIgnore()
    {
        // check if not auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get user object and init model of it
        $user = App::$User->identity();
        $model = new FormIgnoreAdd($user);

        // set user id from ?id= get param if form not sended
        if (!$model->send()) {
            $uid = (int)$this->request->query->get('id');
            if ($uid > 0) {
                $model->id = $uid;
            }
        }

        // sended new block add?
        if ($model->send() && $model->validate()) {
            if ($model->save()) {
                App::$Session->getFlashBag()->add('success', __('User was successful blocked!'));
            } else {
                App::$Session->getFlashBag()->add('error', __('This user is always in ignore list'));
            }
        }

        // get blocked users
        $query = Blacklist::where('user_id', '=', $user->getId());

        $page = (int)$this->request->query->get('page');
        $offset = $page * self::BLOCK_PER_PAGE;

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/ignore'],
            'page' => $page,
            'step' => self::BLOCK_PER_PAGE,
            'total' => $query->count()
        ]);

        // get records as object
        $records = $query->with(['targetUser', 'targetUser.profile'])
            ->skip($offset)
            ->take(self::BLOCK_PER_PAGE)
            ->get();

        // render output view
        return $this->view->render('ignore', [
            'records' => $records,
            'model' => $model,
            'pagination' => $pagination
        ]);
    }

    /**
     * Show user logs
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     */
    public function actionLog()
    {
        // check if user is authorized
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get log records
        $records = UserLog::where('user_id', App::$User->identity()->getId());
        if ($records->count() > 0) {
            $records = $records->orderBy('id', 'DESC');
        }

        // render output view
        return $this->view->render('log', [
            'records' => $records
        ]);
    }

    /**
     * Unblock always blocked user
     * @param string $targetId
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function actionUnblock($targetId)
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

    /**
     * Search users
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws \Ffcms\Core\Exception\NativeException
     */
    public function actionSearch()
    {
        // create model object
        $model = new FormUserSearch();
        $model->setSubmitMethod('GET');

        // get app configs
        $cfgs = $this->getConfigs();

        $records = null;
        $pagination = null;
        // check if request is sended
        if ($model->send() && $model->validate()) {
            // get records from db
            $records = ProfileRecords::where('nick', 'like', '%' . $model->query . '%');
            $page = (int)$this->request->query->get('page');
            $userPerPage = (int)$cfgs['usersOnPage'];
            if ($userPerPage < 1) {
                $userPerPage = 1;
            }

            $offset = $page * $userPerPage;
            // build pagination
            $pagination = new SimplePagination([
                'url' => ['profile/search', null, null, [$model->getFormName().'[query]' => $model->query, $model->getFormName().'[submit]' => true]],
                'page' => $page,
                'step' => $userPerPage,
                'total' => $records->count()
            ]);
            // make query finally
            $records = $records->skip($offset)
                ->take($userPerPage)
                ->get();
        }

        // display response
        return $this->view->render('search', [
            'model' => $model,
            'records' => $records,
            'pagination' => $pagination,
            'ratingOn' => (int)$cfgs['rating']
        ]);
    }

    /**
     * Cron schedule - build user profiles sitemap
     */
    public static function buildSitemapSchedule()
    {
        // get not empty user profiles
        $profiles = ProfileRecords::whereNotNull('nick');
        if ($profiles->count() < 1) {
            return;
        }

        // get languages if multilanguage enabled
        $langs = null;
        if (App::$Properties->get('multiLanguage')) {
            $langs = App::$Properties->get('languages');
        }

        // build sitemap from content items via business model
        $sitemap = new EntityBuildMap($langs);
        foreach ($profiles->get() as $user) {
            $sitemap->add('profile/show/' . $user->user_id, $user->updated_at, 'weekly', 0.2);
        }

        $sitemap->save('profile');
    }

    /**
     * Cleanup tables as scheduled action
     */
    public static function cleanupTablesSchedule()
    {
        // calculate date (now - 1week) for sql query
        $date = (new \DateTime('now'))->modify('-1 week')->format('Y-m-d');
        UserNotification::where('created_at', '<=', $date)->delete();
        UserLog::where('created_at', '<=', $date)->delete();
    }
}
