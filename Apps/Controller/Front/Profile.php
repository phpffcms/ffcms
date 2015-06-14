<?php

namespace Apps\Controller\Front;

use Apps\Model\Front\AvatarUpload;
use Apps\Model\Front\WallPost;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;
use Apps\Model\Basic\Profile as ProfileRecords;


/**
 * Class Profile - user profiles interaction
 * @package Apps\Controller\Front
 */
class Profile extends FrontAppController
{
    public $_self = false;

    const ITEM_PER_PAGE = 10;

    public function actionIndex($filter_name, $filter_value)
    {
        $records = null;

        // set current page and offset
        $page = (int)App::$Request->query->get('page');
        $offset = $page * self::ITEM_PER_PAGE;

        switch ($filter_name) {
            case 'rating': // rating list, order by rating DESC
                $records = (new ProfileRecords())->orderBy('rating', 'DESC');
                break;
            case 'hobby': // search by hobby
                if ($filter_value === null || String::length($filter_value) < 1) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('hobby', 'like', '%' . $filter_value . '%');
                break;
            case 'city':
                if ($filter_value === null || String::length($filter_value) < 1) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('city', '=', $filter_value);
                break;
            case 'born':
                if ($filter_value === null || !Object::isLikeInt($filter_value)) {
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
            'step' => self::ITEM_PER_PAGE,
            'total' => $records->count()
        ]);

        $this->response = App::$View->render('index', [
            'records' => $records->skip($offset)->take(self::ITEM_PER_PAGE)->get(),
            'pagination' => $pagination,
            'id' => $filter_name,
            'add' => $filter_value
        ]);
    }

    /**
     * Show user profile: data, wall posts, other features
     * @param $userId
     * @throws NotFoundException
     */
    public function actionShow($userId)
    {
        // check if target exists
        if (!App::$User->isExist($userId)) {
            throw new NotFoundException('This profile is not exist');
        }

        $targetPersone = App::$User->identity($userId); // target user object instance of Apps\Model\Basic\User
        $viewerPersone = App::$User->identity(); // current user object(viewer) instance of Apps\Model\Basic\User

        // check if it a self profile
        $this->_self = ($viewerPersone !== null && $viewerPersone->id === $targetPersone->id);

        $wallModel = null;
        // if current user is auth - allow to post messages on wall current user
        if (App::$User->isAuth() && $viewerPersone->getRole()->can('global/write')) {
            $wallModel = new WallPost();
            // check if request post is done and rules validated
            if ($wallModel->send() && $wallModel->validate()) {
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
            'wall' => !Object::isObject($wallModel) ? null : $wallModel->export(),
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
}