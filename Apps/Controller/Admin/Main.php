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

        $this->response = App::$View->render('settings', [
            'model' => $model
        ]);
    }
}