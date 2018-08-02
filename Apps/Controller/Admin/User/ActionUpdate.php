<?php

namespace Apps\Controller\Admin\User;

use Apps\Model\Admin\User\FormUserUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\User as UserRecord;

/**
 * Trait ActionUpdate
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionUpdate
{
    /**
     * Edit user profile by id
     * @param string $id
     * @return string
     * @throws SyntaxException
     */
    public function update(?string $id = null): ?string
    {
        $user = UserRecord::findOrNew($id);
        // generate model data based on user object
        $model = new FormUserUpdate($user);

        // check is form is sended
        if ($model->send()) {
            if ($model->validate()) { // check validation
                $model->save();
                App::$Session->getFlashBag()->add('success', __('Data was successful updated'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render viewer
        return $this->view->render('user/user_update', [
            'model' => $model
        ]);
    }
}
