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

    /**
     * Manage settings in web
     */
    public function actionSettings()
    {
        $model = new SettingsForm();

        if ($model->isPostSubmit()) {
            if ($model->validateRules()) {
                if ($model->makeSave()) {
                    // show message about successful save and take system some time ;)
                    $this->response = App::$View->render('settings_save');
                    return;
                } else {
                    App::$Session->getFlashBag()->add('error', __('Configuration file is not writable! Check /Private/Config/ dir and files'));
                }
            } else {
                App::$Session->getFlashBag()->add('error', __('Validation of form data is failed!'));
            }
        }

        $this->response = App::$View->render('settings', [
            'model' => $model // no $model->export() there
        ]);
    }

    /**
     * Manage files via elFinder
     */
    public function actionFiles()
    {
        $this->response = App::$View->render('files', [
            'connector' => App::$Alias->scriptUrl . '/api/main/files?lang=' . App::$Request->getLanguage()
        ]);
    }

    public function actionAntivirus()
    {
        $this->response = App::$View->render('antivirus', [

        ]);
    }
}