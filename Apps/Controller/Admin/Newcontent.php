<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\Newcontent\FormSettings;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

/**
 * Class Newcontent. Admin controller of new content widget.
 * @package Apps\Controller\Admin
 */
class Newcontent extends AdminController
{
    const VERSION = '1.0.1';

    public $type = 'widget';

    /**
     * Show widget settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function actionIndex(): ?string
    {
        // init settings model
        $model = new FormSettings($this->getConfigs());

        // check if request is submited
        if ($model->send() && $model->validate()) {
            $this->setConfigs($model->getAllProperties());
            App::$Session->getFlashBag()->add('success', __('Settings is successful updated'));
        }

        // render viewer
        return $this->view->render('newcontent/index', [
            'model' => $model
        ]);
    }
}
