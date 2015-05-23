<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\Role;
use Apps\ActiveRecord\Wall;
use Apps\Model\Front\WallPost;
use Ffcms\Core\Arch\Controller;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ErrorException;
use Ffcms\Core\Helper\HTML\SimplePagination;


/**
 * Class Profile - user profiles interaction
 * @package Apps\Controller\Front
 */
class Profile extends Controller
{
    public $_self = false;

    public function actionShow($userId)
    {
        // check if target exist
        if (!App::$User->isExist($userId)) {
            $this->title = __('Forbidden!');
            return new ErrorException('This profile is never exist');
        }

        $targetPersone = App::$User->identity($userId); // target user object instance of Apps\Model\Basic\User
        $viewerPersone = App::$User->identity(); // current user object(viewer) instance of Apps\Model\Basic\User

        // check if it a self profile
        $this->_self = ($viewerPersone !== null && $viewerPersone->id === $targetPersone->id);

        $wallModel = null;
        $roles = new Role();
        // if current user is auth - allow to post messages in wall current user
        if (App::$User->isAuth() && $roles->can('global/write')) {
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

        //$wallRecords = Wall::where('target_id', '=', $targetPersone->id)->orderBy('id', 'desc')->skip(5)->take(5)->get();


        $query = $targetPersone->getWall(); // relation hasMany from users to walls
        // pagination and query params
        $wallPage = (int)App::$Request->get('page');
        $wallItems = 5;
        $wallOffset = $wallPage * $wallItems;

        // build pagination
        $wallPagination = new SimplePagination([
            'url' => ['profile/show', $userId],
            'page' => $wallPage,
            'step' => 5,
            'total' => $query->count()
        ]);

        // get wall messages
        $wallRecords = $query->orderBy('id', 'desc')->skip($wallOffset)->take($wallItems)->get();

        $this->response = App::$View->render('show', [
            'user' => $targetPersone,
            'isSelf' => $this->_self,
            'wall' => $wallModel,
            'notify' => App::$Session->getFlashBag()->all(),
            'wallRecords' => $wallRecords,
            'pagination' => $wallPagination->display()
        ]);
    }
}