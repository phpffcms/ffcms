<?php

namespace Apps\Controller\Front;

use Apps\Model\Front\AvatarUpload;
use Apps\Model\Front\WallPost;
use Extend\Core\Arch\FrontController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;


/**
 * Class Profile - user profiles interaction
 * @package Apps\Controller\Front
 */
class Profile extends FrontController
{
    public $_self = false;

    /**
     * Show user profile: data, wall posts, other features
     * @param int $userId
     * @throws NotFoundException
     */
    public function actionShow($userId)
    {
        // check if target exist
        if (!App::$User->isExist($userId)) {
            throw new NotFoundException('This profile is not exist');
        }

        $targetPersone = App::$User->identity($userId); // target user object instance of Apps\Model\Basic\User
        $viewerPersone = App::$User->identity(); // current user object(viewer) instance of Apps\Model\Basic\User

        // check if it a self profile
        $this->_self = ($viewerPersone !== null && $viewerPersone->id === $targetPersone->id);

        $wallModel = null;
        // if current user is auth - allow to post messages in wall current user
        if (App::$User->isAuth() && $viewerPersone->getRole()->can('global/write')) {
            $wallModel = new WallPost();
            // check if request post is done and rules validated
            if ($wallModel->isPostSubmit() && $wallModel->validateRules()) {
                // check if message added
                if ($wallModel->makePost($targetPersone, $viewerPersone)) {
                    App::$Session->getFlashBag()->add('success', __('The message was successful posted!'));
                } else {
                    App::$Session->getFlashBag()->add('warning', __('Posting message was failed! You need to wait some time...'));
                }
            }
        }

        $query = $targetPersone->getWall(); // relation hasMany from users to walls
        // pagination and query params
        $wallPage = (int)App::$Request->query->get('page');
        $wallItems = 5;
        $wallOffset = $wallPage * $wallItems;

        // build pagination
        $wallPagination = new SimplePagination([
            'url' => ['profile/show', $userId, null],
            'page' => $wallPage,
            'step' => 5,
            'total' => $query->count()
        ]);

        // get wall messages
        $wallRecords = $query->orderBy('id', 'desc')->skip($wallOffset)->take($wallItems)->get();

        $this->response = App::$View->render('show', [
            'user' => $targetPersone,
            'isSelf' => $this->_self,
            'wall' => $wallModel->export(),
            'notify' => App::$Session->getFlashBag()->all(),
            'wallRecords' => $wallRecords,
            'pagination' => $wallPagination
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
        $model = new AvatarUpload();

        // validate model post data
        if ($model->isPostSubmit()) {
            if ($model->validateRules()) {
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
}