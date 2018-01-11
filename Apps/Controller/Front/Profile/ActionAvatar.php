<?php


namespace Apps\Controller\Front\Profile;

use Apps\Model\Front\Profile\FormAvatarUpload;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionAvatar
 * @package Apps\Controller\Front\Profile
 * @property View $view
 * @property Request $request
 * @property Response $response
 */
trait ActionAvatar
{
    /**
     * User avatar management
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function avatar()
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
}
