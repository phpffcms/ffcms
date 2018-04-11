<?php

namespace Apps\Controller\Front\Profile;

use Apps\ActiveRecord\Blacklist;
use Apps\ActiveRecord\WallPost;
use Apps\Model\Front\Profile\FormWallPost;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionShow
 * @package Apps\Controller\Front\Profile
 * @property View $view
 * @property Response $response
 */
trait ActionShow
{

    /**
     * Show user profile: data, wall posts, other features
     * @param string $userId
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws NotFoundException
     * @throws ForbiddenException
     */
    public function show(string $userId): ?string
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

        // get wall posts by target user_id
        $wallQuery = WallPost::where('target_id', $targetPersone->getId());

        // pagination and query params
        $wallPage = (int)$this->request->query->get('page');
        $wallStep = (int)$cfg['wallPostOnPage'];
        $wallOffset = $wallPage * $wallStep;
        $wallTotalCount = $wallQuery->count();

        // get wall messages as object
        $wallRecords = $wallQuery->with(['senderUser', 'senderUser.profile', 'senderUser.role'])
            ->orderBy('id', 'desc')
            ->skip($wallOffset)
            ->take($wallStep)
            ->get();

        // render output view
        return $this->view->render('profile/show', [
            'user' => $targetPersone,
            'viewer' => $viewerPersone,
            'isSelf' => ($viewerPersone !== null && $viewerPersone->id === $targetPersone->id),
            'wall' => $wallModel,
            'wallRecords' => $wallRecords,
            'pagination' => [
                'step' => $wallStep,
                'total' => $wallTotalCount,
                'page' => $wallPage
            ],
            'ratingOn' => (int)$cfg['rating'] === 1
        ]);
    }
}
