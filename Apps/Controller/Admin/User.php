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
    const VERSION = '1.0.1';
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

    use User\ActionRoleList {
        listing as actionRolelist;
    }

    use User\ActionRoleUpdate {
        roleUpdate as actionRoleupdate;
    }

    /**
     * User identity settings
     * @return string|null
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSettings(): ?string
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
        return $this->view->render('user/settings', [
            'model' => $model
        ]);
    }
}
