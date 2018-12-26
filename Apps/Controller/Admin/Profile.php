<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Profile\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

/**
 * Class Profile. Admin controller of profile application.
 * @package Apps\Controller\Admin
 */
class Profile extends AdminController
{
    const VERSION = '1.0.1';
    const ITEM_PER_PAGE = 10;

    public $type = 'app';

    /** Import heavy actions */
    use Profile\ActionIndex {
        index as actionIndex;
    }

    use Profile\ActionUpdate {
        profileUpdate as actionUpdate;
    }

    use Profile\ActionFieldList {
        profileFieldList as actionFieldlist;
    }

    use Profile\ActionFieldUpdate {
        profileFieldUpdate as actionFieldupdate;
    }

    use Profile\ActionFieldDelete {
        profileFieldDelete as actionFielddelete;
    }

    /**
     * Show profiles settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionSettings()
    {
        $model = new FormSettings($this->getConfigs());

        if ($model->send()) {
            if ($model->validate()) {
                $this->setConfigs($model->getAllProperties());
                App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
                $this->response->redirect('profile/index');
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        return $this->view->render('profile/settings', [
            'model' => $model
        ]);
    }
}
