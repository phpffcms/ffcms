<?php

namespace Apps\Controller\Admin\User;

use Apps\ActiveRecord\Role;
use Apps\Model\Admin\User\FormUserGroupUpdate;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionRoleUpdate
 * @package Apps\Controller\Admin\User
 * @property Request $request
 * @property Response $response
 * @property View $view
 */
trait ActionRoleUpdate
{

    /**
     * Update and add user role groups
     * @param int $id
     * @return null|string
     * @throws SyntaxException
     */
    public function roleUpdate($id = 0): ?string
    {
        // find role or create new object
        $role = Role::findOrNew($id);

        $model = new FormUserGroupUpdate($role);
        if ($model->send()) { // work with post request
            if ($model->validate()) {
                $model->save();
                App::$Session->getFlashBag()->add('success', __('Data was successful updated'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return $this->view->render('user/role_update', [
            'model' => $model
        ]);
    }
}
