<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\Blacklist;
use Apps\Model\Front\Profile\FormAvatarUpload;
use Apps\Model\Front\Profile\FormIgnoreAdd;
use Apps\Model\Front\Profile\FormIgnoreDelete;
use Apps\Model\Front\Profile\FormPasswordChange;
use Apps\Model\Front\Profile\FormSettings;
use Apps\Model\Front\Profile\FormUserSearch;
use Apps\Model\Front\Profile\FormWallPostDelete;
use Apps\Model\Front\Profile\FormWallPost;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;
use Apps\ActiveRecord\Profile as ProfileRecords;
use Ffcms\Core\Helper\Url;


/**
 * Class Profile - user profiles interaction
 * @package Apps\Controller\Front
 */
class Profile extends FrontAppController
{
    const BLOCK_PER_PAGE = 10;

    public function actionIndex($filter_name, $filter_value = null)
    {
        $records = null;

        // set current page and offset
        $page = (int)App::$Request->query->get('page');
        $cfgs = Serialize::decode($this->application->configs);
        $userPerPage = (int)$cfgs['usersOnPage'];
        if ($userPerPage < 1) {
            $userPerPage = 1;
        }
        $offset = $page * $userPerPage;

        switch ($filter_name) {
            case 'rating': // rating list, order by rating DESC
                // check if rating is enabled
                if ((int)$cfgs['rating'] !== 1) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->orderBy('rating', 'DESC');
                break;
            case 'hobby': // search by hobby
                if ($filter_value === null || Str::length($filter_value) < 1) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('hobby', 'like', '%' . $filter_value . '%');
                break;
            case 'city':
                if ($filter_value === null || Str::length($filter_value) < 1) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('city', '=', $filter_value);
                break;
            case 'born':
                if ($filter_value === null || !Obj::isLikeInt($filter_value)) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('birthday', 'like', $filter_value . '-%');
                break;
            case 'all':
                $records = (new ProfileRecords())->orderBy('id', 'DESC');
                break;
            default:
                App::$Response->redirect('profile/index/all');
                break;
        }

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/index', $filter_name, $filter_value],
            'page' => $page,
            'step' => $userPerPage,
            'total' => $records->count()
        ]);

        $this->response = App::$View->render('index', [
            'records' => $records->skip($offset)->take($userPerPage)->get(),
            'pagination' => $pagination,
            'id' => $filter_name,
            'add' => $filter_value,
            'ratingOn' => (int)$cfgs['rating']
        ]);
    }

    /**
     * Show user profile: data, wall posts, other features
     * @param $userId
     * @throws NotFoundException
     * @throws ForbiddenException
     */
    public function actionShow($userId)
    {
        $cfg = Serialize::decode($this->application->configs);
        if ((int)$cfg['guestView'] !== 1 && !App::$User->isAuth()) {
            throw new ForbiddenException('You must be registered user to view other profile');
        }
        // check if target exists
        if (!App::$User->isExist($userId)) {
            throw new NotFoundException('This profile is not exist');
        }

        $targetPersone = App::$User->identity($userId); // target user object instance of Apps\ActiveRecord\User
        $viewerPersone = App::$User->identity(); // current user object(viewer) instance of Apps\ActiveRecord\User

        $wallModel = null;
        // if current user is auth - allow to post messages on wall current user
        if (App::$User->isAuth() && $viewerPersone->getRole()->can('global/write')) {
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
                        App::$Session->getFlashBag()->add('warning', __('Posting message was failed! You need to wait some time...'));
                    }
                }
            }
        }

        $query = $targetPersone->getWall(); // relation hasMany from users to walls
        // pagination and query params
        $wallPage = (int)App::$Request->query->get('page');
        $wallItems = (int)$cfg['wallPostOnPage'];
        $wallOffset = $wallPage * $wallItems;

        // build pagination
        $wallPagination = new SimplePagination([
            'url' => ['profile/show', $userId, null],
            'page' => $wallPage,
            'step' => $wallItems,
            'total' => $query->count()
        ]);

        // get wall messages
        $wallRecords = $query->orderBy('id', 'desc')->skip($wallOffset)->take($wallItems)->get();

        $this->response = App::$View->render('show', [
            'user' => $targetPersone,
            'viewer' => $viewerPersone,
            'isSelf' => ($viewerPersone !== null && $viewerPersone->id === $targetPersone->id),
            'wall' => !Obj::isObject($wallModel) ? null : $wallModel->export(),
            'notify' => App::$Session->getFlashBag()->all(),
            'wallRecords' => $wallRecords,
            'pagination' => $wallPagination,
            'ratingOn' => (int)$cfg['rating'] === 1
        ]);
    }

    /**
     * User avatar management
     */
    public function actionAvatar()
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('You must be authorized user!');
        }

        // get user identity and model object
        $user = App::$User->identity();
        $model = new FormAvatarUpload();

        // validate model post data
        if ($model->send()) {
            if ($model->validate()) {
                $model->copyFile($user);
                App::$Session->getFlashBag()->add('success', __('Avatar is successful changed'));
            } else {
                App::$Session->getFlashBag()->add('error', __('File upload is failed!'));
            }
        }

        $this->response = App::$View->render('avatar', [
            'user' => $user,
            'model' => $model->export()
        ]);
    }

    /**
     * Allow post owners and targets delete
     * @param int $postId
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
        if (!Obj::isLikeInt($postId) || $postId < 1) {
            throw new NotFoundException();
        }

        // try to find the wall post
        $wallPost = \Apps\ActiveRecord\WallPost::find($postId);
        if (null === $wallPost || false === $wallPost) {
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
            App::$Response->redirect('profile/show/' . $wallPost->target_id);
        }

        $this->response = App::$View->render('wall_delete', [
            'post' => $wallPost,
            'model' => $wallModel->export()
        ]);
    }

    /**
     * Show user messages (based on ajax, all in template)
     * @throws ForbiddenException
     */
    public function actionMessages()
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        $this->response = App::$View->render('messages');
    }

    /**
     * User profile settings
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
        $this->response = App::$View->render('settings', [
            'model' => $model->export()
        ]);
    }

    /**
     * Action change user password
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
            App::$Session->getFlashBag()->add('success', __('Password is successful changed'));
        }

        // set response output
        $this->response = App::$View->render('password', [
            'model' => $model->export()
        ]);
    }

    /**
     * Show users in blacklist and allow add new users
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
            $uid = (int)App::$Request->query->get('id');
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
        $records = Blacklist::where('user_id', '=', $user->getId());

        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::BLOCK_PER_PAGE;

        // build pagination
        $pagination = new SimplePagination([
            'url' => ['profile/ignore'],
            'page' => $page,
            'step' => self::BLOCK_PER_PAGE,
            'total' => $records->count()
        ]);

        $this->response = App::$View->render('ignore', [
            'records' => $records->skip($offset)->take(self::BLOCK_PER_PAGE)->get(),
            'model' => $model->export(),
            'pagination' => $pagination
        ]);
    }

    /**
     * Unblock always blocked user
     * @param string $target_id
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function actionUnblock($target_id)
    {
        // check if user is auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // check if target is defined
        if (!Obj::isLikeInt($target_id) || $target_id < 1 || !App::$User->isExist($target_id)) {
            throw new NotFoundException();
        }

        $user = App::$User->identity();

        // check if target user in blacklist of current user
        if (!Blacklist::have($user->getId(), $target_id)) {
            throw new NotFoundException();
        }

        $model = new FormIgnoreDelete($user, $target_id);

        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Response->redirect(Url::to('profile/ignore'));
        }

        $this->response = App::$View->render('unblock', [
            'model' => $model->export()
        ]);
    }

    /**
     * Search users
     */
    public function actionSearch()
    {
        // create model object
        $model = new FormUserSearch();
        $model->setSubmitMethod('GET');

        // get app configs
        $cfgs = Serialize::decode($this->application->configs);

        $records = null;
        $pagination = null;
        // check if request is sended
        if ($model->send() && $model->validate()) {
            // get records from db
            $records = ProfileRecords::where('nick', 'like', '%' . $model->query . '%');
            $page = (int)App::$Request->query->get('page');
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
            $records = $records->skip($offset)->take($userPerPage)->get();

        }

        // display response
        $this->response = App::$View->render('search', [
            'model' => $model->export(),
            'records' => $records,
            'pagination' => $pagination,
            'ratingOn' => (int)$cfgs['rating']
        ]);
    }
}