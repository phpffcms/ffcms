<?php

namespace Apps\Controller\Front\Profile;

use Apps\ActiveRecord\WallPost;
use Apps\Model\Front\Profile\FormWallPostDelete;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionWallDelete
 * @package Apps\Controller\Front\Profile
 * @property Response $response
 * @property View $view
 */
trait ActionWallDelete
{
    /**
     * Allow post owners and targets delete
     * @param string $postId
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function wallDelete(string $postId): ?string
    {
        // user is auth?
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

        return $this->view->render('profile/wall_delete', [
            'post' => $wallPost,
            'model' => $wallModel
        ]);
    }
}
