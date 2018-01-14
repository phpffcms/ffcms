<?php

namespace Apps\Controller\Admin;

use Apps\ActiveRecord\Role;
use Apps\Model\Admin\User\FormUserGroupUpdate;
use Apps\Model\Admin\User\FormUserSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

/**
 * Class User. Admin controller of user application.
 * @package Apps\Controller\Admin
 */
class User extends AdminController
{
    const VERSION = '1.0.0';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    use User\ActionIndex {
        index as actionIndex;
    }

    use User\ActionUpdate {
        update as actionUpdate;
    }

    use User\ActionDelete {
        delete as actionDelete;
    }

    use User\ActionInvite {
        invite as actionInvite;
    }

    /**
     * Show all role groups
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionGrouplist()
    {
        // get all roles
        $roles = Role::all();

        return $this->view->render('group_list', [
            'records' => $roles
        ]);
    }

    /**
     * Edit and add groups
     * @param int $id
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionGroupUpdate($id)
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
        return $this->view->render('group_update', [
            'model' => $model
        ]);
    }

    /**
     * User identity settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSettings()
    {
        // load model and pass property's as argument
        $model = new FormUserSettings($this->getConfigs());

        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('user/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render view
        return $this->view->render('settings', [
            'model' => $model
        ]);
    }
}
