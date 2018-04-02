<?php


namespace Apps\Controller\Front\Profile;

use Apps\Model\Front\Profile\FormPasswordChange;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Network\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait ActionPassword
 * @package Apps\Controller\Front\Profile
 * @property View $view
 * @property Request $request
 * @property Response $response
 * @method array getConfigs
 */
trait ActionPassword
{

    /**
     * Action change user password
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function password(): ?string
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
        return $this->view->render('profile/password', [
            'model' => $model
        ]);
    }
}
