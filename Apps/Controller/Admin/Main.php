<?php

namespace Apps\Controller\Admin;

use Apps\Model\Admin\SettingsForm;
use Extend\Core\Arch\AdminController;
use Ffcms\Core\App;

class Main extends AdminController
{

    /**
     * Index page of admin dashboard
     */
    public function actionIndex()
    {
        $this->response = App::$View->render('index', [

        ]);
    }

    public function actionSettings()
    {
        $model = new SettingsForm();

        if ($model->isPostSubmit()) {
            if ($model->validateRules()) {
                $model->makeSave();
            } else {
                App::$Session->getFlashBag()->add('error', 'Validation of form data is failed!');
            }
        }

        $this->response = App::$View->render('settings', [
            'model' => $model
        ]);
    }
}