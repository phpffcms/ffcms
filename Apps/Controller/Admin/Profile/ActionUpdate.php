<?php

namespace Apps\Controller\Admin\Profile;

use Apps\ActiveRecord\Profile;
use Apps\Model\Front\Profile\FormSettings as FrontFormSettings;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionUpdate
 * @package Apps\Controller\Admin\Profile
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionUpdate
{
    /**
     * Edit user profile action
     * @param string $id
     * @return null|string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function profileUpdate($id): ?string
    {
        if (!Any::isInt($id) || $id < 1) {
            throw new NotFoundException(__('Wrong profile query id'));
        }

        // get user profile via id
        $profile = Profile::find($id);
        if (!$profile) {
            throw new NotFoundException(__('User profile with id %id% not exist', ['id' => $id]));
        }

        // check if user id for this profile_id is exist
        if (!App::$User->isExist($profile->user_id)) {
            throw new NotFoundException(__('User record not found: %id%', ['id' => $profile->user_id]));
        }

        // initialize settings form and process it
        $model = new FrontFormSettings($profile->user);
        if ($model->send() && $model->validate()) {
            $model->save();
            App::$Session->getFlashBag()->add('success', __('Profile is updated'));
        }

        return $this->view->render('profile/update', [
            'model' => $model,
            'profile' => $profile
        ]);
    }
}
